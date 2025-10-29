<?php
session_start();
include('db_connect.php'); // include your connection file
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user from database using prepared statement
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Verify password using password_verify for hashed passwords
        if (password_verify($password, $row['password'])) {
            $_SESSION['id'] = $row['id'];
$_SESSION['username'] = $row['username'];
$_SESSION['role'] = ($_SESSION['id'] == 1) ? 'admin' : 'user';

            header("Location: testing.php");
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found.";
    }
}

?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Login - srtdash</title>
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
                    <div class="login-form-head">
                        <div class="sjlogo">
            <a href="testing.php"><img src="assets/images/icon/sjlogo.png" alt="logo"></a><h4>Sign In</h4>
        </div>
                        
                    </div>
                    <div class="login-form-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <div class="form-gp1">
                            <label for="exampleInputEmail1">Email address</label>
                            <input type="text" id="exampleInputEmail1" name="username" required>
                            <i class="ti-email"></i>
                        </div>
                        <div class="form-gp1">
                            <label for="exampleInputPassword1">Password</label>
                            <input type="password" id="exampleInputPassword1" name="password" required>
                            <i class="ti-lock"></i>
                        </div>
                        <div class="row mb-4 rmber-area">
                            <div class="col-6">
                                <div class="custom-control custom-checkbox mr-sm-2">
                                    <input type="checkbox" class="custom-control-input" id="customControlAutosizing">
                                    <label class="custom-control-label" for="customControlAutosizing">Remember Me</label>
                                </div>
                            </div>
                            <div class="col-6 text-right">
                                <a href="#">Forgot Password?</a>
                            </div>
                        </div>
                        <div class="submit-btn-area">
                            <button id="form_submit" type="submit">Submit <i class="ti-arrow-right"></i></button>
                            <!-- <div class="login-other row mt-4">
                                <div class="col-6">
                                    <a class="fb-login" href="#">Log in with <i class="fa fa-facebook"></i></a>
                                </div>
                                <div class="col-6">
                                    <a class="google-login" href="#">Log in with <i class="fa fa-google"></i></a>
                                </div>
                            </div> -->
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

    <script>
        $(document).ready(function() {
            // Function to handle input state
            function handleInputState($input) {
                const $parent = $input.parent('.form-gp1');
                if ($input.val()) {
                    $parent.addClass('focused');
                } else {
                    $parent.removeClass('focused');
                }
            }

            // Initialize all form fields
            $('.form-gp1 input').each(function() {
                handleInputState($(this));
            });

            // Handle focus events
            $('.form-gp1 input').on('focus', function() {
                $(this).parent('.form-gp1').addClass('focused');
            });

            // Handle blur events
            $('.form-gp1 input').on('blur', function() {
                handleInputState($(this));
            });

            // Handle input events
            $('.form-gp1 input').on('input', function() {
                handleInputState($(this));
            });
        });
    </script>
</body>
</html>
