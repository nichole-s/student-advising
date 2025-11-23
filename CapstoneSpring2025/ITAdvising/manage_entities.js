console.log("manage_entities.js loaded correctly.");

let currentSort = { column: "last_name", order: "ASC" };
let currentPage = 1;
let recordsPerPage = 5;
let hasNextPage = false;
let currentEntity = "students"; // Default
let currentQuery = ""; // Default
let currentEmphasis = ""; // Default


if (!window.manageEntitiesLoaded) {
    window.manageEntitiesLoaded = true;


    document.addEventListener("DOMContentLoaded", function () {
        console.log("JavaScript Loaded - Initializing...");
        initializeEntityManagement();
    });

    function initializeEntityManagement() {
        console.log("Initialize script loaded...");

        const entity = getEntityType();
        console.log(`🔹 Managing: ${entity}`);
    
        const entityForm = document.getElementById(`${entity}Form`);
        if (entityForm) {
            entityForm.addEventListener("submit", function (event) {
                event.preventDefault();
                submitEntity(entity);
            });
        }
    }
    
    function getEntityType() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get("entity") || "students";
    }

    function showSection(sectionId) {
        console.log(`Switching to section: ${sectionId}`);
    
        // Hide all sections
        document.querySelectorAll(".entity-section").forEach(section => {
            section.style.display = "none";
        });
    
        // Show the target section
        const targetSection = document.getElementById(sectionId);
        if (targetSection) {
            targetSection.style.display = "block";
    
            // Update currentEntity based on sectionId
            if (sectionId === "studentList") currentEntity = "students";
            if (sectionId === "courseList") currentEntity = "courses";
            if (sectionId === "userList") currentEntity = "users";
    
            // Reset search parameters
            currentQuery = "";
            currentEmphasis = "";
    
            // Clear search inputs
            const searchInput = document.getElementById("searchInput");
            if (searchInput) searchInput.value = "";
    
            const searchEmphasis = document.getElementById("searchEmphasis");
            if (searchEmphasis) searchEmphasis.value = "";
    
            // Clear the table
            const table = document.querySelector(`#${currentEntity}Table tbody`);
            if (table) {
                table.innerHTML = "";
            }
    
            // Load entities for the new section
            if (sectionId === "studentList" || sectionId === "courseList" || sectionId === "userList") {
                console.log(`Calling loadEntities('${currentEntity}')...`);
                loadEntities(currentEntity);
            }
        } else {
            console.error(`Section '${sectionId}' not found.`);
        }
    }    

    function loadEntities(entity, query = currentQuery, column = "last_name", order = "ASC", page = 1, emphasis = currentEmphasis) {
        
        currentPage = page;
        currentQuery = query;
        currentEmphasis = emphasis;

        recordsPerPage = document.getElementById("recordsPerPage")?.value || 25;
        let url = `get_${entity}.php?search=${encodeURIComponent(query)}&column=${column}&order=${order}&page=${page}&recordsPerPage=${recordsPerPage}`;

        if (entity === "students" && emphasis) {
            url += `&emphasis=${encodeURIComponent(emphasis)}`;
        }

        console.log(`🔍 Fetching Data: ${url}`);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.status !== "success") {
                    console.error(`Error fetching ${entity}: ${data.message}`);
                    return;
                }

                const entityTable = document.querySelector(`#${entity}Table tbody`);
                if (!entityTable) {
                    console.error(`Table for ${entity} not found.`);
                    return;
                } 

                entityTable.innerHTML = "";
                const items = data.students || data.courses || data.users || [];

                if (items.length === 0) {
                    entityTable.innerHTML = `<tr><td colspan="5">No ${entity} found</td></tr>`;
                    return;
                }

                hasNextPage = data.hasNextPage;

                items.forEach(item => {
                    const row = document.createElement("tr");
                    row.setAttribute("data-id", item.star_id || item.course_code || item.user_id);

                    if (entity === "students") {
                        row.innerHTML = `
                            <td>${item.star_id}</td>
                            <td>${item.first_name}</td>
                            <td>${item.last_name}</td>
                            <td>${item.email || "N/A"}</td>
                            <td>${item.emphasis_name || "N/A"}</td>`;

                        // Context-specific action buttons
                        if (window.location.pathname.includes("advise_students.php")) {
                            row.innerHTML += `
                                <td>
                                    <button onclick="loadAdvisingView('${item.star_id}')">Advise</button>
                                </td>`;
                        } else {
                            row.innerHTML += `
                                <td>
                                    <button onclick="editEntity('students', '${item.star_id}')">Edit</button>
                                    <button onclick="deleteEntity('students', '${item.star_id}')" data-id="${item.star_id}">Delete</button>
                                    <button onclick="navigateToAdvising('${item.star_id}')">Advise</button>
                                </td>`;
                        }
                    } else if (entity === "courses") {
                        row.innerHTML = `
                            <td>${item.course_code}</td>
                            <td>${item.course_name}</td>
                            <td>${item.course_credits}</td>
                            <td>${item.min_accuplacer_score ?? "N/A"}</td>
                            <td>${item.days || "N/A"}</td>
                            <td>${item.start_time || "N/A"}</td>
                            <td>${item.end_time || "N/A"}</td>
                            <td>
                                <button onclick="editEntity('courses', '${item.course_code}')">Edit</button>
                                <button onclick="deleteEntity('courses', '${item.course_code}')" data-id="${item.course_code}">Delete</button>
                            </td>`;
                    } else if (entity === "users") {
                        row.innerHTML = `
                            <td>${item.user_id}</td>
                            <td>${item.first_name}</td>
                            <td>${item.last_name}</td>
                            <td>${item.email}</td>
                            <td>
                                <button onclick="editEntity('users', '${item.user_id}')">Edit</button>
                                <button onclick="deleteEntity('users', '${item.user_id}')" data-id="${item.user_id}">Delete</button>
                            </td>`;
                    }

                    entityTable.appendChild(row);
                });

                console.log("All entities added to table.");
                document.getElementById("pageInfo").textContent = `Page ${currentPage}`;

            })
            .catch(error => console.error(`Error fetching ${entity}:`, error));
    }

    function prevPage() {
        console.log("Going to previous page...");
        if (currentPage > 1) {
            currentPage--;
            loadEntities(currentEntity, currentQuery, currentSort.column, currentSort.order, currentPage, currentEmphasis);
        }
    }
    
    function nextPage() {
        console.log("Going to next page...");
        if (hasNextPage) {
            currentPage++;
            loadEntities(currentEntity, currentQuery, currentSort.column, currentSort.order, currentPage, currentEmphasis);
        }
    } 
    
}

