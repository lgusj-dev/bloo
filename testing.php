<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];

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

// -------------------- DUMMY DATA (from second code) --------------------
$market_values = [
    [
        'logo' => 'assets/images/icon/market-value/icon1.png',
        'name' => 'Dashcoin',
        'buy' => '30%',
        'sell' => '20%',
        'trends' => 'up',
        'attachments' => '$ 56746,857'
    ],
    [
        'logo' => 'assets/images/icon/market-value/icon2.png',
        'name' => 'LiteCoin',
        'buy' => '30%',
        'sell' => '20%',
        'trends' => 'down',
        'attachments' => '$ 56746,857'
    ]
];

$crypto_prices = [
    ['icon' => 'b', 'name' => 'Bitcoin', 'price' => '$876909.00', 'arrow' => 'up'],
    ['icon' => 'l', 'name' => 'Litecoin', 'price' => '$29780.00', 'arrow' => 'up']
];

$buy_orders = [
    ['id' => '78211', 'time' => '4.00 AM', 'status' => 'Pending', 'amount' => '$758.90', 'last_trade' => '$05245.090']
];

$sell_orders = [
    ['id' => '8964978', 'time' => '4.00 AM', 'status' => 'Pending', 'amount' => '$445.90', 'last_trade' => '$094545.090']
];
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Dashboard - Welcome</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="assets/images/icon/favicon.ico">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/metisMenu.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/slicknav.min.css">
    <link rel="stylesheet" href="assets/css/typography.css">
    <link rel="stylesheet" href="assets/css/default-css.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <script src="assets/js/vendor/modernizr-2.8.3.min.js"></script>
</head>

<body>
    <div class="page-container">
        <!-- sidebar menu area start -->
        <div class="sidebar-menu">
            <div class="sidebar-header">
                <div class="logo">
                    <a href="index.php"><img src="assets/images/icon/logo.png" alt="logo"></a>
                </div>
            </div>
            <div class="main-menu">
                <div class="menu-inner">
                    <nav>
                        <ul class="metismenu" id="menu">
                            <li class="active">
                                <a href="index.php" aria-expanded="true"><i class="ti-home"></i><span>Dashboard</span></a>
                            </li>
                            <li>
                                <a href="test.php" aria-expanded="true"><i class="ti-upload"></i><span>File Upload</span></a>
                            </li>
                            <!-- Keep other sidebar items as in your original code -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
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
                                <i class="ti-bell dropdown-toggle" data-toggle="dropdown">
                                    <span>2</span>
                                </i>
                                <!-- notifications content here -->
                            </li>
                            <li class="dropdown">
                                <i class="fa fa-envelope-o dropdown-toggle" data-toggle="dropdown"><span>3</span></i>
                                <!-- messages content here -->
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
                            <h4 class="page-title pull-left">Dashboard</h4>
                            <ul class="breadcrumbs pull-left">
                                <li><a href="index.php">Home</a></li>
                                <li><span>Welcome</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6 clearfix">
                        <div class="user-profile pull-right">
                            <img class="avatar user-thumb" src="assets/images/author/avatar.png" alt="avatar">
                            <?php
                            // default to session username if DB lookup fails
                            $display_name = htmlspecialchars($username);

                            // Try common DB connection variables: $conn (mysqli), $mysqli (mysqli), $pdo (PDO)
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
                            } elseif (isset($mysqli) && $mysqli instanceof mysqli) {
                                if ($stmt = $mysqli->prepare("SELECT name FROM users WHERE username = ? LIMIT 1")) {
                                    $stmt->bind_param("s", $username);
                                    $stmt->execute();
                                    $stmt->bind_result($db_name);
                                    if ($stmt->fetch() && $db_name) {
                                        $display_name = htmlspecialchars($db_name);
                                    }
                                    $stmt->close();
                                }
                            } elseif (isset($pdo) && $pdo instanceof PDO) {
                                $stmt = $pdo->prepare("SELECT name FROM users WHERE username = ? LIMIT 1");
                                if ($stmt->execute([$username])) {
                                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                    if (!empty($row['name'])) {
                                        $display_name = htmlspecialchars($row['name']);
                                    }
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer>
                <div class="footer-area">
                    <p>© Copyright 2025. All right reserved. Template by <a href="https://colorlib.com/wp/">Natsu</a>.</p>
                </div>
            </footer>
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
