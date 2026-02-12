<?php
$title = 'เข้าสู่ระบบ';
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
                <i class="fas fa-wallet"></i>
                <h1>Expense Tracking</h1>
                <p>เข้าสู่ระบบเพื่อจัดการรายรับ-รายจ่าย</p>
            </div>
            
            <?php if ($error = Session::flash('error')): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form action="/login" method="POST" class="auth-form">
                <?php echo Session::csrfField(); ?>
                
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i>
                        ชื่อผู้ใช้หรืออีเมล
                    </label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           class="form-control" 
                           placeholder="กรอกชื่อผู้ใช้หรืออีเมล"
                           required
                           autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        รหัสผ่าน
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           placeholder="กรอกรหัสผ่าน"
                           required>
                </div>
                
                <div class="form-group form-check">
                    <input type="checkbox" 
                           id="remember" 
                           name="remember" 
                           class="form-check-input">
                    <label for="remember" class="form-check-label">
                        จดจำการเข้าสู่ระบบ
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i>
                    เข้าสู่ระบบ
                </button>
                
                <div class="auth-footer">
                    <p>ยังไม่มีบัญชี? <a href="/register">ลงทะเบียน</a></p>
                    <p><a href="/forgot-password">ลืมรหัสผ่าน?</a></p>
                </div>
            </form>
        </div>
    </div>
    
    <script src="/assets/js/app.js"></script>
</body>
</html>
