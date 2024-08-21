<?php
// Database connection parameters
$host = 'localhost';
$db = 'login';
$user = 'username';
$password = 'password';

// Create connection string
$conn_string = "host=$host dbname=$db user=$user password=$password";

// Connect to the database
$dbconn = pg_connect($conn_string);

if (!$dbconn) {
    echo "Error: Unable to connect to the database.";
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate form data
    if (!empty($email) && !empty($password)) {
        // Query to check if the user exists
        $query = "SELECT * FROM users WHERE email = $1 AND password = $2";
        $result = pg_query_params($dbconn, $query, array($email, $password));

        // Check if the user exists
        if (pg_num_rows($result) > 0) {
            // User exists, redirect to explore page
            header("Location: explore_page.html");
            exit();
        } else {
            // User does not exist, show error message
            echo "Invalid email or password";
        }
    } else {
        echo "Please fill in all fields";
    }
}

// Close the database connection
pg_close($dbconn);
?>
