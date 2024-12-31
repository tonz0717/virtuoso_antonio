<?php
session_start();
include('db.php');

$cartItems = [];

if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
    $email = $_SESSION['user'];

    try {
        $sqlUser = "SELECT registration_id FROM login_credentials WHERE email = ?";
        $stmtUser = $conn->prepare($sqlUser);
        if (!$stmtUser) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }

        $stmtUser->bind_param('s', $email);
        $stmtUser->execute();
        $resultUser = $stmtUser->get_result();

        if ($resultUser->num_rows > 0) {
            $user = $resultUser->fetch_assoc();
            $registration_id = $user['registration_id'];

            $sqlCart = "SELECT cart.cart_id, cart.product_id, cart.variant_id, cart.quantity, 
                               products.product_name, variants.variant_name, variants.price 
                        FROM cart
                        INNER JOIN products ON cart.product_id = products.product_id
                        INNER JOIN product_variants AS variants ON cart.variant_id = variants.variant_id
                        WHERE cart.registration_id = ?";
            $stmtCart = $conn->prepare($sqlCart);
            if (!$stmtCart) {
                throw new Exception('Prepare failed: ' . $conn->error);
            }

            $stmtCart->bind_param('i', $registration_id);
            $stmtCart->execute();
            $resultCart = $stmtCart->get_result();

            while ($row = $resultCart->fetch_assoc()) {
                $cartItems[] = $row;
            }
        } else {
            echo 'User not found.';
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!-- HTML for displaying cart (no changes needed here) -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="checkoutstyle.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Playfair+Display:wght@700&display=swap');
    </style>
</head>
<body>
    <header>
        <h1>Checkout</h1>
    </header>
    <main class="checkout-container">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Variant</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalPrice = 0;
                    foreach ($cartItems as $item):
                        $lineTotal = $item['price'] * $item['quantity'];
                        $totalPrice += $lineTotal;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= htmlspecialchars($item['variant_name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>$<?= number_format($item['price'], 2) ?></td>
                        <td>$<?= number_format($lineTotal, 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="total-price">Total Price: $<?= number_format($totalPrice, 2) ?></p>

        <!-- Checkout Form -->
        <form method="POST" action="process_to_checkout.php">
            <h2>Delivery Details</h2>
            <label for="delivery_address">Delivery Address:</label>
            <input type="text" name="delivery_address" id="delivery_address" required>
            
            <h2>Payment Method</h2>
            <label for="payment_method">Choose Payment Method:</label>
            <select name="payment_method" id="payment_method" required>
                <option value="Credit Card">Credit Card</option>
                <option value="Cash on Delivery">Cash on Delivery</option>
                <option value="Bank Transfer">Bank Transfer</option>
            </select>

            <input type="hidden" name="registration_id" value="<?= $registration_id ?>">
            <input type="hidden" name="total_price" value="<?= $totalPrice ?>">

            <button type="submit" class="confirm-btn">Confirm and Pay</button>
        </form>
    </main>
</body>
</html>
