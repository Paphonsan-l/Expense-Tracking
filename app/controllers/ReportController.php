<?php
/**
 * Report Controller
 */

require_once BASE_PATH . '/app/models/Expense.php';
require_once BASE_PATH . '/app/models/Income.php';
require_once BASE_PATH . '/app/models/Category.php';

class ReportController
{
    private $expenseModel;
    private $incomeModel;
    private $categoryModel;

    public function __construct()
    {
        Auth::require();
        $this->expenseModel = new Expense();
        $this->incomeModel = new Income();
        $this->categoryModel = new Category();
    }

    /**
     * Show summary report
     */
    public function index()
    {
        $userId = Auth::id();

        // Get filter parameters
        $period = $_GET['period'] ?? 'month';
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;

        // Calculate date range based on period
        switch ($period) {
            case 'today':
                $startDate = date('Y-m-d');
                $endDate = date('Y-m-d');
                break;
            case 'week':
                $startDate = date('Y-m-d', strtotime('monday this week'));
                $endDate = date('Y-m-d', strtotime('sunday this week'));
                break;
            case 'month':
                $startDate = $startDate ?? date('Y-m-01');
                $endDate = $endDate ?? date('Y-m-t');
                break;
            case 'year':
                $startDate = date('Y-01-01');
                $endDate = date('Y-12-31');
                break;
            case 'custom':
                $startDate = $startDate ?? date('Y-m-01');
                $endDate = $endDate ?? date('Y-m-t');
                break;
            default:
                $startDate = date('Y-m-01');
                $endDate = date('Y-m-t');
        }

        // Get totals
        $totalExpense = $this->expenseModel->getTotalByUser($userId, $startDate, $endDate);
        $totalIncome = $this->incomeModel->getTotalByUser($userId, $startDate, $endDate);
        $balance = $totalIncome - $totalExpense;

        // Get expense summary by category
        $expenseSummary = $this->expenseModel->getSummaryByCategory($userId, $startDate, $endDate);
        $expenseSummary = $this->calculatePercentages($expenseSummary, $totalExpense);

        // Get income summary by category
        $incomeSummary = $this->getIncomeSummaryByCategory($userId, $startDate, $endDate);
        $incomeSummary = $this->calculatePercentages($incomeSummary, $totalIncome);

        // Get daily transactions for chart
        $dailyData = $this->getDailyData($userId, $startDate, $endDate);

        $data = [
            'summary' => [
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'balance' => $balance,
            ],
            'expense_by_category' => $expenseSummary,
            'income_by_category' => $incomeSummary,
            'daily_data' => $dailyData,
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'user' => Auth::user()
        ];

        require_once BASE_PATH . '/app/views/report/index.php';
    }

    /**
     * Get income summary by category
     */
    private function getIncomeSummaryByCategory($userId, $startDate, $endDate)
    {
        $db = Database::getInstance()->getConnection();

        $sql = "SELECT c.name, c.color, c.icon, SUM(i.amount) as total, COUNT(i.id) as count
                FROM incomes i
                LEFT JOIN categories c ON i.category_id = c.id
                WHERE i.user_id = :user_id
                AND i.income_date BETWEEN :start_date AND :end_date
                GROUP BY c.id, c.name, c.color, c.icon
                ORDER BY total DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Get daily income and expense data
     */
    private function getDailyData($userId, $startDate, $endDate)
    {
        $db = Database::getInstance()->getConnection();

        // Get daily expenses
        $sql = "SELECT DATE(expense_date) as date, SUM(amount) as total
                FROM expenses
                WHERE user_id = :user_id
                AND expense_date BETWEEN :start_date AND :end_date
                GROUP BY DATE(expense_date)
                ORDER BY date";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        $dailyExpenses = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // Get daily incomes
        $sql = "SELECT DATE(income_date) as date, SUM(amount) as total
                FROM incomes
                WHERE user_id = :user_id
                AND income_date BETWEEN :start_date AND :end_date
                GROUP BY DATE(income_date)
                ORDER BY date";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        $dailyIncomes = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // Merge data
        $result = [];
        $currentDate = new DateTime($startDate);
        $endDateTime = new DateTime($endDate);

        while ($currentDate <= $endDateTime) {
            $dateStr = $currentDate->format('Y-m-d');
            $result[] = [
                'date' => $dateStr,
                'expense' => $dailyExpenses[$dateStr] ?? 0,
                'income' => $dailyIncomes[$dateStr] ?? 0
            ];
            $currentDate->modify('+1 day');
        }

        return $result;
    }

    /**
     * Export report to CSV
     */
    public function export()
    {
        $userId = Auth::id();
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');

        // Get data
        $expenses = $this->expenseModel->getByDateRange($userId, $startDate, $endDate);
        $incomes = $this->incomeModel->getByDateRange($userId, $startDate, $endDate);

        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=report_' . $startDate . '_to_' . $endDate . '.csv');

        // Create output stream
        $output = fopen('php://output', 'w');

        // BOM for UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Headers for expenses
        fputcsv($output, ['รายจ่าย']);
        fputcsv($output, ['วันที่', 'หมวดหมู่', 'จำนวนเงิน', 'รายละเอียด']);

        foreach ($expenses as $expense) {
            fputcsv($output, [
                $expense['expense_date'],
                $expense['category_name'],
                $expense['amount'],
                $expense['description']
            ]);
        }

        fputcsv($output, []); // Empty line

        // Headers for incomes
        fputcsv($output, ['รายรับ']);
        fputcsv($output, ['วันที่', 'หมวดหมู่', 'จำนวนเงิน', 'รายละเอียด']);

        foreach ($incomes as $income) {
            fputcsv($output, [
                $income['income_date'],
                $income['category_name'],
                $income['amount'],
                $income['description']
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Calculate percentages for summary data
     */
    private function calculatePercentages($data, $total)
    {
        if (empty($data) || $total == 0) {
            return $data;
        }

        foreach ($data as &$item) {
            $item['percentage'] = ($item['total'] / $total) * 100;
        }

        return $data;
    }
}
