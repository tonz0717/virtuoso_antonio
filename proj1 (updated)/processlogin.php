<?php
ob_start();
session_start(); // Start the session to store user information

header("Content-Type: text/plain");
require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Debug: Log input password
        error_log("Input email: " . $email);
        error_log("Input password: " . $password);

        // Query to fetch the hashed password from the database
        $login_sql = "SELECT login_id, password FROM login_credentials WHERE email = ?";
        if ($stmt = mysqli_prepare($conn, $login_sql)) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            if ($result) {
                if ($row = mysqli_fetch_assoc($result)) {
                    $hashed_password = $row['password'];
                    error_log("Stored hashed password: " . $hashed_password);

                    // Verify the entered password against the hashed password
                    if (password_verify($password, $hashed_password)) {
                        $_SESSION['user'] = $email;
                        echo "success";
                        exit;
                    } else {
                        error_log("Password did not match.");
                        echo "Error: Incorrect password.";
                    }
                } else {
                    error_log("No user found with email: $email");
                    echo "Error: Email not found.";
                }
            } else {
                error_log("Error fetching result: " . mysqli_error($conn));
                echo "Error: Could not fetch result.";
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log("Error preparing statement: " . mysqli_error($conn));
            echo "Error: Could not prepare login query.";
        }
    } else {
        echo "Error: Email or password cannot be empty.";
    }

    mysqli_close($conn);
}
ob_end_flush();
