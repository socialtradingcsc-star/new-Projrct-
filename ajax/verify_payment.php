<?php
session_start();
require_once '../core/Database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_id = $_POST['razorpay_payment_id'] ?? '';
    $donation_id = $_POST['donation_id'] ?? '';

    if (!empty($payment_id) && !empty($donation_id)) {
        $db = Database::getInstance();
        
        // डेटाबेस में पेमेंट स्टेटस अपडेट करना
        $stmt = $db->prepare("UPDATE donations SET payment_status = 'successful' WHERE id = ?");
        
        if ($stmt->execute([$donation_id])) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database update failed']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    }
}
?>