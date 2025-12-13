<?php
/**
 * Alumni Donation Page
 * Support your alma mater through donations
 * Verdant SMS v3.0
 */

session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Alumni only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'alumni') {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user = db()->fetch("SELECT * FROM users WHERE id = ?", [$user_id]);

$page_title = 'Support & Donate';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Verdant SMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <style>
        .donate-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .page-header h1 {
            color: var(--cyber-cyan);
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .page-header p {
            color: #888;
            font-size: 1.1rem;
        }
        
        .donation-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .donation-card {
            background: rgba(0, 0, 0, 0.6);
            border: 2px solid var(--cyber-cyan);
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .donation-card:hover, .donation-card.selected {
            border-color: var(--cyber-pink);
            transform: scale(1.05);
            box-shadow: 0 0 30px rgba(255, 0, 100, 0.3);
        }
        
        .donation-card .amount {
            font-size: 2.5rem;
            color: var(--cyber-cyan);
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .donation-card .label {
            color: #888;
            font-size: 0.9rem;
        }
        
        .custom-amount {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid var(--cyber-cyan);
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .custom-amount label {
            display: block;
            color: var(--cyber-cyan);
            margin-bottom: 0.5rem;
        }
        
        .custom-amount input {
            width: 100%;
            padding: 1rem;
            font-size: 1.5rem;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid var(--cyber-cyan);
            border-radius: 5px;
            color: #fff;
            text-align: center;
        }
        
        .donation-areas {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid var(--cyber-cyan);
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .donation-areas h3 {
            color: var(--cyber-cyan);
            margin-bottom: 1rem;
        }
        
        .area-option {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-bottom: 1px solid rgba(0, 255, 255, 0.1);
        }
        
        .area-option:last-child {
            border-bottom: none;
        }
        
        .area-option input[type="radio"] {
            width: 20px;
            height: 20px;
        }
        
        .area-option label {
            color: #ccc;
            cursor: pointer;
        }
        
        .btn-donate {
            display: block;
            width: 100%;
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--cyber-cyan), var(--cyber-pink));
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 1.3rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-donate:hover {
            transform: scale(1.02);
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.5);
        }
        
        .coming-soon-notice {
            text-align: center;
            padding: 2rem;
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid #ffc107;
            border-radius: 10px;
            margin-top: 2rem;
        }
        
        .coming-soon-notice i {
            font-size: 2rem;
            color: #ffc107;
            margin-bottom: 1rem;
        }
        
        .bank-details {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid #00ff00;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        
        .bank-details h4 {
            color: #00ff00;
            margin-bottom: 1rem;
        }
        
        .bank-details p {
            color: #ccc;
            margin: 0.5rem 0;
        }
    </style>
</head>
<body class="cyber-bg">
    <?php include '../includes/cyber-nav.php'; ?>
    
    <main class="cyber-main">
        <div class="donate-container">
            <div class="page-header">
                <h1><i class="fas fa-heart"></i> Support Your Alma Mater</h1>
                <p>Your contribution helps shape the future of education</p>
            </div>
            
            <div class="donation-options">
                <div class="donation-card" onclick="selectAmount(5000)">
                    <div class="amount">₦5,000</div>
                    <div class="label">Bronze Supporter</div>
                </div>
                <div class="donation-card" onclick="selectAmount(10000)">
                    <div class="amount">₦10,000</div>
                    <div class="label">Silver Supporter</div>
                </div>
                <div class="donation-card" onclick="selectAmount(25000)">
                    <div class="amount">₦25,000</div>
                    <div class="label">Gold Supporter</div>
                </div>
                <div class="donation-card" onclick="selectAmount(50000)">
                    <div class="amount">₦50,000</div>
                    <div class="label">Platinum Supporter</div>
                </div>
            </div>
            
            <div class="custom-amount">
                <label for="customAmount">Or Enter Custom Amount (₦)</label>
                <input type="number" id="customAmount" placeholder="Enter amount" min="1000">
            </div>
            
            <div class="donation-areas">
                <h3><i class="fas fa-bullseye"></i> Direct Your Donation</h3>
                <div class="area-option">
                    <input type="radio" name="area" id="general" value="general" checked>
                    <label for="general"><strong>General Fund</strong> - Where needed most</label>
                </div>
                <div class="area-option">
                    <input type="radio" name="area" id="scholarship" value="scholarship">
                    <label for="scholarship"><strong>Scholarship Fund</strong> - Support deserving students</label>
                </div>
                <div class="area-option">
                    <input type="radio" name="area" id="infrastructure" value="infrastructure">
                    <label for="infrastructure"><strong>Infrastructure</strong> - Buildings & facilities</label>
                </div>
                <div class="area-option">
                    <input type="radio" name="area" id="technology" value="technology">
                    <label for="technology"><strong>Technology</strong> - ICT equipment & labs</label>
                </div>
                <div class="area-option">
                    <input type="radio" name="area" id="sports" value="sports">
                    <label for="sports"><strong>Sports</strong> - Athletic programs & equipment</label>
                </div>
            </div>
            
            <div class="coming-soon-notice">
                <i class="fas fa-tools"></i>
                <h3 style="color: #ffc107;">Online Payment Coming Soon!</h3>
                <p style="color: #888;">We're integrating secure payment gateways. For now, please use bank transfer.</p>
            </div>
            
            <div class="bank-details">
                <h4><i class="fas fa-university"></i> Bank Transfer Details</h4>
                <p><strong>Bank:</strong> First Bank of Nigeria</p>
                <p><strong>Account Name:</strong> Verdant School Alumni Association</p>
                <p><strong>Account Number:</strong> 0123456789</p>
                <p style="margin-top: 1rem; font-size: 0.9rem; color: #888;">
                    Please include your name and graduating year in the transfer reference.
                </p>
            </div>
        </div>
    </main>
    
    <script>
        function selectAmount(amount) {
            document.querySelectorAll('.donation-card').forEach(card => {
                card.classList.remove('selected');
            });
            event.target.closest('.donation-card').classList.add('selected');
            document.getElementById('customAmount').value = amount;
        }
    </script>
</body>
</html>
