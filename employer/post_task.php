<?php
include_once("../utils/functions.php");
checkLogin();

if (!isEmployer()) {
    header("Location: ../login.php");
    exit();
}

include_once("../utils/db.php");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = cleanInput($_POST['title']);
    $description = cleanInput($_POST['description']);
    $budget = isset($_POST['budget']) ? floatval($_POST['budget']) : 0;
    $category = !empty($_POST['category']) ? cleanInput($_POST['category']) : null;
    $deadline = cleanInput($_POST['deadline']);

    if (!empty($title) && !empty($description) && $budget > 0 && !empty($deadline)) {
        $employer_id = $_SESSION['user_id'];

        $stmt = $pdo->prepare("
            INSERT INTO tasks (employer_id, title, description, category, budget, deadline, status, created_at)
            VALUES (:eid, :t, :d, :cat, :b, :dl, 'open', NOW())
        ");
        $stmt->bindParam(":eid", $employer_id);
        $stmt->bindParam(":t", $title);
        $stmt->bindParam(":d", $description);
        $stmt->bindParam(":cat", $category);
        $stmt->bindParam(":b", $budget);
        $stmt->bindParam(":dl", $deadline);
        $stmt->execute();

        showMessage("success", "Task posted successfully!");
        header("refresh:2; url=dashboard.php");
        exit();
    } else {
        showMessage("error", "Please fill in all fields and ensure the budget is valid.");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Task | TaskFlow</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/employer.css">
</head>
<body>
    <?php include_once("../components/header.php"); ?>

    <div class="container">
        <h1>Post a New Task</h1>

        <form method="POST" class="form-box">
            <label>Task Title</label>
            <input type="text" name="title" placeholder="Enter task title" required>

            <label>Description</label>
            <textarea name="description" rows="4" placeholder="Describe the task" required></textarea>

            <label>Category</label>
            <input type="text" name="category" placeholder="e.g., Cleaning, IT Support, Delivery">

            <label>Budget (KSH)</label>
            <input type="number" name="budget" step="0.01" min="1" placeholder="Enter task budget" required>

            <label>Deadline</label>
            <input type="date" name="deadline" required>

            <button type="submit" class="btn-primary">Post Task</button>
            <a href="dashboard.php" class="btn-secondary">Back</a>
        </form>
    </div>

    <script src="../js/employer.js"></script>
</body>
</html>
