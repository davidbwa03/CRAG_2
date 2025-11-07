<?php
// Common helper functions for TaskFlow
session_start();

// ✅ Include database connection
include_once(__DIR__ . "/db.php");

// -----------------------------------------------------------------------------
// 🔹 Sanitize user input (to prevent XSS or injection)
// -----------------------------------------------------------------------------
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// -----------------------------------------------------------------------------
// 🔹 Check if user is logged in
// -----------------------------------------------------------------------------
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit();
    }
}

// -----------------------------------------------------------------------------
// 🔹 Check if the logged-in user is an employer
// -----------------------------------------------------------------------------
function isEmployer() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'employer';
}

// -----------------------------------------------------------------------------
// 🔹 Check if the logged-in user is an employee
// -----------------------------------------------------------------------------
function isEmployee() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'employee';
}

// -----------------------------------------------------------------------------
// 🔹 Redirect user based on their role
// -----------------------------------------------------------------------------
function redirectDashboard() {
    if (isEmployer()) {
        header("Location: ../employer/dashboard.php");
    } elseif (isEmployee()) {
        header("Location: ../employee/dashboard.php");
    } else {
        header("Location: ../login.php");
    }
    exit();
}

// -----------------------------------------------------------------------------
// 🔹 Display success or error messages (Bootstrap style)
// -----------------------------------------------------------------------------
function showMessage($type, $message) {
    $class = ($type === 'success') ? 'alert-success' : 'alert-danger';
    echo "<div class='alert $class' role='alert'>$message</div>";
}

// -----------------------------------------------------------------------------
// 🔹 Hash a password securely
// -----------------------------------------------------------------------------
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// -----------------------------------------------------------------------------
// 🔹 Verify a password
// -----------------------------------------------------------------------------
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// -----------------------------------------------------------------------------
// 🔹 Destroy user session (for logout)
// -----------------------------------------------------------------------------
function logoutUser() {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit();
}
?>
