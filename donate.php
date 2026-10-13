<?php 
session_start();
require_once 'includes/header.php'; 
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 p-5">
                <div class="text-center mb-4">
                    <h2 class="text-primary fw-bold">Make a Donation</h2>
                    <p class="text-secondary">Your contribution can change a life. Thank you for your support!</p>
                </div>
                
                <form id="donationForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Full Name *</label>
                            <input type="text" id="name" class="form-control" value="<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Email Address *</label>
                            <input type="email" id="email" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold">Mobile Number *</label>
                            <input type="text" id="mobile" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Donation Amount (₹) *</label>
                            <input type="number" id="amount" class="form-control" placeholder="e.g. 500" min="100" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold">Donation Purpose</label>
                        <select id="purpose" class="form-select">
                            <option value="General Fund">General Fund</option>
                            <option value="Child Education">Child Education</option>
                            <option value="Healthcare">Healthcare & Medicine</option>
                        </select>
                    </div>

                    <button type="button" id="payBtn" class="btn btn-success w-100 py-3 fw-bold fs-5">
                        Proceed to Secure Payment
                    </button>
                    <p class="text-center text-muted mt-3 small">Safe & Secure Payments via Razorpay</p>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<!-- Razorpay Checkout Script (With Verification Logic) -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('payBtn').addEventListener('click', async function(e) {
    e.preventDefault();
    
    let name = document.getElementById('name').value;
    let email = document.getElementById('email').value;
    let mobile = document.getElementById('mobile').value;
    let amount = document.getElementById('amount').value;
    let purpose = document.getElementById('purpose').value;
    
    if(name == '' || email == '' || mobile == '' || amount == '') {
        alert("Please fill all required fields!");
        return;
    }
    
    this.innerText = "Processing...";
    this.disabled = true;

    let formData = new FormData();
    formData.append('name', name);
    formData.append('email', email);
    formData.append('mobile', mobile);
    formData.append('amount', amount);
    formData.append('purpose', purpose);
    
    try {
        let response = await fetch('ajax/process_donation.php', {
            method: 'POST',
            body: formData
        });
        
        let textData = await response.text(); 
        
        try {
            let data = JSON.parse(textData);
            
            if(data.status === 'success') {
                var options = {
                    "key": data.key, 
                    "amount": data.amount, 
                    "currency": "INR",
                    "name": "Dynamic Foundation Trust",
                    "description": "Donation for " + purpose,
                    "handler": async function (res){
                        
                        // Payment Successful, now Verify and Update Database
                        let verifyData = new FormData();
                        verifyData.append('razorpay_payment_id', res.razorpay_payment_id);
                        verifyData.append('donation_id', data.donation_id);

                        try {
                            let verifyReq = await fetch('ajax/verify_payment.php', {
                                method: 'POST',
                                body: verifyData
                            });
                            let verifyRes = await verifyReq.json();

                            if(verifyRes.status === 'success') {
                                alert("Payment Successful! Thank you for your donation.");
                                window.location.href = "user/dashboard.php";
                            } else {
                                alert("Payment received, but database update failed.");
                            }
                        } catch (e) {
                            alert("Error verifying payment.");
                        }
                    },
                    "prefill": { "name": name, "email": email, "contact": mobile },
                    "theme": { "color": "#0d6efd" }
                };
                var rzp1 = new Razorpay(options);
                rzp1.on('payment.failed', function (res){
                    alert("Payment Failed. Please try again.");
                });
                rzp1.open();
            } else {
                alert("Error: " + data.message);
            }
        } catch(err) {
            alert("PHP Error Found:\n" + textData);
        }
    } catch (error) {
        alert("Network Error. Cannot connect to server.");
    }
    
    // Reset Button
    document.getElementById('payBtn').innerText = "Proceed to Secure Payment";
    document.getElementById('payBtn').disabled = false;
});
</script>