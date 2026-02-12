<?php
$title = 'โปรไฟล์';
$currentPage = 'profile';
$errors = $_SESSION['errors'] ?? [];
$passwordErrors = $_SESSION['password_errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['password_errors'], $_SESSION['old']);
require_once BASE_PATH . '/app/views/layouts/header.php';

$user = Auth::user();
?>

<div class="main-wrapper">
    <?php require_once BASE_PATH . '/app/views/layouts/sidebar.php'; ?>

    <main class="main-content">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header">
                <h1>
                    <i class="fas fa-user"></i>
                    โปรไฟล์
                </h1>
            </div>

            <?php if ($message = Session::flash('success')): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($message = Session::flash('error')): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Profile Information -->
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3>
                                <i class="fas fa-user-edit"></i>
                                ข้อมูลส่วนตัว
                            </h3>
                        </div>
                        <div class="card-body">
                            <form action="/profile/update" method="POST" enctype="multipart/form-data">
                                <?php echo Session::csrfField(); ?>

                                <!-- Profile Image Preview -->
                                <div class="form-group" style="text-align: center; margin-bottom: var(--spacing-lg);">
                                    <?php if (!empty($user['profile_image'])): ?>
                                        <img src="/<?php echo htmlspecialchars($user['profile_image'] ?? ''); ?>"
                                            alt="Profile"
                                            style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%; border: 4px solid var(--primary-color);">
                                    <?php else:
                                        $nameParts = explode(' ', $user['full_name']);
                                        $initials = '';
                                        foreach ($nameParts as $part) {
                                            if (!empty($part)) {
                                                $initials .= mb_substr($part, 0, 1);
                                            }
                                        }
                                        $initials = mb_strtoupper(mb_substr($initials, 0, 2));
                                        ?>
                                        <div
                                            style="width: 150px; height: 150px; margin: 0 auto; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; font-size: 48px; color: white; font-weight: bold; border: 4px solid var(--primary-color);">
                                            <?php echo $initials; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="profile_image">
                                        <i class="fas fa-image"></i>
                                        รูปโปรไฟล์
                                    </label>
                                    <input type="file" id="profile_image" name="profile_image" class="form-control"
                                        accept="image/jpeg,image/jpg,image/png">
                                    <?php if (isset($errors['profile_image'])): ?>
                                        <span class="error-message"><?php echo $errors['profile_image']; ?></span>
                                    <?php endif; ?>
                                    <small style="color: #888;">JPG หรือ PNG สูงสุด 5MB</small>
                                </div>

                                <div class="form-group">
                                    <label for="full_name">
                                        <i class="fas fa-user"></i>
                                        ชื่อ-นามสกุล <span style="color: red;">*</span>
                                    </label>
                                    <input type="text" id="full_name" name="full_name"
                                        class="form-control <?php echo isset($errors['full_name']) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo htmlspecialchars($old['full_name'] ?? $user['full_name']); ?>"
                                        required>
                                    <?php if (isset($errors['full_name'])): ?>
                                        <span class="error-message"><?php echo $errors['full_name']; ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="email">
                                        <i class="fas fa-envelope"></i>
                                        อีเมล <span style="color: red;">*</span>
                                    </label>
                                    <input type="email" id="email" name="email"
                                        class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo htmlspecialchars($old['email'] ?? $user['email']); ?>"
                                        required>
                                    <?php if (isset($errors['email'])): ?>
                                        <span class="error-message"><?php echo $errors['email']; ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> บันทึก
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3>
                                <i class="fas fa-lock"></i>
                                เปลี่ยนรหัสผ่าน
                            </h3>
                        </div>
                        <div class="card-body">
                            <form action="/profile/changePassword" method="POST">
                                <?php echo Session::csrfField(); ?>

                                <div class="form-group">
                                    <label for="current_password">
                                        <i class="fas fa-key"></i>
                                        รหัสผ่านปัจจุบัน <span style="color: red;">*</span>
                                    </label>
                                    <input type="password" id="current_password" name="current_password"
                                        class="form-control <?php echo isset($passwordErrors['current_password']) ? 'is-invalid' : ''; ?>"
                                        required>
                                    <?php if (isset($passwordErrors['current_password'])): ?>
                                        <span
                                            class="error-message"><?php echo $passwordErrors['current_password']; ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="new_password">
                                        <i class="fas fa-lock"></i>
                                        รหัสผ่านใหม่ <span style="color: red;">*</span>
                                    </label>
                                    <input type="password" id="new_password" name="new_password"
                                        class="form-control <?php echo isset($passwordErrors['new_password']) ? 'is-invalid' : ''; ?>"
                                        required>
                                    <?php if (isset($passwordErrors['new_password'])): ?>
                                        <span class="error-message"><?php echo $passwordErrors['new_password']; ?></span>
                                    <?php else: ?>
                                        <small style="color: #888;">อย่างน้อย 6 ตัวอักษร</small>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="confirm_password">
                                        <i class="fas fa-lock"></i>
                                        ยืนยันรหัสผ่านใหม่ <span style="color: red;">*</span>
                                    </label>
                                    <input type="password" id="confirm_password" name="confirm_password"
                                        class="form-control <?php echo isset($passwordErrors['confirm_password']) ? 'is-invalid' : ''; ?>"
                                        required>
                                    <?php if (isset($passwordErrors['confirm_password'])): ?>
                                        <span
                                            class="error-message"><?php echo $passwordErrors['confirm_password']; ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-key"></i> เปลี่ยนรหัสผ่าน
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Account Info -->
                    <div class="card">
                        <div class="card-header">
                            <h3>
                                <i class="fas fa-info-circle"></i>
                                ข้อมูลบัญชี
                            </h3>
                        </div>
                        <div class="card-body">
                            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                            <p><strong>สมัครสมาชิกเมื่อ:</strong>
                                <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/navbar.php'; ?>
<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>