<?php
//Här ska du lägga till flera funktioner
//inköpslistan måste clearas efter den är bekräftad.

function addUser($username, $password){
    //öppna databasen genom skapa instans och ange sökvägen
    $db = new SQLite3(__DIR__ . '/../database/account_items.db');
    //extra koll för connection till databasen
    if(!$db) {
        error_log("Connection failed: " . $db->lastErrorMsg());
        return false;
    }
    //hasha lösenordet med SHA3-512.
    $hashpassword = hash('sha3-512', $password);
    //förbered inserten och skydda mot injections genom placeholders
    $stmt = $db->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
    //kolla om prepare funkade
    if($stmt==false){
        error_log("Preparations to SQL insertion failed" . $db->lastErrorMsg());
    }
    //bind parametrarna och behandla som text 
    $stmt->bindValue(1, $username, SQLITE3_TEXT);
    $stmt->bindValue(2, $hashpassword, SQLITE3_TEXT);
    //kör inserten och spara resultatet
    $result = $stmt->execute();
    //kolla om inserten lyckades
    if($result == false) {
        error_log("Insertion failed" . $db->lastErrorMsg());
        $stmt->close();
        $db->close();
        return false; 
    }
    $stmt->close();
    //stäng databasanslutningen
    $db->close();
    //returnera sant för lyckad insertion
    return true;
}

function getUser($username) {
    $db = new SQLite3(__DIR__ . '/../database/account_items.db');
    $stmt = $db->prepare('SELECT * FROM users WHERE Username = :username');
    if ($stmt) {
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        $db->close();
        return $row;
    } else {
        $db->close();
        throw new Exception("Failed to get the username");
    }
}

function checkPassword($inputPassword, $username){
    //hämta användar detaljer genom getUser funktionen
    $userInfo = getUser($username);
    //checkar om datan kunde hämtas och om lösenord existerar
    if($userInfo && !empty($userInfo['password'])) {
        //hasha input lösenordet
        $hashinput = hash('sha3-512', $inputPassword);
        //jämför input lösenord med sparade hash lösenord
        return $hashinput === $userInfo['password'];
    }
    //falskt om fel lösenord eller användaren inte finns
    return false;
}

function getRecommendedItems($userId, $db) {
    // Get items based on purchase history and items that haven't been purchased
    $sql = "
        SELECT 
            i.ItemID,
            i.ItemName AS ItemName, 
            MAX(p.PurchaseDate) AS LastPurchaseDate,
            AVG(p.DateDiff) AS AvgInterval
        FROM Items i
        LEFT JOIN (
            SELECT 
                PurchaseDetails.ItemID, 
                Purchases.PurchaseDate,
                julianday(Purchases.PurchaseDate) - lag(julianday(Purchases.PurchaseDate)) 
                OVER (PARTITION BY PurchaseDetails.ItemID ORDER BY Purchases.PurchaseDate) AS DateDiff
            FROM Purchases
            JOIN PurchaseDetails ON Purchases.PurchaseID = PurchaseDetails.PurchaseID
            WHERE Purchases.UserID = :userId 
        ) p ON i.ItemID = p.ItemID
        GROUP BY i.ItemID;
    ";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':userId', $userId, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $items = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $items[] = $row;
    }
    return $items;
}

function getAllItems($db) {
    $sql = "SELECT ItemID, ItemName FROM Items";
    $result = $db->query($sql);
    $items = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $items[] = $row;
    }
    return $items;
}

function addItem($itemName, $quantity, $db) {
    $stmt = $db->prepare('INSERT INTO Items (ItemName, Quantity) VALUES (:itemName, :quantity)');
    $stmt->bindValue(':itemName', $itemName, SQLITE3_TEXT);
    $stmt->bindValue(':quantity', $quantity, SQLITE3_INTEGER);
    $stmt->execute();
}

function deleteItemPermanently($itemId, $db) {
    // Delete the item from the Items table
    $stmt = $db->prepare('DELETE FROM Items WHERE ItemID = :itemId');
    if ($stmt === false) {
        throw new Exception("Unable to prepare statement: " . $db->lastErrorMsg());
    }
    $stmt->bindValue(':itemId', $itemId, SQLITE3_INTEGER);
    $stmt->execute();

    // Also delete any references in UserShoppingList and PurchaseDetails
    $stmt = $db->prepare('DELETE FROM UserShoppingList WHERE ItemID = :itemId');
    $stmt->bindValue(':itemId', $itemId, SQLITE3_INTEGER);
    $stmt->execute();

    $stmt = $db->prepare('DELETE FROM PurchaseDetails WHERE ItemID = :itemId');
    $stmt->bindValue(':itemId', $itemId, SQLITE3_INTEGER);
    $stmt->execute();
}

