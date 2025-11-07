// Main global JavaScript for TaskFlow

document.addEventListener("DOMContentLoaded", () => {
    console.log("TaskFlow main.js loaded ✅");

    // 🔹 Toggle password visibility (for login/register)
    const togglePassword = document.querySelectorAll(".toggle-password");
    togglePassword.forEach(icon => {
        icon.addEventListener("click", () => {
            const input = icon.previousElementSibling;
            if (input.type === "password") {
                input.type = "text";
                icon.textContent = "🙈";
            } else {
                input.type = "password";
                icon.textContent = "👁️";
            }
        });
    });

    // 🔹 Basic login & register form validation
    const authForms = document.querySelectorAll(".auth-form");
    authForms.forEach(form => {
        form.addEventListener("submit", (e) => {
            const inputs = form.querySelectorAll("input[required]");
            let valid = true;

            inputs.forEach(input => {
                if (input.value.trim() === "") {
                    valid = false;
                    input.style.borderColor = "red";
                } else {
                    input.style.borderColor = "#ccc";
                }
            });

            if (!valid) {
                e.preventDefault();
                alert("⚠️ Please fill in all required fields!");
            }
        });
    });

    // 🔹 Auto-hide alert messages after 5 seconds
    const alerts = document.querySelectorAll(".alert");
    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach(alert => {
                alert.style.opacity = "0";
                setTimeout(() => alert.remove(), 600);
            });
        }, 5000);
    }

    // 🔹 Smooth scrolling for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener("click", function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute("href"));
            if (target) {
                target.scrollIntoView({ behavior: "smooth" });
            }
        });
    });

    // 🔹 Dark mode toggle (optional feature)
    const themeToggle = document.getElementById("themeToggle");
    if (themeToggle) {
        themeToggle.addEventListener("click", () => {
            document.body.classList.toggle("dark-mode");
            if (document.body.classList.contains("dark-mode")) {
                localStorage.setItem("theme", "dark");
            } else {
                localStorage.setItem("theme", "light");
            }
        });
    }

    // Apply saved theme on load
    if (localStorage.getItem("theme") === "dark") {
        document.body.classList.add("dark-mode");
    }
});
