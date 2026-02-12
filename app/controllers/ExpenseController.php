<?php
/**
 * Expense Controller
 */

require_once BASE_PATH . '/app/models/Expense.php';
require_once BASE_PATH . '/app/models/Category.php';

class ExpenseController {
    private $expenseModel;
    private $categoryModel;
    
    public function __construct() {
        Auth::require();
        $this->expenseModel = new Expense();
        $this->categoryModel = new Category();
    }
    
    /**
     * List all expenses
     */
    public function index() {
        $userId = Auth::id();
        
        // Get filter parameters
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');
        $categoryId = $_GET['category_id'] ?? null;
        
        // Get expenses
        if ($categoryId) {
            $expenses = $this->expenseModel->getByCategory($userId, $categoryId);
        } else {
            $expenses = $this->expenseModel->getByDateRange($userId, $startDate, $endDate);
        }
        
        // Get categories for filter
        $categories = $this->categoryModel->getAllByUser($userId, 'expense');
        
        // Get totals
        $totalExpense = $this->expenseModel->getTotalByUser($userId, $startDate, $endDate);
        
        $data = [
            'expenses' => $expenses,
            'categories' => $categories,
            'total_expense' => $totalExpense,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'category_id' => $categoryId,
            'user' => Auth::user()
        ];
        
        require_once BASE_PATH . '/app/views/expense/index.php';
    }
    
    /**
     * Show create form
     */
    public function create() {
        $userId = Auth::id();
        $categories = $this->categoryModel->getAllByUser($userId, 'expense');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->store();
        }
        
        $data = [
            'categories' => $categories,
            'user' => Auth::user()
        ];
        
        require_once BASE_PATH . '/app/views/expense/create.php';
    }
    
    /**
     * Store new expense
     */
    private function store() {
        $validator = new Validator();
        $userId = Auth::id();
        
        $data = [
            'user_id' => $userId,
            'category_id' => $_POST['category_id'] ?? '',
            'amount' => $_POST['amount'] ?? '',
            'description' => Validator::sanitize($_POST['description'] ?? ''),
            'expense_date' => $_POST['expense_date'] ?? date('Y-m-d'),
            'receipt_path' => null
        ];
        
        // Validation
        $validator->required('category_id', $data['category_id']);
        $validator->required('amount', $data['amount']);
        $validator->numeric('amount', $data['amount']);
        $validator->required('expense_date', $data['expense_date']);
        
        // Handle file upload
        if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
            if ($validator->file('receipt', $_FILES['receipt'])) {
                $uploadResult = $this->uploadReceipt($_FILES['receipt']);
                if ($uploadResult['success']) {
                    $data['receipt_path'] = $uploadResult['path'];
                } else {
                    Session::error($uploadResult['error']);
                }
            }
        }
        
        if ($validator->fails()) {
            $_SESSION['errors'] = $validator->getErrors();
            $_SESSION['old'] = $data;
            header('Location: /expense/create');
            exit;
        }
        
        $expenseId = $this->expenseModel->create($data);
        
        if ($expenseId) {
            Session::success('เพิ่มรายจ่ายสำเร็จ');
            header('Location: /expense');
        } else {
            Session::error('เกิดข้อผิดพลาดในการเพิ่มรายจ่าย');
            header('Location: /expense/create');
        }
        exit;
    }
    
    /**
     * Show edit form
     */
    public function edit($id) {
        $userId = Auth::id();
        $expense = $this->expenseModel->findById($id, $userId);
        
        if (!$expense) {
            Session::error('ไม่พบรายการที่ต้องการแก้ไข');
            header('Location: /expense');
            exit;
        }
        
        $categories = $this->categoryModel->getAllByUser($userId, 'expense');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->update($id);
        }
        
        $data = [
            'expense' => $expense,
            'categories' => $categories,
            'user' => Auth::user()
        ];
        
        require_once BASE_PATH . '/app/views/expense/edit.php';
    }
    
    /**
     * Update expense
     */
    private function update($id) {
        $validator = new Validator();
        $userId = Auth::id();
        
        $data = [
            'category_id' => $_POST['category_id'] ?? '',
            'amount' => $_POST['amount'] ?? '',
            'description' => Validator::sanitize($_POST['description'] ?? ''),
            'expense_date' => $_POST['expense_date'] ?? '',
            'receipt_path' => $_POST['existing_receipt'] ?? null
        ];
        
        // Validation
        $validator->required('category_id', $data['category_id']);
        $validator->required('amount', $data['amount']);
        $validator->numeric('amount', $data['amount']);
        $validator->required('expense_date', $data['expense_date']);
        
        // Handle new file upload
        if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
            if ($validator->file('receipt', $_FILES['receipt'])) {
                // Delete old receipt if exists
                if (!empty($data['receipt_path']) && file_exists(UPLOAD_PATH . $data['receipt_path'])) {
                    unlink(UPLOAD_PATH . $data['receipt_path']);
                }
                
                $uploadResult = $this->uploadReceipt($_FILES['receipt']);
                if ($uploadResult['success']) {
                    $data['receipt_path'] = $uploadResult['path'];
                }
            }
        }
        
        if ($validator->fails()) {
            $_SESSION['errors'] = $validator->getErrors();
            header('Location: /expense/edit/' . $id);
            exit;
        }
        
        $result = $this->expenseModel->update($id, $userId, $data);
        
        if ($result) {
            Session::success('แก้ไขรายจ่ายสำเร็จ');
        } else {
            Session::error('เกิดข้อผิดพลาดในการแก้ไขรายจ่าย');
        }
        
        header('Location: /expense');
        exit;
    }
    
    /**
     * Delete expense
     */
    public function delete($id) {
        $userId = Auth::id();
        
        // Get expense to check receipt
        $expense = $this->expenseModel->findById($id, $userId);
        
        if ($expense && $this->expenseModel->delete($id, $userId)) {
            // Delete receipt file if exists
            if (!empty($expense['receipt_path']) && file_exists(UPLOAD_PATH . $expense['receipt_path'])) {
                unlink(UPLOAD_PATH . $expense['receipt_path']);
            }
            
            Session::success('ลบรายจ่ายสำเร็จ');
        } else {
            Session::error('เกิดข้อผิดพลาดในการลบรายจ่าย');
        }
        
        header('Location: /expense');
        exit;
    }
    
    /**
     * Upload receipt file
     */
    private function uploadReceipt($file) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid('receipt_') . '.' . $ext;
        $destination = UPLOAD_PATH . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => true, 'path' => $filename];
        } else {
            return ['success' => false, 'error' => 'ไม่สามารถอัปโหลดไฟล์ได้'];
        }
    }
}
