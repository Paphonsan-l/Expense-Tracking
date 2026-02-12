<?php
/**
 * Dashboard Controller
 */

require_once BASE_PATH . '/app/models/Expense.php';
require_once BASE_PATH . '/app/models/Income.php';
require_once BASE_PATH . '/app/models/Category.php';

class DashboardController
{
    private $expenseModel;
    private $incomeModel;

    public function __construct()
    {
        Auth::require(); // Require authentication
        $this->expenseModel = new Expense();
        $this->incomeModel = new Income();
    }

    /**
     * Show dashboard
     */
    public function index()
    {
        $userId = Auth::id();
        $viewType = $_GET['type'] ?? 'expense'; // Default to expense

        // Get current month dates
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');

        // Get this month's totals
        $totalExpense = $this->expenseModel->getTotalByUser($userId, $startOfMonth, $endOfMonth);
        $totalIncome = $this->incomeModel->getTotalByUser($userId, $startOfMonth, $endOfMonth);

        // Get recent expenses
        $recentExpenses = $this->expenseModel->getAllByUser($userId, 10);

        // Get recent incomes
        $recentIncomes = $this->incomeModel->getAllByUser($userId, 10);

        // Get summary by category
        $expenseCategorySummary = $this->expenseModel->getSummaryByCategory($userId, $startOfMonth, $endOfMonth);
        $incomeCategorySummary = $this->incomeModel->getSummaryByCategory($userId, $startOfMonth, $endOfMonth);

        // Calculate balance
        $balance = $totalIncome - $totalExpense;

        // Pass data to view
        $data = [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'balance' => $balance,
            'recent_expenses' => $recentExpenses,
            'recent_incomes' => $recentIncomes,
            'expense_category_summary' => $expenseCategorySummary,
            'income_category_summary' => $incomeCategorySummary,
            'current_month' => date('F Y'),
            'user' => Auth::user(),
            'view_type' => $viewType
        ];

        require_once BASE_PATH . '/app/views/dashboard/index.php';
    }
}