function waitForUploadForm() {
    const uploadForm = document.getElementById("uploadForm");
    if (uploadForm) {
        console.log("Upload form found. Setting up upload listener.");
        setupStudentUploadForm();
    } else {
        console.log("Waiting for upload form...");
        setTimeout(waitForUploadForm, 100); // Check again in 100ms
    }
}


function setupStudentUploadForm() {
    const uploadForm = document.getElementById("uploadForm");
    if (!uploadForm) {
        console.warn("Upload form not found!");
        return;
    }

    console.log("Setting up upload form...");

    // First remove any previous event listeners, using a safe pattern
    uploadForm.addEventListener("submit", handleStudentUpload, { once: true }); // Only listen ONCE
}

function handleStudentUpload(e) {
    console.log("Upload form submitted!");
    e.preventDefault(); // <- THIS PREVENTS RELOAD

    const formData = new FormData();
    const fileInput = document.getElementById("csvFile");

    if (!fileInput || fileInput.files.length === 0) {
        document.getElementById("uploadMessage").textContent = "Please select a file.";
        return;
    }

    formData.append("csvFile", fileInput.files[0]);

    fetch("upload_students.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        console.log("Upload response:", data);
        document.getElementById("uploadMessage").textContent = data;
        loadEntities("students"); // Refresh list after upload
    })
    .catch(err => {
        console.error("Upload error:", err);
        document.getElementById("uploadMessage").textContent = "Upload failed.";
    });
}

// Submit New Entity
function submitEntity(entity) {
    console.log(`➕ Submitting new ${entity}`);

    const singularEntity = entity.slice(0, -1); // Converts "students" → "student"
    const form = document.getElementById(`${singularEntity}Form`);

    if (!form) {
        console.error(`Form #${entity}Form not found.`);
        return;
    }

    const formData = new FormData(form);

    fetch(`add_${entity}.php`, {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            console.log(`${entity} added successfully.`);
            loadEntities(entity);
            form.reset();
        } else {
            console.error(`Error adding ${entity}:`, data.message);
        }
    })
    .catch(error => console.error(`Fetch error:`, error));
}

// Sorting
function sortEntities(entity, column) {
    console.log(`Sorting ${entity} by ${column}`);

    if (currentSort.column === column) {
        currentSort.order = currentSort.order === "ASC" ? "DESC" : "ASC";
    } else {
        currentSort.column = column;
        currentSort.order = "ASC";
    }

    loadEntities(entity, "", currentSort.column, currentSort.order);
}

