<?php
include_once("../utils/functions.php");
checkLogin();

if (!isEmployer()) {
    header("Location: ../login.php");
    exit();
}

include_once("../utils/db.php");
include_once("../utils/emailService.php");

$employer_id = $_SESSION['user_id'];

// Handle accept or reject actions
if (isset($_GET['action']) && isset($_GET['app_id'])) {
    $action = $_GET['action'];
    $app_id = $_GET['app_id'];

    if ($action === 'accept' || $action === 'reject') {
        $status = ($action === 'accept') ? 'accepted' : 'rejected';

        // Update application status
        $stmt = $pdo->prepare("UPDATE applications SET status = :status WHERE id = :id");
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $app_id);
        $stmt->execute();

        // Fetch employee email and task title
        $info = $pdo->prepare("
            SELECT e.email, t.title 
            FROM applications a
            JOIN employees e ON a.employee_id = e.id
            JOIN tasks t ON a.task_id = t.id
            WHERE a.id = :id
        ");
        $info->bindParam(":id", $app_id);
        $info->execute();
        $data = $info->fetch(PDO::FETCH_ASSOC);

        // Send email notification
        if ($data) {
            $to = $data['email'];
            $subject = "Task Application Update - " . $data['title'];
            $body = ($status === 'accepted')
                ? "Congratulations! Your application for the task '" . $data['title'] . "' has been accepted."
                : "We regret to inform you that your application for the task '" . $data['title'] . "' has been rejected.";
            sendEmail($to, $subject, $body);
        }

        header("Location: view_applications.php?msg=Application has been $status.");
        exit();
    }
}

// Fetch all applications for this employer’s tasks
$stmt = $pdo->prepare("
    SELECT a.id AS app_id, e.full_name AS employee_name, e.email, t.title AS task_title, a.status, a.applied_at
    FROM applications a
    JOIN employees e ON a.employee_id = e.id
    JOIN tasks t ON a.task_id = t.id
    WHERE t.employer_id = :eid
    ORDER BY a.applied_at DESC
");
$stmt->bindParam(":eid", $employer_id);
$stmt->execute();
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applications | TaskFlow</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/employer.css">
    <style>
        .data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .data-table th { background-color: #0d6efd; color: #fff; }
        .btn-accept { background-color: #28a745; color: #fff; padding: 5px 10px; margin-right: 5px; text-decoration: none; border-radius: 4px; }
        .btn-reject { background-color: #dc3545; color: #fff; padding: 5px 10px; text-decoration: none; border-radius: 4px; }
        .badge-success { background-color: #28a745; color: #fff; padding: 3px 7px; border-radius: 4px; }
        .badge-danger { background-color: #dc3545; color: #fff; padding: 3px 7px; border-radius: 4px; }
        .badge-pending { background-color: #ffc107; color: #212529; padding: 3px 7px; border-radius: 4px; }
        .no-data { margin-top: 20px; font-style: italic; color: #666; }
    </style>
</head>
<body>
    <?php include_once("../components/header.php"); ?>

    <div class="container">
        <h1>Applications for Your Tasks</h1>

        <?php if (isset($_GET['msg'])): ?>
            <p class="success-msg"><?php echo htmlspecialchars($_GET['msg']); ?></p>
        <?php endif; ?>

        <?php if (count($applications) > 0): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Email</th>
                        <th>Task Title</th>
                        <th>Applied Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($app['employee_name']); ?></td>
                            <td><?php echo htmlspecialchars($app['email']); ?></td>
                            <td><?php echo htmlspecialchars($app['task_title']); ?></td>
                            <td><?php echo htmlspecialchars($app['applied_at']); ?></td>
                            <td>
                                <?php
                                if ($app['status'] === 'accepted') echo "<span class='badge-success'>Accepted</span>";
                                elseif ($app['status'] === 'rejected') echo "<span class='badge-danger'>Rejected</span>";
                                else echo "<span class='badge-pending'>Pending</span>";
                                ?>
                            </td>
                            <td>
                                <?php if ($app['status'] === 'pending'): ?>
                                    <a href="?action=accept&app_id=<?php echo $app['app_id']; ?>" class="btn-accept">Accept</a>
                                    <a href="?action=reject&app_id=<?php echo $app['app_id']; ?>" class="btn-reject">Reject</a>
                                <?php else: ?>
                                    <em>No further action</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No applications found yet.</p>
        <?php endif; ?>
    </div>

    <script src="../js/employer.js"></script>
</body>
</html>
