<?php
session_start();
require_once '../core/Database.php';

// सुरक्षा: अगर एडमिन नहीं है तो बाहर निकालें
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$db = Database::getInstance();

// वेबसाइट के कुल आँकड़े (Statistics) निकालना
$totalDonations = $db->query("SELECT SUM(amount) FROM donations WHERE payment_status = 'successful'")->fetchColumn() ?? 0;
$totalDonors = $db->query("SELECT COUNT(*) FROM users WHERE role = 'donor'")->fetchColumn();
$recentDonations = $db->query("SELECT * FROM donations ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark shadow">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">⚙️ Admin Control Panel</a>
        <a href="logout.php" class="btn btn-danger btn-sm fw-bold">Logout</a>
    </div>
</nav>

<div class="container mt-4">
    <!-- Reports पेज पर जाने का लिंक -->
    <div class="mb-3 text-end">
        <a href="donations.php" class="btn btn-outline-primary fw-bold shadow-sm">View All Donations & Reports ➔</a>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-success shadow border-0 p-3">
                <h5>Total Funds Raised</h5>
                <h2>₹ <?php echo number_format($totalDonations, 2); ?></h2>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-primary shadow border-0 p-3">
                <h5>Registered Donors</h5>
                <h2><?php echo $totalDonors; ?> Users</h2>
            </div>
        </div>
    </div>

    <div class="card shadow border-0 p-4">
        <h4 class="mb-3">Recent Donations (All Users)</h4>
        <table class="table table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Donor Name</th>
                    <th>Email</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($recentDonations) > 0): ?>
                    <?php foreach ($recentDonations as $don): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($don['name']); ?></td>
                        <td><?php echo htmlspecialchars($don['email']); ?></td>
                        <td class="fw-bold text-success">₹ <?php echo number_format($don['amount'], 2); ?></td>
                        <td>
                            <?php if($don['payment_status'] == 'successful') echo '<span class="badge bg-success">Success</span>'; 
                                  else echo '<span class="badge bg-warning text-dark">Pending</span>'; ?>
                        </td>
                        <td><?php echo date('d M Y', strtotime($don['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">No recent donations found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>