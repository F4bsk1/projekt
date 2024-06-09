<?php
session_start();
include 'functions.php'; // Ensure this path is correct

if (!isset($_SESSION["logged_in_user"]) || !isset($_SESSION["user_id"])) {
    header("Location: index.php"); // Redirect to login if not logged in
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['log_out'])) {
    header("Location: logout.php");
    exit();
}

//unset($_SESSION['shopping_list']);
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Tack för ditt köp</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { padding: 20px; background-color: #f4f4f4; }
        .container { text-align: center; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tack för ditt köp!</h1>
        <p>Ditt köp har registreras i databasen.</p>
        <form action="feedback.php" method="post">
            <button type="submit" name="log_out" class="btn btn-danger">Logga ut</button>
        
        </form>
    </div>
</body>
</html>