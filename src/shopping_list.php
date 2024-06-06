<?php
function products_groupped_by_date($date){
	 $db = new SQLite3("database/products.db");
	 $result = $db->query("SELECT p.product_name FROM products p JOIN bought_products bp ON p.product_id = bp.product_id WHERE bp.purchase_date = '".$date."'");
	 if ($result) {
    	    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
               echo $row['product_name'] . "<br>";
    	    }
	 } else {
    	 echo "Inga produkter hittades för den angivna datumen.";
	 }
	 echo "<hr>";	
}	 


$db = new SQLite3("database/products.db");
$result = $db->query("SELECT DISTINCT purchase_date FROM bought_products");
if ($result) {

   while ($row = $result->fetchArray(SQLITE3_ASSOC)) {

      $date= $row['purchase_date'];
      echo "Produkter som köpts den $date:<br>";		
      products_groupped_by_date($date);

      
   }
} else {
echo "Inga datum hittades";
}
?>

