<?php

include("database.php");

$database = new Database();
$register = [];

if (!$database->prepare_registration()) {
    $register["success"] = false;
    $register["message"] = "Registrierung nicht bereit.";
    echo json_encode($register);
    return false;
}

if (!isset($_GET['username']) || !isset($_GET['password'])) {
    $register["success"] = false;
    $register["message"] = "Parameter fehlen.";
    echo json_encode($register);
    return false;
}

$username = $_GET['username'];
$password = $_GET['password'];

$database->register_user($username, $password);

$register["success"] = false;
$register["message"] = "registration nicht erfolgreich.";
echo json_encode($register);
return false;
