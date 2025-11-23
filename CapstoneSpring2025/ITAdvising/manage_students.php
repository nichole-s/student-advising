<?php
require "db.php";

// Fetch emphasis options
$stmt = $conn->query("SELECT emphasis_id, emphasis_name FROM Emphasis ORDER BY emphasis_name ASC");
$emphasisOptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="manage-container">
    <h2>Manage Students</h2>

    <!-- Navigation Buttons -->
    <div class="section-buttons">
        <button class="button-secondary" onclick="showSection('searchStudents')">Search Students</button>
        <button class="button-secondary" onclick="showSection('addStudent')">Add Student</button>
        <button class="button-secondary" onclick="showSection('studentList')">Student List</button>
    </div>

    <form id="uploadForm" enctype="multipart/form-data">
        <h3>Upload Student CSV</h3>
        <input type="file" name="csvFile" id="csvFile" accept=".csv" required>
        <button type="submit">Upload</button>
        <div id="uploadMessage"></div>
    </form>
    

    <!-- Search Students Section -->
    <div id="searchStudents" class="entity-section" style="display: none;">
        <h3>Search Students</h3>
        <form onsubmit="event.preventDefault(); searchEntities('students');">
            <input type="text" id="searchInput" placeholder="Search by name or STAR ID">
            <select id="searchEmphasis">
                <option value="">-- Search by Emphasis --</option>
                <?php foreach ($emphasisOptions as $emphasis): ?>
                    <option value="<?= $emphasis['emphasis_id'] ?>"><?= htmlspecialchars($emphasis['emphasis_name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="button-primary">Search</button>
            <button type="button" class="button-cancel" onclick="cancelSection('searchStudents')">Cancel</button>
        </form>
    </div>

    <!-- Add Student Section -->
    <div id="addStudent" class="entity-section" style="display: none;">
        <h3>Add Student</h3>
        <form id="studentForm" onsubmit="event.preventDefault(); submitEntity('students');">
            <input type="text" id="star_id" name="star_id" placeholder="STAR ID" required>
            <input type="text" id="first_name" name="first_name" placeholder="First Name" required>
            <input type="text" id="last_name" name="last_name" placeholder="Last Name" required>
            <input type="email" id="email" name="email" placeholder="Email" required>
            <select id="emphasis_id" name="emphasis_id">
                <option value="">-- Select Emphasis --</option>
                <?php foreach ($emphasisOptions as $emphasis): ?>
                    <option value="<?= $emphasis['emphasis_id'] ?>"><?= htmlspecialchars($emphasis['emphasis_name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="button-primary">Add Student</button>
            <button type="button" class="button-cancel" onclick="cancelSection('addStudent')">Cancel</button>
        </form>
    </div>

    <!-- Edit Modal (Always Present) -->
    <div id="editModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <div id="editModalContent">
                <p>Loading...</p>
            </div>
        </div>
    </div>

    <!-- Student List Section (No Table Here) -->
    <div id="studentList" class="entity-section" style="display: none;">
        <h3>Student List</h3>
        <button class="button-cancel" onclick="cancelSection('studentList')">Cancel</button>
    </div>

    <!-- Shared Table (Used for Search Students & Student List) -->
    <table id="studentsTable" class="manage-table">
        <thead>
            <tr>
                <th><button class="button-primary" onclick="sortEntities('students', 'star_id')">STAR ID</button></th>
                <th><button class="button-primary" onclick="sortEntities('students', 'first_name')">First Name</button></th>
                <th><button class="button-primary" onclick="sortEntities('students', 'last_name')">Last Name</button></th>
                <th><button class="button-primary" onclick="sortEntities('students', 'email')">Email</button></th>
                <th><button class="button-primary" onclick="sortEntities('students', 'emphasis_name')">Emphasis</button></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <!-- Controls Below the Table -->
    <div class="table-controls">
        <!-- Records per Page Selection -->
        <label for="recordsPerPage">Records per page:</label>
        <select id="recordsPerPage" onchange="loadEntities('students')">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="All">All</option>
        </select>

        <!-- Pagination Controls -->
        <div class="pagination">
            <button class="button-primary" onclick="prevPage()">Prev</button>
            <span id="pageInfo"></span>
            <button class="button-primary" onclick="nextPage()">Next</button>
        </div>
    </div>
</div>

<script src="manage_entities.js"></script>

<script>
    if (typeof setupStudentUploadForm === 'function') {
        setupStudentUploadForm();
    }
</script>


