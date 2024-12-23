document.querySelectorAll('.view-details-btn').forEach(button => {
    button.addEventListener('click', function () {
        const product = this.closest('.product-item');
        const productId = product.getAttribute('data-product-id');
        const productName = product.getAttribute('data-product-name');
        const description = product.getAttribute('data-description');
        const imageUrl = product.getAttribute('data-image-url');
        const variants = JSON.parse(product.getAttribute('data-variants'));

        // Populate modal content
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

        // Automatically set the hidden input to the first variant's value
        if (variants.length > 0) {
            const firstVariant = variants[0];
            document.getElementById('variant').value = firstVariant.variant_id;
        }

        document.getElementById('modalVariants').addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('variant').value = selectedOption.value;
            updateTotalPrice(); // Ensure total price updates with variant change
        });

        // Reset quantity and calculate initial total price
        document.getElementById('quantity').value = 1; // Default to 1
        updateTotalPrice(); // Calculate total price for the default quantity

        // Set product ID in hidden form input
        document.getElementById('productId').value = productId;

        // Display the modal
        document.getElementById('productModal').style.display = 'block';
    });
});

// Close modal
document.getElementById('closeModal').addEventListener('click', function () {
    document.getElementById('productModal').style.display = 'none';
});

// Update total price dynamically based on selected variant and quantity
document.getElementById('modalVariants').addEventListener('change', updateTotalPrice);
document.getElementById('quantity').addEventListener('input', function () {
    const quantity = parseInt(this.value) || 1; // Ensure a valid number
    this.value = Math.max(quantity, 1); // Prevent quantity less than 1
    updateTotalPrice(); // Recalculate total price
});

function updateTotalPrice() {
    const variantSelect = document.getElementById('modalVariants');
    const selectedOption = variantSelect.options[variantSelect.selectedIndex];
    const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
    const quantity = parseInt(document.getElementById('quantity').value) || 1;
    const totalPrice = price * quantity;

    // Update displayed total price
    document.getElementById('totalPrice').textContent = totalPrice.toFixed(2);

    // Update hidden quantity input (if required for form submission)
    document.getElementById('quantityInput').value = quantity;
}

// Open modal and load cart items
document.getElementById('viewCartButton').addEventListener('click', function () {
    loadCartItems();
    document.getElementById('cartModal').style.display = 'block';
});

// Close cart modal
document.getElementById('closeCartModal').addEventListener('click', function () {
    document.getElementById('cartModal').style.display = 'none';
});

