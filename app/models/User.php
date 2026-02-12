<?php
/**
 * User Model
 */

require_once BASE_PATH . '/app/config/database.php';

class User
{
    private $db;
    private $connection;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->connection = $this->db->getConnection();
    }

    /**
     * Find user by ID
     */
    public function findById($id)
    {
        $stmt = $this->connection->prepare(
            "SELECT id, username, email, full_name, profile_image, created_at, updated_at 
             FROM users WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Find user by ID including password
     */
    public function findWithPasswordById($id)
    {
        $stmt = $this->connection->prepare(
            "SELECT * FROM users WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Find user by username
     */
    public function findByUsername($username)
    {
        $stmt = $this->connection->prepare(
            "SELECT * FROM users WHERE username = :username"
        );
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

    /**
     * Find user by email
     */
    public function findByEmail($email)
    {
        $stmt = $this->connection->prepare(
            "SELECT * FROM users WHERE email = :email"
        );
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Create new user
     */
    public function create($data)
    {
        $stmt = $this->connection->prepare(
            "INSERT INTO users (username, email, password, full_name) 
             VALUES (:username, :email, :password, :full_name)"
        );

        $hashedPassword = password_hash($data['password'], PASSWORD_HASH_ALGO, ['cost' => PASSWORD_HASH_COST]);

        $result = $stmt->execute([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'full_name' => $data['full_name']
        ]);

        if ($result) {
            return $this->connection->lastInsertId();
        }
        return false;
    }

    /**
     * Update user
     */
    public function update($id, $data)
    {
        $fields = [];
        $params = ['id' => $id];

        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                $fields[] = "$key = :$key";
                $params[$key] = $value;
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Update password
     */
    public function updatePassword($id, $newPassword)
    {
        $stmt = $this->connection->prepare(
            "UPDATE users SET password = :password WHERE id = :id"
        );

        $hashedPassword = password_hash($newPassword, PASSWORD_HASH_ALGO, ['cost' => PASSWORD_HASH_COST]);

        return $stmt->execute([
            'id' => $id,
            'password' => $hashedPassword
        ]);
    }

    /**
     * Verify password
     */
    public function verifyPassword($password, $hashedPassword)
    {
        return password_verify($password, $hashedPassword);
    }

    /**
     * Delete user
     */
    public function delete($id)
    {
        $stmt = $this->connection->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
