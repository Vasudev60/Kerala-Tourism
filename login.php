<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: explore_page.html");
    exit();
}

// Database connection parameters
$host = 'localhost';
$db = 'register';
$user = 'postgres';
$password = 'postgres';

try {
    // Connect to the database
    $dsn = "pgsql:host=$host;dbname=$db";
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("Error: Unable to connect to the database. " . $e->getMessage());
}

// Initialize the error message variable
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Validate form data
    if (!empty($email) && !empty($password)) {
        try {
            // Check if user exists
            $query_check_user = "SELECT * FROM users WHERE email = :email LIMIT 1";
            $stmt = $pdo->prepare($query_check_user);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            $user_row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user_row) {
                // Verify password
                if (password_verify($password, $user_row['password'])) {
                    // Password is correct, set session variables
                    $_SESSION['user_id'] = $user_row['user_id'];
                    $_SESSION['username'] = $user_row['username'];
                    
                    // Redirect to explore page after successful login
                    header("Location: explore_page.html");
                    exit();
                } else {
                    // Password is incorrect
                    $error_message = "Invalid email or password.";
                }
            } else {
                // User does not exist
                $error_message = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } else {
        // Form fields are empty
        $error_message = "Please fill in all fields.";
    }
}

// Close the database connection
$pdo = null;
?>

<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <title>Kerala Tourism Login</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
        }
        body {
            background-color: black;
            font-family: "Poppins", sans-serif;
        }
        .login {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login__bg {
            position: absolute;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .login__form {
            background-color: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.7);
            padding: 60.8px 16px;
            color:#fff;
            border-radius: 16px;
            backdrop-filter: blur(16px);
            width: 420px;
        }
        .login__title {
            text-align: center;
            font-size: 32px;
            margin-bottom: 20px;
        }
        .input__box {
            border: 2px solid rgba(255, 255, 255, 0.7);
            border-radius: 4rem;
            margin-bottom: 25.6px;
        }
        .login__input, .login__button {
            border: none;
            outline: none;
        }
        .login__input {
            color: white;
            background: none;
            padding: 16px;
            margin-left: 6px;
        }
        .login__input::placeholder {
            color: #fff;
        }
        .login__button {
            width: 100%;
            padding: 16px;
            margin-top: 16px;
            margin-bottom: 16px;
            background-color: #fff;
            border-radius: 64px;
            color: #000;
            cursor: pointer;
        }
        .login__register {
            font-size: 13px;
            text-align: center;
        }
        .login__register a {
            color: #fff;
            font-weight: 500;
        }
        .error {
            background-color: grey;
            color: white;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login">
        <video class="login__bg" autoplay muted loop>
            <source src="login.mp4" type="video/mp4">
        </video>
        <form action="login.php" method="post" class="login__form">
            <h1 class="login__title">Login</h1>
            <?php if (!empty($error_message)) { ?>
                <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php } ?>
            <div class="login__inputs">
                <div class="input__box">
                    <input type="email" name="email" placeholder="Email ID" required class="login__input">
                </div>
                <div class="input__box">
                    <input type="password" name="password" placeholder="Password" required class="login__input">
                </div>
            </div>
            <input type="submit" class="login__button" value="Login">
            <div class="login__register">
                Don't have an account? <a href="Register.html">Register</a>
            </div>
        </form>
    </div>
</body>
</html>
