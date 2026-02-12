<?php
/**
 * Category Controller
 */

require_once BASE_PATH . '/app/models/Category.php';

class CategoryController {
    private $categoryModel;
    
    public function __construct() {
        Auth::require();
        $this->categoryModel = new Category();
    }
    
    /**
     * List all categories
     */
    public function index() {
        $userId = Auth::id();
        
        // Get all categories
        $expenseCategories = $this->categoryModel->getAllByUser($userId, 'expense');
        $incomeCategories = $this->categoryModel->getAllByUser($userId, 'income');
        
        $data = [
            'expense_categories' => $expenseCategories,
            'income_categories' => $incomeCategories,
            'user' => Auth::user()
        ];
        
        require_once BASE_PATH . '/app/views/category/index.php';
    }
    
    /**
     * Store new category (AJAX)
     */
    public function store() {
        header('Content-Type: application/json');
        
        $validator = new Validator();
        $userId = Auth::id();
        
        $data = [
            'user_id' => $userId,
            'name' => Validator::sanitize($_POST['name'] ?? ''),
            'type' => $_POST['type'] ?? '',
            'icon' => Validator::sanitize($_POST['icon'] ?? ''),
            'color' => $_POST['color'] ?? '#000000'
        ];
        
        // Validation
        $validator->required('name', $data['name']);
        $validator->required('type', $data['type']);
        
        if ($validator->fails()) {
            echo json_encode([
                'success' => false,
                'errors' => $validator->getErrors()
            ]);
            exit;
        }
        
        $categoryId = $this->categoryModel->create($data);
        
        if ($categoryId) {
            echo json_encode([
                'success' => true,
                'message' => 'เพิ่มหมวดหมู่สำเร็จ',
                'category_id' => $categoryId
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการเพิ่มหมวดหมู่'
            ]);
        }
        exit;
    }
    
    /**
     * Update category (AJAX)
     */
    public function update($id) {
        header('Content-Type: application/json');
        
        $validator = new Validator();
        $userId = Auth::id();
        
        $data = [
            'name' => Validator::sanitize($_POST['name'] ?? ''),
            'type' => $_POST['type'] ?? '',
            'icon' => Validator::sanitize($_POST['icon'] ?? ''),
            'color' => $_POST['color'] ?? '#000000'
        ];
        
        // Validation
        $validator->required('name', $data['name']);
        $validator->required('type', $data['type']);
        
        if ($validator->fails()) {
            echo json_encode([
                'success' => false,
                'errors' => $validator->getErrors()
            ]);
            exit;
        }
        
        $result = $this->categoryModel->update($id, $userId, $data);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'แก้ไขหมวดหมู่สำเร็จ'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการแก้ไขหมวดหมู่'
            ]);
        }
        exit;
    }
    
    /**
     * Delete category (AJAX)
     */
    public function delete($id) {
        header('Content-Type: application/json');
        
        $userId = Auth::id();
        
        if ($this->categoryModel->delete($id, $userId)) {
            echo json_encode([
                'success' => true,
                'message' => 'ลบหมวดหมู่สำเร็จ'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'ไม่สามารถลบหมวดหมู่ได้ เนื่องจากมีรายการที่ใช้หมวดหมู่นี้อยู่'
            ]);
        }
        exit;
    }
}
