<?php
// Start output buffering
ob_start();

// Set content type to plain text for AJAX response
header("Content-Type: text/plain");

// Include the database connection file
require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect the form data
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email already exists in the login_credentials table
    $check_email_sql = "SELECT email FROM login_credentials WHERE email = ?";
    if ($check_stmt = mysqli_prepare($conn, $check_email_sql)) {
        mysqli_stmt_bind_param($check_stmt, "s", $email);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            // Email already exists
            echo "Error: Email already exists. Please use a different email.";
        } else {
            // Insert data into the registration table
            $registration_sql = "INSERT INTO registration (name, address, phone_number) VALUES (?, ?, ?)";

            if ($stmt = mysqli_prepare($conn, $registration_sql)) {
                mysqli_stmt_bind_param($stmt, "sss", $name, $address, $phone_number);
                if (mysqli_stmt_execute($stmt)) {
                    $registration_id = mysqli_insert_id($conn);
                    // Insert into login_credentials table
                    $login_sql = "INSERT INTO login_credentials (registration_id, email, password) VALUES (?, ?, ?)";
                    if ($login_stmt = mysqli_prepare($conn, $login_sql)) {
                        mysqli_stmt_bind_param($login_stmt, "iss", $registration_id, $email, $hashed_password);
                        if (mysqli_stmt_execute($login_stmt)) {
                            echo trim("success");
                        } else {
                            error_log("Error inserting login credentials: " . mysqli_error($conn));
                            echo "An error occurred during registration. Please try again.";
                        }
                    } else {
                        error_log("Error preparing login credentials query: " . mysqli_error($conn));
                        echo "An error occurred during registration. Please try again.";
                    }
                } else {
                    error_log("Error inserting registration data: " . mysqli_error($conn));
                    echo "An error occurred during registration. Please try again.";
                }
            } else {
                error_log("Error preparing registration query: " . mysqli_error($conn));
                echo "An error occurred during registration. Please try again.";
            }
        }
    } else {
        error_log("Error checking email: " . mysqli_error($conn));
        echo "An error occurred during registration. Please try again.";
    }

    // Close the database connection
    mysqli_close($conn);
}

// End and clean output buffer
ob_end_flush();
?>
