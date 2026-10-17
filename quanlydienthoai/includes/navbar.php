<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Topbar / Header (Đã tăng chiều cao với py-3 và min-height) -->
<div class="bg-danger text-white py-3 shadow-sm" style="min-height: 80px; display: flex; align-items: center;">
    <div class="container d-flex justify-content-between align-items-center">
        
        <!-- Logo (Đã phóng to) -->
        <a href="index.php" class="text-white text-decoration-none fw-bold fs-3 d-flex align-items-center gap-2">
            <i class="fa-solid fa-mobile-screen-button"></i> CLICKBUY
        </a>

        <!-- Thanh tìm kiếm (Đã tăng chiều cao input) -->
        <div class="flex-grow-1 mx-4" style="max-width: 500px;">
            <form action="index.php" method="GET" class="input-group">
                <input type="text" name="search" class="form-control border-0 py-2 fs-6" placeholder="Bạn cần tìm gì..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <button class="btn btn-light text-danger px-3" type="submit">
                    <i class="fa-solid fa-magnifying-glass fs-5"></i>
                </button>
            </form>
        </div>

        <!-- Các nút tiện ích bên phải (Đã tăng kích thước nút và chữ) -->
        <div class="d-flex align-items-center gap-3">
            <span class="d-none d-lg-inline fs-6"><i class="fa-solid fa-phone me-1"></i> Gọi mua hàng</span>
            <span class="d-none d-lg-inline fs-6"><i class="fa-solid fa-location-dot me-1"></i> Cửa hàng</span>

            <!-- Xử lý Nút Khách hàng / Đăng nhập -->
            <?php if (isset($_SESSION['user_id']) || isset($_SESSION['user'])): ?>
                <!-- ĐÃ ĐĂNG NHẬP -->
                <div class="dropdown">
                    <button class="btn btn-outline-light py-2 px-3 dropdown-toggle rounded-pill fw-bold fs-6" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-circle-user me-1"></i> 
                        <?= htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['user'] ?? 'Khách hàng') ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="userDropdown">
                        <li>
                            <a class="dropdown-item py-2 fs-6" href="pages/profile.php">
                                <i class="fa-solid fa-id-card me-2 text-primary"></i> Thông tin tài khoản
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2 fs-6 text-danger fw-bold" href="/quanlydienthoai/logout.php">
                                <i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất
                            </a>
                        </li>
                    </ul>
                </div>
            <?php else: ?>
                <!-- CHƯA ĐĂNG NHẬP -->
                <a href="pages/login.php" class="btn btn-outline-light py-2 px-3 rounded-pill fw-bold fs-6">
                    <i class="fa-solid fa-right-to-bracket me-1"></i> Đăng nhập
                </a>
            <?php endif; ?>

            <!-- Nút Quản trị Admin -->
            <a href="admin/login.php" class="btn btn-dark py-2 px-3 rounded-pill fw-bold fs-6">
                <i class="fa-solid fa-user-shield me-1"></i> Admin
            </a>
        </div>
    </div>
</div>

<!-- Thanh Menu Danh mục Thương hiệu -->
<div class="bg-white shadow-sm border-bottom py-2">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center overflow-auto text-nowrap">
            <a href="index.php?category=iphone" class="text-dark text-decoration-none fw-bold me-4 fs-6">iPhone</a>
            <a href="index.php?category=samsung" class="text-dark text-decoration-none fw-bold me-4 fs-6">Samsung</a>
            <a href="index.php?category=xiaomi" class="text-dark text-decoration-none fw-bold me-4 fs-6">Xiaomi</a>
            <a href="index.php?category=oppo" class="text-dark text-decoration-none fw-bold me-4 fs-6">Oppo</a>
            <a href="index.php?category=vivo" class="text-dark text-decoration-none fw-bold me-4 fs-6">Vivo</a>
            <a href="index.php?category=realme" class="text-dark text-decoration-none fw-bold me-4 fs-6">Realme</a>
            <a href="index.php?category=nokia" class="text-dark text-decoration-none fw-bold me-4 fs-6">Nokia</a>
            <a href="index.php?category=laptop" class="text-dark text-decoration-none fw-bold me-4 fs-6">Laptop</a>
            <a href="index.php?category=tablet" class="text-dark text-decoration-none fw-bold fs-6">Tablet</a>
        </div>
    </div>
</div>