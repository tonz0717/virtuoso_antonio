<?php
session_start();
header('Content-Type: application/json');
include('db.php');

$response = ['success' => false, 'message' => '', 'data' => []];

if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
    $email = $_SESSION['user'];

    try {
        // Get user registration_id from the login credentials table
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

            // Fetch all orders for this user
            $sqlOrders = "SELECT order_id, total_price, status, order_date FROM orders WHERE registration_id = ?";
            $stmtOrders = $conn->prepare($sqlOrders);
            if (!$stmtOrders) {
                throw new Exception('Prepare failed: ' . $conn->error);
            }

            $stmtOrders->bind_param('i', $registration_id);
            $stmtOrders->execute();
            $resultOrders = $stmtOrders->get_result();

            while ($order = $resultOrders->fetch_assoc()) {
                $order_id = $order['order_id'];

                // Fetch order details (products in each order)
                $sqlOrderDetails = "SELECT od.product_id, od.variant_id, od.price, p.product_name, v.variant_name
                                    FROM order_details od
                                    JOIN products p ON od.product_id = p.product_id
                                    JOIN product_variants v ON od.variant_id = v.variant_id
                                    WHERE od.order_id = ?";
                $stmtOrderDetails = $conn->prepare($sqlOrderDetails);
                if (!$stmtOrderDetails) {
                    throw new Exception('Prepare failed: ' . $conn->error);
                }

                $stmtOrderDetails->bind_param('i', $order_id);
                $stmtOrderDetails->execute();
                $resultOrderDetails = $stmtOrderDetails->get_result();

                $order_products = [];
                while ($detail = $resultOrderDetails->fetch_assoc()) {
                    $order_products[] = $detail;
                }

                // Add order data to the response
                $response['data'][] = [
                    'order_id' => $order['order_id'],
                    'total_price' => $order['total_price'],
                    'status' => $order['status'],
                    'order_date' => $order['order_date'],
                    'products' => $order_products
                ];
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
