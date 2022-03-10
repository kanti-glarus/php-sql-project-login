<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Database {

    private $host = 'localhost';
    private $user = 'root';
    private $password = 'masterpw';
    private $db = 'projektlogin';

    /**
     * Creates a simple database-connection.
     *
     * @return PDO
     */
    private function create_connection() {
        $conn = new PDO("mysql:host=$this->host;dbname=$this->db", $this->user, $this->password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    }

    private function check_if_table_exist($connection, $table) {
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
    private function create_user_table() {
        // here: create table if not exist.
        try {
            $conn = $this->create_connection();
            if (!$this->check_if_table_exist($conn, 'user')) {
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

    public function prepare_login() {
        $this->create_user_table();
        return true;
    }

    public function prepare_registration() {
        $this->create_user_table();
        return true;
    }

    public function register_user($username, $password, $email=null) {
        // here: insert a new user into the database.
        // @todo: check if username is free.
        try {
            $conn = $this->create_connection();

            $sql = 'INSERT INTO user(username, password, email, register_date)
            VALUES(?, ?, ?, NOW())';
            $statement = $conn->prepare($sql);
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $statement->execute([$username, $password_hash, $email]);
            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
