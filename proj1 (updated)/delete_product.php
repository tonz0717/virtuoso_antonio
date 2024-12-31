<?php
include('db.php');
include('header.php');

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Delete the product from the database
    $query = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $product_id);

    if ($stmt->execute()) {
        echo "Product deleted successfully!";
        header("Location: manage_products.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
