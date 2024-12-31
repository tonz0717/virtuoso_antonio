<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartId = intval($_POST['cart_id']);

    if ($cartId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid cart ID.']);
        exit();
    }

    require 'db_connection.php'; // Ensure DB connection file is included

    $sql = "DELETE FROM cart WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $cartId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove item.']);
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>
