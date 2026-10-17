<?php
include "../../config/database.php";
include "../../includes/session.php";

// Kiểm tra quyền Admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Xử lý Xóa tài khoản khách hàng
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    // Chỉ cho phép xóa tài khoản role = 'user' để tránh xóa nhầm admin
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
    $stmt->execute([$user_id]);
    header("Location: index.php");
    exit();
}

// Lấy danh sách tài khoản khách hàng (không bao gồm admin)
$stmt = $conn->prepare("SELECT * FROM users WHERE role = 'user' ORDER BY id DESC");
$stmt->execute();
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Khách hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container-fluid mt-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-danger fw-bold"><i class="fa-solid fa-users"></i> Quản lý khách hàng</h2>
        <a href="../dashboard.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Tổng quan</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-danger">
                    <tr>
                        <th width="80">ID</th>
                        <th>Họ và tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Địa chỉ</th>
                        <th>Ngày tạo</th>
                        <th width="100" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><b>#<?= $user['id'] ?></b></td>
                                <td>
                                    <span class="fw-bold text-dark"><?= htmlspecialchars($user['fullname'] ?? 'Chưa cập nhật') ?></span>
                                </td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['phone'] ?? 'Chưa cập nhật') ?></td>
                                <td><?= htmlspecialchars($user['address'] ?? 'Chưa cập nhật') ?></td>
                                <td><?= isset($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : 'N/A' ?></td>
                                <td class="text-center">
                                    <a href="index.php?delete=<?= $user['id'] ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?')" 
                                       title="Xóa tài khoản">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Chưa có tài khoản khách hàng nào!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>