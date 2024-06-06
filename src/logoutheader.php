<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Din Inköpslista</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { padding: 20px; background-color: #f4f4f4; }
        .logout-button { position: absolute; top: 20px; right: 20px; }
    </style>
</head>
<body>
    <?php if (isset($_SESSION["logged_in_user"])): ?>
        <form action="logout.php" method="post" class="logout-button">
            <button type="submit" class="btn btn-danger">Logga ut</button>
        </form>
    <?php endif; ?>
    <div class="container">