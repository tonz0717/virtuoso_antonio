<?php
session_start();
header('Content-Type: application/json');
include('db.php');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'], $_POST['quantity'])) {
    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity > 0) {
        try {
            $sql = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                throw new Exception('Prepare failed: ' . $conn->error);
            }

            $stmt->bind_param('ii', $quantity, $cart_id);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Cart item quantity updated successfully.';
            } else {
                throw new Exception('Execution failed: ' . $stmt->error);
            }
        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Invalid quantity.';
    }
} else {
    $response['message'] = 'Invalid request.';
}

echo json_encode($response);
?>
