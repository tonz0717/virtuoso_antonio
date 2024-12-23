<?php
session_start();

include('db.php');



// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            const errorMessage = document.createElement('div');
            errorMessage.innerHTML = `
                <div id='login-error-modal' style='position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: flex; justify-content: center; align-items: center; z-index: 1000;'>
                    <div style='background: white; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);'>
                        <h2>Error</h2>
                        <p>You must be logged in to add items to the cart.</p>
                        <button id='redirect-button' style='padding: 10px 20px; background: #ff3361;border: none; border-radius: 4px; cursor: pointer;'>OK</button>
                    </div>
                </div>`;
            document.body.appendChild(errorMessage);

            // Add event listener to redirect button
            document.getElementById('redirect-button').addEventListener('click', function() {
                window.location.href = 'index.php';
            });
        });
    </script>";
    exit;
}




$email = $_SESSION['user'];
$sql = "SELECT registration_id FROM login_credentials WHERE email = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database Error: " . $conn->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($registrationId);
    $stmt->fetch();
} else {
    die("Error: User not found.");
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = isset($_POST['productId']) ? intval($_POST['productId']) : 0;
    $variantId = isset($_POST['variant']) ? intval($_POST['variant']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) :1;

    if ($productId <= 0 || $quantity <= 0) {
        die("Error: Invalid product or quantity.");
    }

    // Check if the item is already in the cart
    $sql = "SELECT quantity FROM cart WHERE registration_id = ? AND product_id = ? AND variant_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $registrationId, $productId, $variantId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Update existing item's quantity
        $stmt->bind_result($existingQuantity);
        $stmt->fetch();
        $newQuantity = $existingQuantity + $quantity;

        $updateSql = "UPDATE cart SET quantity = ? WHERE registration_id = ? AND product_id = ? AND variant_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("iiii", $newQuantity, $registrationId, $productId, $variantId);
        $updateStmt->execute();
        $updateStmt->close();
    } else {
        // Add new item to the cart
        $insertSql = "INSERT INTO cart (registration_id, product_id, variant_id, quantity) VALUES (?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("iiii", $registrationId, $productId, $variantId, $quantity);
        $insertStmt->execute();
        $insertStmt->close();
    }
    $stmt->close();
    $conn->close();

    // Redirect back with success
    header('Location: index.php?added=1');
    exit();
}
