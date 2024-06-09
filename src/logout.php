<?php
session_start();
require('functions.php');
$actual_url = dirname($_SERVER['REQUEST_URI']);

if (isset($_SESSION["user_id"])) {
    clearSessionHash($_SESSION["user_id"]);
}

session_destroy();
header("Location: ".$actual_url."/../");
exit();
?>