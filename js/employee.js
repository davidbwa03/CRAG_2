// Employee-side interactivity for TaskFlow

document.addEventListener("DOMContentLoaded", () => {
    console.log("Employee dashboard loaded");

    // 🔹 Confirm before applying for a job
    const applyButtons = document.querySelectorAll(".apply-btn");
    applyButtons.forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            const jobId = btn.getAttribute("data-id");
            const jobTitle = btn.getAttribute("data-title");

            if (confirm(`Are you sure you want to apply for "${jobTitle}"?`)) {
                window.location.href = `apply_task.php?id=${jobId}`;
            }
        });
    });

    // 🔹 Show live status color indicators
    const statusLabels = document.querySelectorAll(".status-label");
    statusLabels.forEach(label => {
        const status = label.textContent.trim().toLowerCase();

        if (status === "accepted") {
            label.style.color = "green";
            label.style.fontWeight = "bold";
        } else if (status === "rejected") {
            label.style.color = "red";
            label.style.fontWeight = "bold";
        } else {
            label.style.color = "gray";
        }
    });

    // 🔹 Smooth scroll to job listings
    const viewJobsBtn = document.getElementById("viewJobsBtn");
    if (viewJobsBtn) {
        viewJobsBtn.addEventListener("click", () => {
            const jobSection = document.getElementById("availableJobs");
            if (jobSection) jobSection.scrollIntoView({ behavior: "smooth" });
        });
    }
});
