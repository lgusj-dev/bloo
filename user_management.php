<?php
session_start();
include('db_connect.php');

// ✅ Redirect if not logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

// ✅ Redirect if not admin (only ID 1 can access)
if ($_SESSION['id'] != 1) {
    header("Location: home.php");
    exit;
}

// Get admin name from database
$admin_name = "Administrator"; // default value
if ($stmt = $conn->prepare("SELECT name FROM users WHERE id = 1")) {
    $stmt->execute();
    $stmt->bind_result($db_name);
    if ($stmt->fetch() && $db_name) {
        $admin_name = htmlspecialchars($db_name);
    }
    $stmt->close();
}

$error = "";
$success = "";

// Handle user deletion
if (isset($_POST['delete_user'])) {
    $user_id = intval($_POST['delete_user']);
    
    // Don't allow deleting the admin user (ID 1)
    if ($user_id != 1) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $success = "User deleted successfully!";
        } else {
            $error = "Error deleting user.";
        }
        $stmt->close();
    }
}
?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>User Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include('includes/css_links.php'); ?>
</head>

<body>
    <div class="page-container">
        <!-- sidebar menu area start -->
        <?php include('includes/sidebar.php'); ?>
        <!-- sidebar menu area end -->

        <!-- main content area start -->
        <div class="main-content">
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
                            <h4 class="page-title pull-left">User Management</h4>
                            <ul class="breadcrumbs pull-left">
                                <li><a href="home.php">Home</a></li>
                                <li><span>Users</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6 clearfix">
                        <div class="user-profile pull-right">
                            <img class="avatar user-thumb" src="assets/images/author/avatar.png" alt="avatar">
                            <h4 class="user-name dropdown-toggle" data-toggle="dropdown"><?php echo $admin_name; ?> <i class="fa fa-angle-down"></i></h4>
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

            <div class="main-content-inner">
                <div class="row">
                    <!-- Users List -->
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <?php if (!empty($error)): ?>
                                    <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                                <?php elseif (!empty($success)): ?>
                                    <div class="alert alert-success text-center"><?php echo $success; ?></div>
                                <?php endif; ?>

                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="header-title mb-0">Users List</h4>
                                    <a href="register.php" class="btn btn-primary mb-3">
                                        Add New User
                                    </a>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Office</th>
                                                <th>Username</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Show admin (id=1) first, then list users so newest are at the bottom
                                            $query = "SELECT id, name, office, username FROM users ORDER BY CASE WHEN id = 1 THEN 0 ELSE 1 END, id ASC";
                                            $result = $conn->query($query);
                                            while ($row = $result->fetch_assoc()):
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['office']); ?></td>
                                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                                <td>
                                                    <?php if ($row['id'] != 1): // Don't show delete button for admin ?>
                                                    <form method="post" style="display: inline;">
                                                        <input type="hidden" name="delete_user" value="<?php echo $row['id']; ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">
                                                            Delete
                                                        </button>
                                                    </form>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- main content area end -->
    </div>

    <!-- jquery latest version -->
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