<?php
session_start();

// Xóa tất cả các biến session
$_SESSION = array();

// Nếu muốn xóa sạch cookie session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hủy session hoàn toàn
session_destroy();

// Chuyển hướng người dùng về trang chủ
header("Location: /quanlydienthoai/index.php");
exit();
?>