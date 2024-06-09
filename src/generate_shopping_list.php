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

$recommendations = getRecommendedItems($userId, $db);
$shoppingList = getUserShoppingList($userId, $db);
$allItems = getAllItems($db);


// Handle form submission for adding items
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_item'])) {
    $itemId = $_POST['item_id'];
    $quantity = $_POST['quantity'] ?? 1;
    addItemToList($userId, $itemId, $db, $quantity);
    header("Location: generate_shopping_list.php");
    exit();
}

// Handle form submission for deleting items
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_item'])) {
    $itemId = $_POST['item_id'];
    deleteItemFromList($userId, $itemId, $db);
    header("Location: generate_shopping_list.php");
    exit();
}

// Handle form submission for permanently deleting items
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_item_permanently'])) {
    $itemId = $_POST['item_id'];
    deleteItemPermanently($itemId, $db);
    header("Location: generate_shopping_list.php");
    exit();
}

//SKIT I DENNA OM DB updateras direkt (troligtvis) men dock reroute vid klick på spara
// Handle form submission for finalizing the list
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['finalize_list'])) {
    // Add recommended items to UserShoppingList
    foreach ($recommendations as $item) {
        addItemToList($userId, $item['ItemID'], $db, 1); // Default quantity to 1 for recommendations
    }
    header("Location: confirm_purchases.php");
    exit();
}
//ta bort forech för rekomenderade items
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Generera Inköpslista</title>
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
        <h2>Rekommenderad Inköpslista</h2>
        <div class="list-box">
            <ul class="list-group">
                <?php foreach ($recommendations as $item): ?>
                    <li class="list-group-item">
                        <?= htmlspecialchars($item['ItemName']) ?> (rekommenderad)
                        <form action="generate_shopping_list.php" method="post" style="display:inline;">
                            <input type="hidden" name="item_id" value="<?= $item['ItemID'] ?>">
                            <button type="submit" name="delete_item" class="btn btn-danger btn-sm delete-button">Ta bort</button>
                        </form>
                    </li>
                <?php endforeach; ?>
                <?php foreach ($shoppingList as $item): ?>
                    <li class="list-group-item">
                        <?= htmlspecialchars($item['ItemName']) ?> (<?= htmlspecialchars($item['Quantity']) ?> st)
                        <form action="generate_shopping_list.php" method="post" style="display:inline;">
                            <input type="hidden" name="item_id" value="<?= $item['ItemID'] ?>">
                            <button type="submit" name="delete_item" class="btn btn-danger btn-sm delete-button">Ta bort</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="dropdown-box">
            <form action="generate_shopping_list.php" method="post">
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
                <button type="submit" name="finalize_list" class="btn btn-success">Spara och fortsätt</button>
            </form>
            <form action="generate_shopping_list.php" method="post" style="margin-top: 20px;">
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
        </div>
    </div>
</body>
</html>

