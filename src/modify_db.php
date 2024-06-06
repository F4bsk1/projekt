<?php
session_start();
include 'functions.php';
include 'logoutheader.php';

if (!isset($_SESSION["logged_in_user"]) || !isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

$db = new SQLite3(__DIR__ . '/../database/account_items.db');

// Handle form submission for adding items
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_item'])) {
    $itemName = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    addItem($itemName, $quantity, $db);
    header("Location: modify_db.php");
    exit();
}

// Handle form submission for deleting items
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_item'])) {
    $itemId = $_POST['item_id'];
    deleteItem($itemId, $db);
    header("Location: modify_db.php");
    exit();
}

$allItems = getAllItems($db);
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Modifiera Databasen</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
            background-color: #f4f4f4;
        }
        .form-box, .list-box {
            border: 1px solid #ccc;
            padding: 20px;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Lägg till eller Ta bort Hushållsvaror</h2>
        <div class="form-box">
            <h3>Lägg till ny vara</h3>
            <form action="modify_db.php" method="post">
                <div class="form-group">
                    <label for="item_name">Varunamn:</label>
                    <input type="text" name="item_name" id="item_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="quantity">Antal:</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" required>
                </div>
                <button type="submit" name="add_item" class="btn btn-primary">Lägg till</button>
            </form>
        </div>
        <div class="list-box">
            <h3>Nuvarande varor</h3>
            <ul class="list-group">
                <?php foreach ($allItems as $item): ?>
                    <li class="list-group-item">
                        <?= htmlspecialchars($item['ItemName']) ?>
                        <form action="modify_db.php" method="post" style="display:inline;">
                            <input type="hidden" name="item_id" value="<?= $item['ItemID'] ?>">
                            <button type="submit" name="delete_item" class="btn btn-danger btn-sm float-right">Ta bort</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</body>
</html>
