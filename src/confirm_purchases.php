<?php
session_start();

include 'functions.php';
include 'logoutheader.php';

if (!isset($_SESSION["logged_in_user"]) || !isset($_SESSION["user_id"])) {
    header("Location: index.php"); // Redirect to login if not logged in
    exit();
}

$userId = $_SESSION['user_id'];
$db = new SQLite3(__DIR__ . '/../database/account_items.db');

// Fetch items in UserShoppingList
$shoppingList = getUserShoppingList($userId, $db);
$allItems = getAllItems($db);

// Handle form submission for adding items
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_item'])) {
    $itemId = $_POST['item_id'];
    $quantity = $_POST['quantity'] ?? 1;
    addItemToList($userId, $itemId, $db, $quantity);
    header("Location: confirm_purchases.php");
    exit();
}

// Handle form submission for adding new items permanently
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_new_item'])) {
    $itemName = $_POST['new_item_name'];
    $itemId = addNewItem($itemName, $db);
    $quantity = $_POST['new_item_quantity'] ?? 1;
    addItemToList($userId, $itemId, $db, $quantity);
    header("Location: confirm_purchases.php");
    exit();
}

// Handle form submission for deleting items permanently
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_item_permanently'])) {
    $itemId = $_POST['item_id'];
    deleteItemPermanently($itemId, $db);
    header("Location: confirm_purchases.php");
    exit();
}

// Handle form submission for finalizing the list
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['finalize_purchases'])) {
    foreach ($_POST['purchased_items'] as $itemId) {
        $quantity = $_POST['quantity_' . $itemId];
        updatePurchaseDate($userId, $itemId, $quantity, $db);
    }
    header("Location: feedback.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Bekräfta Inköpslista</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { padding: 20px; background-color: #f4f4f4; }
        .list-box { border: 1px solid #ccc; padding: 20px; margin-top: 20px; background-color: #fff; width: 300px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); position: relative; left: 50%; transform: translateX(-50%); }
        .dropdown-box { margin-top: 20px; }
        .delete-button { float: right; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bekräfta Inköpslista</h2>
        <form action="confirm_purchases.php" method="post">
            <div class="list-box">
                <ul class="list-group">
                    <?php foreach ($shoppingList as $item): ?>
                        <li class="list-group-item">
                            <input type="checkbox" name="purchased_items[]" value="<?= $item['ItemID'] ?>">
                            <?= htmlspecialchars($item['ItemName']) ?> (<?= htmlspecialchars($item['Quantity']) ?> st)
                            <input type="hidden" name="quantity_<?= $item['ItemID'] ?>" value="<?= $item['Quantity'] ?>">
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <button type="submit" name="finalize_purchases" class="btn btn-success">Bekräfta Inköp</button>
        </form>
        <div class="dropdown-box">
            <form action="confirm_purchases.php" method="post">
                <div class="form-group">
                    <label for="item_id">Lägg till vara:</label>
                    <select name="item_id" id="item_id" class="form-control">
                        <?php foreach ($allItems as $item): ?>
                            <option value="<?= $item['ItemID'] ?>"><?= htmlspecialchars($item['ItemName']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="quantity">Antal:</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1">
                </div>
                <button type="submit" name="add_item" class="btn btn-primary">Lägg till</button>
            </form>
            <form action="confirm_purchases.php" method="post" style="margin-top: 20px;">
                <div class="form-group">
                    <label for="delete_item_id">Ta bort vara permanent:</label>
                    <select name="item_id" id="delete_item_id" class="form-control">
                        <?php foreach ($allItems as $item): ?>
                            <option value="<?= $item['ItemID'] ?>"><?= htmlspecialchars($item['ItemName']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="delete_item_permanently" class="btn btn-danger">Ta bort permanent</button>
            </form>
            <form action="confirm_purchases.php" method="post" style="margin-top: 20px;">
                <div class="form-group">
                    <label for="new_item_name">Lägg till ny vara:</label>
                    <input type="text" name="new_item_name" id="new_item_name" class="form-control">
                    <label for="new_item_quantity">Antal:</label>
                    <input type="number" name="new_item_quantity" id="new_item_quantity" class="form-control" value="1" min="1">
                </div>
                <button type="submit" name="add_new_item" class="btn btn-primary">Lägg till ny vara</button>
            </form>
        </div>
    </div>
</body>
</html>