<!-- header.php -->
<header>
  <div class="navbar">
    <a href="admin_homepage.php" class="logo">Bon sweets</a>
    <span>Admin Dashboard</span>
    <div class="navbar-links">
      <a href="admin_homepage.php">Dashboard</a>
      <a href="manage_users.php">Users</a>
      <a href="manage_products.php">Products</a>
      <a href="manage_orders.php">Orders</a>
      <a href="reports.php">Reports</a>
      <a href="logout.php">Logout</a>
    </div>
  </div>
</header>

<!-- No more <main> content here as requested -->
<style>
  /* Overall container layout */
  .container {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    background-color: #f4f4f9;
    margin-top: 60px; /* Offset content to avoid navbar */
  }

  /* Navbar Styling */
  header .navbar {
    background-color: #FFE1E1;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 1.2rem;
    font-weight: bold;
    color: #333;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: fixed;
    top: 0;
    left: 0; /* Remove the left margin */
    width: 100%;
    z-index: 100;
  }

  .navbar .logo {
    font-size: 1.6rem;
    font-weight: bold;
    color: #FF6B6B;
    text-decoration: none;
  }

  .navbar-links {
    display: flex;
    gap: 20px;
  }

  .navbar-links a {
    font-family: "Ubuntu";
    font-size: 1rem;
    font-weight: 700;
    color: #793337;
    text-decoration: none;
    padding: 12px;
    border-radius: 4px;
    text-align: center;
    transition: background-color 0.3s, color 0.3s;
  }

  .navbar-links a:hover {
    background-color: #FF4747;
    color: white;
  }

  /* Responsive Design for Mobile */
  @media (max-width: 768px) {
    .navbar-links {
      flex-direction: column;
      align-items: center;
    }

    .navbar-links a {
      padding: 10px;
      margin: 5px 0;
    }

    .container {
      flex-direction: column;
      margin-top: 80px; /* Adjust for top-fixed navbar */
    }
  }
</style>
