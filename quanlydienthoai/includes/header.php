<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLICKBUY - Hệ thống bán lẻ điện thoại</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/quanlydienthoai/assets/css/style.css">
</head>
<body>

<!-- TOP HEADER -->
<header class="bg-danger text-white py-2">
    <div class="container">
        <div class="row align-items-center">
            <!-- Logo kèm Icon điện thoại -->
            <div class="col-lg-3 col-md-3 col-4 d-flex align-items-center">
                <a href="/quanlydienthoai/index.php" class="text-white text-decoration-none fw-bold fs-3 d-flex align-items-center text-nowrap">
                    <i class="fas fa-mobile-alt me-2 border border-2 border-white rounded p-1 fs-5"></i>
                    CLICKBUY
                </a>
            </div>

            <!-- Ô tìm kiếm -->
            <div class="col-lg-4 col-md-4 col-8">
                <form action="/quanlydienthoai/index.php" method="GET" class="d-flex bg-white rounded-pill overflow-hidden px-2 py-1">
                    <input 
                        type="text" 
                        name="keyword" 
                        class="form-control border-0 shadow-none px-3" 
                        placeholder="Bạn cần tìm gì..." 
                        value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>"
                        autocomplete="off"
                    />
                    <button type="submit" class="btn text-muted border-0 bg-transparent">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- Menu bên phải -->
            <div class="col-lg-5 col-md-5 col-12 text-end d-flex align-items-center justify-content-end gap-2 text-nowrap mt-2 mt-md-0">
                <a href="#" class="text-white text-decoration-none small d-inline-flex align-items-center">
                    <i class="fas fa-phone-alt me-1"></i>Gọi mua hàng
                </a>
                <a href="#" class="text-white text-decoration-none small d-inline-flex align-items-center me-1">
                    <i class="fas fa-map-marker-alt me-1"></i>Cửa hàng
                </a>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Dropdown Tài khoản sau khi đăng nhập -->
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-outline-light btn-sm rounded-pill px-3 py-1 dropdown-toggle d-inline-flex align-items-center" 
                                type="button" 
                                id="userMenuDropdown" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false">
                            <i class="fas fa-user me-1"></i> 
                            <?= htmlspecialchars($_SESSION['user_name'] ?? 'Tài khoản'); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userMenuDropdown">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="/quanlydienthoai/pages/profile.php">
                                    <i class="fas fa-user-circle text-primary"></i> Thông tin tài khoản
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="/quanlydienthoai/pages/logout.php">
                                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="/quanlydienthoai/pages/login.php" class="btn btn-outline-light btn-sm rounded-pill px-3 py-1">
                        <i class="fas fa-sign-in-alt me-1"></i> Đăng nhập
                    </a>
                <?php endif; ?>

                <a href="/quanlydienthoai/admin/login.php" class="btn btn-dark btn-sm rounded-pill px-3 py-1 fw-bold">
                    <i class="fas fa-user-shield me-1"></i> Admin
                </a>
            </div>
        </div>
    </div>
</header>

<!-- NAVIGATION MENU -->
<nav class="bg-white border-bottom py-2 shadow-sm">
    <div class="container">
        <ul class="nav justify-content-between align-items-center m-0 p-0 fw-bold small text-nowrap">
            <li class="nav-item"><a class="nav-link text-dark px-2" href="/quanlydienthoai/index.php?category_id=1">iPhone</a></li>
            <li class="nav-item"><a class="nav-link text-dark px-2" href="/quanlydienthoai/index.php?category_id=2">Samsung</a></li>
            <li class="nav-item"><a class="nav-link text-dark px-2" href="/quanlydienthoai/index.php?category_id=3">Xiaomi</a></li>
            <li class="nav-item"><a class="nav-link text-dark px-2" href="/quanlydienthoai/index.php?category_id=4">Oppo</a></li>
            <li class="nav-item"><a class="nav-link text-dark px-2" href="/quanlydienthoai/index.php?category_id=5">Vivo</a></li>
            <li class="nav-item"><a class="nav-link text-dark px-2" href="/quanlydienthoai/index.php?category_id=6">Realme</a></li>
            <li class="nav-item"><a class="nav-link text-dark px-2" href="/quanlydienthoai/index.php?category_id=7">Nokia</a></li>
            <li class="nav-item"><a class="nav-link text-dark px-2" href="#">Laptop</a></li>
            <li class="nav-item"><a class="nav-link text-dark px-2" href="#">Tablet</a></li>
        </ul>
    </div>
</nav>