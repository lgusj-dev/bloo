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
    header("Location: index.php");
    exit;
}

$error = "";
$success = "";

// ✅ Handle registration form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect and sanitize inputs
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $office = htmlspecialchars(trim($_POST['office']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // ✅ Validation
    if (empty($fullname) || empty($office) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // ✅ Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email already exists!";
        } else {
            // ✅ Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // ✅ Insert into database
            $stmt = $conn->prepare("INSERT INTO users (name, office, username, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $fullname, $office, $email, $hashed_password);

            if ($stmt->execute()) {
                $success = "Account created successfully!";
            } else {
                $error = "Error: Unable to register user.";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Register Account</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include('includes/css_links.php'); ?>
</head>

<body>
    <div id="preloader">
        <div class="loader"></div>
    </div>

    <div class="login-area">
        <div class="container">
            <div class="login-box ptb--100">
                <form method="POST" action="">
                    <div class="login-form-head1">
                        <h4>Register a New Account</h4>
                    </div>

                    <div class="login-form-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                        <?php elseif (!empty($success)): ?>
                            <div class="alert alert-success text-center"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <div class="form-gp1">
                            <label for="exampleInputName1">Full Name</label>
                            <input type="text" id="exampleInputName1" name="fullname" required>
                            <i class="ti-user"></i>
                        </div>

                        <div class="form-gp1">
                            <label for="exampleInputOffice"></label>
                            <select id="exampleInputOffice" name="office" class="form-control" required>
                                <option value="">Select Office</option>
                                <option value="IT Department">IT Department</option>
                                <option value="Mayor's Office">Mayor's Office</option>
                                <option value="Accounting">Accounting</option>
                                <option value="Human Resources">Human Resources</option>
                                <option value="Planning Office">Planning Office</option>
                            </select>
                        </div>

                        <div class="form-gp1">
                            <label for="exampleInputEmail1">Email address</label>
                            <input type="email" id="exampleInputEmail1" name="email" required>
                            <i class="ti-email"></i>
                        </div>

                        <div class="form-gp1">
                            <label for="exampleInputPassword1">Password</label>
                            <input type="password" id="exampleInputPassword1" name="password" required>
                            <i class="ti-lock"></i>
                        </div>

                        <div class="form-gp1">
                            <label for="exampleInputPassword2">Confirm Password</label>
                            <input type="password" id="exampleInputPassword2" name="confirm_password" required>
                            <i class="ti-lock"></i>
                        </div>

                        <div class="submit-btn-area">
                            <button id="form_submit" type="submit">Register <i class="ti-arrow-right"></i></button>
                        </div>

                        <div class="form-footer text-center mt-5">
                            <p class="text-muted"><a href="testing.php">← Back to Home</a></p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
