<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function check_if_table_exist($connection, $table) {
    try {
        $connection->query("SELECT 1 FROM $table");
    } catch (PDOException $e) {
        return false;
    }
    return true;
}

/**
 * Create User Table
 * ---
 * Checks if "user" table exists already.
 * Creates the table if not already exist.
 *
 * TABLE user:
 *  - user_id
 *  - username
 *  - password
 *  - email
 *  - register_date
 */
function create_user_table() {
    $host = 'localhost';
    $user = 'root';
    $pass = 'masterpw';
    $db = 'projektlogin';

    // here: create table if not exist.
    try {
        $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if (!check_if_table_exist($conn, 'user')) {
            // sql to create table
            $sql = "CREATE TABLE user (
                user_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(40) NOT NULL,
                password VARCHAR(160) NOT NULL,
                email VARCHAR(60),
                register_date TIMESTAMP )";
            // use exec() because no results are returned
            $conn->exec($sql);
            echo "user table created successfully";
        } else {
            // echo "user table already exist.";
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    $conn = null;
}

function prepare_login() {
    create_user_table();
    return true;
}

function prepare_registration() {
    create_user_table();
    return true;
}

function register_user($username, $password, $email=null) {
    $host = 'localhost';
    $user = 'root';
    $pass = 'root';
    $db = 'projektlogin';

    // here: create table if not exist.
    try {
        $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = 'INSERT INTO user(username, password, email, register_date)
        VALUES(?, ?, ?, NOW())';
        $statement = $conn->prepare($sql);
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $statement->execute([$username, $password_hash, $email]);
        return true;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
