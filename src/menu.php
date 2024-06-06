<?php
session_start();
if (!isset($_SESSION["logged_in_user"]) || !isset($_SESSION["user_id"])) {
    header("Location: index.php"); 
    exit();
}

$loggedInUser = $_SESSION["logged_in_user"];
$userId = $_SESSION["user_id"];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Menu Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        body, html {
            height: 100%;
            margin: 0;
        }
        .sidebar {
            height: 100%; /* Full-height: remove this if you want "auto" height */
            width: 250px; /* Set the width of the sidebar */
            position: fixed; /* Fixed Sidebar (stay in place on scroll) */
            z-index: 1; /* Stay on top */
            top: 0; /* Stay at the top */
            left: 0;
            background-color: #111; /* Black */
            overflow-x: hidden; /* Disable horizontal scroll */
            padding-top: 20px;
        }
        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            color: #818181;
            display: block;
        }
        .sidebar a:hover {
            color: #f1f1f1;
        }
        .main {
            margin-left: 260px; /* Same as the width of the sidebar */
            padding: 0px 10px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="generate_shopping_list.php">Skapa inköpslista</a>
    <a href="modify_db.php">Modifiera databasen</a>
    <a href="confirm_purchases.php">Bekräfta Inköp</a>
    <a href="logout.php" class="btn btn-danger mt-3">Logga ut</a> <!-- Logout button -->
</div>

<div class="main">
    <h2>Välkommen till din inköpslista generator</h2>
    <p>1. Skapa Inköpslista, välj "skapa inköpslista" i menyn till vänster för att se rekomenderade produkter utifrån dina tidigare köpmönster samt att lägga till nya<br>
    2. Modifiera databasen, välj "modifiera databasen" i menyn till vänster för att lägga till / ta bort Hushållsvaror från databasen <br>  
    3. Bekräfta Inköp, välj "bekräfta inköp" i menyn till vänster för att bekräfta din inköpslista, du kan även göra spontaninköp här.</p>
</div>

</body>
</html>



