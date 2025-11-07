<?php
include_once("../utils/functions.php");
checkLogin();

if (!isEmployer()) {
    header("Location: ../login.php");
    exit();
}

include_once("../utils/db.php");

$employer_id = $_SESSION['user_id'];
$message = "";

// Fetch current employer profile data
$stmt = $conn->prepare("SELECT name, email, company_name, location FROM users WHERE id = :id");
$stmt->bindParam(":id", $employer_id);
$stmt->execute();
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $company_name = $_POST['company_name'];
    $location = $_POST['location'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET name = :n, email = :e, company_name = :c, location = :l, password = :p WHERE id = :id");
        $update->bindParam(":p", $hashed);
    } else {
        $update = $conn->prepare("UPDATE users SET name = :n, email = :e, company_name = :c, location = :l WHERE id = :id");
    }

    $update->bindParam(":n", $name);
    $update->bindParam(":e", $email);
    $update->bindParam(":c", $company_name);
    $update->bindParam(":l", $location);
    $update->bindParam(":id", $employer_id);

    if ($update->execute()) {
        $message = "<p class='success-msg'>✅ Profile updated successfully!</p>";
    } else {
        $message = "<p class='error-msg'>❌ Failed to update profile. Try again later.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Profile | TaskFlow</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/employer.css">
</head>
<body>
    <?php include_once("../components/header.php"); ?>

    <div class="container">
        <h1>👤 Employer Profile</h1>
        <?php echo $message; ?>

        <form method="POST" class="profile-form">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($profile['name']); ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($profile['email']); ?>" required>

            <label for="company_name">Company Name</label>
            <input type="text" id="company_name" name="company_name" value="<?php echo htmlspecialchars($profile['company_name']); ?>" required>

            <label for="location">Company Location</label>
            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($profile['location']); ?>">

            <label for="password">Change Password</label>
            <input type="password" id="password" name="password" placeholder="Leave blank to keep current">

            <button type="submit" class="btn-update">💾 Update Profile</button>
        </form>
    </div>

    <script src="../js/employer.js"></script>
</body>
</html>
