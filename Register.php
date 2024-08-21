<?php
// Database connection parameters
$host = 'localhost';
$db = 'register';
$user = 'postgres';
$password = 'postgres';

$error_message = '';

try {
    // Connect to the database using PDO
    $dsn = "pgsql:host=$host;dbname=$db";
    $pdo = new PDO($dsn, $user, $password);

    // Set PDO to throw exceptions on error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data and sanitize
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validate form data
        if (!empty($username) && !empty($email) && !empty($password) && !empty($confirm_password)) {
            if ($password === $confirm_password) {
                // Check if user already exists
                $query_check_user = "SELECT * FROM users WHERE username = :username OR email = :email LIMIT 1";
                $stmt_check_user = $pdo->prepare($query_check_user);
                $stmt_check_user->execute(['username' => $username, 'email' => $email]);
                $existing_user = $stmt_check_user->fetch(PDO::FETCH_ASSOC);

                if (!$existing_user) {
                    // Hash the password securely
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Insert user data into the database
                    $query_insert_user = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
                    $stmt_insert_user = $pdo->prepare($query_insert_user);
                    $stmt_insert_user->execute(['username' => $username, 'email' => $email, 'password' => $hashed_password]);

                    // Redirect to login page after successful registration
                    header("Location: login.php");
                    exit();
                } else {
                    $error_message = "User already exists.";
                }
            } else {
                $error_message = "Passwords do not match.";
            }
        } else {
            $error_message = "Please fill in all fields.";
        }
    }
} catch (PDOException $e) {
    $error_message = "Error: " . $e->getMessage();
}

// Close the database connection
$pdo = null;
?>

<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <title>Register</title>
</head>
<body>
    <?php if (!empty($error_message)) { ?>
        <script>
            alert('<?php echo htmlspecialchars($error_message); ?>');
            window.location.href = 'register.html';
        </script>
    <?php } ?>
</body>
</html>
