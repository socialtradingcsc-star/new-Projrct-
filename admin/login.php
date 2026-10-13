<?php
session_start();
require_once '../core/Database.php';
require_once '../core/Security.php';

// अगर एडमिन पहले से लॉगिन है
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = Security::sanitizeInput($_POST['email']);
    $password = $_POST['password'];

    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT id, name, password_hash, role FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        header("Location: dashboard.php");
        exit;
    } else {
        $message = '<div class="alert alert-danger">Invalid Admin Credentials!</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login - Dynamic Foundation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card shadow-lg p-4" style="width: 400px;">
        <h3 class="text-center text-primary fw-bold mb-4">Admin Panel</h3>
        <?php echo $message; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="fw-bold">Admin Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="fw-bold">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold">Secure Login</button>
        </form>
    </div>
</body>
</html>