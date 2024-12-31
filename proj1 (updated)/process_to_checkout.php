<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registration_id = $_POST['registration_id'];
    $total_price = $_POST['total_price'];
    $delivery_address = $_POST['delivery_address'];
    $payment_method = $_POST['payment_method'];

    try {
        // Start transaction
        $conn->begin_transaction();

        // Insert into `orders` table
        $sqlOrder = "INSERT INTO orders (registration_id, total_price, status, order_date)
                     VALUES (?, ?, 'Pending', NOW())";
        $stmtOrder = $conn->prepare($sqlOrder);
        $stmtOrder->bind_param('id', $registration_id, $total_price);
        $stmtOrder->execute();

        // Get the generated order_id
        $order_id = $conn->insert_id;

        // Check if the order_id is valid
        if (!$order_id) {
            throw new Exception('Failed to retrieve order ID');
        }

        // Get cart items for the current user
        $cartItems = [];
        $sqlCart = "SELECT cart.cart_id, cart.product_id, cart.variant_id, cart.quantity, 
                           products.product_name, variants.variant_name, variants.price 
                    FROM cart
                    INNER JOIN products ON cart.product_id = products.product_id
                    INNER JOIN product_variants AS variants ON cart.variant_id = variants.variant_id
                    WHERE cart.registration_id = ?";
        $stmtCart = $conn->prepare($sqlCart);
        $stmtCart->bind_param('i', $registration_id);
        $stmtCart->execute();
        $resultCart = $stmtCart->get_result();

        while ($row = $resultCart->fetch_assoc()) {
            $cartItems[] = $row;
        }

        if (empty($cartItems)) {
            throw new Exception('Cart is empty.');
        }

        // Insert into `order_details` table
        foreach ($cartItems as $item) {
            $sqlOrderDetail = "INSERT INTO order_details (order_id, product_id, quantity, price)
                               VALUES (?, ?, ?, ?)";
            $stmtOrderDetail = $conn->prepare($sqlOrderDetail);
            $stmtOrderDetail->bind_param('iiid', $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmtOrderDetail->execute();
        }

        // Insert into `payments` table
        $sqlPayment = "INSERT INTO payments (order_id, payment_method, payment_status, payment_date, amount_paid)
                       VALUES (?, ?, 'Unpaid', NOW(), ?)";
        $stmtPayment = $conn->prepare($sqlPayment);
        $stmtPayment->bind_param('isd', $order_id, $payment_method, $total_price);
        $stmtPayment->execute();

        // Clear the cart
        $sqlClearCart = "DELETE FROM cart WHERE registration_id = ?";
        $stmtClearCart = $conn->prepare($sqlClearCart);
        $stmtClearCart->bind_param('i', $registration_id);
        $stmtClearCart->execute();

        // Commit transaction
        $conn->commit();

        // Redirect to receipt page
        header("Location: receipt.php?order_id=$order_id");
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
