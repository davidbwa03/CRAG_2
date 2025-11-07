<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'utils/db.php';
require_once 'utils/emailService.php'; // PHPMailer sendOTP function

// Detect user role from URL (default: employee)
$role = isset($_GET['role']) ? $_GET['role'] : 'employee';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'] ?? 'employee';
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($role === 'employer') {
        $company_name = trim($_POST['company_name']);
        $contact_number = trim($_POST['contact_number']);
        $location = trim($_POST['location']);
    } else {
        $full_name = trim($_POST['full_name']);
        $skills = trim($_POST['skills']);
        $location = trim($_POST['location']);
    }

    // Validate fields
    if (empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all required fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Determine table
        $table = ($role === 'employer') ? 'employers' : 'employees';

        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM $table WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = "Email is already registered.";
        } else {
            // Hash password & generate OTP
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $otp = rand(100000, 999999); // 6-digit OTP

            // Insert user with OTP
            if ($role === 'employer') {
                $sql = "INSERT INTO employers 
                        (company_name, email, password, contact_number, location, otp, otp_verified)
                        VALUES (?, ?, ?, ?, ?, ?, 0)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$company_name, $email, $hashedPassword, $contact_number, $location, $otp]);
            } else {
                $sql = "INSERT INTO employees 
                        (full_name, email, password, skills, location, otp, otp_verified)
                        VALUES (?, ?, ?, ?, ?, ?, 0)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$full_name, $email, $hashedPassword, $skills, $location, $otp]);
            }

            // Send OTP email using PHPMailer
            $subject = "Verify your TaskFlow Account";
            $message = "Hello,<br><br>Your OTP for TaskFlow registration is: <b>$otp</b><br><br>Thank you!";
            
            if (sendOTP($email, $subject, $message)) {
                // Redirect to OTP verification page
                header("Location: verify_otp.php?email=" . urlencode($email) . "&role=" . urlencode($role));
                exit();
            } else {
                $error = "Failed to send OTP email. Please try again later.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | TaskFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="text-center mb-3 text-primary fw-bold">
                        <?php echo ucfirst($role); ?> Registration
                    </h4>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <input type="hidden" name="role" value="<?php echo htmlspecialchars($role); ?>">

                        <?php if ($role === 'employer'): ?>
                            <div class="mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" class="form-control" name="company_name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contact Number</label>
                                <input type="text" class="form-control" name="contact_number" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" name="location" required>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="full_name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Skills</label>
                                <input type="text" class="form-control" name="skills" placeholder="e.g. PHP, JavaScript, Design" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" name="location" required>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" name="confirm_password" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Register</button>
                    </form>

                    <div class="text-center mt-3">
                        <p class="text-muted mb-1">Already have an account?</p>
                        <a href="login.php?role=<?php echo htmlspecialchars($role); ?>" class="text-decoration-none">
                            Login as <?php echo ucfirst($role); ?>
                        </a>
                    </div>

                    <div class="text-center mt-3">
                        <a href="index.html" class="text-secondary small">&larr; Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
