<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: register.php");
    exit();
}

$userID = $_SESSION['user_id'];

$planID   = $_POST['planID'] ?? null;
$category = $_POST['category'] ?? null;
$duration = $_POST['duration'] ?? null;
$price    = $_POST['price'] ?? null;

if (!$planID || !$category || !$duration || !$price) {
    die("Invalid payment data.");
}

$sqlCheck = "SELECT * FROM SubscriptionPlan WHERE PlanID = '$planID' LIMIT 1";
$resultCheck = $conn->query($sqlCheck);
if (!$resultCheck || $resultCheck->num_rows == 0) {
    die("Plan does not exist.");
}

$paymentStatus = "PAID";
$paymentMethod = "Card";
$sqlPayment = "INSERT INTO Payment (PaymentStatus, PaymentMethod, PlanID, UserID)
               VALUES ('$paymentStatus', '$paymentMethod', '$planID', '$userID')";
if (!$conn->query($sqlPayment)) die("Payment insert failed: " . $conn->error);

$startDate = date('Y-m-d');
switch ($duration) {
    case 'Monthly': $expiryDate = date('Y-m-d', strtotime("+1 month")); break;
    case 'Quarterly': $expiryDate = date('Y-m-d', strtotime("+3 months")); break;
    case 'Yearly': $expiryDate = date('Y-m-d', strtotime("+1 year")); break;
    default: $expiryDate = date('Y-m-d'); break;
}

$checkSql = "SELECT * FROM SubscriptionStatus 
             WHERE PlanID = '$planID' 
             AND StartDate = '$startDate' 
             AND ExpiryDate = '$expiryDate'";
$checkResult = $conn->query($checkSql);
if ($checkResult->num_rows == 0) {
    $sqlSub = "INSERT INTO SubscriptionStatus (PlanID, StartDate, ExpiryDate, Status)
               VALUES ('$planID', '$startDate', '$expiryDate', 'ACTIVE')";
    if (!$conn->query($sqlSub)) die("Subscription insert failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Confirmed</title>
<link href="https://fonts.cdnfonts.com/css/netflix-sans" rel="stylesheet">
<style>
    body {
        font-family: 'Netflix Sans', sans-serif;
        background: url('uploads/body.png') no-repeat center center fixed;
        background-size: cover;
        color: #fff;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .card {
        background: rgba(20,20,20,0.95);
        width: 100%;
        max-width: 380px;
        padding: 40px 30px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        text-align: center;
    }

    .icon-container {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }
    
    .success-icon {
        width: 64px;
        height: 64px;
        background-color: #4caf50;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: bold;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    h1 { 
        color: #fff; 
        margin-bottom: 25px; 
        font-size: 24px;
        font-weight: 700;
    }

    .details-table {
        width: 100%;
        margin-bottom: 30px;
        font-size: 14px;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        color: #ccc;
    }

    .detail-label {
        font-weight: 600;
        text-align: left;
    }

    .detail-value {
        font-weight: 600;
        text-align: right;
    }

    .btn { 
        display: block;
        width: 100%;
        padding: 14px;
        background: #6366f1;
        color: white; 
        text-decoration: none; 
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        text-transform: uppercase;
        transition: background-color 0.2s;
    }

    .btn:hover { 
        background: #4f46e5;
    }
</style>
</head>
<body>

<div class="card">
    <div class="icon-container">
        <div class="success-icon">✔</div>
    </div>

    <h1>Payment Confirmed!</h1>

    <div class="details-table">
        <div class="detail-row">
            <span class="detail-label">Amount:</span>
            <span class="detail-value">$<?= number_format($price, 2); ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Date:</span>
            <span class="detail-value"><?= date("M d, Y"); ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Plan:</span>
            <span class="detail-value"><?= htmlspecialchars($category . ' - ' . $duration); ?></span>
        </div>
    </div>

    <a href="media_library.php" class="btn">DONE</a>
</div>

</body>
</html>
