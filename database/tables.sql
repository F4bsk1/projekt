-- database: /Users/Fabbe/AK2/progp/falu-W1/database/account_items.db

-- Use the ▷ button in the top right corner to run the entire file.

CREATE TABLE users (
    UserID INTEGER PRIMARY KEY AUTOINCREMENT,
    Username VARCHAR(255) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL
);

CREATE TABLE items (
    ItemID INTEGER PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(255) NOT NULL,
    Category VARCHAR(255)
);

CREATE TABLE purchase (
    PurchaseID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    PurchaseDate DATE NOT NULL,
    FOREIGN KEY (UserID) 
        REFERENCES users(UserID)
);

CREATE TABLE purchaseDetails (
    PurchaseDetailID INTEGER PRIMARY KEY AUTOINCREMENT ,
    PurchaseID INT NOT NULL,
    ItemID INT NOT NULL,
    Quantity INT NOT NULL,
    FOREIGN KEY (PurchaseID) 
        REFERENCES purchase(PurchaseID),
    FOREIGN KEY (ItemID) 
        REFERENCES items(ItemID)
);

SELECT * FROM users;

