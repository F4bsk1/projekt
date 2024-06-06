-- database: /Users/Fabbe/AK2/progp/falu-W1/database/account_items.db

-- Use the ▷ button in the top right corner to run the entire file.

SELECT ItemID, PurchaseDate
FROM PurchaseDetails
JOIN Purchases ON PurchaseDetails.PurchaseID = Purchases.PurchaseID
WHERE UserID = 4;

SELECT ItemID,
       PurchaseDate,
       julianday('now') - julianday(PurchaseDate) AS DaysSincePurchase
FROM PurchaseDetails
JOIN Purchases ON PurchaseDetails.PurchaseID = Purchases.PurchaseID
WHERE UserID = 4 AND ItemID = 3;

SELECT Items.ItemID, Items.Name, 
                   MAX(Purchases.PurchaseDate) AS LastPurchaseDate,
                   AVG(julianday('now') - julianday(Purchases.PurchaseDate)) AS AvgInterval
            FROM Purchases
            JOIN PurchaseDetails ON Purchases.PurchaseID = PurchaseDetails.PurchaseID
            JOIN Items ON PurchaseDetails.ItemID = Items.ItemID
            WHERE Purchases.UserID = 4
            GROUP BY Items.ItemID;

SELECT 
    p.ItemID,
    i.ItemName AS ItemName, -- Fetching the item name from the Items table
    MAX(p.PurchaseDate) AS LastPurchaseDate,
    AVG(p.DateDiff) AS AvgInterval
FROM (
    SELECT 
        PurchaseDetails.ItemID, 
        Purchases.PurchaseDate,
        julianday(Purchases.PurchaseDate) - lag(julianday(Purchases.PurchaseDate)) OVER (PARTITION BY PurchaseDetails.ItemID ORDER BY Purchases.PurchaseDate) AS DateDiff
    FROM Purchases
    JOIN PurchaseDetails ON Purchases.PurchaseID = PurchaseDetails.PurchaseID
    WHERE Purchases.UserID = 4 -- Assuming dynamic UserID handling in real application
) p
JOIN Items i ON p.ItemID = i.ItemID -- Joining the Items table to get names
WHERE p.DateDiff IS NOT NULL
GROUP BY p.ItemID;

