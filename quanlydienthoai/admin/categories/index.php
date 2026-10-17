<?php
include "../../config/database.php";
include "../../includes/session.php";

// Kiểm tra quyền Admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$message = "";
$error = "";

// Xử lý Thêm danh mục
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        if ($stmt->execute([$name])) {
            $message = "Thêm danh mục thành công!";
        } else {
            $error = "Có lỗi xảy ra, vui lòng thử lại!";
        }
    } else {
        $error = "Vui lòng nhập tên danh mục!";
    }
}

// Xử lý Xóa danh mục
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    if ($stmt->execute([$id])) {
        header("Location: index.php");
        exit();
    }
}

// Lấy danh sách danh mục
$categories = $conn->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Danh mục</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-danger fw-bold"><i class="fa-solid fa-list"></i> Quản lý danh mục</h2>
        <a href="../dashboard.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Tổng quan</a>
    </div>

    <!-- Thông báo -->
    <?php if ($message): ?>
        <div class="alert alert-success py-2"><?= $message ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger py-2"><?= $error ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Form Thêm Danh Mục -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-danger text-white fw-bold">
                    <i class="fa-solid fa-plus me-1"></i> Thêm danh mục mới
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Tên danh mục</label>
                            <input type="text" name="name" class="form-control" placeholder="Ví dụ: iPhone, Samsung..." required>
                        </div>
                        <button type="submit" name="add_category" class="btn btn-danger w-100">Lưu danh mục</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bảng Danh Sách Danh Mục -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-danger">
                            <tr>
                                <th width="80">ID</th>
                                <th>Tên danh mục</th>
                                <th width="100" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($categories) > 0): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <tr>
                                        <td><b>#<?= $cat['id'] ?></b></td>
                                        <td><span class="fw-bold text-dark"><?= htmlspecialchars($cat['name']) ?></span></td>
                                        <td class="text-center">
                                            <a href="index.php?delete=<?= $cat['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')" title="Xóa">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted">Chưa có danh mục nào!</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>