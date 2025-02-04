<?php

    class DatabaseService
    {

        private $conn;

        public function __construct()
        {
            $this->connect();
        }

        private function connect()
        {
            try {
                $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbname};charset=utf8", $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database Connection Failed: " . $e->getMessage());
            }
        }

        public function executeQuery($query, $params = [])
        {
            try {
                $stmt = $this->conn->prepare($query);
                $stmt->execute($params);
                return $stmt;
            } catch (PDOException $e) {
                die("Query Failed: " . $e->getMessage());
            }
        }

        public function fetchAll($query, $params = [])
        {
            return $this->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
        }

        public function fetchOne($query, $params = [])
        {
            return $this->executeQuery($query, $params)->fetch(PDO::FETCH_ASSOC);
        }

        public function insert($query, $params = [])
        {
            return $this->executeQuery($query, $params);
        }

        public function update($query, $params = [])
        {
            return $this->executeQuery($query, $params);
        }

        public function delete($query, $params = [])
        {
            return $this->executeQuery($query, $params);
        }

        public function lastInsertId()
        {
            return $this->conn->lastInsertId();
        }
    }


    /**
     * Sample database usage of this class
     */
    // require_once "DatabaseService.php";

    // $db = new DatabaseService();

    // // Insert example
    // $db->insert("INSERT INTO users (name, email) VALUES (?, ?)", ["John Doe", "john@example.com"]);

    // // Fetch all users
    // $users = $db->fetchAll("SELECT * FROM users");
    // print_r($users);

    // // Fetch single user
    // $user = $db->fetchOne("SELECT * FROM users WHERE id = ?", [1]);
    // print_r($user);

    // // Update user
    // $db->update("UPDATE users SET name = ? WHERE id = ?", ["Jane Doe", 1]);

    // // Delete user
    // $db->delete("DELETE FROM users WHERE id = ?", [1]);
?>