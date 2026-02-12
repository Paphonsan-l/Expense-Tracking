<?php
$title = 'แก้ไขรายจ่าย';
$currentPage = 'expense';
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
require_once BASE_PATH . '/app/views/layouts/header.php';
?>

<div class="main-wrapper">
    <?php require_once BASE_PATH . '/app/views/layouts/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header">
                <h1>
                    <i class="fas fa-edit"></i>
                    แก้ไขรายจ่าย
                </h1>
            </div>
            
            <div class="row">
                <div class="col-12 col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <form action="/expense/edit/<?php echo $data['expense']['id']; ?>" method="POST" enctype="multipart/form-data">
                                <?php echo Session::csrfField(); ?>
                                <input type="hidden" name="existing_receipt" value="<?php echo htmlspecialchars($data['expense']['receipt_path'] ?? ''); ?>">
                                
                                <div class="form-group">
                                    <label for="category_id">
                                        <i class="fas fa-tag"></i>
                                        หมวดหมู่ <span style="color: red;">*</span>
                                    </label>
                                    <select id="category_id" 
                                            name="category_id" 
                                            class="form-control <?php echo isset($errors['category_id']) ? 'is-invalid' : ''; ?>"
                                            required>
                                        <option value="">-- เลือกหมวดหมู่ --</option>
                                        <?php foreach ($data['categories'] as $category): ?>
                                            <option value="<?php echo $category['id']; ?>"
                                                    <?php echo $data['expense']['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['category_id'])): ?>
                                        <span class="error-message"><?php echo $errors['category_id']; ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="amount">
                                        <i class="fas fa-dollar-sign"></i>
                                        จำนวนเงิน (บาท) <span style="color: red;">*</span>
                                    </label>
                                    <input type="number" 
                                           id="amount" 
                                           name="amount" 
                                           class="form-control <?php echo isset($errors['amount']) ? 'is-invalid' : ''; ?>" 
                                           step="0.01"
                                           min="0"
                                           value="<?php echo htmlspecialchars($data['expense']['amount']); ?>"
                                           required>
                                    <?php if (isset($errors['amount'])): ?>
                                        <span class="error-message"><?php echo $errors['amount']; ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="expense_date">
                                        <i class="fas fa-calendar"></i>
                                        วันที่ <span style="color: red;">*</span>
                                    </label>
                                    <input type="date" 
                                           id="expense_date" 
                                           name="expense_date" 
                                           class="form-control <?php echo isset($errors['expense_date']) ? 'is-invalid' : ''; ?>" 
                                           value="<?php echo htmlspecialchars($data['expense']['expense_date']); ?>"
                                           required>
                                    <?php if (isset($errors['expense_date'])): ?>
                                        <span class="error-message"><?php echo $errors['expense_date']; ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="description">
                                        <i class="fas fa-comment"></i>
                                        รายละเอียด
                                    </label>
                                    <textarea id="description" 
                                              name="description" 
                                              class="form-control" 
                                              rows="3"><?php echo htmlspecialchars($data['expense']['description'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="receipt">
                                        <i class="fas fa-receipt"></i>
                                        ใบเสร็จ/หลักฐาน
                                    </label>
                                    <?php if (!empty($data['expense']['receipt_path'])): ?>
                                        <div class="mb-2">
                                            <span class="text-muted">ไฟล์ปัจจุบัน: </span>
                                            <a href="/assets/images/uploads/receipts/<?php echo $data['expense']['receipt_path']; ?>" 
                                               target="_blank">
                                                <i class="fas fa-file"></i> ดูไฟล์
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" 
                                           id="receipt" 
                                           name="receipt" 
                                           class="form-control"
                                           accept="image/*,.pdf">
                                    <small class="form-text text-muted">
                                        อัปโหลดไฟล์ใหม่เพื่อแทนที่ไฟล์เดิม (ถ้ามี)
                                    </small>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> บันทึก
                                    </button>
                                    <a href="/expense" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> ยกเลิก
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/navbar.php'; ?>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
