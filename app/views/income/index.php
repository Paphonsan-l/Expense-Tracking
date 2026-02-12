<?php
$title = 'รายรับ';
$currentPage = 'income';
require_once BASE_PATH . '/app/views/layouts/header.php';
?>

<div class="main-wrapper">
    <?php require_once BASE_PATH . '/app/views/layouts/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header">
                <h1>
                    <i class="fas fa-hand-holding-dollar"></i>
                    จัดการรายรับ
                </h1>
                <div class="page-actions">
                    <a href="/income/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> เพิ่มรายรับ
                    </a>
                </div>
            </div>
            
            <!-- Filter Card -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="/income" class="form-inline">
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
                    <div class="summary-card income">
                        <div class="summary-card-icon">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <div class="summary-card-title">รวมรายรับ</div>
                        <h2 class="summary-card-value">
                            ฿<?php echo number_format($data['total_income'], 2); ?>
                        </h2>
                    </div>
                </div>
            </div>
            
            <!-- Incomes List -->
            <div class="card">
                <div class="card-header">
                    <h3>รายการทั้งหมด (<?php echo count($data['incomes']); ?> รายการ)</h3>
                </div>
                <div class="card-body" style="padding: 0;">
                    <?php if (!empty($data['incomes'])): ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>วันที่</th>
                                            <th>หมวดหมู่</th>
                                            <th>รายละเอียด</th>
                                            <th class="text-right">จำนวนเงิน</th>
                                            <th class="text-center">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['incomes'] as $income): ?>
                                                <tr>
                                                    <td><?php echo date('d/m/Y', strtotime($income['income_date'])); ?></td>
                                                    <td>
                                                        <span style="color: <?php echo htmlspecialchars($income['category_color'] ?? '#666666'); ?>">
                                                            <i class="fas fa-<?php echo htmlspecialchars($income['category_icon'] ?? 'tag'); ?>"></i>
                                                            <?php echo htmlspecialchars($income['category_name'] ?? 'ไม่ระบุ'); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($income['description'] ?? '-'); ?></td>
                                                    <td class="text-right">
                                                        <strong style="color: var(--success-color);">
                                                            ฿<?php echo number_format($income['amount'], 2); ?>
                                                        </strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="/income/edit/<?php echo $income['id']; ?>" 
                                                           class="btn btn-sm btn-warning btn-icon">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="/income/delete/<?php echo $income['id']; ?>" method="POST" style="display:inline;">
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
                                <div class="empty-state-title">ยังไม่มีรายรับ</div>
                                <div class="empty-state-description">
                                    เริ่มต้นบันทึกรายรับของคุณ
                                </div>
                                <a href="/income/create" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> เพิ่มรายรับ
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
function deleteIncome(id) {
    if (confirm('คุณต้องการลบรายการนี้หรือไม่?')) {
        window.location.href = '/income/delete/' + id;
    }
}
</script>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
