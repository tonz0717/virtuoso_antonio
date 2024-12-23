<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="container">
    <h2>Forgot Password</h2>
    <form id="forgotPasswordForm">
        <input type="email" id="email" name="email" placeholder="Enter your email" required>
        <button type="submit">Submit</button>
    </form>
    <div id="message"></div>
</div>

<script>
    $(document).ready(function() {
        // Handle form submission
        $('#forgotPasswordForm').submit(function(event) {
            event.preventDefault();
            var email = $('#email').val();

            $.ajax({
                url: 'reset_password.php', // PHP script to handle password reset
                type: 'POST',
                data: {
                    email: email
                },
                success: function(response) {
                    $('#message').text(response).css('color', 'green');
                },
                error: function() {
                    $('#message').text('An error occurred. Please try again.').css('color', 'red');
                }
            });
        });
    });
</script>

</body>
</html>
