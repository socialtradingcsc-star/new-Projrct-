<?php
session_start();
require_once '../core/Database.php';

// सुरक्षा चेक
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$db = Database::getInstance();
// डेटाबेस से सभी डोनेशन का डेटा निकालना (सबसे नया पहले)
$stmt = $db->query("SELECT * FROM donations ORDER BY created_at DESC");
$allDonations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>All Donations - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark shadow">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="dashboard.php">⬅ Back to Dashboard</a>
        <a href="logout.php" class="btn btn-danger btn-sm fw-bold">Logout</a>
    </div>
</nav>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-primary">All Donations Report</h3>
        <!-- Excel डाउनलोड करने का बटन -->
        <a href="export_donations.php" class="btn btn-success fw-bold shadow-sm">📥 Export to Excel (CSV)</a>
    </div>

    <div class="card shadow border-0 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Donor Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Purpose</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($allDonations) > 0): ?>
                        <?php foreach ($allDonations as $don): ?>
                        <tr>
                            <td>#<?php echo $don['id']; ?></td>
                            <td><?php echo date('d M Y, h:i A', strtotime($don['created_at'])); ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($don['name']); ?></td>
                            <td><?php echo htmlspecialchars($don['mobile']); ?></td>
                            <td><?php echo htmlspecialchars($don['email']); ?></td>
                            <td><?php echo htmlspecialchars($don['purpose']); ?></td>
                            <td class="text-success fw-bold">₹ <?php echo number_format($don['amount'], 2); ?></td>
                            <td>
                                <?php if($don['payment_status'] == 'successful'): ?>
                                    <span class="badge bg-success">Success</span>
                                <?php elseif($don['payment_status'] == 'failed'): ?>
                                    <span class="badge bg-danger">Failed</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">No donations found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>