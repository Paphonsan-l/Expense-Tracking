<?php
/**
 * Income Controller
 */

require_once BASE_PATH . '/app/models/Income.php';
require_once BASE_PATH . '/app/models/Category.php';

class IncomeController {
    private $incomeModel;
    private $categoryModel;
    
    public function __construct() {
        Auth::require();
        $this->incomeModel = new Income();
        $this->categoryModel = new Category();
    }
    
    /**
     * List all incomes
     */
    public function index() {
        $userId = Auth::id();
        
        // Get filter parameters
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');
        $categoryId = $_GET['category_id'] ?? null;
        
        // Get incomes
        if ($categoryId) {
            $incomes = $this->incomeModel->getByCategory($userId, $categoryId);
        } else {
            $incomes = $this->incomeModel->getByDateRange($userId, $startDate, $endDate);
        }
        
        // Get categories for filter
        $categories = $this->categoryModel->getAllByUser($userId, 'income');
        
        // Get totals
        $totalIncome = $this->incomeModel->getTotalByUser($userId, $startDate, $endDate);
        
        $data = [
            'incomes' => $incomes,
            'categories' => $categories,
            'total_income' => $totalIncome,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'category_id' => $categoryId,
            'user' => Auth::user()
        ];
        
        require_once BASE_PATH . '/app/views/income/index.php';
    }
    
    /**
     * Show create form
     */
    public function create() {
        $userId = Auth::id();
        $categories = $this->categoryModel->getAllByUser($userId, 'income');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->store();
        }
        
        $data = [
            'categories' => $categories,
            'user' => Auth::user()
        ];
        
        require_once BASE_PATH . '/app/views/income/create.php';
    }
    
    /**
     * Store new income
     */
    private function store() {
        $validator = new Validator();
        $userId = Auth::id();
        
        $data = [
            'user_id' => $userId,
            'category_id' => $_POST['category_id'] ?? '',
            'amount' => $_POST['amount'] ?? '',
            'description' => Validator::sanitize($_POST['description'] ?? ''),
            'income_date' => $_POST['income_date'] ?? date('Y-m-d')
        ];
        
        // Validation
        $validator->required('category_id', $data['category_id']);
        $validator->required('amount', $data['amount']);
        $validator->numeric('amount', $data['amount']);
        $validator->required('income_date', $data['income_date']);
        
        if ($validator->fails()) {
            $_SESSION['errors'] = $validator->getErrors();
            $_SESSION['old'] = $data;
            header('Location: /income/create');
            exit;
        }
        
        $incomeId = $this->incomeModel->create($data);
        
        if ($incomeId) {
            Session::success('เพิ่มรายรับสำเร็จ');
            header('Location: /income');
        } else {
            Session::error('เกิดข้อผิดพลาดในการเพิ่มรายรับ');
            header('Location: /income/create');
        }
        exit;
    }
    
    /**
     * Show edit form
     */
    public function edit($id) {
        $userId = Auth::id();
        $income = $this->incomeModel->findById($id, $userId);
        
        if (!$income) {
            Session::error('ไม่พบรายการที่ต้องการแก้ไข');
            header('Location: /income');
            exit;
        }
        
        $categories = $this->categoryModel->getAllByUser($userId, 'income');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->update($id);
        }
        
        $data = [
            'income' => $income,
            'categories' => $categories,
            'user' => Auth::user()
        ];
        
        require_once BASE_PATH . '/app/views/income/edit.php';
    }
    
    /**
     * Update income
     */
    private function update($id) {
        $validator = new Validator();
        $userId = Auth::id();
        
        $data = [
            'category_id' => $_POST['category_id'] ?? '',
            'amount' => $_POST['amount'] ?? '',
            'description' => Validator::sanitize($_POST['description'] ?? ''),
            'income_date' => $_POST['income_date'] ?? ''
        ];
        
        // Validation
        $validator->required('category_id', $data['category_id']);
        $validator->required('amount', $data['amount']);
        $validator->numeric('amount', $data['amount']);
        $validator->required('income_date', $data['income_date']);
        
        if ($validator->fails()) {
            $_SESSION['errors'] = $validator->getErrors();
            header('Location: /income/edit/' . $id);
            exit;
        }
        
        $result = $this->incomeModel->update($id, $userId, $data);
        
        if ($result) {
            Session::success('แก้ไขรายรับสำเร็จ');
        } else {
            Session::error('เกิดข้อผิดพลาดในการแก้ไขรายรับ');
        }
        
        header('Location: /income');
        exit;
    }
    
    /**
     * Delete income
     */
    public function delete($id) {
        $userId = Auth::id();
        
        if ($this->incomeModel->delete($id, $userId)) {
            Session::success('ลบรายรับสำเร็จ');
        } else {
            Session::error('เกิดข้อผิดพลาดในการลบรายรับ');
        }
        
        header('Location: /income');
        exit;
    }
}
