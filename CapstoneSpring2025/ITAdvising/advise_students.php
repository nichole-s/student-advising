<?php require "db.php"; ?>

<div class="manage-container">
    <h2>Advise Students</h2>

    <!-- Search -->
    <!-- <div>
        <input type="text" id="studentSearchInput" placeholder="Search by name or STAR ID">
        <button id="triggerSearchButton">Search</button>
        <div id="searchResults" class="search-results"></div>
    </div> -->

    <!-- Student Info -->
    <div id="studentInfo" style="display: none;">
        <div id="emphasisNotes" class="emphasis-note"></div>
        <h3 id="studentName"></h3>

        <!-- Emphasis Dropdown -->
        <div class="emphasis-row">
            <label for="emphasisSelect">Update Emphasis:</label>
            <select id="emphasisSelect"></select>
            <button onclick="saveEmphasis()">Save Emphasis</button>
        </div>

        <!-- Columns -->
        <div class="drag-columns">
            <div class="column course-column" data-column-type="taken">
                <h4>Taken</h4>
                <div id="taken" class="dropzone"></div>
            </div>
            <div class="column course-column" data-column-type="recommended">
                <h4>Recommended</h4>
                <div id="recommended" class="dropzone"></div>
            </div>
            <div class="column course-column" data-column-type="required">
                <h4>Required</h4>
                <div id="required" class="dropzone"></div>
            </div>
        </div>

        <!-- Notes -->
        <h4>Advising Notes</h4>
        <textarea id="advisingNote" placeholder="Enter a note..."></textarea>
        <button onclick="saveNote()">Save Note</button>
        <div id="noteHistory"></div>

        <!-- Download Summary -->
        <button id="downloadSummaryBtn">Download Advising Summary</button>

    </div>
</div>
