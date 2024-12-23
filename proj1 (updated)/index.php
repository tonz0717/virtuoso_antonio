<!DOCTYPE html>
<?php
session_start();

// Include the database connection
include('db.php');


// Fetch products from the database
$product_sql = "SELECT * FROM products";
$result = $conn->query($product_sql);
$products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $product_id = $row['product_id'];
        $product_name = $row['product_name'];
        $description = $row['description'];
        $image_url = $row['image_url']; // Assuming the 'image_url' is correct
    
        // If image_url is stored as a relative path, make sure it points to the right directory.
        // Example: If images are in 'images' folder, append 'images/' to the URL
        if (!preg_match("/^http/", $image_url)) {
            // If it's not an absolute URL, assume it's in the 'images' directory
            $image_url = "images/" . $image_url; 
        }

        // Fetch variants for each product
        $variant_sql = "SELECT * FROM product_variants WHERE product_id = $product_id";
        $variant_result = $conn->query($variant_sql);
        $variants = [];

        if ($variant_result->num_rows > 0) {
            while ($variant = $variant_result->fetch_assoc()) {
                $variants[] = $variant;
            }
        }

        $products[] = [
            'product_id' => $product_id,
            'product_name' => $product_name,
            'description' => $description,
            'image_url' => $image_url,
            'variants' => $variants
        ];
    }
}

$conn->close();
?>

/* BODY */

<html lang="en">
<head>
  <title>Bon Sweetz</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="path/to/script.js"></script>
  <script src="cart.js"></script>
  <link rel="stylesheet" href="style.css">

</head>
<body>

/* NAVIGATION */

<div class="navbar">
  <div>
    <a href="#home">Home</a>
    <a href="#product">Products</a>
    <a href="#cart" id="viewCartButton">View Cart</a>
    

    <?php if (!isset($_SESSION['user'])): ?>
      <a href="Login.php">Login/Signup</a>
    <?php else: ?>
      <a href="logout.php">Logout</a>
    <?php endif; ?>

  </div>
</div>



    <!-- TEXT OVERLAY-->

<div class="text-overlay">
            <h1 style="font-family: Alike; font-size: 80px; color: #97074f">bon sweetz</h1>
            <p style="font-family: Inter Tight; font-size: 20px"> C A K E S</p>
</div>

  </header>
  <div class="slider-frame">
    <div class="slide-images">
      

    </div>
  </div>

  <div class="bakery-title">
    <p>Elevating Baking to an Art Form</p>
  </div>


  <section class="product-section" id="product">
  <h1 style="font-family: Alike; font-size: 30px; color: #fff">Explore Our Creations</h1>
  <p style="font-family: Inter Tight; font-size: 20px; color: #fff">Welcome to our sophisticated and exclusive bakery, where the art of baking meets unparalleled luxury.</p>
  <div class="product-grid">
    <?php foreach ($products as $product): ?>
      <div class="product-item" 
     data-product-id="<?= $product['product_id']; ?>" 
     data-product-name="<?= htmlspecialchars($product['product_name']); ?>" 
     data-description="<?= htmlspecialchars($product['description']); ?>" 
     data-image-url="<?= $product['image_url']; ?>" 
     data-variants="<?= htmlspecialchars(json_encode($product['variants'])); ?>">
    <img src="<?= $product['image_url']; ?>" alt="<?= htmlspecialchars($product['product_name']); ?>">
    <h3><?= htmlspecialchars($product['product_name']); ?></h3>
    <button class="view-details-btn">Order Now!</button>
</div>
<?php endforeach; ?>
  </div>
</section>


