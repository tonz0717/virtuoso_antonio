<?php
include('db.php');
include('header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];

    $query = "INSERT INTO products (product_name, description, image_url) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sss', $product_name, $description, $image_url);

    if ($stmt->execute()) {
        echo "Product added successfully!";
        header("Location: manage_products.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<h2>Add New Product</h2>
<form action="add_product.php" method="POST">
  <label for="product_name">Product Name:</label>
  <input type="text" id="product_name" name="product_name" required><br>

  <label for="description">Description:</label>
  <textarea id="description" name="description" required></textarea><br>

  <label for="image_url">Image URL:</label>
  <input type="text" id="image_url" name="image_url" required><br>

  <button type="submit">Add Product</button>
</form>
