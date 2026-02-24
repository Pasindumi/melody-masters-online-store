<?php
require_once 'config/db.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($username) && !empty($email) && !empty($password)) {
        if ($password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            // Check if username or email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $error = "Username or Email already exists.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'Customer')");
                if ($stmt->execute([$username, $email, $hashed_password])) {
                    $success = "Registration successful! <a href='/melody-masters/login.php'>Login here</a>";
                } else {
                    $error = "Something went wrong. Please try again.";
                }
            }
        }
    } else {
        $error = "Please fill in all fields.";
    }
}

include 'includes/header.php';
?>

<div class="card" style="max-width: 400px; margin: 4rem auto;">
    <h2>Register</h2>
    <?php if ($error): ?>
        <p style="color: var(--secondary-color);"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>
    <form method="POST">
        <div style="margin-bottom: 1rem;">
            <label>Username</label><br>
            <input type="text" name="username" style="width: 100%; padding: 0.5rem;" required>
        </div>
        <div style="margin-bottom: 1rem;">
            <label>Email</label><br>
            <input type="email" name="email" style="width: 100%; padding: 0.5rem;" required>
        </div>
        <div style="margin-bottom: 1rem;">
            <label>Password</label><br>
            <input type="password" name="password" style="width: 100%; padding: 0.5rem;" required>
        </div>
        <div style="margin-bottom: 1rem;">
            <label>Confirm Password</label><br>
            <input type="password" name="confirm_password" style="width: 100%; padding: 0.5rem;" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
    </form>
    <p style="margin-top: 1rem;">Already have an account? <a href="/melody-masters/login.php">Login here</a></p>
</div>

<?php include 'includes/footer.php'; ?>
