<?php
session_start();
require_once '../core/Database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $mobile = htmlspecialchars($_POST['mobile']);
    $password = $_POST['password'];

    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        $message = '<div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6 text-center text-sm">Email already registered!</div>';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $insert = $db->prepare("INSERT INTO users (name, email, mobile, password_hash, role) VALUES (?, ?, ?, ?, 'donor')");
        
        if ($insert->execute([$name, $email, $mobile, $hash])) {
            $_SESSION['user_id'] = $db->lastInsertId();
            $_SESSION['user_name'] = $name;
            header("Location: dashboard.php");
            exit;
        } else {
            $message = '<div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6 text-center text-sm">Registration failed!</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register | Dynamic Foundation Trust</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #030303; color: white; font-family: sans-serif; }
        .glass-panel { background: rgba(15, 15, 15, 0.6); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .glass-input { width: 100%; background: rgba(0, 0, 0, 0.5); border: 1px solid rgba(255, 255, 255, 0.1); color: white; padding: 14px 16px; border-radius: 8px; outline: none; transition: 0.3s; }
        .glass-input:focus { border-color: #00f2ff; background: rgba(0, 242, 255, 0.05); }
        .btn-primary { background: linear-gradient(90deg, #7000ff, #00f2ff); background-size: 200% auto; transition: 0.5s; }
        .btn-primary:hover { background-position: right center; box-shadow: 0 0 20px rgba(0, 242, 255, 0.4); }
        .text-gradient { background: linear-gradient(90deg, #00f2ff, #7000ff); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center relative overflow-hidden py-10">
    <div class="absolute top-0 right-0 w-96 h-96 bg-[#7000ff] rounded-full blur-[150px] opacity-20"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-[#00f2ff] rounded-full blur-[150px] opacity-20"></div>

    <div class="w-full max-w-md px-6 relative z-10">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold">Create an <span class="text-gradient">Account</span></h2>
            <p class="text-gray-400 mt-2 text-sm">Join us to access courses and manage donations</p>
        </div>

        <div class="glass-panel p-8 rounded-2xl shadow-2xl">
            <?php echo $message; ?>
            <form method="POST">
                <div class="mb-4">
                    <label class="block text-gray-400 text-xs font-bold mb-2 uppercase tracking-wide">Full Name</label>
                    <input type="text" name="name" required class="glass-input" placeholder="John Doe">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-400 text-xs font-bold mb-2 uppercase tracking-wide">Email Address</label>
                    <input type="email" name="email" required class="glass-input" placeholder="john@example.com">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-400 text-xs font-bold mb-2 uppercase tracking-wide">Mobile Number</label>
                    <input type="text" name="mobile" required class="glass-input" placeholder="+91 0000000000">
                </div>
                <div class="mb-8">
                    <label class="block text-gray-400 text-xs font-bold mb-2 uppercase tracking-wide">Password</label>
                    <input type="password" name="password" required class="glass-input" placeholder="Create a strong password">
                </div>
                <button type="submit" class="w-full btn-primary text-white font-bold py-4 rounded-xl uppercase tracking-widest text-sm mb-4">
                    Register Now
                </button>
            </form>
            <p class="text-center text-gray-400 text-sm">
                Already have an account? <a href="login.php" class="text-[#00f2ff] font-bold hover:underline">Login Here</a>
            </p>
        </div>
    </div>
</body>
</html>