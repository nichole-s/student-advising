<?php
require "db.php";

ini_set('display_errors', 0); 
error_reporting(E_ALL);

header("Content-Type: application/json");

try {
    $mode = $_GET["mode"] ?? "";
    $course_code = $_GET["course_code"] ?? null;

    // ✅ Special case: return full details for a single course
    if ($mode === "single" && $course_code) {
        $stmt = $conn->prepare("
            SELECT 
                c.course_code, 
                c.course_name, 
                c.course_credits, 
                c.min_accuplacer_score, 
                GROUP_CONCAT(s.day_of_week ORDER BY s.schedule_id SEPARATOR ', ') AS days,
                MIN(s.start_time) AS start_time,
                MAX(s.end_time) AS end_time
            FROM courses c
            LEFT JOIN course_offerings o ON c.course_code = o.course_code
            LEFT JOIN course_schedule s ON o.offering_id = s.offering_id
            WHERE c.course_code = :course_code
            GROUP BY c.course_code, c.course_name, c.course_credits, c.min_accuplacer_score
        ");
        $stmt->execute([":course_code" => $course_code]);
        $course = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "status" => "success",
            "courses" => $course
        ]);
        exit;
    }

    // ✅ Default paginated list or search
    $search = $_GET["search"] ?? "";
    $column = $_GET["column"] ?? "course_name";
    $order = strtoupper($_GET["order"] ?? "ASC");
    $page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
    $recordsPerPage = ($_GET["recordsPerPage"] === "All") ? 1000 : (int)($_GET["recordsPerPage"] ?? 25);
    $offset = ($page - 1) * $recordsPerPage;

    $validColumns = ["course_code", "course_name", "course_credits", "min_accuplacer_score"];
    if (!in_array($column, $validColumns)) {
        $column = "course_name";
    }

    $params = [];

    $sql = "SELECT 
        c.course_code, 
        c.course_name, 
        c.course_credits, 
        c.min_accuplacer_score, 
        GROUP_CONCAT(s.day_of_week ORDER BY s.schedule_id SEPARATOR ', ') AS days,
        MIN(s.start_time) AS start_time,
        MAX(s.end_time) AS end_time
    FROM courses c
    LEFT JOIN course_offerings o ON c.course_code = o.course_code
    LEFT JOIN course_schedule s ON o.offering_id = s.offering_id
    WHERE 1=1";

    if (!empty($search)) {
        $sql .= " AND (c.course_code LIKE :search OR c.course_name LIKE :search)";
        $params[':search'] = "%$search%";
    }

    $sql .= " GROUP BY c.course_code, c.course_name, c.course_credits, c.min_accuplacer_score";
    $sql .= " ORDER BY $column $order LIMIT :limit OFFSET :offset";

    $stmt = $conn->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    $stmt->bindValue(":limit", $recordsPerPage, PDO::PARAM_INT);
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);

    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $hasNextPage = count($courses) === $recordsPerPage;

    echo json_encode([
        "status" => "success",
        "courses" => $courses,
        "currentPage" => $page,
        "hasNextPage" => $hasNextPage
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . $e->getMessage()
    ]);
}

