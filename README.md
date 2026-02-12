# Expense Tracking Application

แอปพลิเคชันสำหรับจัดการและติดตามรายรับรายจ่ายส่วนบุคคล พัฒนาด้วย PHP, MySQL และ Docker

## 📋 สารบัญ

- [Tech Stack](#tech-stack)
- [Features](#features)
- [โครงสร้างโปรเจค](#โครงสร้างโปรเจค)
- [Database Schema](#database-schema)
- [การติดตั้งและใช้งาน](#การติดตั้งและใช้งาน)
- [Docker Setup](#docker-setup)
- [การใช้งาน](#การใช้งาน)
- [API Endpoints](#api-endpoints)

## 🛠 Tech Stack

### Frontend
- **HTML5** - โครงสร้างหน้าเว็บ
- **CSS3** - การจัดรูปแบบและ Responsive Design
- **JavaScript (Vanilla)** - การทำงานฝั่ง Client-side
- **Font Awesome / Bootstrap Icons** - ไอคอนสำหรับ UI

### Backend
- **PHP 8.x** - ภาษาสำหรับ Server-side
- **MySQL 8.0** - ระบบฐานข้อมูล
- **phpMyAdmin** - เครื่องมือจัดการฐานข้อมูล

### DevOps
- **Docker** - Container สำหรับจำลอง Environment
- **Docker Compose** - จัดการ Multi-container

### Architecture
- **MVC Pattern** - Model-View-Controller

## ✨ Features

### การจัดการผู้ใช้งาน
- ระบบ Login/Register
- Authentication และ Session Management
- การจัดการ Profile ส่วนตัว
- เปลี่ยนรหัสผ่าน

### การจัดการรายรับ-รายจ่าย
- เพิ่ม/แก้ไข/ลบ รายการรายรับ-รายจ่าย
- จัดหมวดหมู่รายการ (Category)
- เพิ่มหมายเหตุ (Note) ในแต่ละรายการ
- กำหนดวันที่ของรายการ
- แนบหลักฐานการจ่าย (Receipt Upload - Optional)

### การรายงานและสถิติ
- สรุปยอดรายรับ-รายจ่ายรวม
- แสดงกราฟสถิติตามหมวดหมู่
- กรองข้อมูลตามช่วงเวลา (วัน/สัปดาห์/เดือน/ปี)
- ส่งออกรายงานเป็น PDF/Excel

### Dashboard
- ภาพรวมการเงินส่วนบุคคล
- แสดงรายการล่าสุด
- สรุปรายรับ-รายจ่ายประจำเดือน
- แจ้งเตือนเมื่อใกล้เกินงบประมาณ

### Responsive Design
- รองรับการแสดงผลบนมือถือ (Mobile)
- รองรับการแสดงผลบนแท็บเล็ต (Tablet)
- รองรับการแสดงผลบนเดสก์ท็อป (Desktop)

## 📁 โครงสร้างโปรเจค

```
expense-tracking/
│
├── docker/
│   ├── nginx/
│   │   └── default.conf
│   ├── php/
│   │   └── Dockerfile
│   └── mysql/
│       └── init.sql
│
├── public/
│   ├── index.php
│   ├── assets/
│   │   ├── css/
│   │   │   ├── style.css
│   │   │   ├── responsive.css
│   │   │   └── components/
│   │   │       ├── navbar.css
│   │   │       ├── sidebar.css
│   │   │       ├── card.css
│   │   │       └── form.css
│   │   ├── js/
│   │   │   ├── app.js
│   │   │   ├── auth.js
│   │   │   ├── expense.js
│   │   │   ├── chart.js
│   │   │   └── utils.js
│   │   ├── icons/
│   │   │   └── (Font Awesome or Bootstrap Icons)
│   │   └── images/
│   │       └── uploads/
│   │           └── receipts/
│   └── .htaccess
│
├── app/
│   ├── config/
│   │   ├── database.php
│   │   └── config.php
│   │
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ExpenseController.php
│   │   ├── IncomeController.php

│   │   └── ReportController.php
│   │
│   ├── models/
│   │   ├── User.php
│   │   ├── Expense.php
│   │   ├── Income.php

│   │   └── Database.php
│   │
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── header.php
│   │   │   ├── footer.php
│   │   │   ├── navbar.php
│   │   │   └── sidebar.php
│   │   │
│   │   ├── auth/
│   │   │   ├── login.php
│   │   │   ├── register.php
│   │   │   └── forgot-password.php
│   │   │
│   │   ├── dashboard/
│   │   │   └── index.php
│   │   │
│   │   ├── expense/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   └── edit.php
│   │   │
│   │   ├── income/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   └── edit.php
│   │   │

│   │   ├── report/
│   │   │   ├── summary.php
│   │   │   └── export.php
│   │   │
│   │   └── profile/
│   │       └── index.php
│   │
│   └── helpers/
│       ├── Auth.php
│       ├── Validator.php
│       └── Session.php
│
├── docker-compose.yml
├── .env.example
├── .gitignore
└── README.md
```

## 🗄 Database Schema

### ตาราง users
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### ตาราง categories
```sql
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    icon VARCHAR(50) DEFAULT NULL,
    color VARCHAR(7) DEFAULT '#000000',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### ตาราง expenses
```sql
CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    description TEXT,
    expense_date DATE NOT NULL,
    receipt_path VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);
```

### ตาราง incomes
```sql
CREATE TABLE incomes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    description TEXT,
    income_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);
```

### ตาราง budgets (Optional - สำหรับกำหนดงบประมาณ)
```sql
CREATE TABLE budgets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    month DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);
```

## 🚀 การติดตั้งและใช้งาน

### Prerequisites
- Docker Desktop (สำหรับ Windows/Mac)
- Docker และ Docker Compose (สำหรับ Linux)
- Git

### ขั้นตอนการติดตั้ง

1. **Clone Repository**
```bash
git clone <repository-url>
cd expense-tracking
```

2. **สร้างไฟล์ Environment**
```bash
cp .env.example .env
```

3. **แก้ไขค่าใน .env**
```env
# Database Configuration
DB_HOST=mysql
DB_PORT=3306
DB_NAME=expense_tracking
DB_USER=expense_user
DB_PASSWORD=your_secure_password

# Application Configuration
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8080

# Session Configuration
SESSION_LIFETIME=7200
```

4. **สร้างและรัน Docker Containers**
```bash
docker-compose up -d
```

5. **เข้าถึงแอปพลิเคชัน**
- Application: http://localhost:8080
- phpMyAdmin: http://localhost:8082
  - Username: expense_user
  - Password: your_secure_password

6. **Import Database Schema**
```bash
docker exec -i expense-tracking-mysql mysql -uexpense_user -pyour_secure_password expense_tracking < docker/mysql/init.sql
```

## 🐳 Docker Setup

### docker-compose.yml
```yaml
version: '3.8'

services:
  # PHP-FPM Service
  php:
    build:
      context: ./docker/php
      dockerfile: Dockerfile
    container_name: expense-tracking-php
    volumes:
      - ./:/var/www/html
    networks:
      - expense-network

  # Nginx Web Server
  nginx:
    image: nginx:alpine
    container_name: expense-tracking-nginx
    ports:
      - "8080:80"
    volumes:
      - ./:/var/www/html
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - php
    networks:
      - expense-network

  # MySQL Database
  mysql:
    image: mysql:8.0
    container_name: expense-tracking-mysql
    environment:
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_DATABASE: expense_tracking
      MYSQL_USER: expense_user
      MYSQL_PASSWORD: your_secure_password
    ports:
      - "3306:3306"
    volumes:
      - mysql-data:/var/lib/mysql
      - ./docker/mysql/init.sql:/docker-entrypoint-initdb.d/init.sql
    networks:
      - expense-network

  # phpMyAdmin
  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: expense-tracking-phpmyadmin
    environment:
      PMA_HOST: mysql
      PMA_PORT: 3306
      MYSQL_ROOT_PASSWORD: root_password
    ports:
      - "8082:80"
    depends_on:
      - mysql
    networks:
      - expense-network

networks:
  expense-network:
    driver: bridge

volumes:
  mysql-data:
```

## 💻 การใช้งาน

### MVC Architecture

#### Model (app/models/)
จัดการข้อมูลและ Business Logic
```php
// ตัวอย่าง Expense.php
class Expense {
    private $db;
    
    public function getAllByUser($userId) {
        // ดึงข้อมูลรายจ่ายทั้งหมดของผู้ใช้
    }
    
    public function create($data) {
        // สร้างรายการรายจ่ายใหม่
    }
    
    public function update($id, $data) {
        // อัปเดตรายการรายจ่าย
    }
    
    public function delete($id) {
        // ลบรายการรายจ่าย
    }
}
```

#### View (app/views/)
แสดงผลข้อมูลให้ผู้ใช้
```html
<!-- ตัวอย่าง dashboard/index.php -->
<div class="dashboard">
    <div class="summary-cards">
        <div class="card income">
            <i class="icon-income"></i>
            <h3>รายรับ</h3>
            <p><?php echo number_format($totalIncome, 2); ?> บาท</p>
        </div>
        <div class="card expense">
            <i class="icon-expense"></i>
            <h3>รายจ่าย</h3>
            <p><?php echo number_format($totalExpense, 2); ?> บาท</p>
        </div>
    </div>
</div>
```

#### Controller (app/controllers/)
ควบคุมการทำงานระหว่าง Model และ View
```php
// ตัวอย่าง ExpenseController.php
class ExpenseController {
    private $expenseModel;
    
    public function index() {
        $expenses = $this->expenseModel->getAllByUser($_SESSION['user_id']);
        require_once 'app/views/expense/index.php';
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // สร้างรายการใหม่
        } else {
            require_once 'app/views/expense/create.php';
        }
    }
}
```

## 🔌 API Endpoints

### Authentication
- `POST /api/auth/register` - ลงทะเบียนผู้ใช้ใหม่
- `POST /api/auth/login` - เข้าสู่ระบบ
- `POST /api/auth/logout` - ออกจากระบบ
- `GET /api/auth/profile` - ดึงข้อมูลโปรไฟล์

### Expenses
- `GET /api/expenses` - ดึงรายการรายจ่ายทั้งหมด
- `GET /api/expenses/:id` - ดึงรายการรายจ่ายตาม ID
- `POST /api/expenses` - สร้างรายการรายจ่ายใหม่
- `PUT /api/expenses/:id` - อัปเดตรายการรายจ่าย
- `DELETE /api/expenses/:id` - ลบรายการรายจ่าย

### Incomes
- `GET /api/incomes` - ดึงรายการรายรับทั้งหมด
- `GET /api/incomes/:id` - ดึงรายการรายรับตาม ID
- `POST /api/incomes` - สร้างรายการรายรับใหม่
- `PUT /api/incomes/:id` - อัปเดตรายการรายรับ
- `DELETE /api/incomes/:id` - ลบรายการรายรับ

### Categories
- `GET /api/categories` - ดึงหมวดหมู่ทั้งหมด
- `POST /api/categories` - สร้างหมวดหมู่ใหม่
- `PUT /api/categories/:id` - อัปเดตหมวดหมู่
- `DELETE /api/categories/:id` - ลบหมวดหมู่

### Reports
- `GET /api/reports/summary?start_date=&end_date=` - สรุปรายงาน
- `GET /api/reports/by-category?month=` - รายงานตามหมวดหมู่
- `GET /api/reports/export?format=pdf|excel` - ส่งออกรายงาน

## 📱 Responsive Design Guidelines

### Breakpoints
```css
/* Mobile First Approach */
/* Extra Small (xs) - Default */
/* Small (sm) - 576px and up */
@media (min-width: 576px) { }

/* Medium (md) - 768px and up */
@media (min-width: 768px) { }

/* Large (lg) - 992px and up */
@media (min-width: 992px) { }

/* Extra Large (xl) - 1200px and up */
@media (min-width: 1200px) { }
```

### UI Components
- Flexible Grid System
- Touch-friendly Button Size (min 44x44px)
- Slide Navigation สำหรับ Mobile
- Card Layout สำหรับแสดงรายการ
- Modal สำหรับ Form Input

## 🎨 Icon Libraries (เลือกใช้อย่างใดอย่างหนึ่ง)

### Option 1: Font Awesome
```html
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
```

### Option 2: Bootstrap Icons
```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
```

### Option 3: Material Icons
```html
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
```

## 🔒 Security Features

- Password Hashing (bcrypt)
- SQL Injection Prevention (Prepared Statements)
- XSS Protection (Input Sanitization)
- CSRF Token Protection
- Session Security
- File Upload Validation

## 📊 Chart Library สำหรับ Dashboard

### Chart.js (แนะนำ)
```html
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
```

ใช้สำหรับ:
- Line Chart - แสดงแนวโน้มรายรับ-รายจ่าย
- Bar Chart - เปรียบเทียบรายการตามเดือน
- Pie Chart - สัดส่วนรายจ่ายตามหมวดหมู่
- Doughnut Chart - ภาพรวมการเงิน

## 🛠 Development Tools

### Code Editor Extensions (แนะนำ)
- PHP Intelephense
- HTML CSS Support
- JavaScript (ES6) code snippets
- Docker Extension

### Browser DevTools
- Chrome DevTools
- Firefox Developer Tools
- Responsive Design Mode

## 📝 License

MIT License

## 👥 Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## 📞 Support

หากมีปัญหาหรือข้อสงสัย กรุณาติดต่อ:
- Email: support@expense-tracking.com
- GitHub Issues: [Create an issue](link-to-issues)

---

**สร้างโดย**: [Your Name]  
**วันที่อัปเดตล่าสุด**: February 9, 2026