<!-- Modal Structure -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <span id="closeModal" class="close-btn">&times;</span>
        
        <!-- Modal Image Section (Left side) -->
        <img id="modalImage" class="modal-image" src="" alt="Product Image">
        
        <!-- Modal Details Section (Right side) -->
        <div class="modal-details">
            <h2 id="modalProductName" class="modal-product-name"></h2>
            <p id="modalDescription" class="modal-description"></p>

            <label for="modalVariants">Variant:</label>
            <select id="modalVariants">
                <!-- Options will be dynamically added -->
            </select>

            <label for="quantity">Quantity:</label>
            <div class="quantity-selector">
              <button type="button" id="decreaseQuantity" class="quantity-btn">-</button>
              <input type="number" id="quantity" name="quantity" min="1" value="1" readonly>
              <button type="button" id="increaseQuantity" class="quantity-btn">+</button>
            </div>

            <div class="total-price-section">
                <strong>Total Price:</strong> $<span id="totalPrice">0.00</span>
            </div>

            <!-- Hidden Form for Add to Cart -->
            <form id="addToCartForm" action="add_to_cart.php" method="POST">
                <input type="hidden" name="productId" id="productId">
                <input type="hidden" name="variant" id="variant">
                <input type="hidden" name="quantity" id="quantityInput">
                <button type="submit" class="addtocart-btn">Add to Cart</button>
            </form>
        </div>
    </div>
</div>


<div id="cartModal" class="cartmodal">
    <div class="cartmodal-content">
        <span id="closeCartModal" class="close-btn">&times;</span>
        <h2>Your Cart</h2>
        <table id="cartTable">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Variant</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Dynamic cart items will be populated here -->
            </tbody>
        </table>
        <div class="cart-footer">
            <button id="proceedToCheckoutButton">Proceed to Checkout</button>
        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
document.querySelectorAll('.view-details-btn').forEach(button => {
    button.addEventListener('click', function () {
        const product = this.closest('.product-item');
        const productId = product.getAttribute('data-product-id');
        const productName = product.getAttribute('data-product-name');
        const description = product.getAttribute('data-description');
        const imageUrl = product.getAttribute('data-image-url');
        const variants = JSON.parse(product.getAttribute('data-variants'));

        // Update modal content
        document.getElementById('modalProductName').textContent = productName;
        document.getElementById('modalDescription').textContent = description;
        document.getElementById('modalImage').src = imageUrl;

        const variantSelect = document.getElementById('modalVariants');
        variantSelect.innerHTML = ''; // Clear existing options
        variants.forEach(variant => {
            const option = document.createElement('option');
            option.value = variant.variant_id;
            option.textContent = `${variant.variant_name} ($${variant.price})`;
            option.setAttribute('data-price', variant.price);
            variantSelect.appendChild(option);
        });

        // Show modal
        document.getElementById('productModal').setAttribute('data-product-id', productId);
        document.getElementById('productModal').style.display = 'block';
    });
});
</script>


<footer class="footer">
  </div>
  <p>&copy; 2024 Bakery Delight. All rights reserved.</p>
</footer>

<script src="script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('productModal');
    const quantityInput = document.getElementById('quantity');
    const totalPriceElement = document.getElementById('totalPrice');
    const variantSelect = document.getElementById('modalVariants');
    const increaseButton = document.getElementById('increaseQuantity');
    const decreaseButton = document.getElementById('decreaseQuantity');

    // Function to calculate and update the total price
    const updateTotalPrice = () => {
        const quantity = parseInt(quantityInput.value, 10);
        const selectedVariant = variantSelect.options[variantSelect.selectedIndex];
        const price = parseFloat(selectedVariant.getAttribute('data-price')) || 0;
        totalPriceElement.textContent = (price * quantity).toFixed(2);
    };

    // Event listener for variant selection change
    variantSelect.addEventListener('change', updateTotalPrice);

    // Event listeners for quantity buttons
    increaseButton.addEventListener('click', () => {
        const currentValue = parseInt(quantityInput.value, 10);
        quantityInput.value = currentValue + 1;
        updateTotalPrice();
    });

    decreaseButton.addEventListener('click', () => {
        const currentValue = parseInt(quantityInput.value, 10);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
            updateTotalPrice();
        }
    });

    // Initialize the total price when the modal is opened
    document.querySelectorAll('.view-details-btn').forEach(button => {
        button.addEventListener('click', () => {
            quantityInput.value = 1; // Reset quantity to 1
            updateTotalPrice(); // Update total price
        });
    });
});
</script>
</body>
</html>

