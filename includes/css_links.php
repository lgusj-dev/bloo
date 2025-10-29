<?php
// Helper to return file mtime for cache-busting. Falls back to current time if file missing.
function _css_ver($relPath) {
    $full = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $relPath;
    return (file_exists($full)) ? filemtime($full) : time();
}
?>
<link rel="shortcut icon" type="image/png" href="assets/images/icon/favicon.ico">
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