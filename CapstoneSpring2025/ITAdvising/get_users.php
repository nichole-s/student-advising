<?php
require "db.php";

header("Content-Type: application/json");

try {
    $search = $_GET["search"] ?? "";
    $column = $_GET["column"] ?? "last_name";
    $order = strtoupper($_GET["order"] ?? "ASC");
    $page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
    $recordsPerPage = isset($_GET["recordsPerPage"]) ? ($_GET["recordsPerPage"] === "All" ? 1000 : (int)$_GET["recordsPerPage"]) : 25;
    $offset = ($page - 1) * $recordsPerPage;
    $fetchLimit = $recordsPerPage + 1; // Fetch extra record to detect next page

    $validColumns = ["user_id", "first_name", "last_name", "email"];
    if (!in_array($column, $validColumns)) {
        $column = "last_name";
    }

    $params = [];
    $whereClause = "";

    if (!empty($search)) {
        if (is_numeric($search)) {
            $whereClause = "WHERE user_id = :search_id";
            $params[":search_id"] = (int)$search;
        } else {
            $whereClause = "WHERE first_name LIKE :search OR last_name LIKE :search OR email LIKE :search";
            $params[":search"] = "%$search%";
        }
    }

    $sql = "
        SELECT user_id, first_name, last_name, email
        FROM users
        $whereClause
        ORDER BY $column $order
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $conn->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->bindValue(":limit", $fetchLimit, PDO::PARAM_INT);
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);

    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $hasNextPage = count($users) > $recordsPerPage;
    if ($hasNextPage) {
        array_pop($users); // Remove extra record
    }

    echo json_encode([
        "status" => "success",
        "users" => $users,
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

