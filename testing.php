<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];

// Fetch user info from DB to get ID
$user_id = null;
if (isset($conn) && $conn instanceof mysqli) {
    if ($stmt = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1")) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($uid);
        if ($stmt->fetch()) {
            $user_id = $uid;
        }
        $stmt->close();
    }
}

// Check if the user is an admin
$is_admin = ($user_id === 1);

// -------------------- RANDOM GREETINGS --------------------
$greetings = [
    "Welcome! Let's get started.",
    "Hello, $username! What's on your agenda today?",
    "Greetings! Ready to take on the day?",
    "Hi there! Let's make something awesome.",
    "Welcome back! Let's see what we can do today.",
    "Hey $username! What's there to do today?"
];

// Pick a random greeting
$greeting_message = $greetings[array_rand($greetings)];
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Home - Welcome</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="assets/images/icon/favicon.ico">
    <?php
    function _css_ver($relPath) {
        $full = __DIR__ . DIRECTORY_SEPARATOR . $relPath;
        return (file_exists($full)) ? filemtime($full) : time();
    }
    ?>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css?v=<?php echo _css_ver('assets/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css?v=<?php echo _css_ver('assets/css/font-awesome.min.css'); ?>">
    <link rel="stylesheet" href="assets/css/themify-icons.css?v=<?php echo _css_ver('assets/css/themify-icons.css'); ?>">
    <link rel="stylesheet" href="assets/css/metisMenu.css?v=<?php echo _css_ver('assets/css/metisMenu.css'); ?>">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css?v=<?php echo _css_ver('assets/css/owl.carousel.min.css'); ?>">
    <link rel="stylesheet" href="assets/css/slicknav.min.css?v=<?php echo _css_ver('assets/css/slicknav.min.css'); ?>">
    <link rel="stylesheet" href="assets/css/typography.css?v=<?php echo _css_ver('assets/css/typography.css'); ?>">
    <link rel="stylesheet" href="assets/css/default-css.css?v=<?php echo _css_ver('assets/css/default-css.css'); ?>">
    <link rel="stylesheet" href="assets/css/styles.css?v=<?php echo _css_ver('assets/css/styles.css'); ?>">
    <link rel="stylesheet" href="assets/css/responsive.css?v=<?php echo _css_ver('assets/css/responsive.css'); ?>">
    <script src="assets/js/vendor/modernizr-2.8.3.min.js"></script>
</head>

<body>
    <div class="page-container">
        <!-- sidebar menu area start -->
        <?php include('includes/sidebar.php'); ?>
        <!-- sidebar menu area end -->

        <!-- main content area start -->
        <div class="main-content">
            <!-- header area start -->
            <div class="header-area">
                <div class="row align-items-center">
                    <div class="col-md-6 col-sm-8 clearfix">
                        <div class="nav-btn pull-left">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                        <div class="search-box pull-left">
                            <form action="#">
                                <input type="text" name="search" placeholder="Search..." required>
                                <i class="ti-search"></i>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-4 clearfix">
                        <ul class="notification-area pull-right">
                            <li id="full-view"><i class="ti-fullscreen"></i></li>
                            <li id="full-view-exit"><i class="ti-zoom-out"></i></li>
                            <li class="dropdown">
                                <i class="ti-bell dropdown-toggle" data-toggle="dropdown"><span>2</span></i>
                            </li>
                            <li class="dropdown">
                                <i class="fa fa-envelope-o dropdown-toggle" data-toggle="dropdown"><span>3</span></i>
                            </li>
                            <li class="settings-btn">
                                <i class="ti-settings"></i>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- header area end -->

            <!-- page title area start -->
            <div class="page-title-area">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <div class="breadcrumbs-area clearfix">
                            <h4 class="page-title pull-left">Home</h4>
                            <ul class="breadcrumbs pull-left">
                                <li><a href="testing.php">Home</a></li>
                                <li><span>Welcome</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6 clearfix">
                        <div class="user-profile pull-right">
                            <img class="avatar user-thumb" src="assets/images/author/avatar.png" alt="avatar">
                            <?php
                            $display_name = htmlspecialchars($username);
                            if (isset($conn) && $conn instanceof mysqli) {
                                if ($stmt = $conn->prepare("SELECT name FROM users WHERE username = ? LIMIT 1")) {
                                    $stmt->bind_param("s", $username);
                                    $stmt->execute();
                                    $stmt->bind_result($db_name);
                                    if ($stmt->fetch() && $db_name) {
                                        $display_name = htmlspecialchars($db_name);
                                    }
                                    $stmt->close();
                                }
                            }
                            ?>
                            <h4 class="user-name dropdown-toggle" data-toggle="dropdown"><?php echo $display_name; ?> <i class="fa fa-angle-down"></i></h4>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#">Message</a>
                                <a class="dropdown-item" href="#">Settings</a>
                                <a class="dropdown-item" href="login.php">Log Out</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- page title area end -->

            <!-- main content inner -->
            <div class="main-content-inner">
                <div class="row">
                    <div class="col-12">
                        <div class="card text-center">
                            <div class="card-body">
                                <h2><?php echo $greeting_message; ?></h2>
                                <p>Use the sidebar to navigate through your tasks and features.</p>
                                <?php if ($is_admin): ?>
                                    <p><strong>Admin Mode:</strong> You have access to user registration and management tools.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- main content area end -->
    </div>

    <!-- JS files -->
    <script src="assets/js/vendor/jquery-2.2.4.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/metisMenu.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/jquery.slicknav.min.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/scripts.js"></script>
</body>

</html>
