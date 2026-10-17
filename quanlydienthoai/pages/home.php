<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php 
// 1. LẤY DỮ LIỆU TỪ URL (CATEGORY HOẶC KEYWORD TÌM KIẾM)
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

// 2. LẤY TÊN DANH MỤC ĐỂ HIỂN THỊ TIÊU ĐỀ
$category_name = '';
if ($category_id > 0) {
    $stmt_cat = $conn->prepare("SELECT name FROM categories WHERE id = :cat_id");
    $stmt_cat->execute(['cat_id' => $category_id]);
    $cat_data = $stmt_cat->fetch();
    if ($cat_data) {
        $category_name = $cat_data['name'];
    }
}

// 3. XÁC ĐỊNH TIÊU ĐỀ KHU VỰC SẢN PHẨM
$page_title = 'ĐIỆN THOẠI NỔI BẬT';
if (!empty($keyword)) {
    $page_title = 'KẾT QUẢ TÌM KIẾM: "' . htmlspecialchars($keyword) . '"';
} elseif ($category_id > 0 && !empty($category_name)) {
    $page_title = htmlspecialchars($category_name);
}
?>

<div class="container mt-3">
    <div class="row">
        <!-- Sidebar danh mục -->
        <div class="col-lg-3 col-md-4">
            <?php include "includes/sidebar.php"; ?>
        </div>

        <!-- Chỉ hiện Banner khi ở Trang chủ (không bấm Tìm kiếm & không chọn Danh mục) -->
        <?php if ($category_id == 0 && empty($keyword)): ?>
            <div class="col-lg-6 col-md-8">
                <div id="slider" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php
                        $sql = "SELECT * FROM banners WHERE status=1";
                        $banner = $conn->query($sql);
                        $i = 0;
                        while ($row = $banner->fetch()) {
                        ?>
                            <div class="carousel-item <?= ($i == 0) ? 'active' : ''; ?>">
                                <img src="assets/uploads/<?= $row['image'] ?>" class="d-block w-100 banner-image">
                            </div>
                        <?php $i++; } ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 d-none d-lg-block">
                <div class="promo-box">
                    <h5>Khuyến mãi nổi bật</h5>
                    <img src="assets/uploads/banner1.jpg" class="w-100 mb-2">
                    <img src="assets/uploads/banner2.jpg" class="w-100">
                </div>
            </div>
        <?php else: ?>
            <!-- Khi bấm Tìm kiếm hoặc chọn Danh mục: Hiển thị thanh thông báo tiêu đề -->
            <div class="col-lg-9 col-md-8">
                <div class="p-3 bg-white rounded shadow-sm border mb-3">
                    <h4 class="text-uppercase text-danger fw-bold m-0">
                        <?= $page_title ?>
                    </h4>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- KHU VỰC HIỂN THỊ DANH SÁCH SẢN PHẨM -->
<div class="container mt-4">
    <h3 class="title-phone text-uppercase">
        <?= $page_title ?>
    </h3>

    <div class="row mt-3">
        <?php
        // 4. TRUY VẤN LỌC SẢN PHẨM THEO KEYWORD / CATEGORY_ID
        if (!empty($keyword)) {
            // Tìm kiếm sản phẩm theo tên
            $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE :keyword AND status = 1 ORDER BY id DESC");
            $stmt->execute(['keyword' => '%' . $keyword . '%']);
            $product = $stmt;
        } elseif ($category_id > 0) {
            // Lọc sản phẩm theo danh mục
            $stmt = $conn->prepare("SELECT * FROM products WHERE category_id = :cat_id AND status = 1 ORDER BY id DESC");
            $stmt->execute(['cat_id' => $category_id]);
            $product = $stmt;
        } else {
            // Lấy tất cả sản phẩm mới nhất ở trang chủ
            $product = $conn->query("SELECT * FROM products WHERE status = 1 ORDER BY id DESC LIMIT 12");
        }

        if ($product && $product->rowCount() > 0) {
            while ($row = $product->fetch()) {
            ?>
                <div class="col-lg-3 col-md-4 col-6 mb-4">
                    <div class="product-card p-2 border rounded bg-white h-100">
                        <div class="sale-tag bg-danger text-white px-2 py-1 rounded small display-inline-block">Trả góp 0%</div>
                        <img src="assets/uploads/<?= $row['image'] ?>" class="img-fluid my-2 d-block mx-auto" style="height: 180px; object-fit: contain;">
                        <h6 class="text-truncate"><?= htmlspecialchars($row['name']) ?></h6>
                        <p class="new-price text-danger fw-bold mb-0"><?= number_format($row['price']) ?> đ</p>
                        <p class="old-price text-muted text-decoration-line-through small">
                            <?= isset($row['old_price']) && $row['old_price'] > 0 ? number_format($row['old_price']) . ' đ' : '' ?>
                        </p>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="pages/checkout.php?id=<?= $row['id'] ?>" class="btn btn-danger w-100 mt-2">
                                Mua ngay
                            </a>
                        <?php else: ?>
                            <button type="button" onclick="requireLogin()" class="btn btn-danger w-100 mt-2">
                                Mua ngay
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php 
            }
        } else {
            // Hiển thị thông báo khi không tìm thấy kết quả
            echo '<div class="col-12"><p class="alert alert-warning">Không tìm thấy sản phẩm nào phù hợp!</p></div>';
        }
        ?>
    </div>
</div>

<script>
function requireLogin() {
    Swal.fire({
        title: 'Yêu cầu đăng nhập!',
        text: 'Vui lòng đăng nhập tài khoản để tiến hành mua hàng.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Đăng nhập ngay',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'pages/login.php';
        }
    });
}
</script>