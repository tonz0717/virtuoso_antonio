<?php
include('db.php');
session_start();

// Fetch all products
$query = "SELECT * FROM products";
$result = $conn->query($query);
$products = $result->fetch_all(MYSQLI_ASSOC);

// If image_url is stored as a relative path, make sure it points to the right directory.
        // Example: If images are in 'images' folder, append 'images/' to the URL
        if (!preg_match("/^http/", $image_url)) {
          // If it's not an absolute URL, assume it's in the 'images' directory
          $image_url = "images/" . $image_url; 
      }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Navbar Style */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #FFE1E1;
            z-index: 1000;
            height: 60px;
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        .navbar a {
        font-family: "Ubuntu";
        font-size: 1rem;
        font-weight: 700;
        color: #333;
        text-decoration: none;
        padding-left: 8px 12px;
        margin: 0 20px;
        text-align: center;
        }

        .navbar a:hover {
            color: #FF3366;
        }

        /* Main content */
        .main-content {
            margin-top: 60px; /* Space for fixed navbar */
            padding: 20px;
            background-color: #f8f8f8;
        }

        .manage-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-family: 'Alike', serif;
            text-align: center;
            font-size: 36px;
            color: #422e1b;
            margin-top: 30px;
        }

        .add-product-btn {
            background-color: #ff3366;
            border: none;
            border-radius: 5px;
            color: white;
            padding: 10px 20px;
            font-size: 18px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .add-product-btn:hover {
            background-color: #e02557;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #FFFAE6;
            font-weight: bold;
            color: #422e1b;
        }

        td {
            background-color: #fff;
        }

        td img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .action-btn {
            background-color: #ff3366;
            border: none;
            border-radius: 5px;
            color: white;
            padding: 5px 15px;
            font-size: 14px;
            margin-right: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .action-btn:hover {
            background-color: #e02557;
        }

        .action-btn:active {
            background-color: #85064b;
            transform: scale(0.95);
        }

        /* Background */
        body {
            font-family: 'Inter Tight', sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
        }

    </style>
</head>
<body>

    <!-- NAVIGATION BAR -->
    <div class="navbar">
        <a href="admin_homepage.php">Dashboard</a>
        <a href="manage_products.php">Products</a>
        <a href="manage_orders.php">Orders</a>
        <a href="reports.php">Reports</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="manage-container">
            <h2>Manage Products</h2>
            <a href="add_product.php">
                <button class="add-product-btn">Add New Product</button>
            </a>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['product_id']; ?></td>
                            <td><?php echo $product['product_name']; ?></td>
                            <td><?php echo $product['description']; ?></td>
                            <td><img src="<?php echo $product['image_url']; ?>" alt="Product Image"></td>
                            <td>
                                <a href="edit_product.php?id=<?php echo $product['product_id']; ?>">
                                    <button class="action-btn">Edit</button>
                                </a>
                                <a href="delete_product.php?id=<?php echo $product['product_id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this product?')">
                                    <button class="action-btn">Delete</button>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
