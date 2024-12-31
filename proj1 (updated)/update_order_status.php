<?php
include('db.php');
session_start();

// Get order ID and new status from POST request
if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // Prepare and execute the update query
    $query = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $status, $order_id);
    if ($stmt->execute()) {
        // Redirect back to manage orders page
        header("Location: manage_orders.php");
    } else {
        echo "Error updating status.";
    }
} else {
    echo "Invalid request.";
}
?>
