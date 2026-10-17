<?php
include "../../config/database.php";
include "../../includes/session.php";

if (isset($conn)) {
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

// Kiểm tra quyền Admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$error_msg = "";

// 1. XỬ LÝ XÓA ĐƠN HÀNG
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'])) {
    $delete_id = intval($_POST['order_id']);

    try {
        // Xóa chi tiết đơn hàng trước (để tránh lỗi ràng buộc khóa ngoại)
        $stmt_del_details = $conn->prepare("DELETE FROM order_details WHERE order_id = ?");
        $stmt_del_details->execute([$delete_id]);

        // Sau đó xóa đơn hàng chính
        $stmt_del_order = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt_del_order->execute([$delete_id]);

        header("Location: index.php?msg=deleted");
        exit();
    } catch (PDOException $e) {
        $error_msg = "Không thể xóa đơn hàng này: " . $e->getMessage();
    }
}

// 2. XỬ LÝ CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG & KHO HÀNG CHUẨN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = trim($_POST['status']);

    try {
        $stmt = $conn->prepare("SELECT status, total_money FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);
        $current_order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($current_order) {
            $old_status = trim($current_order['status']);

            // Chỉ xử lý khi trạng thái thực sự thay đổi
            if ($old_status !== $new_status) {
                
                // Lấy danh sách sản phẩm trong đơn hàng
                $stmt_items = $conn->prepare("SELECT product_id, quantity FROM order_details WHERE order_id = ?");
                $stmt_items->execute([$order_id]);
                $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

                // Nếu đơn chưa có chi tiết, tự động sửa dữ liệu từ tổng tiền
                if (empty($items)) {
                    $total_money = $current_order['total_money'];
                    $stmt_find_prod = $conn->prepare("SELECT id FROM products WHERE price = ? LIMIT 1");
                    $stmt_find_prod->execute([$total_money]);
                    $prod_found = $stmt_find_prod->fetch(PDO::FETCH_ASSOC);

                    if ($prod_found) {
                        $stmt_fix_detail = $conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, 1, ?)");
                        $stmt_fix_detail->execute([$order_id, $prod_found['id'], $total_money]);
                        $items = [['product_id' => $prod_found['id'], 'quantity' => 1]];
                    }
                }

                // Phân nhóm trạng thái
                $deducted_group = ['Đang giao', 'Đã giao'];     // Nhóm ĐÃ TRỪ KHO
                $non_deducted_group = ['Chờ xác nhận', 'Đã hủy']; // Nhóm CHƯA/KHÔNG TRỪ KHO

                // TRƯỜNG HỢP 1: Chuyển từ (Chờ xác nhận / Đã hủy) -> (Đang giao / Đã giao) => TRỪ KHO
                if (in_array($old_status, $non_deducted_group) && in_array($new_status, $deducted_group)) {
                    foreach ($items as $item) {
                        $stmt_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
                        $stmt_stock->execute([$item['quantity'], $item['product_id'], $item['quantity']]);
                    }
                }

                // TRƯỜNG HỢP 2: Chuyển từ (Đang giao / Đã giao) -> (Chờ xác nhận / Đã hủy) => CỘNG LẠI KHO
                if (in_array($old_status, $deducted_group) && in_array($new_status, $non_deducted_group)) {
                    foreach ($items as $item) {
                        $stmt_stock = $conn->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
                        $stmt_stock->execute([$item['quantity'], $item['product_id']]);
                    }
                }

                // Cập nhật trạng thái đơn hàng mới vào CSDL
                $stmt_update = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
                $stmt_update->execute([$new_status, $order_id]);
            }

            header("Location: index.php?msg=success");
            exit();
        }
    } catch (PDOException $e) {
        $error_msg = "Lỗi CSDL: " . $e->getMessage();
    }
}

// Lấy danh sách đơn hàng
$sql = "SELECT orders.*, users.fullname, users.phone, users.email 
        FROM orders 
        LEFT JOIN users ON orders.user_id = users.id 
        ORDER BY orders.id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container-fluid mt-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-danger fw-bold"><i class="fa-solid fa-cart-shopping"></i> Quản lý đơn hàng</h2>
        <a href="../dashboard.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Tổng quan</a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Cập nhật trạng thái thành công!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            Đã xóa đơn hàng thành công!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error_msg) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-danger">
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Số điện thoại</th>
                        <th>Địa chỉ giao hàng</th>
                        <th>Tổng tiền</th>
                        <th>Ngày đặt</th>
                        <th>Trạng thái</th>
                        <th width="220" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><b>#<?= $order['id'] ?></b></td>
                                <td>
                                    <strong><?= htmlspecialchars($order['fullname'] ?? 'Khách vãng lai') ?></strong>
                                    <br><small class="text-muted"><?= htmlspecialchars($order['email'] ?? '') ?></small>
                                </td>
                                <td><?= htmlspecialchars($order['phone'] ?? 'Chưa cập nhật') ?></td>
                                <td><?= htmlspecialchars($order['address'] ?? 'N/A') ?></td>
                                <td class="text-danger fw-bold"><?= number_format($order['total_money'] ?? 0) ?> đ</td>
                                <td><?= date('d/m/Y H:i', strtotime($order['created_at'] ?? 'now')) ?></td>
                                <td>
                                    <?php
                                    $status = trim($order['status'] ?? 'Chờ xác nhận');
                                    $badgeClass = 'bg-warning text-dark';

                                    if ($status === 'Đã giao') {
                                        $badgeClass = 'bg-success';
                                    } elseif ($status === 'Đã hủy') {
                                        $badgeClass = 'bg-danger';
                                    } elseif ($status === 'Đang giao') {
                                        $badgeClass = 'bg-info text-dark';
                                    }
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <!-- Form Cập nhật Trạng thái -->
                                        <form method="POST" action="index.php" class="d-flex align-items-center gap-1">
                                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                            <select name="status" class="form-select form-select-sm" style="width: 120px;">
                                                <option value="Chờ xác nhận" <?= $status === 'Chờ xác nhận' ? 'selected' : '' ?>>Chờ xác nhận</option>
                                                <option value="Đang giao" <?= $status === 'Đang giao' ? 'selected' : '' ?>>Đang giao</option>
                                                <option value="Đã giao" <?= $status === 'Đã giao' ? 'selected' : '' ?>>Đã giao</option>
                                                <option value="Đã hủy" <?= $status === 'Đã hủy' ? 'selected' : '' ?>>Đã hủy</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn btn-sm btn-primary" title="Lưu trạng thái">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>

                                        <!-- Form Xóa Đơn Hàng -->
                                        <form method="POST" action="index.php" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đơn hàng #<?= $order['id'] ?> này không?');">
                                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                            <button type="submit" name="delete_order" class="btn btn-sm btn-danger" title="Xóa đơn hàng">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Chưa có đơn hàng nào!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>