// Optional: Close modal on outside click
window.addEventListener('click', function (event) {
    const modal = document.getElementById('cartModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});

// Function to load cart items
function loadCartItems() {
    $.ajax({
        url: 'fetch_cart.php',
        method: 'GET',
        success: function (data) {
            if (data.success) {
                const cartTableBody = $('#cartTable tbody');
                cartTableBody.empty(); // Clear table before appending new rows

                // Populate cart items
                data.data.forEach(item => {
                    const price = parseFloat(item.price).toFixed(2);
                    const quantity = parseInt(item.quantity, 10);
                    const totalPrice = (price * quantity).toFixed(2);

                    cartTableBody.append(`
                        <tr data-cart-id="${item.cart_id}">
                            <td>${item.product_name}</td>
                            <td>${item.variant_name}</td>
                            <td>
                                <button class="decrease-btn">-</button>
                                <span class="quantity">${quantity}</span>
                                <button class="increase-btn">+</button>
                            </td>
                            <td class="price" data-price="${price}">$${price}</td>
                            <td class="total">$${totalPrice}</td>
                            <td>
                                <button class="remove-btn">Remove</button>
                            </td>
                        </tr>
                    `);
                });

                // Attach listeners to buttons
                attachCartActionListeners();
            } else {
                alert(data.message); // Show error message if fetch fails
            }
        },
        error: function (xhr, status, error) {
            console.error(`AJAX Error: ${error}`);
            alert('Error fetching cart items.');
        }
    });
}

// Attach listeners for increase, decrease, and remove actions
document.querySelectorAll('.increase-btn').forEach(button => {
    button.addEventListener('click', function () {
        // Log the button that was clicked
        console.log('Increase button clicked:', this);

        const row = this.closest('tr'); // Find the parent row
        const cartId = row.getAttribute('data-cart-id'); // Get the cart ID
        const quantitySpan = row.querySelector('.quantity'); // Get the quantity span
        let quantity = parseInt(quantitySpan.textContent); // Get current quantity

        // Increment quantity
        quantity++;
        quantitySpan.textContent = quantity; // Update the quantity display
        console.log(`Cart ID: ${cartId}, New Quantity: ${quantity}`); // Log cart ID and new quantity

        // Update row total and server
        updateRowTotal($(row), quantity);
        updateCartQuantity(cartId, quantity);
    });
});


    document.querySelectorAll('.decrease-btn').forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            const cartId = row.getAttribute('data-cart-id');
            const quantitySpan = row.querySelector('.quantity');
            let quantity = parseInt(quantitySpan.textContent);
            if (quantity > 1) {
                quantity--;
                quantitySpan.textContent = quantity;
                updateRowTotal($(row), quantity);
                updateCartQuantity(cartId, quantity);
            }
        });
    });

    document.querySelectorAll('.remove-btn').forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            const cartId = row.getAttribute('data-cart-id');
            row.remove(); // Remove row visually
            removeCartItem(cartId);
        });
    });


// Function to update row total
function updateRowTotal(row, quantity) {
    const price = parseFloat(row.find('.price').data('price'));
    const total = (price * quantity).toFixed(2);
    row.find('.total').text(`$${total}`);
}

// Update cart quantity on the server
function updateCartQuantity(cartId, quantity) {
    console.log(`Updating Cart ID: ${cartId}, New Quantity: ${quantity}`); // Debugging

    // Properly enable and modify the quantity field
    const quantityField = document.getElementById('quantity');
    if (quantityField) {
        quantityField.disabled = false; // Plain JavaScript equivalent of `.prop('disabled', false)`
        quantityField.readOnly = false; // Plain JavaScript equivalent of `.prop('readonly', false)`
    }

    // Send the updated cart quantity to the server
    $.ajax({
        url: 'update_cart.php',
        method: 'POST',
        data: { cart_id: cartId, quantity: quantity },
        success: function (data) {
            console.log('Server Response:', data); // Check server response
            if (!data.success) {
                alert('Error updating cart quantity: ' + data.message);
            }
        },
        error: function (xhr, status, error) {
            console.error(`AJAX Error: ${error}`);
            alert('An error occurred while updating the cart.');
        }
    });
}


// Remove cart item on the server
function removeCartItem(cartId) {
    $.ajax({
        url: 'remove_cart.php',
        method: 'POST',
        data: { cart_id: cartId },
        success: function (data) {
            if (data.success) {
                alert(data.message); // Optional: Display success message
            } else {
                console.error(data.message); // Log failure message
            }
        },
        error: function (xhr, status, error) {
            console.error(`AJAX Error: ${error}`);
        }
    });
}

// Add event listener to the "Proceed to Checkout" button
document.getElementById('proceedToCheckoutButton').addEventListener('click', function () {
    window.location.href = 'checkout.php';
});

// Example fetch request to the PHP endpoint
fetch('fetch_cart.php', {
    method: 'GET', // or 'POST' depending on your implementation
    headers: {
        'Content-Type': 'application/json'
    }
})
    .then(response => response.json()) // Parse the JSON response
    .then(data => {
        // Log the JSON response to the console
        console.log('JSON Response:', data);

        // Optional: Handle the data
        if (data.success) {
            console.log('Cart Data:', data.data);
        } else {
            console.error('Error:', data.message);
        }
    })
    .catch(error => console.error('Fetch Error:', error));
