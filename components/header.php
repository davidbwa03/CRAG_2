<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Determine dashboard link based on role
$dashboardLink = '../employee/dashboard.php';
if (isset($_SESSION['role']) && $_SESSION['role'] === 'employer') {
    $dashboardLink = '../employer/dashboard.php';
}

// Determine user name safely
$userName = 'User'; // fallback
if (isset($_SESSION['name']) && !empty($_SESSION['name'])) {
    $userName = $_SESSION['name'];
}
?>

<header class="header">
    <link rel="stylesheet" href="../css/employer.css">

    <div class="logo">
        <a href="<?php echo $dashboardLink; ?>" class="logo-link">
            TaskFlow
        </a>
    </div>

    <div class="user-actions">
        <span class="user-name">Welcome, <?php echo htmlspecialchars($userName); ?>!</span>
        <a href="../logout.php" class="btn-logout">Logout</a>
    </div>
</header>
