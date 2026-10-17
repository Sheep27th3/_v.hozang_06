<?php
session_start();

// 1. Nhúng file kết nối CSDL
require_once "../config/database.php";

// 2. Lấy ID sản phẩm từ URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    echo "<script>alert('Sản phẩm không hợp lệ!'); window.location.href='../index.php';</script>";
    exit();
}

// 3. Truy vấn thông tin sản phẩm từ CSDL
try {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id AND status = 1");
    $stmt->execute(['id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "<script>alert('Sản phẩm không tồn tại hoặc đã ngừng kinh doanh!'); window.location.href='../index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    die("Lỗi truy vấn dữ liệu: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận thanh toán - <?= htmlspecialchars($product['name']) ?></title>
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-danger m-0">
            <i class="fa-solid fa-cart-shopping"></i> XÁC NHẬN ĐƠN HÀNG & THANH TOÁN
        </h3>
        <a href="../index.php" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Quay lại trang chủ
        </a>
    </div>

    <div class="row">
        <!-- CỘT BÊN TRÁI: THÔNG TIN CHI TIẾT SẢN PHẨM -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold m-0 text-primary">
                        <i class="fa-solid fa-mobile-screen-button"></i> Thông tin sản phẩm đã chọn
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Hình ảnh sản phẩm -->
                        <div class="col-md-5 text-center mb-3">
                            <img src="../assets/uploads/<?= htmlspecialchars($product['image']) ?>" 
                                 alt="<?= htmlspecialchars($product['name']) ?>" 
                                 class="img-fluid rounded border p-2" 
                                 style="max-height: 230px; object-fit: contain;">
                        </div>

                        <!-- Thông số cơ bản & Giá -->
                        <div class="col-md-7">
                            <h4 class="fw-bold"><?= htmlspecialchars($product['name']) ?></h4>
                            <p class="text-danger fs-4 fw-bold mb-1">
                                <?= number_format($product['price'], 0, ',', '.') ?> đ
                            </p>
                            <?php if (!empty($product['old_price']) && $product['old_price'] > $product['price']): ?>
                                <p class="text-muted text-decoration-line-through small">
                                    <?= number_format($product['old_price'], 0, ',', '.') ?> đ
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($product['color'])): ?>
                                <div class="mb-2">
                                    <strong>Màu sắc:</strong> 
                                    <span class="badge bg-secondary fs-6"><?= htmlspecialchars($product['color']) ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($product['storage'])): ?>
                                <div class="mb-2">
                                    <strong>Bộ nhớ trong:</strong> 
                                    <span class="badge bg-light text-dark border"><?= htmlspecialchars($product['storage']) ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($product['ram'])): ?>
                                <div class="mb-2">
                                    <strong>RAM:</strong> <?= htmlspecialchars($product['ram']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr>

                    <!-- Cấu hình chi tiết -->
                    <h6 class="fw-bold text-secondary mb-3"><i class="fa-solid fa-microchip"></i> Cấu hình & Thông số kỹ thuật</h6>
                    <table class="table table-striped table-bordered small">
                        <tbody>
                            <?php if (!empty($product['screen'])): ?>
                                <tr>
                                    <th style="width: 35%;">Màn hình</th>
                                    <td><?= htmlspecialchars($product['screen']) ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (!empty($product['cpu'])): ?>
                                <tr>
                                    <th>Chipset (CPU)</th>
                                    <td><?= htmlspecialchars($product['cpu']) ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (!empty($product['camera'])): ?>
                                <tr>
                                    <th>Camera</th>
                                    <td><?= htmlspecialchars($product['camera']) ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (!empty($product['battery'])): ?>
                                <tr>
                                    <th>Pin & Sạc</th>
                                    <td><?= htmlspecialchars($product['battery']) ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <?php if (!empty($product['description'])): ?>
                        <div class="mt-3">
                            <h6 class="fw-bold text-secondary">Mô tả sản phẩm:</h6>
                            <p class="small text-muted mb-0"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- CỘT BÊN PHẢI: FORM NHẬP THÔNG TIN GIAO HÀNG -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold m-0 text-success"><i class="fa-solid fa-truck-fast"></i> Thông tin nhận hàng</h5>
                </div>
                <div class="card-body">
                    <form action="process_checkout.php" method="POST">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="price" value="<?= $product['price'] ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Họ và tên người nhận (*)</label>
                            <input type="text" name="fullname" class="form-control" placeholder="Nguyễn Văn A" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Số điện thoại (*)</label>
                            <input type="tel" name="phone" class="form-control" placeholder="0901234567" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Địa chỉ nhận hàng (*)</label>
                            <textarea name="address" class="form-control" rows="3" placeholder="Số nhà, tên đường, phường/xã, quận/huyện..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Ghi chú đơn hàng</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="Ghi chú thêm về thời gian giao hàng..."></textarea>
                        </div>

                        <div class="p-3 bg-light rounded border mb-3">
                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>Tổng thanh toán:</span>
                                <span class="text-danger"><?= number_format($product['price'], 0, ',', '.') ?> đ</span>
                            </div>
                        </div>

                        <button type="submit" name="btn_order" class="btn btn-danger btn-lg w-100 fw-bold">
                            XÁC NHẬN ĐẶT HÀNG
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>