<?php
$title = 'รายจ่าย';
$currentPage = 'expense';
require_once BASE_PATH . '/app/views/layouts/header.php';
?>

<div class="main-wrapper">
    <?php require_once BASE_PATH . '/app/views/layouts/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header">
                <h1>
                    <i class="fas fa-money-bill-wave"></i>
                    จัดการรายจ่าย
                </h1>
                <div class="page-actions">
                    <a href="/expense/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> เพิ่มรายจ่าย
                    </a>
                </div>
            </div>
            
            <!-- Filter Card -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="/expense" class="form-inline">
                        <div class="form-group">
                            <label for="start_date">จากวันที่</label>
                            <input type="date" 
                                   id="start_date" 
                                   name="start_date" 
                                   class="form-control" 
                                   value="<?php echo $data['start_date']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="end_date">ถึงวันที่</label>
                            <input type="date" 
                                   id="end_date" 
                                   name="end_date" 
                                   class="form-control" 
                                   value="<?php echo $data['end_date']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="category_id">หมวดหมู่</label>
                            <select id="category_id" name="category_id" class="form-control">
                                <option value="">ทั้งหมด</option>
                                <?php foreach ($data['categories'] as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"
                                            <?php echo $data['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-filter"></i> กรอง
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Summary Card -->
            <div style="margin: 10px;">
                <div class="summary-cards" style="margin-bottom: 0;">
                    <div class="summary-card expense">
                        <div class="summary-card-icon">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <div class="summary-card-title">รวมรายจ่าย</div>
                        <h2 class="summary-card-value">
                            ฿<?php echo number_format($data['total_expense'], 2); ?>
                        </h2>
                    </div>
                </div>
            </div>
            
            <!-- Expenses List -->
            <div class="card">
                <div class="card-header">
                    <h3>รายการทั้งหมด (<?php echo count($data['expenses']); ?> รายการ)</h3>
                </div>
                <div class="card-body" style="padding: 0;">
                    <?php if (!empty($data['expenses'])): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>วันที่</th>
                                        <th>หมวดหมู่</th>
                                        <th>รายละเอียด</th>
                                        <th class="text-right">จำนวนเงิน</th>
                                        <th>ใบเสร็จ</th>
                                        <th class="text-center">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['expenses'] as $expense): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($expense['expense_date'])); ?></td>
                                            <td>
                                                <span style="color: <?php echo htmlspecialchars($expense['category_color'] ?? '#666666'); ?>">
                                                    <i class="fas fa-<?php echo htmlspecialchars($expense['category_icon'] ?? 'tag'); ?>"></i>
                                                    <?php echo htmlspecialchars($expense['category_name'] ?? 'ไม่ระบุ'); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($expense['description'] ?? '-'); ?></td>
                                            <td class="text-right">
                                                <strong style="color: var(--danger-color);">
                                                    ฿<?php echo number_format($expense['amount'], 2); ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <?php if ($expense['receipt_path']): ?>
                                                    <a href="/assets/images/uploads/receipts/<?php echo $expense['receipt_path']; ?>" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-info">
                                                        <i class="fas fa-file"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-light">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="/expense/edit/<?php echo $expense['id']; ?>" 
                                                   class="btn btn-sm btn-warning btn-icon">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="/expense/delete/<?php echo $expense['id']; ?>" method="POST" style="display:inline;">
                                                    <button type="submit" class="btn btn-sm btn-danger btn-icon" onclick="return confirmDelete(event)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <div class="empty-state-title">ยังไม่มีรายจ่าย</div>
                            <div class="empty-state-description">
                                เริ่มต้นบันทึกรายจ่ายของคุณ
                            </div>
                            <a href="/expense/create" class="btn btn-primary">
                                <i class="fas fa-plus"></i> เพิ่มรายจ่าย
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/navbar.php'; ?>

<script>
function deleteExpense(id) {
    if (confirm('คุณต้องการลบรายการนี้หรือไม่?')) {
        window.location.href = '/expense/delete/' + id;
    }
}
</script>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
