<?php
session_start();
include "config/database.php"; // File kết nối CSDL của bạn

// 1. Chỉ include header 1 lần duy nhất ở đầu trang
include "includes/header.php";

// 2. Include nội dung trang chủ
include "pages/home.php";

// 3. Include footer ở cuối trang (nếu có)
if (file_exists("includes/footer.php")) {
    include "includes/footer.php";
}
?>
</body>
</html>