<?php
$title = 'รายงาน';
$currentPage = 'report';
require_once BASE_PATH . '/app/views/layouts/header.php';
?>

<div class="main-wrapper">
    <?php require_once BASE_PATH . '/app/views/layouts/sidebar.php'; ?>

    <main class="main-content">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header">
                <h1>
                    <i class="fas fa-chart-line"></i>
                    รายงานสรุป
                </h1>
                <div class="page-actions">
                    <form method="GET" action="/report/export" style="display: inline;">
                        <input type="hidden" name="period" value="<?php echo $data['period']; ?>">
                        <input type="hidden" name="start_date" value="<?php echo $data['start_date']; ?>">
                        <input type="hidden" name="end_date" value="<?php echo $data['end_date']; ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-download"></i> ดาวน์โหลด CSV
                        </button>
                    </form>
                </div>
            </div>

            <!-- Period Filter -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="/report" class="form-inline">
                        <div class="form-group">
                            <label for="period">ช่วงเวลา</label>
                            <select id="period" name="period" class="form-control" onchange="toggleCustomDates()">
                                <option value="today" <?php echo $data['period'] == 'today' ? 'selected' : ''; ?>>วันนี้
                                </option>
                                <option value="week" <?php echo $data['period'] == 'week' ? 'selected' : ''; ?>>สัปดาห์นี้
                                </option>
                                <option value="month" <?php echo $data['period'] == 'month' ? 'selected' : ''; ?>>เดือนนี้
                                </option>
                                <option value="year" <?php echo $data['period'] == 'year' ? 'selected' : ''; ?>>ปีนี้
                                </option>
                                <option value="custom" <?php echo $data['period'] == 'custom' ? 'selected' : ''; ?>>
                                    กำหนดเอง</option>
                            </select>
                        </div>

                        <div id="customDates"
                            style="display: <?php echo $data['period'] == 'custom' ? 'flex' : 'none'; ?>; gap: 1rem;">
                            <div class="form-group">
                                <label for="start_date">จากวันที่</label>
                                <input type="date" id="start_date" name="start_date" class="form-control"
                                    value="<?php echo $data['start_date']; ?>">
                            </div>

                            <div class="form-group">
                                <label for="end_date">ถึงวันที่</label>
                                <input type="date" id="end_date" name="end_date" class="form-control"
                                    value="<?php echo $data['end_date']; ?>">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-filter"></i> กรอง
                        </button>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div style="margin: 10px;">
                <div class="summary-cards" style="margin-bottom: 0;">
                    <div class="summary-card expense">
                        <div class="summary-card-icon">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <div class="summary-card-title">รวมรายจ่าย</div>
                        <h2 class="summary-card-value">
                            ฿<?php echo number_format($data['summary']['total_expense'], 2); ?>
                        </h2>
                    </div>

                    <div class="summary-card income">
                        <div class="summary-card-icon">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <div class="summary-card-title">รวมรายรับ</div>
                        <h2 class="summary-card-value">
                            ฿<?php echo number_format($data['summary']['total_income'], 2); ?>
                        </h2>
                    </div>

                    <div class="summary-card balance">
                        <div class="summary-card-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="summary-card-title">คงเหลือ</div>
                        <h2 class="summary-card-value">
                            ฿<?php echo number_format($data['summary']['balance'], 2); ?>
                        </h2>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3>
                                <i class="fas fa-pie-chart"></i>
                                รายจ่ายตามหมวดหมู่
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas id="expenseChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3>
                                <i class="fas fa-pie-chart"></i>
                                รายรับตามหมวดหมู่
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas id="incomeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Trend Chart -->
            <div class="card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-chart-line"></i>
                        กราฟแนวโน้มรายวัน
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <!-- Category Tables -->
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3>รายละเอียดรายจ่ายตามหมวดหมู่</h3>
                        </div>
                        <div class="card-body" style="padding: 0;">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>หมวดหมู่</th>
                                            <th class="text-right" style="width: 25%">จำนวนเงิน</th>
                                            <th class="text-right" style="width: 15%">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($data['expense_by_category'])): ?>
                                            <?php foreach ($data['expense_by_category'] as $item): ?>
                                                <tr>
                                                    <td>
                                                        <i class="fas fa-<?php echo htmlspecialchars($item['icon'] ?? 'tag'); ?>"
                                                            style="color: <?php echo htmlspecialchars($item['color'] ?? '#666666'); ?>"></i>
                                                        <?php echo htmlspecialchars($item['name'] ?? 'ไม่ระบุ'); ?>
                                                    </td>
                                                    <td class="text-right">
                                                        ฿<?php echo number_format($item['total'], 2); ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?php echo number_format($item['percentage'], 1); ?>%
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center">ไม่มีข้อมูล</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3>รายละเอียดรายรับตามหมวดหมู่</h3>
                        </div>
                        <div class="card-body" style="padding: 0;">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>หมวดหมู่</th>
                                            <th class="text-right" style="width: 25%">จำนวนเงิน</th>
                                            <th class="text-right" style="width: 15%">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($data['income_by_category'])): ?>
                                            <?php foreach ($data['income_by_category'] as $item): ?>
                                                <tr>
                                                    <td>
                                                        <i class="fas fa-<?php echo htmlspecialchars($item['icon'] ?? 'tag'); ?>"
                                                            style="color: <?php echo htmlspecialchars($item['color'] ?? '#666666'); ?>"></i>
                                                        <?php echo htmlspecialchars($item['name'] ?? 'ไม่ระบุ'); ?>
                                                    </td>
                                                    <td class="text-right">
                                                        ฿<?php echo number_format($item['total'], 2); ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?php echo number_format($item['percentage'], 1); ?>%
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center">ไม่มีข้อมูล</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/navbar.php'; ?>

