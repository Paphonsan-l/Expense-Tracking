<?php
/**
 * Category Model
 */

require_once BASE_PATH . '/app/config/database.php';

class Category {
    private $db;
    private $connection;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->connection = $this->db->getConnection();
    }
    
    /**
     * Get all categories for a user
     */
    public function getAllByUser($userId, $type = null) {
        $sql = "SELECT * FROM categories WHERE user_id = :user_id";
        $params = ['user_id' => $userId];
        
        if ($type !== null) {
            $sql .= " AND type = :type";
            $params['type'] = $type;
        }
        
        $sql .= " ORDER BY name ASC";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get category by ID
     */
    public function findById($id, $userId) {
        $stmt = $this->connection->prepare(
            "SELECT * FROM categories WHERE id = :id AND user_id = :user_id"
        );
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        return $stmt->fetch();
    }
    
    /**
     * Create new category
     */
    public function create($data) {
        $stmt = $this->connection->prepare(
            "INSERT INTO categories (user_id, name, type, icon, color)
             VALUES (:user_id, :name, :type, :icon, :color)"
        );
        
        $result = $stmt->execute([
            'user_id' => $data['user_id'],
            'name' => $data['name'],
            'type' => $data['type'],
            'icon' => $data['icon'] ?? null,
            'color' => $data['color'] ?? '#000000'
        ]);
        
        if ($result) {
            return $this->connection->lastInsertId();
        }
        return false;
    }
    
    /**
     * Update category
     */
    public function update($id, $userId, $data) {
        $stmt = $this->connection->prepare(
            "UPDATE categories 
             SET name = :name, type = :type, icon = :icon, color = :color
             WHERE id = :id AND user_id = :user_id"
        );
        
        return $stmt->execute([
            'id' => $id,
            'user_id' => $userId,
            'name' => $data['name'],
            'type' => $data['type'],
            'icon' => $data['icon'] ?? null,
            'color' => $data['color'] ?? '#000000'
        ]);
    }
    
    /**
     * Delete category
     */
    public function delete($id, $userId) {
        $stmt = $this->connection->prepare(
            "DELETE FROM categories WHERE id = :id AND user_id = :user_id"
        );
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }
    
    /**
     * Create default categories for new user
     */
    public function createDefaults($userId) {
        $defaults = [
            // Expense categories
            ['name' => 'อาหารและเครื่องดื่ม', 'type' => 'expense', 'icon' => 'utensils', 'color' => '#FF6384'],
            ['name' => 'ค่าพาหนะ', 'type' => 'expense', 'icon' => 'car', 'color' => '#36A2EB'],
            ['name' => 'ค่าที่พัก', 'type' => 'expense', 'icon' => 'home', 'color' => '#FFCE56'],
            ['name' => 'ค่าสาธารณูปโภค', 'type' => 'expense', 'icon' => 'bolt', 'color' => '#4BC0C0'],
            ['name' => 'ช้อปปิ้ง', 'type' => 'expense', 'icon' => 'shopping-cart', 'color' => '#9966FF'],
            ['name' => 'สุขภาพ', 'type' => 'expense', 'icon' => 'heart-pulse', 'color' => '#FF9F40'],
            ['name' => 'บันเทิง', 'type' => 'expense', 'icon' => 'gamepad', 'color' => '#FF6384'],
            ['name' => 'การศึกษา', 'type' => 'expense', 'icon' => 'book', 'color' => '#36A2EB'],
            ['name' => 'อื่นๆ', 'type' => 'expense', 'icon' => 'ellipsis', 'color' => '#C9CBCF'],
            
            // Income categories
            ['name' => 'เงินเดือน', 'type' => 'income', 'icon' => 'wallet', 'color' => '#4CAF50'],
            ['name' => 'โบนัส', 'type' => 'income', 'icon' => 'gift', 'color' => '#8BC34A'],
            ['name' => 'รายได้เสริม', 'type' => 'income', 'icon' => 'hand-holding-dollar', 'color' => '#CDDC39'],
            ['name' => 'เงินลงทุน', 'type' => 'income', 'icon' => 'chart-line', 'color' => '#FFC107'],
            ['name' => 'อื่นๆ', 'type' => 'income', 'icon' => 'ellipsis', 'color' => '#FF9800']
        ];
        
        $stmt = $this->connection->prepare(
            "INSERT INTO categories (user_id, name, type, icon, color)
             VALUES (:user_id, :name, :type, :icon, :color)"
        );
        
        foreach ($defaults as $category) {
            $stmt->execute([
                'user_id' => $userId,
                'name' => $category['name'],
                'type' => $category['type'],
                'icon' => $category['icon'],
                'color' => $category['color']
            ]);
        }
        
        return true;
    }
}
