document.addEventListener("DOMContentLoaded", function () {
    console.log("Script.js loaded successfully!");

    checkSessionAndLoadContent();

    // Attach event listeners dynamically for dashboard navigation
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("nav-link")) {
            event.preventDefault();
            const page = event.target.getAttribute("data-page");
            if (page) {
                loadContent(page);
            }
        }
    });
});

// Checks if user is logged in and loads appropriate content
function checkSessionAndLoadContent() {
    fetch("session_check.php")
        .then(response => response.json())
        .then(data => {
            console.log("🔍 Session check response:", data);

            if (data.logged_in) {
                loadDashboard(); // Load dashboard if logged in
            } else {
                loadContent("login_form.php"); // Show login form if not logged in
            }
        })
        .catch(error => console.error("Session check error:", error));
}

// Loads dashboard but keeps it visible at all times
function loadDashboard() {
    const dashboardDiv = document.getElementById("dashboard");
    if (!dashboardDiv) return;

    fetch("dashboard.php")
        .then(response => response.text())
        .then(html => {
            dashboardDiv.innerHTML = html; 
            console.log("Dashboard loaded.");
        })
        .catch(error => console.error("Error loading dashboard:", error));
}

//Loads content inside the dynamic content area
function loadContent(page) {
    // First, check session to ensure password isn't expired
    fetch("session_check.php")
        .then(response => response.json())
        .then(session => {
            console.log("🔍 Session check inside loadContent:", session);

            if (session.password_expired) {
                const allowedPages = [
                    "change_password_form.php",
                    "change_password.php",
                    "logout.php",
                    "login.php"
                ];

                if (!allowedPages.includes(page)) {
                    alert("Your password has expired. Please change it to continue.");
                    page = "change_password_form.php?expired=1";
                }
            }

            // Proceed to load the desired content
            fetch(page)
                .then(response => response.text())
                .then(html => {
                    const contentDiv = document.getElementById("dynamicContent");
                    if (!contentDiv) {
                        console.error("Error: #dynamicContent not found.");
                        return;
                    }
                    contentDiv.innerHTML = html;
                    console.log(`Loaded content from ${page}`);

                    // Load appropriate scripts if needed
                    if (page.includes("manage_") && !document.querySelector('script[src="manage_entities.js"]')) {
                        const script = document.createElement("script");
                        script.src = "manage_entities.js";
                        script.defer = true;
                        document.body.appendChild(script);
                        console.log("manage_entities.js loaded.");
                    }

                    if (page.includes("manage_students")) {
                        console.log("Setting up Student Upload Form after loading manage_students.php...");
                        if (typeof setupStudentUploadForm === "function") {
                            setupStudentUploadForm();
                        } else {
                            console.warn("setupStudentUploadForm not found yet.");
                        }
                    }

                    if (page.includes("advise_students") && !document.querySelector('script[src="advise_students.js"]')) {
                        const script = document.createElement("script");
                        script.src = "advise_students.js";
                        script.onload = () => {
                            console.log("advise_students.js loaded.");
                            if (typeof initializeAdvisingPage === "function") {
                                initializeAdvisingPage(); // Actually run it
                            } else {
                                console.error("initializeAdvisingPage() not found after script load.");
                            }
                        };
                        document.body.appendChild(script);
                    } else if (page.includes("advise_students")) {
                        // If the script was already loaded, call the function directly
                        if (typeof initializeAdvisingPage === "function") {
                            initializeAdvisingPage();
                        }
                    }

                    if (page.includes("change_password_form") && !document.querySelector('script[src="dashboard.js"]')) {
                        const script = document.createElement("script");
                        script.src = "dashboard.js";
                        script.defer = true;
                        document.body.appendChild(script);
                        console.log("dashboard.js loaded for password form.");
                    }
                })
                .catch(error => console.error(`Error loading ${page}:`, error));
        })
        .catch(error => {
            console.error("Session check failed:", error);
        });
}


// Handles login form submission dynamically
function setupLoginForm() {
    const loginForm = document.getElementById("loginForm");
    if (!loginForm) {
        console.error("Login form not found.");
        return;
    }

    console.log("Setting up login form...");

    loginForm.addEventListener("submit", function (event) {
        event.preventDefault();
        const formData = new FormData(loginForm);

        console.log("🔍 Submitting login form with data:");
        for (const pair of formData.entries()) {
            console.log(pair[0] + ": " + pair[1]);
        }

        fetch("login.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log("Login response:", data);
            if (data.status === "success") {
                setTimeout(() => {
                    window.location.href = data.redirect; // Redirect after login
                }, 1000);
            } else {
                document.getElementById("loginMessage").innerText = data.message;
            }
        })
        .catch(error => console.error("Login error:", error));
    });
}

// Handles logout function
function logout() {
    fetch("logout.php")
        .then(() => {
            console.log("✅ User logged out.");
            loadContent("login_form.php"); // Redirect to login form after logout
        })
        .catch(error => console.error("Logout error:", error));
}

