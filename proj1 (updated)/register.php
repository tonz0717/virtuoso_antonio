<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - E-Commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="register.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="register.js" defer></script> <!-- Include the new register.js -->
</head>
<body>

    <div class="container">
        <!-- Form Section -->
        <div class="form-container">
            <div class="card">
                <div class="card-header">
                    <h3>Register</h3>
                </div>
                <div class="card-body">
                    <form id="registrationForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Register</button>
                        </div>
                        <div id="error-message" class="alert-danger"></div>
                    </form>
                </div>
                <div class="login-options">
                    <p>
                        <a href="login.php">Already have an account? Login</a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div class="modal" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="successModalLabel">Registration Successful</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Your registration was successful. You will be redirected to the login page.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="redirectToLogin">OK</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#registrationForm').submit(function(event) {
                event.preventDefault();

                var name = $('#name').val();
                var address = $('#address').val();
                var phone_number = $('#phone_number').val();
                var email = $('#email').val();
                var password = $('#password').val();

                $.ajax({
                    url: 'register_user.php', 
                    type: 'POST',
                    data: {
                        name: name,
                        address: address,
                        phone_number: phone_number,
                        email: email,
                        password: password
                    },
                    success: function(response) {
                        if (response.trim() === 'success') {
                            // Show the success modal
                            $('#successModal').modal('show');
                        } else {
                            $('#error-message').show().text(response);
                        }
                    },
                    error: function() {
                        $('#error-message').show().text('An error occurred. Please try again.');
                    }
                });
            });

            // Redirect to login after clicking OK on the success modal
            $('#redirectToLogin').on('click', function() {
                window.location.href = 'login.php'; // Redirect to login page
            });
        });
    </script>

</body>
</html>
