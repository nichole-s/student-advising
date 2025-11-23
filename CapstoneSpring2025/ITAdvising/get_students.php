<?php
require "db.php";

header("Content-Type: application/json");

try {
    // Get search parameters
    $search = isset($_GET["search"]) ? trim($_GET["search"]) : "";
    $emphasis = isset($_GET["emphasis"]) && $_GET["emphasis"] !== "" ? (int)$_GET["emphasis"] : null;
    $column = $_GET["column"] ?? "last_name";
    $order = strtoupper($_GET["order"] ?? "ASC");
    $page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
    $recordsPerPage = isset($_GET["recordsPerPage"]) ? ($_GET["recordsPerPage"] === "All" ? 1000 : (int)$_GET["recordsPerPage"]) : 25;
    $offset = ($page - 1) * $recordsPerPage;
    $fetchLimit = $recordsPerPage + 1; // Fetch one extra record to check if another page exists

    // Validate sorting column
    $validColumns = ["star_id", "first_name", "last_name", "email", "emphasis_name"];
    if (!in_array($column, $validColumns)) {
        $column = "last_name"; 
    }

    // Build query conditions
    $queryConditions = "WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        $queryConditions .= " AND (Students.star_id LIKE :search 
                                  OR Students.first_name LIKE :search 
                                  OR Students.last_name LIKE :search
                                  OR Students.email LIKE :search)";
        $params[":search"] = "%$search%";
    }

    if ($emphasis !== null) {
        $queryConditions .= " AND Students.emphasis_id = :emphasis";
        $params[":emphasis"] = $emphasis;
    }

    // Data Fetch Query
    $sqlDataFetch = "SELECT Students.star_id, Students.first_name, Students.last_name, Students.email, Emphasis.emphasis_name
                     FROM Students 
                     LEFT JOIN Emphasis ON Students.emphasis_id = Emphasis.emphasis_id
                     $queryConditions
                     ORDER BY $column $order
                     LIMIT :limit OFFSET :offset";

    $stmtDataFetch = $conn->prepare($sqlDataFetch);

    foreach ($params as $key => $value) {
        $stmtDataFetch->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmtDataFetch->bindValue(":limit", $fetchLimit, PDO::PARAM_INT);
    $stmtDataFetch->bindValue(":offset", $offset, PDO::PARAM_INT);

    $stmtDataFetch->execute();
    $students = $stmtDataFetch->fetchAll(PDO::FETCH_ASSOC);

    $hasNextPage = count($students) > $recordsPerPage;
    if ($hasNextPage) {
        array_pop($students); // Remove the extra record
    }

    echo json_encode([
        "status" => "success",
        "students" => $students,
        "currentPage" => $page,
        "hasNextPage" => $hasNextPage
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>