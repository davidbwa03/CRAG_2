<?php
session_start();
require_once 'utils/db.php';

// Detect role (optional, for redirecting to correct login)
$role = isset($_GET['role']) ? $_GET['role'] : ''; 
$email = isset($_GET['email']) ? trim($_GET['email']) : '';

if (!$email) {
    die("Invalid access.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entered_otp = trim($_POST['otp']);
    $role = $_POST['role']; // Hidden input to know which table

    $table = ($role === 'employer') ? 'employers' : 'employees';

    // Check OTP
    $stmt = $pdo->prepare("SELECT otp, otp_verified FROM $table WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        if ($user['otp_verified']) {
            $message = "Your account is already verified. <a href='login.php?role=$role'>Login here</a>.";
        } elseif ($user['otp'] == $entered_otp) {
            // Correct OTP, mark as verified
            $stmt = $pdo->prepare("UPDATE $table SET otp_verified = 1 WHERE email = ?");
            $stmt->execute([$email]);
            $message = "OTP verified successfully! <a href='login.php?role=$role'>Login here</a>.";
        } else {
            $error = "Invalid OTP. Please try again.";
        }
    } else {
        $error = "No user found with this email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP | TaskFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="text-center mb-3 text-primary fw-bold">
                        OTP Verification
                    </h4>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if (isset($message)): ?>
                        <div class="alert alert-success text-center"><?php echo $message; ?></div>
                    <?php else: ?>
                        <form method="POST" action="">
                            <input type="hidden" name="role" value="<?php echo htmlspecialchars($role); ?>">
                            <div class="mb-3">
                                <label class="form-label">Enter the OTP sent to your email</label>
                                <input type="text" class="form-control" name="otp" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Verify OTP</button>
                        </form>
                        <div class="text-center mt-3">
                            <p class="small text-muted">Didn’t receive the OTP? <a href="resend_otp.php?email=<?php echo urlencode($email); ?>&role=<?php echo $role; ?>">Resend</a></p>
                        </div>
                    <?php endif; ?>

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
