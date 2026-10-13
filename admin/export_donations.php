<?php
session_start();
require_once '../core/Database.php';

// सुरक्षा चेक
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$db = Database::getInstance();
$stmt = $db->query("SELECT id, name, email, mobile, amount, purpose, payment_status, created_at FROM donations ORDER BY created_at DESC");
$donations = $stmt->fetchAll();

// ब्राउज़र को बताना कि यह एक Excel/CSV फाइल है
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Donations_Report_' . date('Y-m-d') . '.csv');

// आउटपुट स्ट्रीम खोलना
$output = fopen('php://output', 'w');

// Excel फाइल की पहली लाइन (Headings) सेट करना
fputcsv($output, array('Receipt ID', 'Donor Name', 'Email', 'Mobile Number', 'Donation Amount (Rs)', 'Purpose/Fund', 'Payment Status', 'Date & Time'));

// डेटाबेस से एक-एक करके डेटा निकालना और फाइल में लिखना
foreach ($donations as $row) {
    fputcsv($output, array(
        'REC-' . str_pad($row['id'], 5, '0', STR_PAD_LEFT),
        $row['name'],
        $row['email'],
        $row['mobile'],
        $row['amount'],
        $row['purpose'],
        strtoupper($row['payment_status']),
        date('d M Y, h:i A', strtotime($row['created_at']))
    ));
}

fclose($output);
exit;
?>