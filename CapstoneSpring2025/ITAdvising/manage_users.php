<?php
require "db.php";
?>

<div class="manage-container">
    <h2>Manage Users</h2>

    <!-- Navigation Buttons -->
    <div class="section-buttons">
        <button class="button-secondary" onclick="showSection('searchUsers')">Search Users</button>
        <button class="button-secondary" onclick="showSection('addUser')">Add User</button>
        <button class="button-secondary" onclick="showSection('userList')">User List</button>
    </div>

    <!-- Search Users Section -->
    <div id="searchUsers" class="entity-section" style="display: none;">
        <h3>Search Users</h3>
        <form onsubmit="event.preventDefault(); searchEntities('users');">
            <input type="text" id="searchInput" placeholder="Search by name or email">
            <button type="submit" class="button-primary">Search</button>
            <button type="button" class="button-cancel" onclick="cancelSection('searchUsers')">Cancel</button>
        </form>
    </div>

    <!-- Add User Section -->
    <div id="addUser" class="entity-section" style="display: none;">
        <h3>Add User</h3>
        <form id="userForm" onsubmit="event.preventDefault(); submitEntity('users');">
            <input type="text" id="first_name" name="first_name" placeholder="First Name" required>
            <input type="text" id="last_name" name="last_name" placeholder="Last Name" required>
            <input type="email" id="email" name="email" placeholder="Email" autocomplete="username" required>
            <input type="password" id="password" name="password" placeholder="Password" autocomplete="current-password" required>
            <button type="submit" class="button-secondary">Add User</button>
            <button type="button" class="button-cancel" onclick="cancelSection('addUser')">Cancel</button>
        </form>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <div id="editModalContent">
                <p>Loading...</p>
            </div>
        </div>
    </div>

    <!-- User List Section (No table here) -->
    <div id="userList" class="entity-section" style="display: none;">
        <h3>User List</h3>
        <button class="button-cancel" onclick="cancelSection('userList')">Cancel</button>
    </div>

    <!-- Shared Table (Visible in both search + list) -->
    <table id="usersTable" class="manage-table">
        <thead>
            <tr>
                <th><button class="button-primary" onclick="sortEntities('users', 'user_id')">User ID</button></th>
                <th><button class="button-primary" onclick="sortEntities('users', 'first_name')">First Name</button></th>
                <th><button class="button-primary" onclick="sortEntities('users', 'last_name')">Last Name</button></th>
                <th><button class="button-primary" onclick="sortEntities('users', 'email')">Email</button></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <!-- Table Controls -->
    <div class="table-controls">
        <label for="recordsPerPage">Records per page:</label>
        <select id="recordsPerPage" onchange="loadEntities('users')">
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="All">All</option>
        </select>

        <div class="pagination">
            <button class="button-primary" onclick="prevPage()">Prev</button>
            <span id="pageInfo"></span>
            <button class="button-primary" onclick="nextPage()">Next</button>
        </div>
    </div>
</div>

<script src="manage_entities.js"></script>