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
                echo "user table created successfully.<br>";
            } else {
                echo "user table already exist.<br>";
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        $conn = null;
    }

    private function create_order_table() {
        // here: create order table if not exist.
        try {
            $conn = $this->create_connection();
            if (!$this->check_if_table_exist($conn, '`order`')) {
                // sql to create table
                $sql = "CREATE TABLE `order` (
                    order_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    user_id INT(11) UNSIGNED NOT NULL,
                    price FLOAT(10),
                    order_date TIMESTAMP
                    )";
                // use exec() because no results are returned
                $conn->exec($sql);

                // Add connection between order and user table.
                $sql = "
                    ALTER TABLE `order`  
                    ADD CONSTRAINT `FK_order_user` 
                        FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) 
                            ON UPDATE CASCADE 
                            ON DELETE CASCADE;
                    ";
                // use exec() because no results are returned
                $conn->exec($sql);
                echo "order table created and connected successfully.<br>";
            } else {
                echo "order table already exist.<br>";
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        $conn = null;
    }

    public function prepare_shop() {
        $this->create_user_table();
        $this->create_order_table();
        return true;
    }

    public function prepare_login() {
        $this->create_user_table();
        return true;
    }

    public function prepare_registration() {
        $this->create_user_table();
        return true;
    }

    public function login_user($username, $password) {
        try {
            $conn = $this->create_connection();
            $query = "SELECT * FROM `user` WHERE username = ?";
            $statement = $conn->prepare($query);
            $statement->execute([$username]);

            $user = $statement->fetchAll(PDO::FETCH_CLASS);

            if (empty($user)) {
                return false;
            }

            // user exist, we only look at the first entry.
            $user = $user[0];

            $password_saved = $user->password;
            if (!password_verify($password, $password_saved)) {
                return false;
            }

            // remove the password, we don't want to transfer this anywhere.
            unset($user->password);

            return $user;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return false;
    }

    public function register_user($username, $password, $email=null) {
        // here: insert a new user into the database.
        try {
            $conn = $this->create_connection();
            $query = "SELECT * FROM `user` WHERE username = ?";
            $statement = $conn->prepare($query);
            $statement->execute([$username]);

            $user = $statement->fetchAll(PDO::FETCH_CLASS);
            if (!empty($user)) {
                return false;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        // now: save user.
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

        return false;
    }

    public function drop_all() {
        try {
            $conn = $this->create_connection();

            $sql = 'ALTER TABLE `order`
                DROP FOREIGN KEY `FK_order_user`;';
            $conn->exec($sql);

            $sql = 'DROP TABLE `user`';
            $conn->exec($sql);

            $sql = 'DROP TABLE `order`';
            $conn->exec($sql);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return false;
    }
}
