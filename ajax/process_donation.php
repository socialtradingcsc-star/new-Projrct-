<?php
session_start();
require_once '../core/Database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    $amount = $_POST['amount'] ?? 0;
    $purpose = $_POST['purpose'] ?? 'General Fund';
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if ($amount < 100) {
        echo json_encode(['status' => 'error', 'message' => 'Minimum donation is ₹100']);
        exit;
    }

    $db = Database::getInstance();
    $stmt = $db->prepare("INSERT INTO donations (user_id, name, email, mobile, amount, purpose) VALUES (?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$user_id, $name, $email, $mobile, $amount, $purpose])) {
        // यहाँ से हम डेटाबेस में सेव हुई नई ID निकाल रहे हैं
        $donation_id = $db->lastInsertId(); 
        
        echo json_encode([
            'status' => 'success',
            'amount' => $amount * 100,
            'key' => 'rzp_test_YOUR_KEY_HERE', // यहाँ अपनी असली Razorpay Test Key डालें
            'donation_id' => $donation_id
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
}
?>