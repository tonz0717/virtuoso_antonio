<?php
session_start();
include('db.php');  // Ensure this includes your database connection file

// Check if the user is logged in
if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

$email = $_SESSION['user'];  // The logged-in user's email

// Get the user's registration_id from the login_credentials table
$sqlUser = "SELECT registration_id FROM login_credentials WHERE email = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param('s', $email);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();

if ($resultUser->num_rows > 0) {
    $user = $resultUser->fetch_assoc();
    $registration_id = $user['registration_id'];
    
    // Fetch all orders for this user from the orders table
    $sqlOrders = "SELECT order_id, total_price, status, order_date FROM orders WHERE registration_id = ?";
    $stmtOrders = $conn->prepare($sqlOrders);
    $stmtOrders->bind_param('i', $registration_id);
    $stmtOrders->execute();
    $resultOrders = $stmtOrders->get_result();
    
    // Fetch order details for each order
    $orders = [];
    while ($order = $resultOrders->fetch_assoc()) {
        $order_id = $order['order_id'];
        
        // Fetch products in the current order (no variant_id needed)
        $sqlOrderDetails = "SELECT od.product_id, od.price, p.product_name
                            FROM order_details od
                            JOIN products p ON od.product_id = p.product_id
                            WHERE od.order_id = ?";
        $stmtOrderDetails = $conn->prepare($sqlOrderDetails);
        $stmtOrderDetails->bind_param('i', $order_id);
        $stmtOrderDetails->execute();
        $resultOrderDetails = $stmtOrderDetails->get_result();

        // Check if products are being retrieved correctly
        $order['products'] = [];
        while ($product = $resultOrderDetails->fetch_assoc()) {
            $order['products'][] = $product;
        }

        // Add order and products to the orders array
        $orders[] = $order;
    }
} else {
    // Handle case where user is not found
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Order History</title>
    <link rel="stylesheet" href="style.css">  <!-- Include your styles if any -->
    <style>
        /* Adjust the navbar and ensure it stays fixed */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #FFE1E1; /* Your navbar color */
            z-index: 1000; /* Makes sure the navbar stays on top */
            height: 60px; /* Adjust based on your navbar height */
        }

        /* Adjust the content below the navbar */
        .main-content {
            margin-top: 60px; /* Pushes the content down to avoid being covered by navbar */
            padding: 20px;
        }

        /* Optional: Adding some spacing to the order history header */
        .order-history {
            padding-top: 20px; /* Adds extra padding to the order history section */
        }


        /* Reset default button styles */
button {
    all: unset; /* Remove all browser-specific styles */
    position: relative; /* Allow positioning of content and arrow */
    background-color:rgb(232, 166, 166); /* Background color */
    color: #444; /* Text color */
    padding: 10px 30px; /* Padding for the button */
    width: 100%; /* Ensure the button takes full width */
    border: 2px solid  #FFE1E1;; /* Border style */
    font-size: 16px; /* Set font size */
    cursor: pointer; /* Show pointer cursor */
    text-align: left; /* Align text to the left */
    transition: background-color 0.3s, transform 0.3s; /* Smooth transition on click */
}

button:hover {
    background-color: #FFE1E1; /* Change background on hover */
}

button.active {
    background-color: rgb(255, 173, 173); /* Change background when active */
    transform: scale(1.05); /* Slightly scale up the button */
}

/* Collapsible text positioning */
button .collapsible-text {
    position: absolute; /* Position the text absolutely */
    left: 10px; /* Align the text to the left */
    top: 50%;
    transform: translateY(-50%); /* Vertically center the text */
    width: calc(100% - 40px); /* Allow space for the arrow */
    white-space: nowrap; /* Prevent text wrapping */
    overflow: hidden; /* Hide overflow text */
    text-overflow: ellipsis; /* Show ellipsis if text is too long */
}

/* Arrow positioning and rotation */
button::after {
    content: ' ▼';
    font-size: 16px;
    position: absolute;
    right: 68px; /* Position the arrow on the right */
    top: 50%;
    transform: translateY(-50%); /* Vertically center the arrow */
    transition: transform 0.3s; /* Smooth arrow rotation */
}

button.active::after {
    transform: translateY(-50%) rotate(180deg); /* Rotate the arrow when active */
}

/* Collapsible content styling */
.content {
    padding: 10px 20px;
    display: none;
    background-color: #f9f9f9;
    transition: max-height 0.2s ease-out;
}

/* Table styling inside collapsible content */
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
    background-color:rgb(247, 217, 217);
}
    </style>
</head>
<body>

<!-- NAVIGATION BAR -->
<div class="navbar">
  <div>
    <a href="index.php">Home</a>

    <?php if (!isset($_SESSION['user'])): ?>
      <a href="Login.php">Login/Signup</a>
    <?php else: ?>
      <a href="logout.php">Logout</a>
    <?php endif; ?>

  </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <h1 class="order-history">Order History</h1>
    
    <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $order): ?>
            <div>
                <!-- Order Summary -->
                <button class="collapsible"><?= "Order ID: " . $order['order_id'] . " | Total Price: $" . $order['total_price'] . " | Date: " . $order['order_date'] . " | Status: " . $order['status'] ?></button>
                
                <!-- Collapsible Table for Order Products -->
                <div class="content">
                    <table>
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($order['products']) && !empty($order['products'])): ?>
                                <?php foreach ($order['products'] as $product): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($product['product_name']) ?></td>
                                        <td>$<?= htmlspecialchars($product['price']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2">No products found for this order.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>You have no orders yet.</p>
    <?php endif; ?>
</div>

<script>
    // JavaScript to handle collapsible functionality
    var coll = document.getElementsByClassName("collapsible");
    for (var i = 0; i < coll.length; i++) {
        coll[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var content = this.nextElementSibling;
            if (content.style.display === "block") {
                content.style.display = "none";
            } else {
                content.style.display = "block";
            }
        });
    }
</script>

</body>
</html>
