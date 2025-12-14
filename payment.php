<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: register.php");
    exit();
}

$sql = "SELECT * FROM SubscriptionPlan ORDER BY PlanID";
$result = $conn->query($sql);

$plans = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (preg_match('/^(Student|Family)(Monthly|Quarterly|Yearly)$/', $row['PlanName'], $matches)) {
            $category = $matches[1];
            $duration = $matches[2];
            $plans[$category][$duration] = $row;
        }
    }
} else {
    die("No subscription plans found in database.");
}

$defaultCategory = array_key_first($plans);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Select Subscription Plan</title>
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

    h1 {
        text-align: center;
        margin-bottom: 20px;
        font-size: 28px;
        font-weight: 700;
    }

    form.container {
        background: #141414;
        padding: 30px 25px;
        border-radius: 10px;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        color: #fff;
    }

    .section-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #ccc;
    }

    .plan-option {
        margin-bottom: 12px;
        font-size: 15px;
        display: flex;
        align-items: center;
        color: #fff;
    }

    input[type="radio"] {
        transform: scale(1.2);
        margin-right: 10px;
        accent-color: #6366f1;
        cursor: pointer;
    }

    #durations .plan-option span {
        font-weight: 500;
    }

    input[type="submit"] {
        width: 100%;
        padding: 12px;
        margin-top: 15px;
        border-radius: 6px;
        background-color: #6366f1;
        border: none;
        color: white;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    input[type="submit"]:hover {
        background-color: #4f46e5;
    }

    hr {
        border: 0;
        border-top: 1px solid #333;
        margin: 20px 0;
    }
</style>

<script>
    let plans = <?php echo json_encode($plans, JSON_HEX_TAG); ?>;

    function showDurations(category) {
        let durationsDiv = document.getElementById('durations');
        let html = '';

        if (!plans[category]) {
            durationsDiv.innerHTML = "<p>No durations available</p>";
            return;
        }

        for (let duration in plans[category]) {
            let price = plans[category][duration]['Price'];
            html += `<div class="plan-option">
                        <input type="radio" name="duration" value="${duration}" required>
                        <span>${duration} – $${parseFloat(price).toFixed(2)}</span>
                     </div>`;
        }
        durationsDiv.innerHTML = html;
    }

    window.onload = function() {
        let defaultCategory = "<?php echo $defaultCategory; ?>";
        let catInput = document.querySelector(`input[name='category'][value='${defaultCategory}']`);
        if(catInput) {
            catInput.checked = true;
            showDurations(defaultCategory);
        }
    };
</script>
</head>
<body>

<form method="POST" action="payment_form.php" class="container">
    <h1>Select Your Subscription Plan</h1>

    <div class="section-title">Step 1: Choose Category</div>
    <?php foreach($plans as $category => $durations): ?>
        <div class="plan-option">
            <input type="radio" name="category" value="<?= $category ?>"
                   onclick="showDurations('<?= $category ?>')"
                   <?= $category === $defaultCategory ? 'checked' : '' ?>>
            <?= $category ?>
        </div>
    <?php endforeach; ?>

    <hr>

    <div class="section-title">Step 2: Choose Duration</div>
    <div id="durations"></div>

    <input type="submit" value="Proceed to Payment">
</form>

</body>
</html>
