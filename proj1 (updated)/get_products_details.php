<?php
// Include the database connection
include('db.php');

// Check if product ID is passed via GET
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch product details
    $product_sql = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($product_sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product_result = $stmt->get_result();
    
    if ($product_result->num_rows > 0) {
        $product = $product_result->fetch_assoc();

        // Fetch variants for the product
        $variant_sql = "SELECT * FROM product_variants WHERE product_id = ?";
        $variant_stmt = $conn->prepare($variant_sql);
        $variant_stmt->bind_param("i", $product_id);
        $variant_stmt->execute();
        $variant_result = $variant_stmt->get_result();

        $variants = [];
        if ($variant_result->num_rows > 0) {
            while ($variant = $variant_result->fetch_assoc()) {
                $variants[] = $variant;
            }
        }

        // Return product data as JSON
        echo json_encode([
            'product_name' => $product['product_name'],
            'description' => $product['description'],
            'image_url' => $product['image_url'],
            'variants' => $variants
        ]);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }

    // Close the database connection
    $stmt->close();
    $variant_stmt->close();
}

$conn->close();
?>
