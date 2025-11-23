<?php
require "db.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $course_code = $_POST["course_code"] ?? "";
    $course_name = $_POST["course_name"] ?? "";
    $course_credits = $_POST["course_credits"] ?? "";
    $min_accuplacer_score = $_POST["min_accuplacer_score"] ?? null;

    $days = $_POST["days"] ?? [];
    $is_asynchronous = in_array("N/A", $days);

    if (!$is_asynchronous) {
        $start_hour = $_POST["start_time_hour"] ?? "";
        $start_minute = $_POST["start_time_minute"] ?? "";
        $start_ampm = $_POST["start_time_ampm"] ?? "";
        $end_hour = $_POST["end_time_hour"] ?? "";
        $end_minute = $_POST["end_time_minute"] ?? "";
        $end_ampm = $_POST["end_time_ampm"] ?? "";

        function convertTo24Hour($hour, $minute, $ampm) {
            $hour = str_pad((int)$hour, 2, "0", STR_PAD_LEFT);
            $minute = str_pad((int)$minute, 2, "0", STR_PAD_LEFT);
            if (strtoupper($ampm) === "PM" && $hour !== "12") {
                $hour = (string)($hour + 12);
            }
            if (strtoupper($ampm) === "AM" && $hour === "12") {
                $hour = "00";
            }
            return "$hour:$minute:00";
        }

        $start_time = convertTo24Hour($start_hour, $start_minute, $start_ampm);
        $end_time = convertTo24Hour($end_hour, $end_minute, $end_ampm);
    }

    try {
        // Insert into courses (ignore duplicates)
        $stmt = $conn->prepare("INSERT IGNORE INTO courses (course_code, course_name, course_credits, min_accuplacer_score) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $course_code,
            $course_name,
            $course_credits,
            $min_accuplacer_score !== "" ? $min_accuplacer_score : null
        ]);

        // Insert into course_offerings (no semester)
        $stmt = $conn->prepare("INSERT INTO course_offerings (course_code) VALUES (?)");
        $stmt->execute([$course_code]);
        $offering_id = $conn->lastInsertId();

        // Insert into course_schedule
        $stmt = $conn->prepare("INSERT INTO course_schedule (offering_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");

        if ($is_asynchronous) {
            $stmt->execute([$offering_id, "N/A", null, null]);
        } else {
            foreach ($days as $day) {
                $stmt->execute([$offering_id, $day, $start_time, $end_time]);
            }
        }

        echo json_encode(["status" => "success", "message" => "Course added successfully."]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
}
?>