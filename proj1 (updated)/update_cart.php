<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartId = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);

    if ($cartId <= 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid cart ID or quantity.']);
        exit();
    }

    require 'db_connection.php'; // Ensure DB connection file is included

    $sql = "UPDATE cart SET quantity = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $quantity, $cartId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update quantity.']);
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>
