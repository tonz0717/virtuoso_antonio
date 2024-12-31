<?php
session_start();  // Make sure session is started
include('db.php');  // Include database connection

// Get order_id from URL parameters
$order_id = $_GET['order_id'];

// Prepare SQL query to get order data
$sqlOrder = "SELECT * FROM orders WHERE order_id = ?";
$stmtOrder = $conn->prepare($sqlOrder);
$stmtOrder->bind_param('i', $order_id);
$stmtOrder->execute();
$order = $stmtOrder->get_result()->fetch_assoc();

// Prepare SQL query to get order details
$sqlOrderDetails = "SELECT * FROM order_details WHERE order_id = ?";
$stmtOrderDetails = $conn->prepare($sqlOrderDetails);
$stmtOrderDetails->bind_param('i', $order_id);
$stmtOrderDetails->execute();
$orderDetails = $stmtOrderDetails->get_result();

// Prepare SQL query to get payment details
$sqlPayment = "SELECT * FROM payments WHERE order_id = ?";
$stmtPayment = $conn->prepare($sqlPayment);
$stmtPayment->bind_param('i', $order_id);
$stmtPayment->execute();
$payment = $stmtPayment->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <link rel="stylesheet" href="style.css">  <!-- Include your styles if any -->
    <style>
        /* Adjust the navbar and ensure it stays fixed */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #FFE1E1; /* Corrected color code */
            z-index: 1000; /* Makes sure the navbar stays on top */
            height: 60px; /* Adjust based on your navbar height */
        }

        /* Adjust the content below the navbar */
        .main-content {
            margin-top: 60px; /* Pushes the content down to avoid being covered by navbar */
            padding: 20px;
        }

        /* Optional: Adding some spacing to the receipt header */
        .receipt-header {
            padding-top: 20px; /* Adds extra padding to the receipt section */
        }
    </style>
</head>
<body>

<!-- NAVIGATION BAR -->
<div class="navbar">
  <div>
    <a href="#home">Home</a>
    <a href="#product">Products</a>
    <a href="#cart" id="viewCartButton">View Cart</a>
    <a href="orders.php">Order History</a>

    <?php if (!isset($_SESSION['user'])): ?>
      <a href="Login.php">Login/Signup</a>
    <?php else: ?>
      <a href="logout.php">Logout</a>
    <?php endif; ?>
  </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <h1 class="receipt-header">Receipt</h1>
    <p><strong>Order ID:</strong> <?= $order['order_id'] ?></p>
    <p><strong>Total Price:</strong> $<?= number_format($order['total_price'], 2) ?></p>
    <p><strong>Status:</strong> <?= $order['status'] ?></p>

    <h2>Order Details</h2>
    <ul>
        <?php if ($orderDetails->num_rows > 0): ?>
            <?php while ($detail = $orderDetails->fetch_assoc()): ?>
                <li><?= $detail['product_id'] ?> - <?= $detail['quantity'] ?> x $<?= number_format($detail['price'], 2) ?></li>
            <?php endwhile; ?>
        <?php else: ?>
            <li>No products found for this order.</li>
        <?php endif; ?>
    </ul>

    <h2>Payment</h2>
    <?php if ($payment): ?>
        <p><strong>Method:</strong> <?= $payment['payment_method'] ?></p>
        <p><strong>Status:</strong> <?= $payment['payment_status'] ?></p>
    <?php else: ?>
        <p>No payment details found.</p>
    <?php endif; ?>

    <div>
        <a href="index.php" style="text-decoration: none; padding: 10px 20px; background-color: #ff3366; color: white; border-radius: 5px; font-size: 16px;">Return to Home</a>
    </div>
</div>

</body>
</html>