<script>
    // Toggle custom dates visibility
    function toggleCustomDates() {
        const period = document.getElementById('period').value;
        const customDates = document.getElementById('customDates');
        customDates.style.display = period === 'custom' ? 'flex' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Expense by Category Chart
        const expenseData = <?php echo json_encode($data['expense_by_category']); ?>;
        if (expenseData && expenseData.length > 0) {
            const expenseCtx = document.getElementById('expenseChart').getContext('2d');
            new Chart(expenseCtx, {
                type: 'doughnut',
                data: {
                    labels: expenseData.map(item => item.name),
                    datasets: [{
                        data: expenseData.map(item => item.total),
                        backgroundColor: expenseData.map(item => item.color),
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.label + ': ฿' + context.parsed.toLocaleString('th-TH', { minimumFractionDigits: 2 });
                                }
                            }
                        }
                    }
                }
            });
        }

        // Income by Category Chart
        const incomeData = <?php echo json_encode($data['income_by_category']); ?>;
        if (incomeData && incomeData.length > 0) {
            const incomeCtx = document.getElementById('incomeChart').getContext('2d');
            new Chart(incomeCtx, {
                type: 'doughnut',
                data: {
                    labels: incomeData.map(item => item.name),
                    datasets: [{
                        data: incomeData.map(item => item.total),
                        backgroundColor: incomeData.map(item => item.color),
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.label + ': ฿' + context.parsed.toLocaleString('th-TH', { minimumFractionDigits: 2 });
                                }
                            }
                        }
                    }
                }
            });
        }

        // Daily Trend Chart
        const dailyData = <?php echo json_encode($data['daily_data']); ?>;
        if (dailyData && dailyData.length > 0) {
            const trendCtx = document.getElementById('trendChart').getContext('2d');
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: dailyData.map(item => item.date),
                    datasets: [
                        {
                            label: 'รายจ่าย',
                            data: dailyData.map(item => item.expense),
                            borderColor: '#e74c3c',
                            backgroundColor: 'rgba(231, 76, 60, 0.1)',
                            tension: 0.4
                        },
                        {
                            label: 'รายรับ',
                            data: dailyData.map(item => item.income),
                            borderColor: '#4CAF50',
                            backgroundColor: 'rgba(76, 175, 80, 0.1)',
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.dataset.label + ': ฿' + context.parsed.y.toLocaleString('th-TH', { minimumFractionDigits: 2 });
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function (value) {
                                        return '฿' + value.toLocaleString('th-TH');
                                    }
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>