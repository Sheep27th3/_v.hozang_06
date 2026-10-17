<?php
include "../../config/database.php";
include "../../includes/session.php";

// Kiểm tra quyền Admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Xử lý bộ lọc & tìm kiếm
$where = [];
$params = [];

if (!empty($_GET['keyword'])) {
    $where[] = "products.name LIKE ?";
    $params[] = "%" . trim($_GET['keyword']) . "%";
}

if (!empty($_GET['category'])) {
    $where[] = "products.category_id = ?";
    $params[] = $_GET['category'];
}

$sql = "SELECT products.*, categories.name AS category_name
        FROM products
        JOIN categories ON products.category_id = categories.id";

if (count($where) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

// Phân trang
$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$countSQL = str_replace("products.*, categories.name AS category_name", "COUNT(*)", $sql);
$countStmt = $conn->prepare($countSQL);
$countStmt->execute($params);
$totalRows = $countStmt->fetchColumn();
$totalPage = ceil($totalRows / $limit);

$sql .= " ORDER BY products.id DESC LIMIT $start, $limit";
$stmt = $conn->prepare($sql);
$stmt->execute($params);

// Thống kê nhanh
$totalProduct = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalCategory = $conn->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalStock = $conn->query("SELECT SUM(stock) FROM products")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container-fluid mt-4">
    <!-- Tiêu đề & Nút thêm -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-danger fw-bold">
            <i class="fa fa-mobile-screen-button"></i> Quản lý sản phẩm
        </h2>
        <div>
            <a href="../dashboard.php" class="btn btn-secondary me-2">
                <i class="fa fa-arrow-left"></i> Tổng quan
            </a>
            <a href="add.php" class="btn btn-danger">
                <i class="fa fa-plus"></i> Thêm sản phẩm
            </a>
        </div>
    </div>

    <!-- Thanh tìm kiếm & lọc -->
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-5">
            <input type="text" name="keyword" class="form-control" placeholder="Nhập tên điện thoại..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">Tất cả danh mục</option>
                <?php
                $list = $conn->query("SELECT * FROM categories");
                while ($c = $list->fetch()) {
                    $selected = (isset($_GET['category']) && $_GET['category'] == $c['id']) ? 'selected' : '';
                    echo "<option value='{$c['id']}' {$selected}>{$c['name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">
                <i class="fa fa-search"></i> Tìm kiếm
            </button>
        </div>
        <div class="col-md-2">
            <a href="index.php" class="btn btn-secondary w-100">Làm mới</a>
        </div>
    </form>

    <!-- Bảng sản phẩm -->
    <div class="card shadow-sm mb-4">
        <div class="card-body p-0">
            <table class="table table-bordered table-hover align-middle bg-white mb-0">
                <thead class="table-danger">
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Tên điện thoại</th>
                        <th>Danh mục</th>
                        <th>Giá bán</th>
                        <th>Tồn kho</th>
                        <th width="120" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($stmt->rowCount() > 0): ?>
                    <?php while ($row = $stmt->fetch()): ?>
                    <tr>
                        <td><b>#<?=$row['id']?></b></td>
                        <td class="text-center">
                            <img src="../../assets/uploads/<?=$row['image']?>" width="60" height="60" style="object-fit:contain" onerror="this.src='https://via.placeholder.com/60'">
                        </td>
                        <td>
                            <b class="text-dark"><?=$row['name']?></b>
                            <br>
                            <small class="text-muted"><?=$row['storage']?></small>
                        </td>
                        <td>
                            <span class="badge bg-primary"><?=$row['category_name']?></span>
                        </td>
                        <td>
                            <span class="text-danger fw-bold"><?=number_format($row['price'])?> đ</span>
                            <?php if ($row['old_price'] > 0): ?>
                                <br><small class="text-decoration-line-through text-muted"><?=number_format($row['old_price'])?> đ</small>
                            <?php endif; ?>
                        </td>
                        <td><?=$row['stock']?> máy</td>
                        <td class="text-center">
                            <a href="edit.php?id=<?=$row['id']?>" class="btn btn-warning btn-sm" title="Sửa">
                                <i class="fa fa-pen"></i>
                            </a>
                            <a href="delete.php?id=<?=$row['id']?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')" title="Xóa">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-3 text-muted">Không tìm thấy sản phẩm nào!</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Phân trang -->
    <?php if ($totalPage > 1): ?>
    <nav>
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPage; $i++): ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?=$i?>&keyword=<?=isset($_GET['keyword'])?$_GET['keyword']:''?>&category=<?=isset($_GET['category'])?$_GET['category']:''?>">
                        <?=$i?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>

    <!-- Thống kê phía dưới -->
    <div class="row mt-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm text-center p-3 border-danger">
                <h6 class="text-muted">Tổng sản phẩm</h6>
                <h3 class="text-danger mb-0"><?=$totalProduct?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm text-center p-3 border-primary">
                <h6 class="text-muted">Tổng danh mục</h6>
                <h3 class="text-primary mb-0"><?=$totalCategory?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm text-center p-3 border-success">
                <h6 class="text-muted">Tổng tồn kho</h6>
                <h3 class="text-success mb-0"><?=$totalStock ? $totalStock : 0?></h3>
            </div>
        </div>
    </div>
</div>

</body>
</html>