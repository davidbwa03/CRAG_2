<?php
include_once("../utils/functions.php");
checkLogin();

if (!isEmployer()) {
    header("Location: ../login.php");
    exit();
}

include_once("../utils/db.php");

// Handle delete action
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_job_id'])) {
    $job_id = $_POST['delete_job_id'];
    $employer_id = $_SESSION['user_id'];

    // Delete the task only if it belongs to the logged-in employer
    $del = $pdo->prepare("DELETE FROM tasks WHERE id = :id AND employer_id = :eid");
    $del->bindParam(":id", $job_id);
    $del->bindParam(":eid", $employer_id);
    $del->execute();

    showMessage("success", "Job deleted successfully!");
    header("refresh:2; url=dashboard.php");
    exit();
}

// Fetch all jobs posted by this employer
$employer_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE employer_id = :eid ORDER BY created_at DESC");
$stmt->bindParam(":eid", $employer_id);
$stmt->execute();
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Dashboard | TaskFlow</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/employer.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container flex-between">
            <h2 class="logo">TaskFlow</h2>
            <div class="user-actions">
<span>
    Welcome, <?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'User'; ?>!
</span>
                <a href="../logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <p>Manage your job postings and applications below.</p>

        <div class="actions">
            <a href="post_task.php" class="btn-primary">Post New Job</a>
            <a href="view_applications.php" class="btn-secondary">View Applications</a>
        </div>

        <hr>

        <h2>Your Posted Jobs</h2>

        <?php if (count($jobs) > 0): ?>
            <div class="job-list">
                <?php foreach ($jobs as $job): ?>
                    <div class="job-card">
                        <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                        <p><?php echo htmlspecialchars($job['description']); ?></p>
                        <p><b>Deadline:</b> <?php echo htmlspecialchars($job['deadline']); ?></p>
                        <p><b>Status:</b> <?php echo ucfirst($job['status']); ?></p>

                        <div class="job-actions">
                            <a href="manage_tasks.php?id=<?php echo $job['id']; ?>" class="btn-edit">Edit</a>
                            <a href="view_applications.php?task_id=<?php echo $job['id']; ?>" class="btn-view">View Applicants</a>

                            <!-- Delete form -->
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this job?');">
                                <input type="hidden" name="delete_job_id" value="<?php echo $job['id']; ?>">
                                <button type="submit" class="btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No jobs posted yet. <a href="post_task.php">Post one now!</a></p>
        <?php endif; ?>
    </div>

    <script src="../js/employer.js"></script>
</body>
</html>
