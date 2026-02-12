<?php $currentUser = Auth::user(); ?>
<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-brand">

            <a href="/dashboard" class="brand-link">
                <i class="fas fa-wallet"></i>
                <span>Expense Tracking</span>
            </a>
        </div>

        <?php if ($currentUser): ?>
            <div class="navbar-menu">
                <div class="navbar-user">
                    <?php if (!empty($currentUser['profile_image'])): ?>
                        <img src="<?php echo htmlspecialchars($currentUser['profile_image'] ?? ''); ?>" alt="User Avatar"
                            class="user-avatar">
                    <?php else:
                        // Get initials from full name
                        $nameParts = explode(' ', $currentUser['full_name'] ?? '');
                        $initials = '';
                        if (count($nameParts) >= 2) {
                            $initials = strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[count($nameParts) - 1], 0, 1));
                        } else {
                            $initials = strtoupper(mb_substr($currentUser['full_name'], 0, 2));
                        }
                        ?>
                        <div class="user-avatar-placeholder">
                            <?php echo $initials; ?>
                        </div>
                    <?php endif; ?>
                    <span class="user-name"><?php echo htmlspecialchars($currentUser['full_name'] ?? 'User'); ?></span>
                    <div class="user-dropdown">
                        <a href="/profile">
                            <i class="fas fa-user"></i> โปรไฟล์
                        </a>
                        <a href="/settings">
                            <i class="fas fa-cog"></i> ตั้งค่า
                        </a>
                        <hr>
                        <a href="/auth/logout">
                            <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</nav>