<?php
// admin/login.php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: /admin/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = $admin['id'];
        header('Location: /admin/index.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Iceonfire</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: var(--shadow);
        }
        .form-group { margin-bottom: 1.5rem; }
        input { width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 8px; }
        .error { color: #f5576c; margin-bottom: 1rem; font-size: 0.9rem; }
    </style>
</head>
<body style="background: var(--primary-gradient); min-height: 100vh; display: flex; align-items: center;">
    <div class="login-container">
        <h2 style="margin-bottom: 2rem; text-align: center;">Admin Login</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="add-btn" style="width: 100%;">Login</button>
        </form>
    </div>
</body>
</html>
