<?php
include('db.php');

$order_id = $_GET['order_id'];

$sqlOrder = "SELECT * FROM orders WHERE order_id = ?";
$stmtOrder = $conn->prepare($sqlOrder);
$stmtOrder->bind_param('i', $order_id);
$stmtOrder->execute();
$order = $stmtOrder->get_result()->fetch_assoc();

$sqlOrderDetails = "SELECT * FROM order_details WHERE order_id = ?";
$stmtOrderDetails = $conn->prepare($sqlOrderDetails);
$stmtOrderDetails->bind_param('i', $order_id);
$stmtOrderDetails->execute();
$orderDetails = $stmtOrderDetails->get_result();

$sqlPayment = "SELECT * FROM payments WHERE order_id = ?";
$stmtPayment = $conn->prepare($sqlPayment);
$stmtPayment->bind_param('i', $order_id);
$stmtPayment->execute();
$payment = $stmtPayment->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
</head>
<body>
    <h1>Receipt</h1>
    <p>Order ID: <?= $order['order_id'] ?></p>
    <p>Total Price: $<?= number_format($order['total_price'], 2) ?></p>
    <p>Status: <?= $order['status'] ?></p>
    <h2>Order Details</h2>
    <ul>
        <?php while ($detail = $orderDetails->fetch_assoc()): ?>
            <li><?= $detail['product_id'] ?> - <?= $detail['quantity'] ?> x $<?= number_format($detail['price'], 2) ?></li>
        <?php endwhile; ?>
    </ul>
    <h2>Payment</h2>
    <p>Method: <?= $payment['payment_method'] ?></p>
    <p>Status: <?= $payment['payment_status'] ?></p>
</body>
</html>
