<?php
session_start();
require_once 'utils/db.php';

// Detect user role from URL (default: employee)
$role = isset($_GET['role']) ? $_GET['role'] : 'employee';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Determine table based on role
    $table = ($role === 'employer') ? 'employers' : 'employees';

    // Fetch user by email
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Check OTP verification
        if ($user['otp_verified'] != 1) {
            $error = "Your account is not verified. Please check your email for the OTP.";
        } elseif (password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $role;
            

            // Redirect to dashboard
            if ($role === 'employer') {
                header("Location: employer/dashboard.php");
            } else {
                header("Location: employee/dashboard.php");
            }
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Email not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | TaskFlow</title>
    <!-- Bootstrap CSS -->
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
                        <?php echo ucfirst($role); ?> Login
                    </h4>

                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if(isset($_GET['registered'])): ?>
                        <div class="alert alert-success text-center">
                            Registration successful! Please check your email for OTP verification.
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>

                    <div class="text-center mt-3">
                        <p class="text-muted mb-1">Don't have an account?</p>
                        <a href="register.php?role=<?php echo $role; ?>" class="text-decoration-none">
                            Register as <?php echo ucfirst($role); ?>
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
