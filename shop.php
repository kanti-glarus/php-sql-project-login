<?php

include("database.php");

$database = new Database();

if (!$database->prepare_shop()) {
    return false;
}

return true;
