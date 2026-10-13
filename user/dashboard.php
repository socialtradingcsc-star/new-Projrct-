<?php
session_start();
require_once '../core/Database.php';

// अगर यूजर लॉगिन नहीं है, तो लॉगिन पेज पर भेजें
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$db = Database::getInstance();

// 1. कुल सफल डोनेशन (Total Successful Donations) का अमाउंट निकालें
$stmtTotal = $db->prepare("SELECT SUM(amount) as total FROM donations WHERE user_id = ? AND payment_status = 'successful'");
$stmtTotal->execute([$user_id]);
$totalDonations = $stmtTotal->fetch()['total'] ?? 0;

// 2. डोनेशन की पूरी हिस्ट्री निकालें (सबसे नई डोनेशन सबसे ऊपर)
$stmtHistory = $db->prepare("SELECT * FROM donations WHERE user_id = ? ORDER BY created_at DESC");
$stmtHistory->execute([$user_id]);
$donations = $stmtHistory->fetchAll();

require_once '../includes/header.php';
?>

<div class="container mt-5 mb-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="list-group shadow-sm">
                <a href="dashboard.php" class="list-group-item list-group-item-action active">Dashboard</a>
                <a href="#" class="list-group-item list-group-item-action">My Profile</a>
                <a href="../donate.php" class="list-group-item list-group-item-action text-success fw-bold">Donate Again</a>
                <a href="logout.php" class="list-group-item list-group-item-action text-danger fw-bold">Logout</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card shadow border-0 p-4 mb-4">
                <h3 class="text-primary">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h3>
                <p class="text-secondary">Here you can manage your profile, view donation history, and download receipts.</p>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card bg-light border-0 p-3 text-center shadow-sm">
                            <h5 class="text-muted">Total Successful Donations</h5>
                            <h2 class="text-success">₹ <?php echo number_format($totalDonations, 2); ?></h2>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light border-0 p-3 text-center shadow-sm">
                            <h5 class="text-muted">Total Certificates</h5>
                            <h2 class="text-warning">0</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Donation History Table -->
            <div class="card shadow border-0 p-4">
                <h4 class="mb-3">My Donation History</h4>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Purpose</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($donations) > 0): ?>
                                <?php foreach ($donations as $don): ?>
                                    <tr>
                                        <td><?php echo date('d M Y', strtotime($don['created_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($don['purpose']); ?></td>
                                        <td class="fw-bold">₹ <?php echo number_format($don['amount'], 2); ?></td>
                                        <td>
                                            <?php if ($don['payment_status'] == 'successful'): ?>
                                                <span class="badge bg-success">Success</span>
                                            <?php elseif ($don['payment_status'] == 'failed'): ?>
                                                <span class="badge bg-danger">Failed</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($don['payment_status'] == 'successful'): ?>
                                                <a href="download_receipt.php?id=<?php echo $don['id']; ?>" class="btn btn-sm btn-outline-primary" target="_blank">Download Receipt</a>
                                            <?php else: ?>
                                                <span class="text-muted small">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No donations found yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>