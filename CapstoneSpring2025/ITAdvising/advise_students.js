console.log("advise_students.js loaded");

function initializeAdvisingPage() {
    console.log("Initializing advising page...");
    setupDragAndDrop();

    const btn = document.getElementById("triggerSearchButton");
    if (btn) {
        console.log("Binding Search button directly");
        btn.addEventListener("click", triggerSearch);
    } else {
        console.warn("Search button not found when initializing");
    }

    const electiveSection = document.getElementById("electivesSection");
    if (electiveSection && sessionStorage.getItem("electivesVisible") === "true") {
        electiveSection.style.display = "block";
    }
}

function setupDragAndDrop() {
    const dropzones = document.querySelectorAll(".dropzone");

    dropzones.forEach(dropzone => {
        dropzone.addEventListener("dragover", function (e) {
            e.preventDefault();
        });

        dropzone.addEventListener("drop", function (e) {
            e.preventDefault();
            e.stopPropagation(); // Stop bubbling just in case

            const rawId = e.dataTransfer.getData("text/plain");
            const courseId = rawId.replace("course_", "");
            const dragged = document.getElementById(rawId);

            if (dragged && dropzone !== dragged.parentNode) {
                dropzone.querySelectorAll("p").forEach(p => p.remove());

                if (dragged.parentNode) {
                    dragged.parentNode.removeChild(dragged);
                }

                dropzone.appendChild(dragged);

                const columnType = dropzone.parentNode.dataset.columnType;
                updateCourseRecommendation(courseId, columnType);
            }
        });
    });
}


function triggerSearch() {
    const searchInput = document.getElementById("studentSearchInput");
    const resultsBox = document.getElementById("searchResults");
    const query = searchInput.value.trim();

    console.log("triggerSearch() called");
    console.log("Query entered:", query);

    if (query.length < 2) {
        resultsBox.innerHTML = "";
        return;
    }

    fetch(`search_students.php?q=${encodeURIComponent(query)}`)
        .then(response => response.text())
        .then(raw => {
            console.log("Raw response from search_students.php:", raw);

            try {
                const data = JSON.parse(raw);
                console.log("Parsed JSON:", data);

                if (Array.isArray(data) && data.length > 0) {
                    loadStudentData(data[0].id);
                } else {
                    resultsBox.innerHTML = "<p>No matches found</p>";
                }
            } catch (err) {
                console.error("JSON parsing failed:", err);
            }
        })
        .catch(error => {
            console.error("Fetch error:", error);
        });
}

function updateCourseRecommendation(courseId, columnType) {
    const starId = document.getElementById("studentInfo")?.dataset.starId;

    if (!starId) {
        console.warn("No student selected, can't update course recommendation.");
        return;
    }

    const bodyData = { 
        courseId: courseId, 
        columnType: columnType, 
        starId: starId 
    };

    console.log("Sending update request with:", bodyData);  

    fetch("update_course_column.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ courseId, columnType, starId })
    })
    .then(res => res.json())
    .then(data => {
        console.log("update_course_column.php response:", data);
        if (data.success) {
            console.log("Course column updated successfully");
            loadStudentData(starId);
        } else {
            console.error("Failed:", data.message);
            alert("Failed to update course column: " + data.message);
        }
    })
    .catch(error => {
        console.error("Error during updateCourseRecommendation:", error);
    });
}


