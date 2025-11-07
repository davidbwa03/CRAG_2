<?php
include_once("../utils/functions.php");
checkLogin();

if (!isEmployee()) {
    header("Location: ../login.php");
    exit();
}

include_once("../utils/db.php");

$employee_id = $_SESSION['user_id'];

// Fetch all applications by this employee
$stmt = $pdo->prepare("
    SELECT a.id AS app_id, t.title AS task_title, t.budget AS budget, t.deadline, a.status, a.applied_at
    FROM applications a
    JOIN tasks t ON a.task_id = t.id
    WHERE a.employee_id = :eid
    ORDER BY a.applied_at DESC
");
$stmt->bindParam(":eid", $employee_id);
$stmt->execute();
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications | TaskFlow</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/employee.css">
</head>
<body>
<?php include_once("../components/header.php"); ?>

<div class="container">
    <h1>My Applications</h1>

    <?php if (count($applications) > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Budget</th>
                    <th>Deadline</th>
                    <th>Applied On</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $app): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($app['task_title']); ?></td>
                        <td><?php echo number_format((float)$app['budget'], 2); ?></td>
                        <td><?php echo htmlspecialchars($app['deadline']); ?></td>
                        <td><?php echo htmlspecialchars($app['applied_at']); ?></td>
                        <td>
                            <?php
                                if ($app['status'] === 'accepted') echo "<span class='badge-success'>Accepted</span>";
                                elseif ($app['status'] === 'rejected') echo "<span class='badge-danger'>Rejected</span>";
                                else echo "<span class='badge-pending'>Pending</span>";
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-data">You haven’t applied for any jobs yet. Go to the dashboard to apply!</p>
    <?php endif; ?>
</div>

<script src="../js/employee.js"></script>
</body>
</html>
