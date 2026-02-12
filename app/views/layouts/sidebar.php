<aside class="sidebar" id="sidebar">
    <ul class="sidebar-menu">
        <li class="menu-item <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
            <a href="/dashboard">
                <i class="fas fa-home"></i>
                <span>หน้าหลัก</span>
            </a>
        </li>

        <li class="menu-item <?php echo $currentPage === 'expense' ? 'active' : ''; ?>">
            <a href="/expense">
                <i class="fas fa-money-bill-wave"></i>
                <span>รายจ่าย</span>
            </a>
        </li>

        <li class="menu-item <?php echo $currentPage === 'income' ? 'active' : ''; ?>">
            <a href="/income">
                <i class="fas fa-hand-holding-dollar"></i>
                <span>รายรับ</span>
            </a>
        </li>



        <li class="menu-item <?php echo $currentPage === 'report' ? 'active' : ''; ?>">
            <a href="/report">
                <i class="fas fa-chart-pie"></i>
                <span>รายงาน</span>
            </a>
        </li>

        <li class="menu-divider"></li>

        <li class="menu-item <?php echo $currentPage === 'profile' ? 'active' : ''; ?>">
            <a href="/profile">
                <i class="fas fa-user"></i>
                <span>โปรไฟล์</span>
            </a>
        </li>
    </ul>
</aside>