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
            if (strtoupper($ampm) === "PM" && $hour !== "12") $hour += 12;
            if (strtoupper($ampm) === "AM" && $hour === "12") $hour = "00";
            return "$hour:$minute:00";
        }

        $start_time = convertTo24Hour($start_hour, $start_minute, $start_ampm);
        $end_time = convertTo24Hour($end_hour, $end_minute, $end_ampm);
    }

    try {
        // Update courses
        $stmt = $conn->prepare("UPDATE courses SET course_name = ?, course_credits = ?, min_accuplacer_score = ? WHERE course_code = ?");
        $stmt->execute([
            $course_name,
            $course_credits,
            $min_accuplacer_score !== "" ? $min_accuplacer_score : null,
            $course_code
        ]);

        // Update offering (assume one per course for now)
        $stmt = $conn->prepare("SELECT offering_id FROM course_offerings WHERE course_code = ?");
        $stmt->execute([$course_code]);
        $offering = $stmt->fetch(PDO::FETCH_ASSOC);
        $offering_id = $offering["offering_id"] ?? null;

        if (!$offering_id) {
            echo json_encode(["status" => "error", "message" => "Offering not found."]);
            exit;
        }

        // Delete old schedule
        $conn->prepare("DELETE FROM course_schedule WHERE offering_id = ?")->execute([$offering_id]);

        // Insert updated schedule
        $stmt = $conn->prepare("INSERT INTO course_schedule (offering_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");

        if ($is_asynchronous) {
            $stmt->execute([$offering_id, "N/A", null, null]);
        } else {
            foreach ($days as $day) {
                $stmt->execute([$offering_id, $day, $start_time, $end_time]);
            }
        }

        echo json_encode(["status" => "success", "message" => "Course updated successfully."]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
    exit;
}

// If GET: render the edit form
$course_code = $_GET["id"] ?? "";
if (!$course_code) {
    echo "<p>Error: Course code required.</p>";
    exit;
}

$stmt = $conn->prepare("
    SELECT 
        c.course_code, c.course_name, c.course_credits, c.min_accuplacer_score,
        s.day_of_week, s.start_time, s.end_time
    FROM courses c
    LEFT JOIN course_offerings o ON c.course_code = o.course_code
    LEFT JOIN course_schedule s ON o.offering_id = s.offering_id
    WHERE c.course_code = ?
");
$stmt->execute([$course_code]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$results) {
    echo "<p>Error: Course not found.</p>";
    echo "<!-- Debug: course_code = $course_code -->";
    exit;
}

$first = $results[0];
$selected_days = array_column($results, "day_of_week");
$is_async = in_array("N/A", $selected_days);

function to12Hour($time) {
    if ($time === "N/A" || !$time) return ["", "", ""];

    $parts = explode(":", $time);
    $hour = (int)$parts[0];
    $minute = $parts[1];
    $ampm = $hour >= 12 ? "PM" : "AM";
    $hour = $hour % 12;
    if ($hour === 0) $hour = 12;

    return [$hour, $minute, $ampm];
}

[$start_hour, $start_minute, $start_ampm] = to12Hour($first["start_time"]);
[$end_hour, $end_minute, $end_ampm] = to12Hour($first["end_time"]);
?>

<div class="modal-form-content">
    <form id="editCoursesForm">
        <input type="hidden" name="course_code" value="<?= htmlspecialchars($first["course_code"]) ?>">

        <label>Course Name</label>
        <input type="text" name="course_name" value="<?= htmlspecialchars($first["course_name"]) ?>" required>

        <label>Credits</label>
        <input type="number" name="course_credits" value="<?= htmlspecialchars($first["course_credits"]) ?>" required>

        <label>Minimum Accuplacer Score (optional)</label>
        <input type="number" name="min_accuplacer_score" step="0.01" value="<?= htmlspecialchars($first["min_accuplacer_score"] ?? "") ?>">

        <label>Days of the Week</label><br>
        <?php
        $weekdays = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
        foreach ($weekdays as $day) {
            $checked = in_array($day, $selected_days) ? "checked" : "";
            echo "<label><input type='checkbox' name='days[]' value='$day' $checked> $day</label> ";
        }
        ?>
        <br><label><input type="checkbox" name="days[]" value="N/A" id="asyncCheckbox" <?= $is_async ? "checked" : "" ?>> Asynchronous</label>

        <div id="timeInputs" <?= $is_async ? 'style="display:none;"' : '' ?>>
            <label>Start Time</label><br>
            <input type="text" name="start_time_hour" size="2" placeholder="01–12" value="<?= $start_hour ?>">
            :
            <input type="text" name="start_time_minute" size="2" placeholder="00–59" value="<?= $start_minute ?>">
            <select name="start_time_ampm">
                <option value="AM" <?= $start_ampm === "AM" ? "selected" : "" ?>>AM</option>
                <option value="PM" <?= $start_ampm === "PM" ? "selected" : "" ?>>PM</option>
            </select>

            <br><label>End Time</label><br>
            <input type="text" name="end_time_hour" size="2" placeholder="01–12" value="<?= $end_hour ?>">
            :
            <input type="text" name="end_time_minute" size="2" placeholder="00–59" value="<?= $end_minute ?>">
            <select name="end_time_ampm">
                <option value="AM" <?= $end_ampm === "AM" ? "selected" : "" ?>>AM</option>
                <option value="PM" <?= $end_ampm === "PM" ? "selected" : "" ?>>PM</option>
            </select>
        </div>

        <br><br>
        <button class="button-primary" onclick="event.preventDefault(); saveEntityEdit('courses');">Save Changes</button>
        <button class="button-cancel" type="button" onclick="closeEditModal()">Cancel</button>
    </form>
</div>

<script>
document.getElementById("asyncCheckbox").addEventListener("change", function () {
    const timeInputs = document.getElementById("timeInputs");
    timeInputs.style.display = this.checked ? "none" : "block";
});
</script>