// Search Entities
function searchEntities(entity) {
    console.log(`🔍 Searching ${entity}...`);

    const query = document.getElementById("searchInput")?.value.trim() || "";
    const emphasis = document.getElementById("searchEmphasis")?.value || "";
    
    let searchParams = `search=${encodeURIComponent(query)}`;

    if (entity === "students" && emphasis) {
        searchParams += `&emphasis=${encodeURIComponent(emphasis)}`;
    }

    console.log(`🔍 Fetching: get_${entity}.php?${searchParams}`);

    fetch(`get_${entity}.php?${searchParams}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                console.log(`Search results found for ${entity}.`);
                loadEntities(entity, query, "last_name", "ASC", 1, emphasis);
            } else {
                console.error(`Error fetching ${entity}:`, data.message);
            }
        })
        .catch(error => console.error(`Fetch error:`, error));
}

// Edit Entity
function editEntity(entity, id) {
    console.log(`Editing ${entity} with ID: ${id}`);

    fetch(`edit_${entity}.php?id=${id}`)
    .then(response => response.text())
    .then(html => {
        const modal = document.getElementById("editModal");
        const modalContent = document.getElementById("editModalContent");

        if (modal && modalContent) {
            modalContent.innerHTML = html;
            modal.style.display = "block"; // Show modal
        } else {
            console.error("Edit modal or content not found.");
        }
    })
    .catch(error => console.error(`Error editing ${entity}:`, error));
}


// Function to Save Edits for Any Entity
function saveEntityEdit(entity) {
    console.log(`Saving edits for ${entity}...`);

    const form = document.getElementById(`edit${capitalizeFirstLetter(entity)}Form`);
    if (!form) {
        console.error(`Edit form not found for ${entity}`);
        return;
    }

    const formData = new FormData(form);
    const entityId = formData.get("star_id") || formData.get("user_id") || formData.get("course_code");

    fetch(`edit_${entity}.php`, {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            console.log(` ${entity} updated successfully.`);

            closeEditModal(); // Close modal

            // Update only the edited student, user, or course
            updateEntityRow(entity, entityId);
        } else {
            console.error(`Error updating ${entity}:`, data.message);
        }
    })
    .catch(error => console.error(`Fetch error:`, error));
}


// Helper Function: Capitalize First Letter
function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function updateEntityRow(entity, id) {
    console.log(`Updating row for ${entity} with ID: ${id}`);

    fetch(`get_${entity}.php?search=${id}`)
    .then(response => response.json())
    .then(data => {
        let items = [];
        if (entity === "students") items = data.students || [];
        else if (entity === "users") items = data.users || [];
        else if (entity === "courses") items = data.courses || [];

        if (data.status !== "success" || !Array.isArray(items) || items.length === 0) {
            console.error(`No updated data found for ${entity} with ID: ${id}`);
            return;
        }

        const updatedEntity = items[0];
        const entityTable = document.querySelector(`#${entity}Table tbody`);

        if (!entityTable) {
            console.error(`Table for ${entity} not found.`);
            return;
        }

        // Find the existing row in the table
        const row = Array.from(entityTable.rows).find(row => row.cells[0].innerText === id);
        if (!row) {
            console.error(`Row for ${entity} with ID: ${id} not found.`);
            return;
        }

        // Update row contents dynamically
        if (entity === "students") {
            row.innerHTML = `
                <td>${updatedEntity.star_id}</td>
                <td>${updatedEntity.first_name}</td>
                <td>${updatedEntity.last_name}</td>
                <td>${updatedEntity.email || "N/A"}</td>
                <td>${updatedEntity.emphasis_name || "N/A"}</td>
                <td>
                    <button onclick="editEntity('${entity}', '${updatedEntity.star_id}')">Edit</button>
                    <button onclick="deleteEntity('${entity}', '${updatedEntity.star_id}')">Delete</button>
                    <button onclick="navigateToAdvising('${updatedEntity.star_id}')">Advise</button>
                </td>`;
        } else if (entity === "users") {
            row.innerHTML = `
                <td>${updatedEntity.user_id}</td>
                <td>${updatedEntity.first_name}</td>
                <td>${updatedEntity.last_name}</td>
                <td>${updatedEntity.email}</td>
                <td>
                    <button onclick="editEntity('${entity}', '${updatedEntity.user_id}')">Edit</button>
                    <button onclick="deleteEntity('${entity}', '${updatedEntity.user_id}')">Delete</button>
                </td>`;
        } else if (entity === "courses") {
            row.innerHTML = `
                <td>${updatedEntity.course_code}</td>
                <td>${updatedEntity.course_name}</td>
                <td>${updatedEntity.course_credits}</td>
                <td>${updatedEntity.min_accuplacer_score ?? "N/A"}</td>
                <td>${updatedEntity.days || "N/A"}</td>
                <td>${updatedEntity.start_time || "N/A"}</td>
                <td>${updatedEntity.end_time || "N/A"}</td>
                <td>
                    <button onclick="editEntity('${entity}', '${updatedEntity.course_code}')">Edit</button>
                    <button onclick="deleteEntity('${entity}', '${updatedEntity.course_code}')">Delete</button>
                </td>`;
        }

        console.log(`${entity} row updated successfully.`);
    })
    .catch(error => console.error(`Error fetching updated ${entity}:`, error));
}


