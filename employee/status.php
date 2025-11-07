<?php
include_once("../utils/functions.php");
checkLogin();

if (!isEmployee()) {
    header("Location: ../login.php");
    exit();
}

include_once("../utils/db.php");

$employee_id = $_SESSION['user_id'];

// Count applications by status
$stmt = $pdo->prepare("
    SELECT status, COUNT(*) as count
    FROM applications
    WHERE employee_id = :eid
    GROUP BY status
");
$stmt->bindParam(":eid", $employee_id);
$stmt->execute();
$statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

$status_count = ['pending'=>0,'accepted'=>0,'rejected'=>0];
foreach($statuses as $s){
    $status_count[$s['status']] = $s['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Status | TaskFlow</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/employee.css">
</head>
<body>
<?php include_once("../components/header.php"); ?>

<div class="container">
    <h1>Application Status Overview</h1>

    <div class="status-cards">
        <div class="status-card badge-pending">
            <h3>Pending</h3>
            <p><?php echo $status_count['pending']; ?></p>
        </div>
        <div class="status-card badge-success">
            <h3>Accepted</h3>
            <p><?php echo $status_count['accepted']; ?></p>
        </div>
        <div class="status-card badge-danger">
            <h3>Rejected</h3>
            <p><?php echo $status_count['rejected']; ?></p>
        </div>
    </div>

    <p>Go to <a href="my_applications.php">My Applications</a> for detailed view.</p>
</div>

<script src="../js/employee.js"></script>
</body>
</html>
