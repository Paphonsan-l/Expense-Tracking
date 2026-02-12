<?php
/**
 * Expense Model
 */

require_once BASE_PATH . '/app/config/database.php';

class Expense
{
    private $db;
    private $connection;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->connection = $this->db->getConnection();
    }

    /**
     * Get all expenses for a user
     */
    public function getAllByUser($userId, $limit = null, $offset = 0)
    {
        $sql = "SELECT e.*, c.name as category_name, c.color as category_color, c.icon as category_icon
                FROM expenses e
                LEFT JOIN categories c ON e.category_id = c.id
                WHERE e.user_id = :user_id
                ORDER BY e.expense_date DESC, e.created_at DESC";

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
     * Get expense by ID
     */
    public function findById($id, $userId)
    {
        $stmt = $this->connection->prepare(
            "SELECT e.*, c.name as category_name, c.color as category_color
             FROM expenses e
             LEFT JOIN categories c ON e.category_id = c.id
             WHERE e.id = :id AND e.user_id = :user_id"
        );
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        return $stmt->fetch();
    }

    /**
     * Get expenses by date range
     */
    public function getByDateRange($userId, $startDate, $endDate)
    {
        $stmt = $this->connection->prepare(
            "SELECT e.*, c.name as category_name, c.color as category_color, c.icon as category_icon
             FROM expenses e
             LEFT JOIN categories c ON e.category_id = c.id
             WHERE e.user_id = :user_id 
             AND e.expense_date BETWEEN :start_date AND :end_date
             ORDER BY e.expense_date DESC"
        );
        $stmt->execute([
            'user_id' => $userId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        return $stmt->fetchAll();
    }

    /**
     * Get expenses by category
     */
    public function getByCategory($userId, $categoryId)
    {
        $stmt = $this->connection->prepare(
            "SELECT e.*, c.name as category_name, c.color as category_color, c.icon as category_icon
             FROM expenses e
             LEFT JOIN categories c ON e.category_id = c.id
             WHERE e.user_id = :user_id AND e.category_id = :category_id
             ORDER BY e.expense_date DESC"
        );
        $stmt->execute(['user_id' => $userId, 'category_id' => $categoryId]);
        return $stmt->fetchAll();
    }

    /**
     * Create new expense
     */
    public function create($data)
    {
        $stmt = $this->connection->prepare(
            "INSERT INTO expenses (user_id, category_id, amount, description, expense_date, receipt_path)
             VALUES (:user_id, :category_id, :amount, :description, :expense_date, :receipt_path)"
        );

        $result = $stmt->execute([
            'user_id' => $data['user_id'],
            'category_id' => $data['category_id'],
            'amount' => $data['amount'],
            'description' => $data['description'] ?? null,
            'expense_date' => $data['expense_date'],
            'receipt_path' => $data['receipt_path'] ?? null
        ]);

        if ($result) {
            return $this->connection->lastInsertId();
        }
        return false;
    }

    /**
     * Update expense
     */
    public function update($id, $userId, $data)
    {
        $stmt = $this->connection->prepare(
            "UPDATE expenses 
             SET category_id = :category_id, amount = :amount, description = :description, 
                 expense_date = :expense_date, receipt_path = :receipt_path
             WHERE id = :id AND user_id = :user_id"
        );

        return $stmt->execute([
            'id' => $id,
            'user_id' => $userId,
            'category_id' => $data['category_id'],
            'amount' => $data['amount'],
            'description' => $data['description'] ?? null,
            'expense_date' => $data['expense_date'],
            'receipt_path' => $data['receipt_path'] ?? null
        ]);
    }

    /**
     * Delete expense
     */
    public function delete($id, $userId)
    {
        $stmt = $this->connection->prepare(
            "DELETE FROM expenses WHERE id = :id AND user_id = :user_id"
        );
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }

    /**
     * Get total expenses for user
     */
    public function getTotalByUser($userId, $startDate = null, $endDate = null)
    {
        $sql = "SELECT SUM(amount) as total FROM expenses WHERE user_id = :user_id";
        $params = ['user_id' => $userId];

        if ($startDate && $endDate) {
            $sql .= " AND expense_date BETWEEN :start_date AND :end_date";
            $params['start_date'] = $startDate;
            $params['end_date'] = $endDate;
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Get expenses summary by category
     */
    public function getSummaryByCategory($userId, $startDate = null, $endDate = null)
    {
        $sql = "SELECT c.name, c.color, c.icon, SUM(e.amount) as total, COUNT(e.id) as count
                FROM expenses e
                LEFT JOIN categories c ON e.category_id = c.id
                WHERE e.user_id = :user_id";

        $params = ['user_id' => $userId];

        if ($startDate && $endDate) {
            $sql .= " AND e.expense_date BETWEEN :start_date AND :end_date";
            $params['start_date'] = $startDate;
            $params['end_date'] = $endDate;
        }

        $sql .= " GROUP BY c.id, c.name, c.color, c.icon ORDER BY total DESC";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
