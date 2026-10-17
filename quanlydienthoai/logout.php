<?php
session_start();

// Xóa sạch tất cả các biến Session
$_SESSION = array();

// Hủy Session
session_destroy();

// Chuyển hướng người dùng về trang chủ
header("Location: index.php");
exit();
?>