<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: register.php");
    exit();
}

$category = $_POST['category'] ?? null;
$duration = $_POST['duration'] ?? null;

if (!$category || !$duration) {
    die("Invalid plan selection.");
}

$sql = "SELECT * FROM SubscriptionPlan WHERE PlanName = '{$category}{$duration}' LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $plan = $result->fetch_assoc();
    $planID = $plan['PlanID'];
    $price = $plan['Price'];
} else {
    die("Selected plan does not exist.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Details</title>
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
        background: rgba(20, 20, 20, 0.95);
        width: 100%;
        max-width: 420px;
        padding: 35px 30px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        color: #fff;
    }

    h2 {
        text-align: center;
        margin-bottom: 25px;
        font-size: 24px;
        font-weight: 700;
        color: #fff;
    }

    .summary-box {
        background: rgba(255, 255, 255, 0.05);
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 25px;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .summary-box p {
        margin: 5px 0;
        font-size: 14px;
        display: flex;
        justify-content: space-between;
        color: #ccc;
    }
    .summary-box strong {
        color: #fff;
    }

    .input-group { margin-bottom: 15px; }
    label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
        color: #ccc;
    }

    input[type="text"], input[type="number"] {
        width: 100%;
        padding: 12px;
        border-radius: 6px;
        border: 1px solid #555;
        background: rgba(255,255,255,0.05);
        color: #fff;
        font-size: 15px;
        outline: none;
        transition: border-color 0.2s;
    }
    input[type="text"]:focus, input[type="number"]:focus {
        border-color: #6366f1;
        background: rgba(255,255,255,0.1);
    }

    .row {
        display: flex;
        gap: 15px;
    }
    .col { flex: 1; }

    input[type="submit"] {
        width: 100%;
        padding: 14px;
        margin-top: 10px;
        border-radius: 6px;
        background-color: #6366f1;
        border: none;
        color: white;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    input[type="submit"]:hover {
        background-color: #4f46e5;
    }
</style>
</head>
<body>

<div class="card">
    <h2>Payment Information</h2>

    <div class="summary-box">
        <p><strong>Category:</strong> <span><?= htmlspecialchars($category); ?></span></p>
        <p><strong>Duration:</strong> <span><?= htmlspecialchars($duration); ?></span></p>
        <p style="border-top: 1px solid rgba(255,255,255,0.1); padding-top:8px; margin-top:8px;">
            <strong>Total Price:</strong> 
            <span style="color:#2e7d32;font-weight:bold;">$<?= number_format($price,2); ?></span>
        </p>
    </div>

    <form action="confirm_payment.php" method="POST">
        <div class="input-group">
            <label>Cardholder Name</label>
            <input type="text" name="card_name" placeholder="John Doe" required>
        </div>

        <div class="input-group">
            <label>Card Number</label>
            <input type="text" name="card_number" maxlength="16" placeholder="1234 5678 9012 3456" required>
        </div>

        <div class="row input-group">
            <div class="col">
                <label>Expiry (MM/YY)</label>
                <input type="text" name="expiry" placeholder="08/28" required>
            </div>
            <div class="col">
                <label>CVV</label>
                <input type="number" name="cvv" maxlength="3" placeholder="123" required>
            </div>
        </div>

        <input type="hidden" name="planID" value="<?= $planID; ?>">
        <input type="hidden" name="category" value="<?= htmlspecialchars($category); ?>">
        <input type="hidden" name="duration" value="<?= htmlspecialchars($duration); ?>">
        <input type="hidden" name="price" value="<?= $price; ?>">

        <input type="submit" value="Confirm Payment">
    </form>
</div>

</body>
</html>
