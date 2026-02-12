<?php
$title = 'หน้าหลัก';
$currentPage = 'dashboard';
require_once BASE_PATH . '/app/views/layouts/header.php';
?>

<div class="main-wrapper">
    <?php require_once BASE_PATH . '/app/views/layouts/sidebar.php'; ?>

    <main class="main-content">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header">
                <h1>
                    <i class="fas fa-home"></i>
                    Dashboard
                </h1>
                <p class="text-secondary">ภาพรวมการเงินของคุณ - <?php echo $data['current_month']; ?></p>
            </div>

            <!-- Summary Cards -->
            <div class="summary-cards">
                <div class="summary-card income">
                    <div class="summary-card-icon">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <div class="summary-card-title">รายรับ</div>
                    <h2 class="summary-card-value">
                        ฿<?php echo number_format($data['total_income'], 2); ?>
                    </h2>
                </div>

                <div class="summary-card expense">
                    <div class="summary-card-icon">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <div class="summary-card-title">รายจ่าย</div>
                    <h2 class="summary-card-value">
                        ฿<?php echo number_format($data['total_expense'], 2); ?>
                    </h2>
                </div>

                <div class="summary-card balance">
                    <div class="summary-card-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="summary-card-title">คงเหลือ</div>
                    <h2 class="summary-card-value">
                        ฿<?php echo number_format($data['balance'], 2); ?>
                    </h2>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <div class="btn-group" role="group">
                        <a href="?type=expense"
                            class="btn <?php echo ($data['view_type'] == 'expense') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            รายจ่าย (Expenses)
                        </a>
                        <a href="?type=income"
                            class="btn <?php echo ($data['view_type'] == 'income') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            รายรับ (Incomes)
                        </a>
                    </div>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="row">
                <!-- Recent Transactions -->
                <div class="col-12 col-md-6">
                    <?php if ($data['view_type'] == 'expense'): ?>
                        <!-- Recent Expenses -->
                        <div class="card">
                            <div class="card-header">
                                <h3>
                                    <i class="fas fa-history"></i>
                                    รายการล่าสุด
                                </h3>
                            </div>
                            <div class="card-body" style="padding: 0;">
                                <?php if (!empty($data['recent_expenses'])): ?>
                                    <?php foreach ($data['recent_expenses'] as $expense): ?>
                                        <div class="transaction-card">
                                            <div class="transaction-icon expense">
                                                <i
                                                    class="fas fa-<?php echo htmlspecialchars($expense['category_icon'] ?? 'circle'); ?>"></i>
                                            </div>
                                            <div class="transaction-info">
                                                <div class="transaction-category">
                                                    <?php echo htmlspecialchars($expense['category_name'] ?? 'ไม่ระบุ'); ?>
                                                </div>
                                                <div class="transaction-description">
                                                    <?php echo htmlspecialchars($expense['description'] ?? '-'); ?>
                                                </div>
                                                <div class="transaction-date">
                                                    <?php echo date('d/m/Y', strtotime($expense['expense_date'])); ?>
                                                </div>
                                            </div>
                                            <div class="transaction-amount expense">
                                                -฿<?php echo number_format($expense['amount'], 2); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="fas fa-inbox"></i>
                                        </div>
                                        <div class="empty-state-title">ยังไม่มีรายการ</div>
                                        <div class="empty-state-description">
                                            เริ่มต้นเพิ่มรายจ่ายของคุณ
                                        </div>
                                        <a href="/expense/create" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> เพิ่มรายจ่าย
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($data['recent_expenses'])): ?>
                                <div class="card-footer">
                                    <a href="/expense" class="btn btn-secondary btn-block">
                                        ดูทั้งหมด
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <!-- Recent Incomes -->
                        <div class="card">
                            <div class="card-header">
                                <h3>
                                    <i class="fas fa-history"></i>
                                    รายรับล่าสุด
                                </h3>
                            </div>
                            <div class="card-body" style="padding: 0;">
                                <?php if (!empty($data['recent_incomes'])): ?>
                                    <?php foreach ($data['recent_incomes'] as $income): ?>
                                        <div class="transaction-card">
                                            <div class="transaction-icon income"
                                                style="background-color: <?php echo htmlspecialchars($income['category_color'] ?? '#28a745'); ?>20; color: <?php echo htmlspecialchars($income['category_color'] ?? '#28a745'); ?>;">
                                                <i
                                                    class="fas fa-<?php echo htmlspecialchars($income['category_icon'] ?? 'circle'); ?>"></i>
                                            </div>
                                            <div class="transaction-info">
                                                <div class="transaction-category">
                                                    <?php echo htmlspecialchars($income['category_name'] ?? 'ไม่ระบุ'); ?>
                                                </div>
                                                <div class="transaction-description">
                                                    <?php echo htmlspecialchars($income['description'] ?? '-'); ?>
                                                </div>
                                                <div class="transaction-date">
                                                    <?php echo date('d/m/Y', strtotime($income['income_date'])); ?>
                                                </div>
                                            </div>
                                            <div class="transaction-amount income">
                                                +฿<?php echo number_format($income['amount'], 2); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="fas fa-inbox"></i>
                                        </div>
                                        <div class="empty-state-title">ยังไม่มีรายการ</div>
                                        <div class="empty-state-description">
                                            เริ่มต้นเพิ่มรายรับของคุณ
                                        </div>
                                        <a href="/income/create" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> เพิ่มรายรับ
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($data['recent_incomes'])): ?>
                                <div class="card-footer">
                                    <a href="/income" class="btn btn-secondary btn-block">
                                        ดูทั้งหมด
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Charts Column -->
                <div class="col-12 col-md-6">
                    <?php if ($data['view_type'] == 'expense'): ?>
                        <!-- Expense Chart -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3>
                                    <i class="fas fa-chart-pie"></i>
                                    รายจ่ายตามหมวดหมู่
                                </h3>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($data['expense_category_summary'])): ?>
                                    <canvas id="expenseChart"></canvas>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function () {
                                            const ctx = document.getElementById('expenseChart').getContext('2d');
                                            new Chart(ctx, {
                                                type: 'doughnut',
                                                data: {
                                                    labels: <?php echo json_encode(array_column($data['expense_category_summary'], 'name')); ?>,
                                                    datasets: [{
                                                        data: <?php echo json_encode(array_column($data['expense_category_summary'], 'total')); ?>,
                                                        backgroundColor: <?php echo json_encode(array_column($data['expense_category_summary'], 'color')); ?>,
                                                    }]
                                                },
                                                options: {
                                                    responsive: true,
                                                    maintainAspectRatio: true,
                                                    plugins: {
                                                        legend: { position: 'bottom' },
                                                        tooltip: {
                                                            callbacks: {
                                                                label: function (context) {
                                                                    let label = context.label || '';
                                                                    if (label) { label += ': '; }
                                                                    label += '฿' + context.parsed.toLocaleString('th-TH', { minimumFractionDigits: 2 });
                                                                    return label;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            });
                                        });
                                    </script>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="fas fa-chart-pie"></i></div>
                                        <div class="empty-state-title">ยังไม่มีข้อมูล</div>
                                        <div class="empty-state-description">เริ่มต้นบันทึกรายจ่ายเพื่อดูกราฟ</div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Income Chart -->
                        <div class="card">
                            <div class="card-header">
                                <h3>
                                    <i class="fas fa-chart-pie"></i>
                                    รายรับตามหมวดหมู่
                                </h3>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($data['income_category_summary'])): ?>
                                    <canvas id="incomeChart"></canvas>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function () {
                                            const ctx = document.getElementById('incomeChart').getContext('2d');
                                            new Chart(ctx, {
                                                type: 'doughnut',
                                                data: {
                                                    labels: <?php echo json_encode(array_column($data['income_category_summary'], 'name')); ?>,
                                                    datasets: [{
                                                        data: <?php echo json_encode(array_column($data['income_category_summary'], 'total')); ?>,
                                                        backgroundColor: <?php echo json_encode(array_column($data['income_category_summary'], 'color')); ?>,
                                                    }]
                                                },
                                                options: {
                                                    responsive: true,
                                                    maintainAspectRatio: true,
                                                    plugins: {
                                                        legend: { position: 'bottom' },
                                                        tooltip: {
                                                            callbacks: {
                                                                label: function (context) {
                                                                    let label = context.label || '';
                                                                    if (label) { label += ': '; }
                                                                    label += '฿' + context.parsed.toLocaleString('th-TH', { minimumFractionDigits: 2 });
                                                                    return label;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            });
                                        });
                                    </script>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="fas fa-chart-pie"></i></div>
                                        <div class="empty-state-title">ยังไม่มีข้อมูล</div>
                                        <div class="empty-state-description">เริ่มต้นบันทึกรายรับเพื่อดูกราฟ</div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-bolt"></i>
                        เมนูด่วน
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row" style="margin: 0 -16px; margin-bottom: -16px;">
                        <div class="col-12 col-md-4 mb-3 p-3">
                            <a href="/expense/create" class="btn btn-primary btn-block">
                                <i class="fas fa-plus"></i>
                                เพิ่มรายจ่าย
                            </a>
                        </div>
                        <div class="col-12 col-md-4 mb-3 p-3">
                            <a href="/income/create" class="btn btn-primary btn-block">
                                <i class="fas fa-plus"></i>
                                เพิ่มรายรับ
                            </a>
                        </div>
                        <div class="col-12 col-md-4 mb-3 p-3">
                            <a href="/report" class="btn btn-info btn-block">
                                <i class="fas fa-file-alt"></i>
                                ดูรายงาน
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/navbar.php'; ?>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>