document.addEventListener("DOMContentLoaded", function () {
    attachDashboardEventListeners();
});

// Attach event listeners to dashboard elements
function attachDashboardEventListeners() {
    document.querySelectorAll(".nav-link").forEach(link => {
        link.addEventListener("click", function (event) {
            event.preventDefault();
            const page = this.getAttribute("data-page");
            loadContent(page);
        });
    });
}

// Load dynamic content into the dashboard
function loadContent(page) {
    fetch(page)
        .then(response => response.text())
        .then(html => {
            document.getElementById("dashboardContent").innerHTML = html;
        })
        .catch(error => console.error("Error loading page:", error));
}