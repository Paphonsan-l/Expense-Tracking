<?php
/**
 * Authentication Controller
 */

require_once BASE_PATH . '/app/models/User.php';
require_once BASE_PATH . '/app/models/Category.php';

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Index method - redirect to login
     */
    public function index()
    {
        $this->login();
    }

    /**
     * Show login form
     */
    public function login()
    {
        Auth::guest(); // Redirect if already logged in

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleLogin();
        }

        require_once BASE_PATH . '/app/views/auth/login.php';
    }

    /**
     * Handle login
     */
    private function handleLogin()
    {
        $validator = new Validator();

        $username = Validator::sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        // Validation
        $validator->required('username', $username);
        $validator->required('password', $password);

        if ($validator->fails()) {
            Session::error('กรุณากรอกข้อมูลให้ครบถ้วน');
            require_once BASE_PATH . '/app/views/auth/login.php';
            return;
        }

        // Find user
        $user = $this->userModel->findByUsername($username);

        if (!$user) {
            $user = $this->userModel->findByEmail($username);
        }

        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            Session::error('ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
            require_once BASE_PATH . '/app/views/auth/login.php';
            return;
        }

        // Login successful
        Auth::login($user['id'], $remember);
        Session::success('เข้าสู่ระบบสำเร็จ');
        header('Location: /dashboard');
        exit;
    }

    /**
     * Show register form
     */
    public function register()
    {
        Auth::guest(); // Redirect if already logged in

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleRegister();
        }

        require_once BASE_PATH . '/app/views/auth/register.php';
    }

    /**
     * Handle registration
     */
    private function handleRegister()
    {
        $validator = new Validator();

        $data = [
            'username' => Validator::sanitize($_POST['username'] ?? ''),
            'email' => Validator::sanitize($_POST['email'] ?? ''),
            'full_name' => Validator::sanitize($_POST['full_name'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? ''
        ];

        // Validation
        $validator->required('username', $data['username']);
        $validator->minLength('username', $data['username'], 3);
        $validator->maxLength('username', $data['username'], 50);
        $validator->unique('username', $data['username'], 'users', 'username');

        $validator->required('email', $data['email']);
        $validator->email('email', $data['email']);
        $validator->unique('email', $data['email'], 'users', 'email');

        $validator->required('full_name', $data['full_name']);

        $validator->required('password', $data['password']);
        $validator->minLength('password', $data['password'], 6);
        $validator->match('password_confirm', $data['password_confirm'], $data['password'], 'รหัสผ่านไม่ตรงกัน');

        if ($validator->fails()) {
            $_SESSION['errors'] = $validator->getErrors();
            $_SESSION['old'] = $data;
            require_once BASE_PATH . '/app/views/auth/register.php';
            return;
        }

        // Create user
        $userId = $this->userModel->create($data);

        if ($userId) {
            // Create default categories for new user
            $categoryModel = new Category();
            $categoryModel->createDefaults($userId);

            // Login automatically
            Auth::login($userId);
            Session::success('ลงทะเบียนสำเร็จ');
            header('Location: /dashboard');
            exit;
        } else {
            Session::error('เกิดข้อผิดพลาดในการลงทะเบียน');
            require_once BASE_PATH . '/app/views/auth/register.php';
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        Auth::logout();
        Session::success('ออกจากระบบเรียบร้อย');
        header('Location: /login');
        exit;
    }
}
