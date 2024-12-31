<?php
include('db.php');
session_start();

// Fetch all orders
$query = "SELECT * FROM orders";
$result = $conn->query($query);
$orders = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
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

        /* Form and Button Styling */
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

        select {
            padding: 5px;
            font-size: 14px;
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            cursor: pointer;
        }

        button[type="submit"] {
            background-color: #ff3366;
            border: none;
            color: white;
            padding: 5px 15px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #e02557;
        }

        button[type="submit"]:active {
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
            <h2>Manage Orders</h2>

            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>User ID</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo $order['order_id']; ?></td>
                            <td><?php echo $order['registration_id']; ?></td>
                            <td>Php <?php echo number_format($order['total_price'], 2); ?></td>
                            <td><?php echo $order['status']; ?></td>
                            <td>
                                <!-- Form to update status -->
                                <form method="POST" action="update_order_status.php" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <select name="status">
                                        <option value="Pending" <?php echo $order['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Completed" <?php echo $order['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="Cancelled" <?php echo $order['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit">Update</button>
                                </form>

                                <!-- Button to cancel order -->
                                <?php if ($order['status'] != 'Cancelled'): ?>
                                    <form method="POST" action="cancel_order.php" style="display:inline;">
                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                        <button type="submit">Cancel Order</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
