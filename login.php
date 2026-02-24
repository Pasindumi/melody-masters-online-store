<?php
require_once 'config/db.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect based on role
            switch ($user['role']) {
                case 'Admin': redirect('admin/dashboard.php'); break;
                case 'Staff': redirect('staff/dashboard.php'); break;
                default: redirect('customer/dashboard.php'); break;
            }
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}

include 'includes/header.php';
?>

<div class="card" style="max-width: 400px; margin: 4rem auto;">
    <h2>Login</h2>
    <?php if ($error): ?>
        <p style="color: var(--secondary-color);"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST">
        <div style="margin-bottom: 1rem;">
            <label>Username</label><br>
            <input type="text" name="username" style="width: 100%; padding: 0.5rem;" required>
        </div>
        <div style="margin-bottom: 1rem;">
            <label>Password</label><br>
            <input type="password" name="password" style="width: 100%; padding: 0.5rem;" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
    </form>
    <p style="margin-top: 1rem;">Don't have an account? <a href="/melody-masters/register.php">Register here</a></p>
</div>

<?php include 'includes/footer.php'; ?>
