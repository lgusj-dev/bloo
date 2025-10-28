<?php
session_start();
include('db_connect.php'); // your existing database connection

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// -------------------- IMAGE UPLOAD FUNCTION --------------------
function uploadImage($file, $username) {
    global $conn;
    $uploadDir = __DIR__ . "/assets/images/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
        $tmpName = $file['tmp_name'];
        $originalName = basename($file['name']);
        $originalName = preg_replace("/[^A-Za-z0-9_\-\.]/", "_", $originalName);

        if ($file['size'] > 2 * 1024 * 1024) {
            echo "<script>alert('File too large. Max size is 2MB.');</script>";
            return "";
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmpName);
        finfo_close($finfo);
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($mime, $allowed)) {
            echo "<script>alert('Invalid file type. Please upload an image.');</script>";
            return "";
        }

        $uniquePart = bin2hex(random_bytes(4));
        $uniqueName = $username . "_" . time() . "_" . $uniquePart . "_" . $originalName;
        $destination = $uploadDir . $uniqueName;

        if (move_uploaded_file($tmpName, $destination)) {
            $stmt = $conn->prepare("INSERT INTO uploads (filename, username, upload_date) VALUES (?, ?, NOW())");
            $stmt->bind_param("ss", $uniqueName, $username);
            $stmt->execute();
            $stmt->close();
            echo "<script>alert('File uploaded successfully!');</script>";
            return $uniqueName;
        } else {
            echo "<script>alert('Error moving uploaded file.');</script>";
        }
    } elseif (isset($file['error']) && $file['error'] !== UPLOAD_ERR_NO_FILE) {
        echo "<script>alert('File upload error.');</script>";
    }
    return "";
}

// -------------------- HANDLE FILE UPLOAD --------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['task_image'])) {
    uploadImage($_FILES['task_image'], $username);
}

// -------------------- HANDLE FILE DELETE --------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_file'])) {
    $fileId = intval($_POST['delete_file_id']);

    $stmt = $conn->prepare("SELECT filename FROM uploads WHERE id = ?");
    $stmt->bind_param("i", $fileId);
    $stmt->execute();
    $stmt->bind_result($filename);
    if ($stmt->fetch()) {
        $filePath = __DIR__ . "/assets/images/" . $filename;
        if (file_exists($filePath)) unlink($filePath);
    }
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM uploads WHERE id = ?");
    $stmt->bind_param("i", $fileId);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('File deleted successfully.'); window.location.href='test.php';</script>";
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>File Upload - Dashboard</title>
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
</head>
<body>
<div class="page-container">
    <!-- sidebar menu -->
    <div class="sidebar-menu">
        <div class="sidebar-header">
            <div class="logo"><a href="test.php"><img src="assets/images/icon/logo.png" alt="logo"></a></div>
        </div>
        
        <div class="main-menu">
            <div class="menu-inner">
                <nav>
                    <ul class="metismenu" id="menu">
                        <li><a href="testing.php" aria-expanded="true"><i class="ti-home"></i><span>Home</span></a></li>
                        <li class="active"><a href="test.php" aria-expanded="true"><i class="ti-upload"></i><span>Upload File</span></a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

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


        <!-- page content -->
        <div class="main-content-inner">
            <div class="row">
                <div class="col-12">
                    <div class="card mt-5">
                        <div class="card-body">
                            <h4 class="header-title mb-3">Upload your image</h4>
                            <form method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <input type="file" name="task_image" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </form>

                            <!-- uploaded files table -->
                            <h4 class="mt-4">Uploaded Files</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Filename</th>
                                            <th>Uploaded By</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $result = $conn->query("SELECT * FROM uploads");
                                        while ($row = $result->fetch_assoc()):
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['filename']); ?></td>
                                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                                            <td><?php echo htmlspecialchars($row['upload_date']); ?></td>
                                            <td>
                                                <form method="post" style="display:inline;">
                                                    <input type="hidden" name="delete_file_id" value="<?php echo $row['id']; ?>">
                                                    <button type="submit" name="delete_file" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this file?');">Delete</button>
                                                </form>
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
</div>

<!-- scripts -->
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
