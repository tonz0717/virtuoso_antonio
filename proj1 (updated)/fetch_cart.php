<?php
session_start();
header('Content-Type: application/json');
include('db.php');

$response = ['success' => false, 'message' => '', 'data' => []];

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
                $response['data'][] = $row;
            }

            $response['success'] = true;
        } else {
            $response['message'] = 'User not found in login_credentials.';
        }
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'User session not set.';
}

echo json_encode($response);
?>