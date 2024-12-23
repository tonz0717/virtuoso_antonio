<?php
session_start();
header('Content-Type: application/json');
include('db.php');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    $cart_id = intval($_POST['cart_id']);

    try {
        $sql = "DELETE FROM cart WHERE cart_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }

        $stmt->bind_param('i', $cart_id);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Cart item removed successfully.';
        } else {
            throw new Exception('Execution failed: ' . $stmt->error);
        }
        $stmt->close(); // Close the statement
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request.';
}

// Send the JSON response
echo json_encode($response);
?>
