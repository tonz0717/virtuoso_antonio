<?php
include('db.php');
session_start();

// Check if the user is an admin
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");  // Redirect if not admin
    exit();
}

// Get the order ID and new payment status from POST request
if (isset($_POST['order_id']) && isset($_POST['payment_status'])) {
    $order_id = $_POST['order_id'];
    $payment_status = $_POST['payment_status'];

    // Prepare and execute the update query
    $query = "UPDATE payments SET payment_status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $payment_status, $order_id);
    if ($stmt->execute()) {
        // Redirect back to manage orders page
        header("Location: manage_orders.php");
    } else {
        echo "Error updating payment status.";
    }
} else {
    echo "Invalid request.";
}
?>
