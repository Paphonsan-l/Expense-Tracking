<?php
$title = 'เพิ่มรายรับ';
$currentPage = 'income';
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
require_once BASE_PATH . '/app/views/layouts/header.php';
?>

<div class="main-wrapper">
    <?php require_once BASE_PATH . '/app/views/layouts/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header">
                <h1>
                    <i class="fas fa-plus"></i>
                    เพิ่มรายรับ
                </h1>
            </div>
            
            <div class="row">
                <div class="col-12 col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <form action="/income/create" method="POST">
                                <?php echo Session::csrfField(); ?>
                                
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
                                                    <?php echo ($old['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
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
                                           placeholder="0.00"
                                           step="0.01"
                                           min="0"
                                           value="<?php echo htmlspecialchars($old['amount'] ?? ''); ?>"
                                           required>
                                    <?php if (isset($errors['amount'])): ?>
                                        <span class="error-message"><?php echo $errors['amount']; ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="income_date">
                                        <i class="fas fa-calendar"></i>
                                        วันที่ <span style="color: red;">*</span>
                                    </label>
                                    <input type="date" 
                                           id="income_date" 
                                           name="income_date" 
                                           class="form-control <?php echo isset($errors['income_date']) ? 'is-invalid' : ''; ?>" 
                                           value="<?php echo htmlspecialchars($old['income_date'] ?? date('Y-m-d')); ?>"
                                           required>
                                    <?php if (isset($errors['income_date'])): ?>
                                        <span class="error-message"><?php echo $errors['income_date']; ?></span>
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
                                              rows="3"
                                              placeholder="เพิ่มรายละเอียด (ถ้ามี)"><?php echo htmlspecialchars($old['description'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> บันทึก
                                    </button>
                                    <a href="/income" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> ยกเลิก
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3>
                                <i class="fas fa-info-circle"></i>
                                คำแนะนำ
                            </h3>
                        </div>
                        <div class="card-body">
                            <ul style="padding-left: 20px;">
                                <li>เลือกหมวดหมู่ที่ตรงกับประเภทรายรับ</li>
                                <li>กรอกจำนวนเงินที่แน่นอน</li>
                                <li>เพิ่มรายละเอียดเพื่อง่ายต่อการจำ</li>
                                <li>บันทึกรายรับทุกครั้งเพื่อติดตามยอดคงเหลือ</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/navbar.php'; ?>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