// Function to permanently add a new item to the Items table
function addNewItem($itemName, $db) {
    // Check if the item already exists
    $stmt = $db->prepare('SELECT ItemID FROM Items WHERE ItemName = :itemName');
    $stmt->bindValue(':itemName', $itemName, SQLITE3_TEXT);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);
    
    if ($row) {
        // Item already exists, return its ID
        return $row['ItemID'];
    } else {
        // Item does not exist, insert it
        $stmt = $db->prepare('INSERT INTO Items (ItemName, Quantity) VALUES (:itemName, 0)');
        $stmt->bindValue(':itemName', $itemName, SQLITE3_TEXT);
        $stmt->execute();
        return $db->lastInsertRowID();
    }
}
//se över namnen
function addItemToList($userId, $itemId, $db, $quantity = 1) {
    // Check if the item is already in the list
    $stmt = $db->prepare('SELECT COUNT(*) as count FROM UserShoppingList WHERE UserID = :userId AND ItemID = :itemId');
    $stmt->bindValue(':userId', $userId, SQLITE3_INTEGER);
    $stmt->bindValue(':itemId', $itemId, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);
    
    if ($row['count'] == 0) {
        // Item is not in the list, insert it
        $stmt = $db->prepare('INSERT INTO UserShoppingList (UserID, ItemID, Quantity) VALUES (:userId, :itemId, :quantity)');
        $stmt->bindValue(':userId', $userId, SQLITE3_INTEGER);
        $stmt->bindValue(':itemId', $itemId, SQLITE3_INTEGER);
        $stmt->bindValue(':quantity', $quantity, SQLITE3_INTEGER);
        $stmt->execute();
    } else {
        // Item is already in the list, update the quantity
        $stmt = $db->prepare('UPDATE UserShoppingList SET Quantity = Quantity + :quantity WHERE UserID = :userId AND ItemID = :itemId');
        $stmt->bindValue(':quantity', $quantity, SQLITE3_INTEGER);
        $stmt->bindValue(':userId', $userId, SQLITE3_INTEGER);
        $stmt->bindValue(':itemId', $itemId, SQLITE3_INTEGER);
        $stmt->execute();
    }
}

// Function to delete items from UserShoppingList
function deleteItemFromList($userId, $itemId, $db) {
    $stmt = $db->prepare('DELETE FROM UserShoppingList WHERE UserID = :userId AND ItemID = :itemId');
    $stmt->bindValue(':userId', $userId, SQLITE3_INTEGER);
    $stmt->bindValue(':itemId', $itemId, SQLITE3_INTEGER);
    $stmt->execute();
}

// Function to fetch items from UserShoppingList
function getUserShoppingList($userId, $db) {
    $stmt = $db->prepare('SELECT Items.ItemID, Items.ItemName, UserShoppingList.Quantity FROM UserShoppingList JOIN Items ON UserShoppingList.ItemID = Items.ItemID WHERE UserShoppingList.UserID = :userId');
    $stmt->bindValue(':userId', $userId, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $items = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $items[] = $row;
    }
    return $items;
}
function updatePurchaseDate($userId, $itemId, $quantity, $db) {
    // Insert into Purchases
    $stmt = $db->prepare('INSERT INTO Purchases (UserID, PurchaseDate) VALUES (:userId, :purchaseDate)');
    $stmt->bindValue(':userId', $userId, SQLITE3_INTEGER);
    $stmt->bindValue(':purchaseDate', date('Y-m-d'), SQLITE3_TEXT);
    $stmt->execute();
    
    // Get the last inserted PurchaseID
    $purchaseId = $db->lastInsertRowID();
    
    // Insert into PurchaseDetails
    $stmt = $db->prepare('INSERT INTO PurchaseDetails (PurchaseID, ItemID, Quantity) VALUES (:purchaseId, :itemId, :quantity)');
    $stmt->bindValue(':purchaseId', $purchaseId, SQLITE3_INTEGER);
    $stmt->bindValue(':itemId', $itemId, SQLITE3_INTEGER);
    $stmt->bindValue(':quantity', $quantity, SQLITE3_INTEGER);
    $stmt->execute();
}

//Ta bort replecement tablet och lägg in båda varorna direkt med datum 
// Function to add a replacement to the Replacements table
function addReplacement($originalItemId, $replacementItemId, $db) {
    $stmt = $db->prepare('INSERT INTO Replacements (OriginalItemID, ReplacementItemID) VALUES (:originalItemId, :replacementItemId)');
    $stmt->bindValue(':originalItemId', $originalItemId, SQLITE3_INTEGER);
    $stmt->bindValue(':replacementItemId', $replacementItemId, SQLITE3_INTEGER);
    $stmt->execute();
}
//från labbinstruktionen
// //Observera att följande funktion är sårbar för sql-injection och behöver förbättras
// function selectPwd($username){
//     // Öppna SQLite-databasen
//     $db = new SQLite3('../database/account_items.db');

//     // Förbered SQL-frågan
//     $sql = "SELECT password FROM users WHERE username = '".$username."'";

//     // Utför frågan
//     $result = $db->query($sql);

//     // Hämta raden från resultatet
//     $row = $result->fetchArray();

//     // Stäng databasanslutningen
//     $db->close();
//     // Returnera resultatet (kan vara null om användarnamnet inte hittades)
//     return $row;
// }
?>