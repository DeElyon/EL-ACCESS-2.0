// app.js

// Wait for the DOM to fully load
document.addEventListener("DOMContentLoaded", () => {
    // Handle dynamic navigation
    const navLinks = document.querySelectorAll(".nav-link");
    const contentArea = document.getElementById("content");
    const alertContainer = document.createElement("div");
    alertContainer.className = "alert-container";
    document.body.appendChild(alertContainer);

    navLinks.forEach(link => {
        link.addEventListener("click", event => {
            event.preventDefault();
            const targetPage = link.getAttribute("data-target");
            updateActiveNav(link);
            loadPage(targetPage);
        });
    });

    function updateActiveNav(activeLink) {
        navLinks.forEach(link => link.classList.remove("active"));
        activeLink.classList.add("active");
    }

    function loadPage(page) {
        contentArea.classList.add("fade-out");
        contentArea.innerHTML = `<div class="loading">Loading ${page}...</div>`;
        
        // Simulate API fetch
        setTimeout(() => {
            contentArea.classList.remove("fade-out");
            contentArea.classList.add("fade-in");
            contentArea.innerHTML = `<h1>${page} Page</h1><p>Welcome to the ${page} page!</p>`;
            runPageAnimations();
            showAlert(`Successfully loaded ${page}`, "success");
        }, 800);
    }

    function showAlert(message, type = "info") {
        const alert = document.createElement("div");
        alert.className = `alert alert-${type}`;
        alert.textContent = message;
        alertContainer.appendChild(alert);
        
        setTimeout(() => {
            alert.classList.add("fade-out");
            setTimeout(() => alert.remove(), 300);
        }, 3000);
    }

    function runPageAnimations() {
        const elements = document.querySelectorAll(".animate");
        elements.forEach(el => {
            el.classList.add("fade-in");
            el.addEventListener("animationend", () => {
                el.classList.remove("fade-in");
            });
        });
    }

    // Initial page load
    if (navLinks.length > 0) {
        const defaultPage = navLinks[0].getAttribute("data-target");
        updateActiveNav(navLinks[0]);
        loadPage(defaultPage);
    }
});