<?php

require_once("database.php");

$login = [];

if (!prepare_login()) {
    $login["success"] = false;
    $login["message"] = "Login nicht bereit.";
    echo json_encode($login);
    return false;
}

if (!isset($_GET['username']) || !isset($_GET['password'])) {
    $login["success"] = false;
    $login["message"] = "Parameter fehlen.";
    echo json_encode($login);
    return false;
}

$username = $_GET['username'];
$password = $_GET['password'];

if ($username === 'admin' && $password === '12345678') {
    $login["success"] = true;
    $login["message"] = "Login erfolgreich.";
    echo json_encode($login);
    return true;
}
$login["success"] = false;
$login["message"] = "login nicht erfolgreich. Username oder Passwort sind falsch.";    
echo json_encode($login);
return false;

?>