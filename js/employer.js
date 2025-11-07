// Employer-side interactivity for TaskFlow

document.addEventListener("DOMContentLoaded", () => {
    console.log("Employer dashboard loaded");

    // 🔹 Validate job posting form before submission
    const postForm = document.getElementById("postJobForm");
    if (postForm) {
        postForm.addEventListener("submit", (e) => {
            const title = document.getElementById("title").value.trim();
            const description = document.getElementById("description").value.trim();
            const deadline = document.getElementById("deadline").value.trim();

            if (title === "" || description === "" || deadline === "") {
                e.preventDefault();
                alert("⚠️ Please fill in all fields before submitting.");
            }
        });
    }

    // 🔹 Handle accept / reject buttons on applications list
    const actionButtons = document.querySelectorAll(".action-btn");
    actionButtons.forEach((btn) => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();

            const action = btn.getAttribute("data-action");
            const appId = btn.getAttribute("data-id");
            const empName = btn.getAttribute("data-empname");
            const jobTitle = btn.getAttribute("data-jobtitle");

            let confirmMsg =
                action === "accept"
                    ? ` Accept ${empName} for "${jobTitle}"?`
                    : ` Reject ${empName} for "${jobTitle}"?`;

            if (confirm(confirmMsg)) {
                // Redirect to server handler with status update
                window.location.href = `view_applications.php?action=${action}&id=${appId}`;
            }
        });
    });

    // 🔹 Highlight job cards on hover
    const jobCards = document.querySelectorAll(".job-card");
    jobCards.forEach((card) => {
        card.addEventListener("mouseenter", () => {
            card.style.boxShadow = "0 0 10px rgba(0, 123, 255, 0.3)";
        });
        card.addEventListener("mouseleave", () => {
            card.style.boxShadow = "none";
        });
    });
});
