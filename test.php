<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// -------------------- IMAGE UPLOAD FUNCTION --------------------
function uploadImage($file, $username) {
    $uploadDir = __DIR__ . "/assets/images/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

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
            global $conn;
            $stmt = $conn->prepare("INSERT INTO uploads (filename, username, upload_date) VALUES (?, ?, NOW())");
            $stmt->bind_param("ss", $uniqueName, $username);
            $stmt->execute();
            $stmt->close();
            echo "<script>alert('File uploaded successfully!');</script>";
            return $uniqueName;
        } else {
            echo "<script>alert('Error moving uploaded file.');</script>";
        }
    } else if (isset($file['error']) && $file['error'] !== UPLOAD_ERR_NO_FILE) {
        echo "<script>alert('File upload error.');</script>";
    }

    return "";
}

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_FILES['task_image'])) {
        uploadImage($_FILES['task_image'], $username);
    } else {
        echo "<script>alert('Please select a file to upload.');</script>";
    }
}

// -------------------- GET USER UPLOADS --------------------
global $conn;
// $stmt = $conn->prepare("SELECT filename FROM uploads WHERE username = ? ORDER BY upload_date DESC");
// $stmt->bind_param("s", $username);
// $stmt->execute();
// $result = $stmt->get_result();
// $uploads = $result->fetch_all(MYSQLI_ASSOC);
// $stmt->close();
// ?>

<!DOCTYPE html>
<html lang="en">
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
    <style>
        .upload-gallery {
            margin-top: 30px;
        }
        .upload-gallery img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <!-- page container area start -->
    <div class="page-container">
        <!-- sidebar menu area start -->
        <div class="sidebar-menu">
            <div class="sidebar-header">
                <a href="testing.php"><h4>My Dashboard</h4></a>
            </div>
            <div class="main-menu">
                <ul class="metismenu" id="menu">
                    <li><a href="testing.php"><i class="ti-home"></i> <span>Home</span></a></li>
                    <li><a href="test.php"><i class="ti-upload"></i> <span>File Upload</span></a></li>
                </ul>
            </div>
        </div>
        <!-- sidebar menu area end -->

        <!-- main content area start -->
        <div class="main-content">
            <!-- header area start -->
            <div class="header-area">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="nav-btn pull-left">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                    <div class="col-md-6 clearfix">
                        <ul class="notification-area pull-right">
                            <li class="dropdown">
                                <i class="fa fa-envelope-o dropdown-toggle" data-toggle="dropdown"></i>
                                <div class="dropdown-menu">
                                    <p class="text-center">No new messages</p>
                                </div>
                            </li>
                            <li class="dropdown">
                                <i class="fa fa-bell-o dropdown-toggle" data-toggle="dropdown"></i>
                                <div class="dropdown-menu">
                                    <p class="text-center">No new notifications</p>
                                </div>
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
                            <h4 class="page-title pull-left">File Upload</h4>
                            <ul class="breadcrumbs pull-left">
                                <li><a href="testing.php">Home</a></li>
                                <li><span>File Upload</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- page title area end -->

            <!-- main content inner start -->
            <div class="main-content-inner">
                <div class="row">
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title">Upload your image</h4>
                                <p>Welcome, <?php echo htmlspecialchars($username); ?>! Choose a file to upload:</p>
                                <form method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <input type="file" name="task_image" class="form-control" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Upload</button>
                                </form>

                                <?php if(!empty($uploads)): ?>
                                <div class="upload-gallery">
                                    <h5>Your Uploaded Images</h5>
                                    <?php foreach($uploads as $upload): ?>
                                        <img src="assets/images/<?php echo htmlspecialchars($upload['filename']); ?>" alt="Uploaded Image">
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- main content inner end -->
        </div>
        <!-- main content area end -->

        <!-- footer area start -->
        <footer>
            <div class="footer-area">
                <p>© Copyright 2025. All right reserved.</p>
            </div>
        </footer>
        <!-- footer area end -->
    </div>
    <!-- page container area end -->

    <script src="assets/js/vendor/jquery-2.2.4.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/metisMenu.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/jquery.slicknav.min.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/scripts.js"></script>
</body>
</html>
