<?php
session_start();
require_once '../core/Database.php';

// Composer की फाइल्स को लोड करना
require_once '../vendor/autoload.php';
use Dompdf\Dompdf;

// सुरक्षा: अगर यूजर लॉगिन नहीं है या ID नहीं मिली, तो रोक दें
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    die("Unauthorized access!");
}

$db = Database::getInstance();

// सुरक्षा: केवल उसी यूजर की रसीद निकालें जो लॉगिन है (IDOR Protection)
$stmt = $db->prepare("SELECT * FROM donations WHERE id = ? AND user_id = ? AND payment_status = 'successful'");
$stmt->execute([$_GET['id'], $_SESSION['user_id']]);
$donation = $stmt->fetch();

if (!$donation) {
    die("Receipt not found or you are not authorized to download it.");
}

// रसीद का डिज़ाइन (HTML में)
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #0d6efd; padding-bottom: 20px; margin-bottom: 30px; }
        .title { font-size: 28px; font-weight: bold; color: #0d6efd; text-transform: uppercase; }
        .subtitle { font-size: 14px; color: #666; margin-top: 5px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        .table th { background-color: #f8f9fa; width: 40%; }
        .footer { margin-top: 60px; text-align: right; }
        .sign { border-top: 1px solid #000; display: inline-block; padding-top: 5px; width: 200px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Dynamic Foundation Trust</div>
        <div class="subtitle">Official Donation Receipt (80G Tax Exemption Eligible)</div>
    </div>
    
    <p><strong>Receipt Number:</strong> REC-' . str_pad($donation['id'], 5, '0', STR_PAD_LEFT) . '</p>
    <p><strong>Date of Donation:</strong> ' . date('d M Y, h:i A', strtotime($donation['created_at'])) . '</p>
    
    <table class="table">
        <tr>
            <th>Donor Name</th>
            <td>' . htmlspecialchars($donation['name']) . '</td>
        </tr>
        <tr>
            <th>Email Address</th>
            <td>' . htmlspecialchars($donation['email']) . '</td>
        </tr>
        <tr>
            <th>Mobile Number</th>
            <td>' . htmlspecialchars($donation['mobile']) . '</td>
        </tr>
        <tr>
            <th>Donation Amount</th>
            <td><strong>Rs. ' . number_format($donation['amount'], 2) . '</strong></td>
        </tr>
        <tr>
            <th>Purpose / Fund</th>
            <td>' . htmlspecialchars($donation['purpose']) . '</td>
        </tr>
        <tr>
            <th>Payment Status</th>
            <td><span style="color: green;">SUCCESSFUL</span></td>
        </tr>
    </table>

    <p style="margin-top: 30px; font-size: 12px; color: #666;">
        <em>This is a computer-generated receipt and does not require a physical signature. Thank you for your generous contribution towards building a better future!</em>
    </p>

    <div class="footer">
        <div class="sign">Authorized Signatory</div>
    </div>
</body>
</html>
';

// PDF जनरेट करना
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// PDF फाइल डाउनलोड करवाएं
$dompdf->stream("Receipt_REC-" . $donation['id'] . ".pdf", ["Attachment" => true]);
?>