<?php
include('db.php'); // Database connection

// Fetch total sales (sum of all completed orders)
$totalSalesQuery = "SELECT SUM(o.total_price) AS total_sales 
                    FROM orders o 
                    WHERE o.status = 'Completed'";
$resultTotalSales = $conn->query($totalSalesQuery);
$totalSalesData = $resultTotalSales->fetch_assoc();
$totalSales = $totalSalesData['total_sales'];

// Fetch orders placed this month
$ordersThisMonthQuery = "SELECT COUNT(o.order_id) AS orders_this_month 
                         FROM orders o 
                         WHERE MONTH(o.order_date) = MONTH(CURRENT_DATE())";
$resultOrdersThisMonth = $conn->query($ordersThisMonthQuery);
$ordersThisMonthData = $resultOrdersThisMonth->fetch_assoc();
$ordersThisMonth = $ordersThisMonthData['orders_this_month'];

// Fetch active users (users who have placed at least one order)
$activeUsersQuery = "SELECT COUNT(DISTINCT r.registration_id) AS active_users 
                     FROM registration r 
                     JOIN orders o ON r.registration_id = o.registration_id";
$resultActiveUsers = $conn->query($activeUsersQuery);
$activeUsersData = $resultActiveUsers->fetch_assoc();
$activeUsers = $activeUsersData['active_users'];

// Fetch top-selling items
$topSellingQuery = "SELECT p.product_name, SUM(od.quantity) AS total_sales 
                    FROM order_details od 
                    JOIN products p ON od.product_id = p.product_id 
                    GROUP BY p.product_name 
                    ORDER BY total_sales DESC LIMIT 5";
$resultTopSelling = $conn->query($topSellingQuery);
$topSelling = $resultTopSelling->fetch_all(MYSQLI_ASSOC);

// Fetch top user (user with the most orders)
$topUserQuery = "SELECT r.name, COUNT(o.order_id) AS total_orders 
                 FROM orders o 
                 JOIN registration r ON o.registration_id = r.registration_id 
                 GROUP BY r.registration_id 
                 ORDER BY total_orders DESC LIMIT 1";
$resultTopUser = $conn->query($topUserQuery);
$topUserData = $resultTopUser->fetch_assoc();
$topUserName = $topUserData['name'];
$topUserOrders = $topUserData['total_orders'];

// Fetch leaderboard for top users (users with the most orders)
$topUsersLeaderboardQuery = "SELECT r.name, COUNT(o.order_id) AS total_orders 
                             FROM orders o 
                             JOIN registration r ON o.registration_id = r.registration_id 
                             GROUP BY r.registration_id 
                             ORDER BY total_orders DESC LIMIT 10";
$resultTopUsersLeaderboard = $conn->query($topUsersLeaderboardQuery);
$topUsersLeaderboard = $resultTopUsersLeaderboard->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Reports</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .navbar {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      background-color: #FFE1E1;
      z-index: 1000;
      height: 60px;
    }

    .navbar a {
      padding: 15px;
      text-decoration: none;
      color: #333;
    }

    .main-content {
      margin-top: 60px;
      padding: 20px;
    }

    .dashboard-grid {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr 1fr;
      gap: 20px;
      margin-bottom: 30px;
    }

    .dashboard-card {
      background-color: #FFE1E1;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .dashboard-card h3 {
      margin-bottom: 10px;
    }

    .dashboard-card h2 {
      font-size: 1.5rem;
      color: #333;
    }

    .dashboard-card .btn {
      background-color: #FFACAC;
      padding: 10px;
      text-align: center;
      color: white;
      text-decoration: none;
      border-radius: 4px;
      transition: background-color 0.3s;
    }

    .dashboard-card .btn:hover {
      background-color: #F57373;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 8px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #F7D9D9;
    }
  </style>
</head>
<body>

<!-- NAVIGATION BAR -->
<div class="navbar">
  <a href="admin_homepage.php">Dashboard</a>
  <a href="manage_users.php">Users</a>
  <a href="manage_products.php">Products</a>
  <a href="manage_orders.php">Orders</a>
  <a href="reports.php">Reports</a>
  <a href="logout.php">Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
  <h1>Reports Overview</h1>
  
  <div class="dashboard-grid">
    <!-- Total Sales -->
    <div class="dashboard-card">
      <h3>Total Sales</h3>
      <h2>Php <?php echo number_format($totalSales, 2); ?></h2>
    </div>

    <!-- Orders This Month -->
    <div class="dashboard-card">
      <h3>Orders This Month</h3>
      <h2><?php echo $ordersThisMonth; ?></h2>
    </div>

    <!-- Active Users -->
    <div class="dashboard-card">
      <h3>Active Users</h3>
      <h2><?php echo $activeUsers; ?></h2>
    </div>

    <!-- Top User -->
    <div class="dashboard-card">
      <h3>Top User</h3>
      <h2><?php echo htmlspecialchars($topUserName); ?> (<?php echo $topUserOrders; ?> Orders)</h2>
    </div>
  </div>

  <!-- Top Selling Items Section -->
  <h2>Top Selling Items</h2>
  <div class="dashboard-card">
    <table>
      <thead>
        <tr>
          <th>Product Name</th>
          <th>Sold Quantity</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($topSelling as $item): ?>
          <tr>
            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
            <td><?php echo $item['total_sales']; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Top Users Leaderboard -->
  <h2>Leaderboard - Top Users</h2>
  <div class="dashboard-card">
    <table>
      <thead>
        <tr>
          <th>User Name</th>
          <th>Total Orders</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($topUsersLeaderboard as $user): ?>
          <tr>
            <td><?php echo htmlspecialchars($user['name']); ?></td>
            <td><?php echo $user['total_orders']; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
