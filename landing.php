<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .welcome-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .btn {
            background: rgb(22, 230, 230);
            border: none;
        }
        .btn:hover{
            background: rgb(0, 185, 185);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="welcome-container">
            <h2>Welcome, <?php echo htmlspecialchars($user); ?>!</h2>
            <p>You have successfully logged in.</p>
            <form action="logout.php" method="POST">
                <button type="submit" class="btn btn-primary">Logout</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
