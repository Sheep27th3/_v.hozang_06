<?php
require_once '../../config/database.php'; // Đường dẫn tới file kết nối CSDL của bạn

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status'];

    // 1. Lấy thông tin và trạng thái hiện tại của đơn hàng
    $stmt = $conn->prepare("SELECT status FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_order = $result->fetch_assoc();

    if ($current_order) {
        $old_status = $current_order['status'];

        // 2. Nếu chuyển từ "Chờ xử lý" sang "Đang giao" hoặc "Hoàn thành" -> Tiến hành TRỪ TỒN KHO
        if ($old_status === 'Chờ xử lý' && ($new_status === 'Đang giao' || $new_status === 'Hoàn thành')) {
            
            // Lấy danh sách sản phẩm và số lượng mua trong đơn hàng này
            $detail_stmt = $conn->prepare("SELECT product_id, quantity FROM order_details WHERE order_id = ?");
            $detail_stmt->bind_param("i", $order_id);
            $detail_stmt->execute();
            $details = $detail_stmt->get_result();

            // Trừ kho từng sản phẩm
            while ($item = $details->fetch_assoc()) {
                $p_id = $item['product_id'];
                $qty = $item['quantity'];

                $update_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
                $update_stock->bind_param("iii", $qty, $p_id, $qty);
                $update_stock->execute();
            }
        }<?php
require_once '../../config/database.php'; // Đường dẫn tới file kết nối CSDL

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id   = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $new_status = isset($_POST['status']) ? trim($_POST['status']) : '';

    if ($order_id > 0 && !empty($new_status)) {
        try {
            // 1. Lấy trạng thái hiện tại của đơn hàng
            $stmt = $conn->prepare("SELECT status FROM orders WHERE id = :id");
            $stmt->execute([':id' => $order_id]);
            $old_status = $stmt->fetchColumn();

            // Nếu trạng thái mới khác trạng thái hiện tại thì xử lý
            if ($old_status !== false && $old_status !== $new_status) {

                // Bắt đầu transaction để đảm bảo dữ liệu toàn vẹn
                $conn->beginTransaction();

                // Lấy danh sách sản phẩm và số lượng trong đơn hàng
                $detail_stmt = $conn->prepare("SELECT product_id, quantity FROM order_details WHERE order_id = :order_id");
                $detail_stmt->execute([':order_id' => $order_id]);
                $details = $detail_stmt->fetchAll(PDO::FETCH_ASSOC);

                // Định nghĩa nhóm trạng thái ĐÃ TRỪ KHO và CHƯA TRỪ KHO
                $deducted_statuses     = ['Đang giao', 'Đã giao'];
                $non_deducted_statuses = ['Chờ xác nhận', 'Đã hủy', 'Hủy'];

                // TRƯỜNG HỢP 1: Chuyển từ CHƯA TRỪ KHO -> ĐÃ TRỪ KHO (Thực hiện TRỪ KHO)
                if (in_array($old_status, $non_deducted_statuses) && in_array($new_status, $deducted_statuses)) {
                    $update_stock = $conn->prepare("UPDATE products SET stock = stock - :qty WHERE id = :p_id AND stock >= :qty");
                    foreach ($details as $item) {
                        $update_stock->execute([
                            ':qty'  => $item['quantity'],
                            ':p_id' => $item['product_id']
                        ]);
                    }
                }

                // TRƯỜNG HỢP 2: Chuyển từ ĐÃ TRỪ KHO -> CHƯA TRỪ KHO (Thực hiện CỘNG LẠI KHO)
                if (in_array($old_status, $deducted_statuses) && in_array($new_status, $non_deducted_statuses)) {
                    $restore_stock = $conn->prepare("UPDATE products SET stock = stock + :qty WHERE id = :p_id");
                    foreach ($details as $item) {
                        $restore_stock->execute([
                            ':qty'  => $item['quantity'],
                            ':p_id' => $item['product_id']
                        ]);
                    }
                }

                // 2. Cập nhật trạng thái mới cho đơn hàng
                $update_order = $conn->prepare("UPDATE orders SET status = :status WHERE id = :id");
                $update_order->execute([
                    ':status' => $new_status,
                    ':id'     => $order_id
                ]);

                // Hoàn tất lưu CSDL
                $conn->commit();
            }

        } catch (PDOException $e) {
            // Nếu lỗi rollback lại dữ liệu
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            die("Lỗi cập nhật trạng thái đơn hàng: " . $e->getMessage());
        }
    }

    // Quay trở lại trang quản lý đơn hàng
    header("Location: index.php");
    exit();
}
?>

        // 3. Cập nhật trạng thái mới cho đơn hàng
        $update_order = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $update_order->bind_param("si", $new_status, $order_id);
        $update_order->execute();
    }

    // Quay trở lại trang quản lý đơn hàng
    header("Location: index.php");
    exit();
}
?>