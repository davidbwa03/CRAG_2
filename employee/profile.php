<?php
include_once("../utils/functions.php");
checkLogin();

if (!isEmployee()) {
    header("Location: ../login.php");
    exit();
}

include_once("../utils/db.php");

$employee_id = $_SESSION['user_id'];
$message = "";

// Fetch employee info
$stmt = $pdo->prepare("SELECT * FROM employees WHERE id = :eid");
$stmt->bindParam(":eid", $employee_id);
$stmt->execute();
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

// Update profile
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';

    $update = $pdo->prepare("UPDATE employees SET full_name=:fn, email=:em WHERE id=:eid");
    $update->bindParam(":fn", $full_name);
    $update->bindParam(":em", $email);
    $update->bindParam(":eid", $employee_id);

    if ($update->execute()) {
        $message = "<span class='success-msg'>Profile updated successfully!</span>";
        $_SESSION['name'] = $full_name; // update session name
        $employee['full_name'] = $full_name;
        $employee['email'] = $email;
    } else {
        $message = "<span class='error-msg'>Failed to update profile.</span>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | TaskFlow</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/employee.css">
</head>
<body>
<?php include_once("../components/header.php"); ?>

<div class="container">
    <h1>My Profile</h1>
    <?php echo $message; ?>

    <form method="POST" class="form-box">
        <label>Full Name</label>
        <input type="text" name="full_name" value="<?php echo htmlspecialchars($employee['full_name']); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>" required>

        <div class="form-buttons">
            <button type="submit" class="btn-primary">Update Profile</button>
        </div>
    </form>
</div>

<script src="../js/employee.js"></script>
</body>
</html>
