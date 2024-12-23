<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Commerce</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible&display=swap" rel="stylesheet">
    <!-- Custom CSS -->


    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&family=Righteous&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap');

        body {
            
            background-image: url(images/bg3.jpg);
            background-size: cover;
            background-position: center;
            color: #fff;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .card-header {
            background-color: #FF6B6B;
            color: #fff;
            text-align: center;
            padding: 15px;
            border-radius: 12px 12px 0 0;
            font-family;'Ubuntu';
            font-size: 1.6rem;
            font-weight: bold;
        }


        .card {
            border: none;
            font-family: 'Atkinson Hyperlegible', sans-serif;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            background-color: #FFF;
            padding: 20px;
            width: 100%;
            margin:auto;
            max-width: 400px;
        }

        
        .form-label {
            font-weight: 600;
            color: #333;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #FF6B6B;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 12px;
        }

        .btn-primary {
            background-color: #FF6B6B;
            border-color: #FF6B6B;
            font-weight: bold;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #FF4747;
            border-color: #FF4747;
        }

        .alert-danger {
            font-size: 0.9rem;
            display: none;
            margin-top: 10px;
            padding: 10px;
            background-color: #f8d7da;
            color: #721c24;
            border-radius: 5px;
        }

        .login-options {
            text-align: center;
            margin-top: 20px;
        }

        .login-options a {
            color: #FF6B6B;
            font-weight: bold;
            text-decoration: none;
            margin: 0 10px;
        }

        .login-options a:hover {
            color: #FF4747;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h3>Login</h3>
                    </div>
                    <div class="card-body">
                        <!-- Login Form -->
                        <form id="loginForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                            <div id="error-message" class="alert-danger">
                                Invalid username or password.
                            </div>
                        </form>
                    </div>
                    <!-- Login options (Register, Forgot Password) -->
                    <div class="login-options">
                        <p>
                            <a href="register.php">Register an Account</a> | 
                            <a href="forgot_password.php">Forgot Password?</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional for dropdowns, modals, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle form submission
            $('#loginForm').submit(function(event) {
                event.preventDefault();

                var email = $('#email').val();
                var password = $('#password').val();

                // Send login data via AJAX
                $.ajax({
                    url: 'processlogin.php', // Server-side PHP script
                    type: 'POST',
                    data: {
                        email: email,
                        password: password
                    },
                    success: function(response) {
                        if (response.trim() === 'success') {
                            setTimeout(function() {
                                window.location.href = 'index.php'; // Redirect after a brief delay
                            }, 500); // Delay of 500ms
                        } else {
                            $('#error-message').show().text('Invalid username or password.');
                        }
                    },
                    error: function() {
                        $('#error-message').show().text('An error occurred. Please try again.');
                    }
                });
            });
        });
    </script>
</body>
</html>
