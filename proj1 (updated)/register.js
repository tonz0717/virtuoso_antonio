$(document).ready(function () {
    $('#registrationForm').submit(function (event) {
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
            success: function (response) {
                if (response.trim() === 'success') {
                    // Show an alert and redirect after clicking OK
                    alert('Registration Successful!');
                    window.location.href = 'login.php'; // Redirect to login page
                } else {
                    $('#error-message').show().text(response); // Display the error message
                }
            },
            error: function () {
                $('#error-message').show().text('An error occurred. Please try again.');
            }
        });
    });
});

