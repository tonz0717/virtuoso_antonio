<?php
include('db.php');
session_start();

// Get order ID from POST request
if (isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    // Prepare and execute the update query to cancel the order
    $query = "UPDATE orders SET status = 'Cancelled' WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $order_id);
    if ($stmt->execute()) {
        // Redirect back to manage orders page
        header("Location: manage_orders.php");
    } else {
        echo "Error cancelling the order.";
    }
} else {
    echo "Invalid request.";
}
?>
