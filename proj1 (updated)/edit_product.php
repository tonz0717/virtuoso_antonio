<?php
include('db.php');
include('header.php');

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch the existing product details
    $query = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];

    $query = "UPDATE products SET product_name = ?, description = ?, image_url = ? WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssi', $product_name, $description, $image_url, $product_id);

    if ($stmt->execute()) {
        echo "Product updated successfully!";
        header("Location: manage_products.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<h2>Edit Product</h2>
<form action="edit_product.php?id=<?php echo $product['product_id']; ?>" method="POST">
  <label for="product_name">Product Name:</label>
  <input type="text" id="product_name" name="product_name" value="<?php echo $product['product_name']; ?>" required><br>

  <label for="description">Description:</label>
  <textarea id="description" name="description" required><?php echo $product['description']; ?></textarea><br>

  <label for="image_url">Image URL:</label>
  <input type="text" id="image_url" name="image_url" value="<?php echo $product['image_url']; ?>" required><br>

  <button type="submit">Update Product</button>
</form>
