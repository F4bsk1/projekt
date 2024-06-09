Här har du fått en filstruktur för att lättare komma igång med labben. Du behöver alltså lägga till flera PHP-filer. Men det är inget krav att du använder denna filstruktur.
Nedanstående kommandon körs lämpligtvis på skolans datorer i en UNIX-lik terminal, till exempel bash som är standard (det
är den terminalen som har ett '$'-tecken som prompt och den är default under Ubuntu Linux.)

VIKTIGT: databasfilen account_items.db finns i mappen database. För att det ska vara möjligt att använda databasen behöver du sätta om afs-rättigheter för mappen database enligt följande kommando i terminalen.
fs sa ~/smart_shopping_list/database httpd-course rlidwk

När det gäller andra mappar så kan du behöva köra följande kommandon:
fs sa ~/smart_shopping_list/ httpd-course rl
fs sa ~/smart_shopping_list/public httpd-course rl
fs sa ~/smart_shopping_list/public/js httpd-course rl
fs sa ~/smart_shopping_list/src httpd-course rl
