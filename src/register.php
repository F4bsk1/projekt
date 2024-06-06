<!-- Den här sidan har till uppgift att skapa användarkonto i databasen.<br>
Men innan kontot skapas ska en del check på indata, d.v.s. användarnamn och lösenorden göra. T.ex att användarnamnet är inte redan upptagen och indata är schyst och försöker inte manipulera databasen, databas-inhejction osv...

När alla kontroll har genpomförts och kontot har skapats förslagsvis ska användaren bli inloggad och skickas till sidan menu.php eller du kan välja göra något annat vettigt val istället. -->

<?php
//starta eller återuppta
session_start();
//inkludera functions filen som tar hand om databasen
require 'functions.php';
//$_SERVER är en super global variabel som håller headers, paths, script locations osv
if($_SERVER["REQUEST_METHOD"] == "POST") {
    //spara användar input ?? är null coalescing
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    //check för tomma fält
    if(empty($username) || empty($password)) {
        //skriv ut felmedelande till användaren
        echo "Please fill in both username and password";
        exit;
    }

    // //kolla om användarnamnet redan finns via getUser från functions.php
    // if(getUser($username) !== null) {
    //     echo "Username already exist, please chose another username";
    //     exit;
    // }

    //om allt funkar hittils så lägger vi till användaren till databasen via addUser från functions.php
    if(addUser($username, $password)) {
        //logga in användaren
        $_SESSION["logged_in_user"] = $username;
        //till menysidan
        header("Location: menu.php");
        exit();
    } else {
        echo "Error signing up, please try again";
    }
}
?>