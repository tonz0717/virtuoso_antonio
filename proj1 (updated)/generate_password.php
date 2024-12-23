<?php
// Replace 'testpassword' with the desired plain-text password
$new_password = 'testpassword';

// Generate a hashed version of the password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Display the hashed password
echo $hashed_password;
?>