function loadStudentData(studentId) {
    console.log("Starting loadStudentData for:", studentId);

    fetch(`search_students.php?q=${encodeURIComponent(studentId)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Network response was not ok (${response.status})`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Fetched student data:", data);

            if (!Array.isArray(data) || data.length === 0) {
                alert("No student found.");
                return;
            }

            const student = data[0];
            const infoBox = document.getElementById("studentInfo");
            infoBox.style.display = "block";
            infoBox.dataset.starId = student.id;

            // Leaving this here for future notes based on emphasis
            const emphasisNotes = document.getElementById("emphasisNotes");
            switch (student.emphasis_id) {
                case 1:
                case 2:
                case 3:
                case 4:
                    emphasisNotes.textContent = "";
                    break;
                default:
                    emphasisNotes.textContent = "";
            }

            const emphasisName = student.emphasis_name || "";
            document.getElementById("studentName").textContent = `${student.id} – ${student.name} – ${emphasisName}`;
            loadEmphasisOptions(student.emphasis_id);

            const downloadBtn = document.getElementById("downloadSummaryBtn");
            if (downloadBtn) {
                downloadBtn.onclick = () => {
                    window.open(`download_summary.php?star_id=${student.id}`, "_blank");
                };
            }

            document.getElementById("taken").innerHTML = "";
            document.getElementById("recommended").innerHTML = "";
            document.getElementById("required").innerHTML = "";

            if (student.taken.length > 0) {
                student.taken.forEach(course => {
                    const div = createCourseCard(course);
                    document.getElementById("taken").appendChild(div);
                });
            } else {
                document.getElementById("taken").innerHTML = "<p>No courses taken yet.</p>";
            }

            if (student.recommended.length > 0) {
                student.recommended.forEach(course => {
                    const div = createCourseCard(course);
                    document.getElementById("recommended").appendChild(div);
                });
            } else {
                document.getElementById("recommended").innerHTML = "<p>No courses recommended.</p>";
            }

            if (student.required.length > 0) {
                const requiredHeader = document.createElement("h5");
                requiredHeader.textContent = "Required Courses";
                document.getElementById("required").appendChild(requiredHeader);
                student.required.forEach(course => {
                    const div = createCourseCard(course);
                    document.getElementById("required").appendChild(div);
                });
            }

            const existingToggle = document.getElementById("toggleElectivesBtn");
            if (existingToggle) existingToggle.remove();
            const existingElectives = document.getElementById("electivesSection");
            if (existingElectives) existingElectives.remove();

            if (student.electives && student.electives.length > 0) {
                const electiveHeader = document.createElement("h5");
                electiveHeader.id = "toggleElectivesBtn";
                electiveHeader.textContent = "Other";
                electiveHeader.style.cursor = "pointer";
                electiveHeader.onclick = () => {
                    const electiveSection = document.getElementById("electivesSection");
                    const isVisible = electiveSection.style.display === "block";
                    electiveSection.style.display = isVisible ? "none" : "block";
                    sessionStorage.setItem("electivesVisible", !isVisible);
                };

                const electiveSection = document.createElement("div");
                electiveSection.id = "electivesSection";
                electiveSection.style.display = sessionStorage.getItem("electivesVisible") === "true" ? "block" : "none";
                electiveSection.className = "dropzone";
                electiveSection.dataset.columnType = "required";

                student.electives.forEach(course => {
                    const div = createCourseCard(course);
                    electiveSection.appendChild(div);
                });

                document.getElementById("required").appendChild(electiveHeader);
                document.getElementById("required").appendChild(electiveSection);
            } else if (!student.required.length) {
                document.getElementById("required").innerHTML = "<p>All required and elective courses complete.</p>";
            }

            // Load advising notes after everything else
            console.log("Now loading notes for student:", student.id);
            loadNotes(student.id);
        })
        .catch(error => {
            console.error("Error loading student data:", error);
            alert("Error loading student information. Please try again.");
        });
}


function createCourseCard(course) {
    const div = document.createElement("div");
    div.id = "course_" + course.course_code;
    div.textContent = course.course_code + ": " + course.course_name;
    div.className = "draggable";
    div.setAttribute("draggable", "true");

    div.addEventListener("dragstart", function (e) {
        e.dataTransfer.setData("text/plain", div.id);
    });

    return div;
}

function loadAdvisingView(star_id) {
    console.log("➡️ Loading advising view for student:", star_id);
    document.getElementById("studentSearchTable").style.display = "none";
    document.querySelector(".table-controls").style.display = "none";
    loadStudentData(star_id);
}

function saveNote() {
    const starId = document.getElementById("studentInfo")?.dataset.starId;
    const noteText = document.getElementById("advisingNote").value.trim();

    if (!starId || noteText === "") {
        alert("Cannot save note. Either no student selected or the note is empty.");
        return;
    }

    fetch("save_note.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ star_id: starId, note_text: noteText })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            console.log("Note saved successfully.");
            document.getElementById("advisingNote").value = "";
            loadNotes(starId);
        } else {
            console.error("Failed to save note:", data.message);
            alert("Error saving note.");
        }
    })
    .catch(err => {
        console.error("Fetch error while saving note:", err);
    });
}

function loadNotes(starId) {
    fetch(`get_notes.php?star_id=${encodeURIComponent(starId)}`)
        .then(res => res.json())
        .then(data => {
            const noteHistory = document.getElementById("noteHistory");
            noteHistory.innerHTML = "";

            if (!data.success || !Array.isArray(data.notes) || data.notes.length === 0) {
                noteHistory.innerHTML = "<p>No advising notes found.</p>";
                return;
            }

            data.notes.forEach(note => {
                const div = document.createElement("div");
                div.className = "note-entry";
                div.innerHTML = `<strong>${new Date(note.note_date).toLocaleString()}</strong><br>${note.note_text}`;
                noteHistory.appendChild(div);
            });
        })
        .catch(err => {
            console.error("Error loading notes:", err);
        });
}

function loadEmphasisOptions(selectedId) {
    fetch("get_emphases.php")
        .then(res => res.json())
        .then(data => {
            const dropdown = document.getElementById("emphasisSelect");
            dropdown.innerHTML = "";

            data.forEach(emphasis => {
                const option = document.createElement("option");
                option.value = emphasis.emphasis_id;
                option.textContent = emphasis.emphasis_name;
                if (emphasis.emphasis_id == selectedId) {
                    option.selected = true;
                }
                dropdown.appendChild(option);
            });
        })
        .catch(err => {
            console.error("Error loading emphases:", err);
        });
}

function saveEmphasis() {
    const starId = document.getElementById("studentInfo")?.dataset.starId;
    const newEmphasis = document.getElementById("emphasisSelect").value;

    if (!starId || !newEmphasis) {
        alert("Missing student or emphasis.");
        return;
    }

    fetch("update_emphasis.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ star_id: starId, emphasis_id: newEmphasis })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            console.log("Emphasis updated.");
            loadStudentData(starId);
        } else {
            alert("Failed to update emphasis.");
            console.error("Server message:", data.message);
        }
    })
    .catch(err => {
        console.error("Error updating emphasis:", err);
    });
}

initializeAdvisingPage();