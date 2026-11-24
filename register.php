
<?php
require_once "db.php";

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {

        $error = "Please enter email and password.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif (strlen($password) < 8) {

        $error = "Password must contain at least 8 characters.";

    } else {

        try {

            $stmt = $pdo->prepare(
                "SELECT id FROM users WHERE email = ?"
            );

            $stmt->execute([$email]);

            if ($stmt->fetch()) {

                $error = "Email already registered. Please login.";

            } else 
            {
                $hashedPassword = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                // Save user
                $stmt = $pdo->prepare(
                    "INSERT INTO users (email, password)
                     VALUES (?, ?)"
                );

                $stmt->execute([
                    $email,
                    $hashedPassword
                ]);

                // Start user session
                $userId = $pdo->lastInsertId();

                session_regenerate_id(true);

                $_SESSION['user_id'] = $userId;
                $_SESSION['email'] = $email;

                // Redirect to dashboard
                header("Location: dashboard.php");
                exit;
            }

        } catch (PDOException $e) {

            error_log($e->getMessage());
            $error = "Registration failed. Please check your database setup.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - Car Parking Management System</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="login-box">

        <h2>Create Account</h2>

        <p>Register for Car Parking Management System</p>

        <?php if (!empty($error)): ?>
            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

            <label>Email</label>

            <input
                type="email"
                name="email"
                placeholder="Enter your email"
                required
            >

            <label>Password</label>

            <input
                type="password"
                name="password"
                placeholder="Create a password"
                minlength="8"
                required
            >

            <button type="submit">REGISTER</button>

        </form>

        <div class="small-text">
            Already have an account?
            <a href="index.php">Login here</a>
        </div>

    </div>

</body>
</html>
