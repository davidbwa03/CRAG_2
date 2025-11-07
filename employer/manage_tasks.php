<?php
include_once("../utils/functions.php");
checkLogin();

if (!isEmployer()) {
    header("Location: ../login.php");
    exit();
}

include_once("../utils/db.php");

// Get task ID from query string
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$task_id = $_GET['id'];
$employer_id = $_SESSION['user_id'];

// Fetch the job data
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = :id AND employer_id = :eid");
$stmt->bindParam(":id", $task_id);
$stmt->bindParam(":eid", $employer_id);
$stmt->execute();
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    die("Job not found or unauthorized access.");
}

// Handle job update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update'])) {
    $title = cleanInput($_POST['title']);
    $description = cleanInput($_POST['description']);
    $deadline = cleanInput($_POST['deadline']);
    $status = cleanInput($_POST['status']);
    $budget = cleanInput($_POST['budget']); // new budget field

    $update = $pdo->prepare("
        UPDATE tasks 
        SET title=:t, description=:d, deadline=:dl, status=:s, budget=:b 
        WHERE id=:id AND employer_id=:eid
    ");
    $update->bindParam(":t", $title);
    $update->bindParam(":d", $description);
    $update->bindParam(":dl", $deadline);
    $update->bindParam(":s", $status);
    $update->bindParam(":b", $budget);
    $update->bindParam(":id", $task_id);
    $update->bindParam(":eid", $employer_id);
    $update->execute();

    showMessage("success", "Job updated successfully!");
    header("refresh:2; url=dashboard.php");
}

// Handle delete
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete'])) {
    $delete = $pdo->prepare("DELETE FROM tasks WHERE id=:id AND employer_id=:eid");
    $delete->bindParam(":id", $task_id);
    $delete->bindParam(":eid", $employer_id);
    $delete->execute();

    showMessage("success", "Job deleted successfully!");
    header("refresh:2; url=dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Job | TaskFlow</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/employer.css">
</head>
<body>
    <?php include_once("../components/header.php"); ?>

    <div class="container">
        <h1>Manage Job: <?php echo htmlspecialchars($task['title']); ?></h1>

        <form method="POST" class="form-box">
            <label>Job Title</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>

            <label>Description</label>
            <textarea name="description" rows="4" required><?php echo htmlspecialchars($task['description']); ?></textarea>

            <label>Pay Rate / Budget</label>
            <input type="number" name="budget" value="<?php echo htmlspecialchars($task['budget']); ?>" min="0" step="0.01" required>

            <label>Deadline</label>
            <input type="date" name="deadline" value="<?php echo htmlspecialchars($task['deadline']); ?>" required>

            <label>Status</label>
            <select name="status" required>
                <option value="open" <?php echo ($task['status'] == 'open') ? 'selected' : ''; ?>>Open</option>
                <option value="closed" <?php echo ($task['status'] == 'closed') ? 'selected' : ''; ?>>Closed</option>
            </select>

            <div class="form-buttons">
                <button type="submit" name="update" class="btn-primary">Update</button>
                <button type="submit" name="delete" class="btn-danger" onclick="return confirm('Are you sure you want to delete this job?');">Delete</button>
                <a href="dashboard.php" class="btn-secondary">Back</a>
            </div>
        </form>
    </div>

    <script src="../js/employer.js"></script>
</body>
</html>
