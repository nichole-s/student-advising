<?php require "db.php"; ?>

<div class="manage-container">
    <h2>Manage Courses</h2>
    <div class="section-buttons">
        <button class="button-secondary" onclick="showSection('searchCourses')">Search Courses</button>
        <button class="button-secondary" onclick="showSection('addCourse')">Add Course</button>
        <button class="button-secondary" onclick="showSection('courseList')">Course List</button>
    </div>

    <div id="searchCourses" class="entity-section" style="display: none;">
        <h3>Search Courses</h3>
        <form onsubmit="event.preventDefault(); searchEntities('courses');">
            <input type="text" id="searchInput" placeholder="Search by course name or code">
            <button type="submit" class="button-primary">Search</button>
            <button type="button" class="button-cancel" onclick="cancelSection('searchCourses')">Cancel</button>
        </form>
    </div>

    <div id="addCourse" class="entity-section" style="display: none;">
        <h3>Add Course</h3>
        <form id="courseForm" onsubmit="event.preventDefault(); submitEntity('courses');">
            <input type="text" name="course_code" placeholder="Course Code" required>
            <input type="text" name="course_name" placeholder="Course Name" required>
            <input type="number" name="course_credits" placeholder="Credits" required>

            <label>Minimum Accuplacer Score (optional)</label>
            <input type="number" name="min_accuplacer_score" step="0.01">

            <label>Days of the Week:</label><br>
            <label><input type="checkbox" name="days[]" value="Monday"> Mon</label>
            <label><input type="checkbox" name="days[]" value="Tuesday"> Tue</label>
            <label><input type="checkbox" name="days[]" value="Wednesday"> Wed</label>
            <label><input type="checkbox" name="days[]" value="Thursday"> Thu</label>
            <label><input type="checkbox" name="days[]" value="Friday"> Fri</label><br>
            <label><input type="checkbox" id="asyncCheckbox" name="days[]" value="N/A"> Asynchronous</label>

            <div id="timeInputs">
                <label>Start Time:</label><br>
                <input type="text" name="start_time_hour" placeholder="HH" size="2">
                :
                <input type="text" name="start_time_minute" placeholder="MM" size="2">
                <select name="start_time_ampm">
                    <option value="AM">AM</option>
                    <option value="PM">PM</option>
                </select>

                <br><label>End Time:</label><br>
                <input type="text" name="end_time_hour" placeholder="HH" size="2">
                :
                <input type="text" name="end_time_minute" placeholder="MM" size="2">
                <select name="end_time_ampm">
                    <option value="AM">AM</option>
                    <option value="PM">PM</option>
                </select>
            </div>

            <br><br>
            <button type="submit" class="button-secondary">Add Course</button>
            <button type="button" class="button-cancel" onclick="cancelSection('addCourse')">Cancel</button>
        </form>
    </div>

    <script>
    document.getElementById("asyncCheckbox").addEventListener("change", function () {
        const timeInputs = document.getElementById("timeInputs");
        const dayCheckboxes = document.querySelectorAll("input[name='days[]']:not(#asyncCheckbox)");

        if (this.checked) {
            timeInputs.style.display = "none";
            dayCheckboxes.forEach(cb => cb.disabled = true);
        } else {
            timeInputs.style.display = "block";
            dayCheckboxes.forEach(cb => cb.disabled = false);
        }
    });
    </script>

    <!-- Edit Modal -->
    <div id="editModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <div id="editModalContent">
                <p>Loading...</p>
            </div>
        </div>
    </div>

    <div id="courseList" class="entity-section" style="display: none;">
        <h3>Course List</h3>
        <button class="button-cancel" onclick="cancelSection('courseList')">Cancel</button>
    </div>

    <table id="coursesTable" class="manage-table">
        <thead>
            <tr>
                <th><button class="button-primary" onclick="sortEntities('courses', 'course_code')">Course Code</button></th>
                <th><button class="button-primary" onclick="sortEntities('courses', 'course_name')">Course Name</button></th>
                <th><button class="button-primary" onclick="sortEntities('courses', 'course_credits')">Credits</button></th>
                <th><button class="button-primary" onclick="sortEntities('courses', 'min_accuplacer_score')">Min Accuplacer Score</button></th>
                <th>Days</th>
                <th>Start</th>
                <th>End</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <div class="table-controls">
        <label for="recordsPerPage">Records per page:</label>
        <select id="recordsPerPage" onchange="loadEntities('courses')">
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="All">All</option>
        </select>
        <div class="pagination">
            <button onclick="prevPage()">Prev</button>
            <span id="pageInfo"></span>
            <button onclick="nextPage()">Next</button>
        </div>
    </div>
</div>

<script src="manage_entities.js"></script>