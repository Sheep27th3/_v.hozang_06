<?php
include "../config/database.php";
include "../includes/session.php";

// Kiểm tra quyền Admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Thống kê dữ liệu
$totalProducts = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalCategories = $conn->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalUsers = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClickBuy Admin - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .sidebar { min-height: 100vh; background-color: #212529; color: #fff; }
        .sidebar .nav-link { color: #cfd8dc; padding: 12px 20px; font-weight: 500; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #dc3545; color: #fff; border-radius: 5px; }
        .sidebar-brand { font-size: 1.5rem; font-weight: bold; color: #dc3545; text-align: center; padding: 20px 0; border-bottom: 1px solid #373b3e; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Menu bên trái -->
        <div class="col-md-2 sidebar p-3">
            <div class="sidebar-brand mb-3">
                <i class="fa-solid fa-mobile-screen-button"></i> CLICKBUY
            </div>
            <ul class="nav flex-column gap-1">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link active">
                        <i class="fa-solid fa-gauge me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="products/index.php" class="nav-link">
                        <i class="fa-solid fa-box me-2"></i> Sản phẩm
                    </a>
                </li>
                <li class="nav-item">
                    <a href="categories/index.php" class="nav-link">
                        <i class="fa-solid fa-list me-2"></i> Danh mục
                    </a>
                </li>
                <li class="nav-item">
                    <a href="orders/index.php" class="nav-link">
                        <i class="fa-solid fa-cart-shopping me-2"></i> Đơn hàng
                    </a>
                </li>
                <li class="nav-item">
                    <a href="users/index.php" class="nav-link">
                        <i class="fa-solid fa-users me-2"></i> Khách hàng
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <a href="logout.php" class="nav-link text-danger fw-bold">
                        <i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất
                    </a>
                </li>
            </ul>
        </div>

        <!-- Nội dung chính bên phải -->
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
                <h4 class="m-0 fw-bold text-secondary">Tổng quan hệ thống</h4>
                <div class="fw-semibold">
                    <i class="fa-solid fa-user-gear text-danger me-1"></i> Xin chào, 
                    <span class="text-danger"><?= $_SESSION['admin'] ?? 'Quản trị viên' ?></span>
                </div>
            </div>

            <!-- Thống kê Card -->
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 border-start border-danger border-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Sản phẩm</h6>
                                <h3 class="fw-bold m-0 text-danger"><?= $totalProducts ?></h3>
                            </div>
                            <i class="fa-solid fa-mobile-screen fs-1 text-danger opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 border-start border-primary border-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Danh mục</h6>
                                <h3 class="fw-bold m-0 text-primary"><?= $totalCategories ?></h3>
                            </div>
                            <i class="fa-solid fa-layer-group fs-1 text-primary opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 border-start border-success border-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Khách hàng</h6>
                                <h3 class="fw-bold m-0 text-success"><?= $totalUsers ?></h3>
                            </div>
                            <i class="fa-solid fa-users fs-1 text-success opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lối truy cập nhanh -->
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-white fw-bold">
                    <i class="fa-solid fa-rocket me-1 text-danger"></i> Lối truy cập nhanh
                </div>
                <div class="card-body">
                    <a href="products/add.php" class="btn btn-outline-danger me-2 mb-2">
                        <i class="fa-solid fa-plus me-1"></i> Thêm sản phẩm mới
                    </a>
                    <a href="products/index.php" class="btn btn-outline-secondary me-2 mb-2">
                        <i class="fa-solid fa-list me-1"></i> Quản lý danh sách sản phẩm
                    </a>
                    <a href="../index.php" target="_blank" class="btn btn-outline-dark mb-2">
                        <i class="fa-solid fa-globe me-1"></i> Xem trang chủ Khách hàng
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>