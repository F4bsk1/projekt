<?php
session_start();
$actual_url = dirname($_SERVER['REQUEST_URI']);

session_destroy();
header("Location: ".$actual_url."/../");
exit();
?>