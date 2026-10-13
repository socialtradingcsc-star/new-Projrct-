<?php
require_once 'core/Database.php';
$db = Database::getInstance();

try {
    // डेटाबेस में 'admin' रोल को सपोर्ट करने के लिए टेबल अपडेट करना
    $db->query("ALTER TABLE users MODIFY role ENUM('admin', 'donor', 'volunteer', 'guest') DEFAULT 'donor'");

    // एडमिन का पासवर्ड सेट करना (पासवर्ड: Admin@123)
    $password = 'Admin@123';
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // चेक करें कि एडमिन पहले से है या नहीं
    $stmt = $db->prepare("SELECT id FROM users WHERE email = 'admin@dynamicfoundation.com'");
    $stmt->execute();
    
    if(!$stmt->fetch()) {
        // नया एडमिन अकाउंट बनाना
        $insert = $db->prepare("INSERT INTO users (name, email, password_hash, role) VALUES ('Sonu Admin', 'admin@dynamicfoundation.com', ?, 'admin')");
        $insert->execute([$hash]);
        
        echo "<div style='font-family:sans-serif; padding:20px; text-align:center;'>";
        echo "<h2 style='color:green;'>✅ Admin Account Created Successfully!</h2>";
        echo "<p><strong>Email:</strong> admin@dynamicfoundation.com</p>";
        echo "<p><strong>Password:</strong> Admin@123</p>";
        echo "<p><a href='admin/login.php' style='background:blue; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Go to Admin Login</a></p>";
        echo "</div>";
    } else {
        echo "<h2 style='text-align:center; font-family:sans-serif;'>Admin account already exists!</h2>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>