<?php
session_start();
include('db_connect.php');

// ✅ Ensure user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

// ✅ Ensure only admin can access
if ($_SESSION['id'] != 1) {
    header("Location: test.php");
    exit;
}

// ✅ Handle image upload
function uploadImage($file, $email, $conn) {
    $targetDir = "uploads/";
    $fileName = basename($file["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];

    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
            $stmt = $conn->prepare("INSERT INTO uploads (email, filename) VALUES (?, ?)");
            $stmt->bind_param("ss", $email, $fileName);
            $stmt->execute();
            $stmt->close();
            return true;
        }
    }
    return false;
}

// ✅ Handle delete (admin only)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM uploads WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_test.php");
    exit;
}

// ✅ Handle upload form
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["file"])) {
    $email = htmlspecialchars($_POST["email"]);
    if (uploadImage($_FILES["file"], $email, $conn)) {
        $success = "File uploaded successfully!";
    } else {
        $error = "Failed to upload file.";
    }
}

// ✅ Fetch uploaded files
$result = $conn->query("SELECT * FROM uploads ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body>

<div class="d-flex">
    <!-- ✅ Sidebar -->
    <div class="bg-dark text-white p-3" style="width:250px; height:100vh;">
        <h4 class="mb-4">Admin Panel</h4>
        <ul class="nav flex-column">
            <li class="nav-item"><a href="admin_test.php" class="nav-link text-white">Dashboard</a></li>
            <li class="nav-item"><a href="register.php" class="nav-link text-white">Register</a></li>
            <li class="nav-item"><a href="logout.php" class="nav-link text-white">Logout</a></li>
        </ul>
    </div>

    <!-- ✅ Main Content -->
    <div class="p-4 flex-grow-1">
        <h2>Welcome, Admin</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- ✅ Upload Form -->
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label>Choose File</label>
                <input type="file" name="file" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload File</button>
        </form>

        <hr>

        <!-- ✅ Uploaded Files Table -->
        <h4>Uploaded Files</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Filename</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['filename']); ?></td>
                    <td>
                        <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
