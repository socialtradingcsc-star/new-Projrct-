<?php
session_start();
require_once 'core/Database.php';

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $message = htmlspecialchars($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($message)) {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $phone, $message])) {
                $success_msg = "Thank you! Your message has been sent successfully. Our team will contact you soon.";
            } else {
                $error_msg = "Something went wrong. Please try again.";
            }
        } catch (Exception $e) {
            $error_msg = "Database error: " . $e->getMessage();
        }
    } else {
        $error_msg = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Dynamic Foundation Trust</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .contact-header {
            background: linear-gradient(135deg, #1e3a8a, #172554);
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        .contact-info-box {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            height: 100%;
        }
        .icon-circle {
            width: 60px; height: 60px;
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; margin-bottom: 20px;
        }
    </style>
</head>
<body class="bg-light">

    <!-- Navbar (Simple Version for Contact Page) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-hands-holding-child text-success me-2"></i> Dynamic Foundation</a>
            <a href="donate.php" class="btn btn-success fw-bold ms-auto">Donate Now</a>
        </div>
    </nav>

    <!-- Header -->
    <div class="contact-header">
        <div class="container">
            <h1 class="display-5 fw-bold mb-3">Get in Touch</h1>
            <p class="lead" style="color: rgba(255,255,255,0.8);">Have questions? We'd love to hear from you.</p>
        </div>
    </div>

    <div class="container mt-5 mb-5">
        <div class="row g-5">
            <!-- Contact Information -->
            <div class="col-lg-4">
                <div class="contact-info-box">
                    <h4 class="fw-bold mb-4">Contact Information</h4>
                    
                    <div class="d-flex mb-4">
                        <div class="icon-circle me-3"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1">Our Location</h6>
                            <p class="text-muted small mb-0">Deoria, Uttar Pradesh<br>India - 274001</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-4">
                        <div class="icon-circle me-3"><i class="fas fa-phone-alt"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1">Call Us</h6>
                            <p class="text-muted small mb-0">+91 98765 43210</p>
                        </div>
                    </div>

                    <div class="d-flex">
                        <div class="icon-circle me-3"><i class="fas fa-envelope"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1">Email Us</h6>
                            <p class="text-muted small mb-0">contact@dynamicfoundation.com</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 p-4 p-md-5 rounded-4">
                    <h3 class="fw-bold mb-4">Send Us a Message</h3>
                    
                    <?php if($success_msg): ?>
                        <div class="alert alert-success fw-semibold"><i class="fas fa-check-circle me-2"></i> <?= $success_msg ?></div>
                    <?php endif; ?>
                    
                    <?php if($error_msg): ?>
                        <div class="alert alert-danger fw-semibold"><i class="fas fa-exclamation-circle me-2"></i> <?= $error_msg ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="fw-semibold mb-1">Your Name *</label>
                                <input type="text" name="name" class="form-control form-control-lg bg-light border-0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-semibold mb-1">Email Address *</label>
                                <input type="email" name="email" class="form-control form-control-lg bg-light border-0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="fw-semibold mb-1">Phone Number</label>
                            <input type="text" name="phone" class="form-control form-control-lg bg-light border-0">
                        </div>
                        <div class="mb-4">
                            <label class="fw-semibold mb-1">Your Message *</label>
                            <textarea name="message" class="form-control form-control-lg bg-light border-0" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg fw-bold px-5" style="background-color: #1e3a8a; border: none;">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>