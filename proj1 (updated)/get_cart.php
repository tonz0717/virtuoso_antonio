<?php
require 'db_connection.php'; // Ensure DB connection file is included

$sql = "SELECT c.id AS cart_id, p.name AS product_name, c.variant_id AS variant, c.quantity, p.price
        FROM cart c
        INNER JOIN products p ON c.product_id = p.id";
$result = $conn->query($sql);

if ($result) {
    $cart = [];
    while ($row = $result->fetch_assoc()) {
        $cart[] = [
            'cart_id' => $row['cart_id'],
            'product_name' => $row['product_name'],
            'variant' => $row['variant'],
            'quantity' => $row['quantity'],
            'price' => $row['price']
        ];
    }
    echo json_encode(['success' => true, 'cart' => $cart]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch cart.']);
}

$conn->close();
?>
