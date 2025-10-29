<?php
// Get the current page name for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar-menu">
    <div class="sidebar-header">
        <div class="logo">
            <a href="home.php"><img src="assets/images/icon/sjlogo.png" alt="logo"></a>
        </div>
    </div>
    <div class="main-menu">
        <div class="menu-inner">
            <nav>
                <ul class="metismenu" id="menu">
                    <li class="<?php echo $current_page == 'home.php' ? 'active' : ''; ?>">
                        <a href="home.php"><i class="ti-home"></i><span>Home</span></a>
                    </li>
                    <li class="<?php echo $current_page == 'file_upload.php' ? 'active' : ''; ?>">
                        <a href="file_upload.php"><i class="ti-upload"></i><span>Upload File</span></a>
                    </li>
                    <?php if (isset($_SESSION['id']) && $_SESSION['id'] == 1): ?>
                    <li class="<?php echo in_array($current_page, ['user_management.php', 'register.php']) ? 'active' : ''; ?>">
                        <a href="user_management.php"><i class="ti-user"></i><span>Users</span></a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>