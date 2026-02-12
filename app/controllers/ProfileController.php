<?php
/**
 * Profile Controller
 */

require_once BASE_PATH . '/app/models/User.php';
require_once BASE_PATH . '/app/helpers/Validator.php';

class ProfileController
{
    private $userModel;

    public function __construct()
    {
        Auth::require(); // Require authentication
        $this->userModel = new User();
    }

    /**
     * Show profile page
     */
    public function index()
    {
        $user = Auth::user();

        require_once BASE_PATH . '/app/views/profile/index.php';
    }

    /**
     * Update profile
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /profile');
            exit;
        }

        // Verify CSRF token
        if (!Session::verifyToken($_POST['csrf_token'] ?? '')) {
            Session::error('Invalid request');
            header('Location: /profile');
            exit;
        }

        $userId = Auth::id();
        $data = [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
        ];

        // Validate
        $errors = [];
        $validator = new Validator();

        if (empty($data['full_name'])) {
            $errors['full_name'] = 'กรุณากรอกชื่อ-นามสกุล';
        }

        if (!$validator->email('email', $data['email'])) {
            $errors['email'] = 'รูปแบบอีเมลไม่ถูกต้อง';
        } else {
            // Check if email already exists (exclude current user)
            $existingUser = $this->userModel->findByEmail($data['email']);
            if ($existingUser && $existingUser['id'] != $userId) {
                $errors['email'] = 'อีเมลนี้มีผู้ใช้แล้ว';
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            header('Location: /profile');
            exit;
        }

        // Update profile
        $updateData = [
            'full_name' => $data['full_name'],
            'email' => $data['email']
        ];

        // Handle profile image upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadProfileImage($_FILES['profile_image']);

            if ($uploadResult['success']) {
                $updateData['profile_image'] = $uploadResult['path'];

                // Delete old image if exists
                $currentUser = Auth::user();
                if (!empty($currentUser['profile_image']) && file_exists(BASE_PATH . '/public/' . $currentUser['profile_image'])) {
                    // Note: We need to handle the public path correctly if stored relative to public
                    // The uploaded path is now relative like 'assets/...'
                    unlink(BASE_PATH . '/public/' . $currentUser['profile_image']);
                } elseif (!empty($currentUser['profile_image']) && file_exists(BASE_PATH . '/' . $currentUser['profile_image'])) {
                    // Fallback for legacy paths
                    unlink(BASE_PATH . '/' . $currentUser['profile_image']);
                }
            } else {
                $_SESSION['errors'] = ['profile_image' => $uploadResult['message']];
                header('Location: /profile');
                exit;
            }
        }

        $result = $this->userModel->update($userId, $updateData);

        if ($result) {
            // Update session data
            $_SESSION['user'] = $this->userModel->findById($userId);
            Session::success('อัปเดตข้อมูลสำเร็จ');
        } else {
            Session::error('ไม่สามารถอัปเดตข้อมูลได้');
        }

        header('Location: /profile');
        exit;
    }

    /**
     * Change password
     */
    public function changePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /profile');
            exit;
        }

        // Verify CSRF token
        if (!Session::verifyToken($_POST['csrf_token'] ?? '')) {
            Session::error('Invalid request');
            header('Location: /profile');
            exit;
        }

        $userId = Auth::id();
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate
        $errors = [];
        $validator = new Validator();

        if (empty($currentPassword)) {
            $errors['current_password'] = 'กรุณากรอกรหัสผ่านปัจจุบัน';
        } else {
            // Verify current password
            $user = $this->userModel->findWithPasswordById($userId);
            if (!password_verify($currentPassword, $user['password'])) {
                $errors['current_password'] = 'รหัสผ่านปัจจุบันไม่ถูกต้อง';
            }
        }

        if (!$validator->minLength('new_password', $newPassword, 6)) {
            $errors['new_password'] = 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร';
        }

        if ($newPassword !== $confirmPassword) {
            $errors['confirm_password'] = 'รหัสผ่านไม่ตรงกัน';
        }

        if (!empty($errors)) {
            $_SESSION['password_errors'] = $errors;
            header('Location: /profile');
            exit;
        }

        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $result = $this->userModel->update($userId, ['password' => $hashedPassword]);

        if ($result) {
            Session::success('เปลี่ยนรหัสผ่านสำเร็จ');
        } else {
            Session::error('ไม่สามารถเปลี่ยนรหัสผ่านได้');
        }

        header('Location: /profile');
        exit;
    }

    /**
     * Upload profile image
     */
    private function uploadProfileImage($file)
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $maxSize = MAX_FILE_SIZE; // 5MB from config

        // Validate file type
        if (!in_array($file['type'], $allowedTypes)) {
            return [
                'success' => false,
                'message' => 'ไฟล์ต้องเป็นรูปภาพ JPG หรือ PNG เท่านั้น'
            ];
        }

        // Validate file size
        if ($file['size'] > $maxSize) {
            return [
                'success' => false,
                'message' => 'ขนาดไฟล์ต้องไม่เกิน ' . ($maxSize / 1024 / 1024) . 'MB'
            ];
        }

        // Create upload directory if not exists
        // Use standard path for profile images
        $uploadDir = BASE_PATH . '/public/assets/images/uploads/profile';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'profile_' . Auth::id() . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . '/' . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'success' => true,
                'path' => 'assets/images/uploads/profile/' . $filename
            ];
        }

        return [
            'success' => false,
            'message' => 'ไม่สามารถอัปโหลดไฟล์ได้'
        ];
    }
}
