<?php
include_once("../utils/functions.php");
checkLogin();

if (!isEmployee()) {
    header("Location: ../login.php");
    exit();
}

include_once("../utils/db.php");

$employee_id = $_SESSION['user_id'];

// Fetch jobs that employee hasn't applied to
$stmt = $pdo->prepare("
    SELECT t.id AS task_id, t.title, t.description, t.budget, t.deadline, e.company_name AS employer_name
    FROM tasks t
    JOIN employers e ON t.employer_id = e.id
    WHERE t.id NOT IN (
        SELECT task_id FROM applications WHERE employee_id = :eid
    )
    ORDER BY t.created_at DESC
");
$stmt->bindParam(":eid", $employee_id);
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard | TaskFlow</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/employee.css">
</head>
<body>
<?php include_once("../components/header.php"); ?>

<div class="container">
    <h1>Available Jobs</h1>
    <?php if(count($tasks) > 0): ?>
        <div class="task-list">
            <?php foreach($tasks as $task): ?>
                <div class="task-card">
                    <h3><?= htmlspecialchars($task['title']); ?></h3>
                    <p><strong>Employer:</strong> <?= htmlspecialchars($task['employer_name']); ?></p>
                    <p><strong>Budget:</strong> <?= number_format($task['budget'] ?? 0, 2); ?> Ksh</p>
                    <p><strong>Deadline:</strong> <?= htmlspecialchars($task['deadline']); ?></p>
                    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($task['description'])); ?></p>
                    <form action="apply_task.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="task_id" value="<?= $task['task_id']; ?>">
                        <label>Upload Resume:</label>
                        <input type="file" name="resume" accept=".pdf,.doc,.docx" required>
                        <button type="submit" class="btn-apply">Apply</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No available jobs at the moment. Check back later!</p>
    <?php endif; ?>
</div>

</body>
</html>
