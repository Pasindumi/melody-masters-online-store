<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

requireRole('Admin');

// Simple role update logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $id = $_POST['user_id'];
    $role = $_POST['role'];
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$role, $id]);
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

include '../includes/header.php';
?>

<h2>Manage Users</h2>

<table class="cart-table">
    <thead>
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Joined</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?php echo $u['username']; ?></td>
                <td><?php echo $u['email']; ?></td>
                <td>
                    <form method="POST" style="display: flex; gap: 0.5rem;">
                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                        <select name="role" style="padding: 0.3rem;">
                            <option value="Customer" <?php echo $u['role'] == 'Customer' ? 'selected' : ''; ?>>Customer</option>
                            <option value="Staff" <?php echo $u['role'] == 'Staff' ? 'selected' : ''; ?>>Staff</option>
                            <option value="Admin" <?php echo $u['role'] == 'Admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                        <button type="submit" name="update_role" class="btn btn-primary" style="padding: 0.3rem 0.6rem;">Update</button>
                    </form>
                </td>
                <td><?php echo date('Y-m-d', strtotime($u['created_at'])); ?></td>
                <td>
                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                        <button class="btn" style="background: var(--secondary-color); color: white; padding: 0.3rem 0.6rem;">Delete</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="dashboard.php" class="btn btn-primary" style="margin-top: 1rem;">Back to Dashboard</a>

<?php include '../includes/footer.php'; ?>
