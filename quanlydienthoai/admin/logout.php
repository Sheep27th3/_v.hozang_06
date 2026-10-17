<?php
include "../includes/session.php";

session_start();
session_unset();
session_destroy();

// Chuyển hướng về trang chủ giao diện cửa hàng
header("Location: ../index.php");
exit();
?>
