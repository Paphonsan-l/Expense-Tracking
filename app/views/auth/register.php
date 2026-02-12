<?php
$title = 'ลงทะเบียน';
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - <?php echo APP_NAME; ?></title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/components/form.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <i class="fas fa-user-plus"></i>
                <h1>สร้างบัญชีใหม่</h1>
                <p>ลงทะเบียนเพื่อเริ่มต้นใช้งาน</p>
            </div>
            
            <form action="/register" method="POST" class="auth-form">
                <?php echo Session::csrfField(); ?>
                
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i>
                        ชื่อผู้ใช้
                    </label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" 
                           placeholder="กรอกชื่อผู้ใช้"
                           value="<?php echo htmlspecialchars($old['username'] ?? ''); ?>"
                           required>
                    <?php if (isset($errors['username'])): ?>
                        <span class="error-message"><?php echo $errors['username']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        อีเมล
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                           placeholder="กรอกอีเมล"
                           value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>"
                           required>
                    <?php if (isset($errors['email'])): ?>
                        <span class="error-message"><?php echo $errors['email']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="full_name">
                        <i class="fas fa-id-card"></i>
                        ชื่อ-นามสกุล
                    </label>
                    <input type="text" 
                           id="full_name" 
                           name="full_name" 
                           class="form-control <?php echo isset($errors['full_name']) ? 'is-invalid' : ''; ?>" 
                           placeholder="กรอกชื่อ-นามสกุล"
                           value="<?php echo htmlspecialchars($old['full_name'] ?? ''); ?>"
                           required>
                    <?php if (isset($errors['full_name'])): ?>
                        <span class="error-message"><?php echo $errors['full_name']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        รหัสผ่าน
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                           placeholder="กรอกรหัสผ่าน (อย่างน้อย 6 ตัวอักษร)"
                           required>
                    <?php if (isset($errors['password'])): ?>
                        <span class="error-message"><?php echo $errors['password']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="password_confirm">
                        <i class="fas fa-lock"></i>
                        ยืนยันรหัสผ่าน
                    </label>
                    <input type="password" 
                           id="password_confirm" 
                           name="password_confirm" 
                           class="form-control <?php echo isset($errors['password_confirm']) ? 'is-invalid' : ''; ?>" 
                           placeholder="กรอกรหัสผ่านอีกครั้ง"
                           required>
                    <?php if (isset($errors['password_confirm'])): ?>
                        <span class="error-message"><?php echo $errors['password_confirm']; ?></span>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-user-plus"></i>
                    ลงทะเบียน
                </button>
                
                <div class="auth-footer">
                    <p>มีบัญชีอยู่แล้ว? <a href="/login">เข้าสู่ระบบ</a></p>
                </div>
            </form>
        </div>
    </div>
    
    <script src="/assets/js/app.js"></script>
</body>
</html>
