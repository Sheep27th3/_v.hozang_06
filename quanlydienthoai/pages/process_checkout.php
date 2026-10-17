<?php
session_start();

// 1. Nhúng file kết nối CSDL
require_once "../config/database.php";

// 2. Kiểm tra xem có submit form đặt hàng không
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Lấy dữ liệu gửi từ checkout.php
    $product_id     = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $price          = isset($_POST['price']) ? floatval($_POST['price']) : 0;
    $fullname       = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
    $phone          = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $address        = isset($_POST['address']) ? trim($_POST['address']) : '';
    $note           = isset($_POST['note']) ? trim($_POST['note']) : '';
    $payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : 'COD';
    
    // Lấy user_id nếu đã đăng nhập (nếu chưa thì lưu NULL)
    $user_id        = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : NULL;

    // Kiểm tra dữ liệu bắt buộc
    if ($product_id <= 0 || empty($fullname) || empty($phone) || empty($address)) {
        echo "<script>
                alert('Vui lòng nhập đầy đủ thông tin giao hàng!');
                window.history.back();
              </script>";
        exit();
    }

    try {
        // Bắt đầu Transaction
        $conn->beginTransaction();

        // A. Thêm vào bảng orders (khớp chính xác các cột trong CSDL của bạn)
        $sql_order = "INSERT INTO orders (user_id, fullname, phone, address, total, payment_method, status, total_money) 
                      VALUES (:user_id, :fullname, :phone, :address, :total, :payment_method, 'Chờ xác nhận', :total_money)";
        
        $stmt_order = $conn->prepare($sql_order);
        $stmt_order->execute([
            ':user_id'        => $user_id,
            ':fullname'       => $fullname,
            ':phone'          => $phone,
            ':address'        => $address,
            ':total'          => $price,
            ':payment_method' => $payment_method,
            ':total_money'    => $price
        ]);

        // Lấy order_id vừa tạo
        $order_id = $conn->lastInsertId();

        // B. Thêm vào bảng order_details (khớp chính xác các cột)
        $sql_detail = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                       VALUES (:order_id, :product_id, 1, :price)";
        
        $stmt_detail = $conn->prepare($sql_detail);
        $stmt_detail->execute([
            ':order_id'   => $order_id,
            ':product_id' => $product_id,
            ':price'      => $price
        ]);

        // Hoàn tất lưu dữ liệu
        $conn->commit();

        // Thông báo và chuyển hướng về trang chủ
        echo "<script>
                alert('Đặt hàng thành công! Đơn hàng của bạn đang được xử lý.');
                window.location.href = '../index.php';
              </script>";
        exit();

    } catch (PDOException $e) {
        // Nếu lỗi thì rollback toàn bộ
        $conn->rollBack();
        echo "Lỗi hệ thống khi lưu đơn hàng: " . $e->getMessage();
    }

} else {
    header("Location: ../index.php");
    exit();
}