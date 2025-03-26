<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cfd";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define restricted characters
$restrictedChars = ["<", ">", "'", "\"", "--", ";"];

// Function to detect restricted characters and return only those found
function detectRestrictedChars($input, $restrictedChars) {
    $found = array_filter($restrictedChars, function ($char) use ($input) {
        return strpos($input, $char) !== false;
    });
    return implode(" ", $found); // Return detected characters as a string
}

// Function to sanitize input by removing restricted characters
function sanitizeInput($input, $restrictedChars) {
    return str_replace($restrictedChars, "", trim($input));
}

$loginError = "";
$charError = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Detect restricted characters
    $invalidChars = detectRestrictedChars($user, $restrictedChars);
    
    if (!empty($invalidChars)) {
        $charError = "Invalid characters detected: " . htmlspecialchars($invalidChars);
    } else {
        $user = sanitizeInput($user, $restrictedChars);
        $pass = sanitizeInput($pass, $restrictedChars);

        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("SELECT username FROM users WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $user, $pass);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $_SESSION['username'] = $row['username']; 
            header("Location: landing.php");
            exit();
        } else {
            $loginError = "Invalid username or password!";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }
        .login-error {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 0, 0, 0.8);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            display: <?php echo empty($loginError) ? 'none' : 'block'; ?>;
        }
        .btn {
            background: rgb(22, 230, 230);
            border: none;
        }
        .btn:hover {
            background: rgb(0, 185, 185);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-error"><?php echo $loginError; ?></div> <!-- Invalid Credentials Message -->
            <h3 class="text-center">Login</h3>
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Username:</label>
                    <input type="text" name="username" class="form-control" required>
                    <?php if (!empty($charError)) : ?>
                        <div class="error-message"><?php echo $charError; ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