// Delete Entity
function deleteEntity(entity, id) {
    if (!id) {
        console.error(`Error: No ID provided for deleting ${entity}.`);
        return;
    }

    const singularEntity = entity.slice(0, -1); // Convert "students" to "student", "users" to "user"
    
    if (!confirm(`Are you sure you want to delete this ${singularEntity}?`)) return;

    console.log(`Deleting ${entity} with ID: ${id}`);

    fetch(`delete_${entity}.php`, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${encodeURIComponent(id)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            console.log(` ${entity} deleted successfully.`);
        
            const row = document.querySelector(`[data-id="${id}"]`);
            if (row) {
                // Set initial highlight and transition
                row.style.backgroundColor = "#ffcccc";
                row.style.transition = "opacity 0.5s ease, background-color 0.5s ease";
                row.style.opacity = "0";
        
                // After transition, reload the table
                setTimeout(() => {
                    loadEntities(entity, currentQuery, currentSort.column, currentSort.order, currentPage, currentEmphasis);
                }, 500); // Match the duration of the fade
            } else {
                // Fallback if row not found
                loadEntities(entity, currentQuery, currentSort.column, currentSort.order, currentPage, currentEmphasis);
            }
        } else {
            console.error(`Error deleting ${entity}:`, data.message);
        }
    })
    .catch(error => console.error(`Fetch error:`, error));
}


// Remove Deleted Entity from Table
function removeDeletedEntityRow(entity, id) {
    const rowSelector = entity === "students" ? `#studentsTable tr[data-id="${id}"]` : `#${entity}Table tr[data-id="${id}"]`;
    const row = document.querySelector(rowSelector);

    if (row) {
        row.remove();
        console.log(`Removed ${entity} row for ID: ${id}`);
    } else {
        console.warn(`Could not find row for deleted ${entity} with ID: ${id}`);
    }
}

// Cancel Section
function cancelSection(sectionId) {
    console.log(`Cancelling section: ${sectionId}`);
    const section = document.getElementById(sectionId);
    if (section) {
        section.style.display = "none";
    }

    // Optionally hide the table if shared
    const entity = getEntityType();
    const table = document.getElementById(`${entity}sTable`);
    if (table) table.style.display = "none";

    // Hide pagination and controls if needed
    document.querySelector(".table-controls")?.style.setProperty("display", "none");
}

// Close Edit Modal
function closeEditModal() {
    document.getElementById("editModal").style.display = "none";
}

function navigateToAdvising(starId) {
    console.log(`Navigating to advise_students.php with STAR ID: ${starId}`);

    fetch('advise_students.php')
        .then(response => response.text())
        .then(html => {
            const contentDiv = document.getElementById("dynamicContent");
            if (!contentDiv) {
                console.error("#dynamicContent not found.");
                return;
            }
            contentDiv.innerHTML = html;
            console.log("advise_students.php loaded into dynamicContent.");

            // Create a temporary storage for star_id
            contentDiv.dataset.starId = starId; 

            const script = document.createElement("script");
            script.src = "advise_students.js";
            script.onload = () => {
                console.log("advise_students.js loaded after navigating.");

                if (typeof initializeAdvisingPage === "function") {
                    console.log("Calling initializeAdvisingPage()...");
                    initializeAdvisingPage();
                }

                if (typeof loadStudentData === "function") {
                    const storedStarId = contentDiv.dataset.starId;
                    console.log(`Loading student data immediately for STAR ID: ${storedStarId}`);
                    loadStudentData(storedStarId);
                }
            };
            document.body.appendChild(script);
        })
        .catch(error => {
            console.error("Error loading advise_students.php:", error);
        });
}


