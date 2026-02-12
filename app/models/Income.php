<?php
/**
 * Income Model
 */

require_once BASE_PATH . '/app/config/database.php';

class Income
{
    private $db;
    private $connection;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->connection = $this->db->getConnection();
    }

    /**
     * Get all incomes for a user
     */
    public function getAllByUser($userId, $limit = null, $offset = 0)
    {
        $sql = "SELECT i.*, c.name as category_name, c.color as category_color, c.icon as category_icon
                FROM incomes i
                LEFT JOIN categories c ON i.category_id = c.id
                WHERE i.user_id = :user_id
                ORDER BY i.income_date DESC, i.created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get income by ID
     */
    public function findById($id, $userId)
    {
        $stmt = $this->connection->prepare(
            "SELECT i.*, c.name as category_name, c.color as category_color
             FROM incomes i
             LEFT JOIN categories c ON i.category_id = c.id
             WHERE i.id = :id AND i.user_id = :user_id"
        );
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        return $stmt->fetch();
    }

    /**
     * Get incomes by date range
     */
    public function getByDateRange($userId, $startDate, $endDate)
    {
        $stmt = $this->connection->prepare(
            "SELECT i.*, c.name as category_name, c.color as category_color, c.icon as category_icon
             FROM incomes i
             LEFT JOIN categories c ON i.category_id = c.id
             WHERE i.user_id = :user_id 
             AND i.income_date BETWEEN :start_date AND :end_date
             ORDER BY i.income_date DESC"
        );
        $stmt->execute([
            'user_id' => $userId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        return $stmt->fetchAll();
    }

    /**
     * Create new income
     */
    public function create($data)
    {
        $stmt = $this->connection->prepare(
            "INSERT INTO incomes (user_id, category_id, amount, description, income_date)
             VALUES (:user_id, :category_id, :amount, :description, :income_date)"
        );

        $result = $stmt->execute([
            'user_id' => $data['user_id'],
            'category_id' => $data['category_id'],
            'amount' => $data['amount'],
            'description' => $data['description'] ?? null,
            'income_date' => $data['income_date']
        ]);

        if ($result) {
            return $this->connection->lastInsertId();
        }
        return false;
    }

    /**
     * Update income
     */
    public function update($id, $userId, $data)
    {
        $stmt = $this->connection->prepare(
            "UPDATE incomes 
             SET category_id = :category_id, amount = :amount, description = :description, 
                 income_date = :income_date
             WHERE id = :id AND user_id = :user_id"
        );

        return $stmt->execute([
            'id' => $id,
            'user_id' => $userId,
            'category_id' => $data['category_id'],
            'amount' => $data['amount'],
            'description' => $data['description'] ?? null,
            'income_date' => $data['income_date']
        ]);
    }

    /**
     * Delete income
     */
    public function delete($id, $userId)
    {
        $stmt = $this->connection->prepare(
            "DELETE FROM incomes WHERE id = :id AND user_id = :user_id"
        );
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }

    /**
     * Get incomes by category
     */
    public function getByCategory($userId, $categoryId)
    {
        $stmt = $this->connection->prepare(
            "SELECT i.*, c.name as category_name, c.color as category_color, c.icon as category_icon
             FROM incomes i
             LEFT JOIN categories c ON i.category_id = c.id
             WHERE i.user_id = :user_id AND i.category_id = :category_id
             ORDER BY i.income_date DESC"
        );
        $stmt->execute([
            'user_id' => $userId,
            'category_id' => $categoryId
        ]);
        return $stmt->fetchAll();
    }

    /**
     * Get total incomes for user
     */
    public function getTotalByUser($userId, $startDate = null, $endDate = null)
    {
        $sql = "SELECT SUM(amount) as total FROM incomes WHERE user_id = :user_id";
        $params = ['user_id' => $userId];

        if ($startDate && $endDate) {
            $sql .= " AND income_date BETWEEN :start_date AND :end_date";
            $params['start_date'] = $startDate;
            $params['end_date'] = $endDate;
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
    /**
     * Get incomes summary by category
     */
    public function getSummaryByCategory($userId, $startDate = null, $endDate = null)
    {
        $sql = "SELECT c.name, c.color, c.icon, SUM(i.amount) as total, COUNT(i.id) as count
                FROM incomes i
                LEFT JOIN categories c ON i.category_id = c.id
                WHERE i.user_id = :user_id";

        $params = ['user_id' => $userId];

        if ($startDate && $endDate) {
            $sql .= " AND i.income_date BETWEEN :start_date AND :end_date";
            $params['start_date'] = $startDate;
            $params['end_date'] = $endDate;
        }

        $sql .= " GROUP BY c.id, c.name, c.color, c.icon ORDER BY total DESC";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
