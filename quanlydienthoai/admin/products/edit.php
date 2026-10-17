<?php
include "../../config/database.php";
include "../../includes/session.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit();
}

// Lấy thông tin sản phẩm hiện tại
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['update'])) {
    $category = $_POST['category_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $old_price = $_POST['old_price'];
    $stock = $_POST['stock'];

    $screen = $_POST['screen'];
    $cpu = $_POST['cpu'];
    $ram = $_POST['ram'];
    $storage = $_POST['storage'];
    $battery = $_POST['battery'];
    $camera = $_POST['camera'];
    $color = $_POST['color'];
    $description = $_POST['description'];

    $image = $product['image'];

    // Cập nhật ảnh mới nếu có chọn
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../../assets/uploads/" . $image);

        if ($product['image'] != "" && file_exists("../../assets/uploads/" . $product['image'])) {
            unlink("../../assets/uploads/" . $product['image']);
        }
    }

    $sql = $conn->prepare("
        UPDATE products SET 
            category_id = ?, name = ?, price = ?, old_price = ?, stock = ?, image = ?,
            description = ?, screen = ?, cpu = ?, ram = ?, storage = ?, battery = ?, camera = ?, color = ?
        WHERE id = ?
    ");

    $sql->execute([
        $category, $name, $price, $old_price, $stock, $image,
        $description, $screen, $cpu, $ram, $storage, $battery, $camera, $color, $id
    ]);

    header("Location: index.php?updated=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="card shadow p-4">
        <h2 class="text-warning mb-4">✏️ Sửa sản phẩm</h2>

        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Tên sản phẩm</label>
                    <input class="form-control" name="name" value="<?=$product['name']?>" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Danh mục</label>
                    <select class="form-select" name="category_id" required>
                        <?php
                        $list = $conn->query("SELECT * FROM categories");
                        while ($row = $list->fetch()) {
                            $selected = ($row['id'] == $product['category_id']) ? 'selected' : '';
                            echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Giá bán</label>
                    <input class="form-control" type="number" name="price" value="<?=$product['price']?>">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Giá cũ</label>
                    <input class="form-control" type="number" name="old_price" value="<?=$product['old_price']?>">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Số lượng</label>
                    <input class="form-control" type="number" name="stock" value="<?=$product['stock']?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label>RAM</label>
                    <input class="form-control" name="ram" value="<?=$product['ram']?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Bộ nhớ</label>
                    <input class="form-control" name="storage" value="<?=$product['storage']?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Màn hình</label>
                    <input class="form-control" name="screen" value="<?=$product['screen']?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Chip</label>
                    <input class="form-control" name="cpu" value="<?=$product['cpu']?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Pin</label>
                    <input class="form-control" name="battery" value="<?=$product['battery']?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Camera</label>
                    <input class="form-control" name="camera" value="<?=$product['camera']?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Màu sắc</label>
                    <input class="form-control" name="color" value="<?=$product['color']?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Ảnh sản phẩm</label>
                    <input type="file" class="form-control" name="image">
                    <?php if ($product['image']): ?>
                        <img src="../../assets/uploads/<?=$product['image']?>" width="80" class="mt-2 img-thumbnail">
                    <?php endif; ?>
                </div>

                <div class="col-12 mb-3"    >
                    <label>Mô tả</label>
                    <textarea class="form-control" rows="4" name="description"><?=$product['description']?></textarea>
                </div>
            </div>

            <button name="update" class="btn btn-warning text-white">Cập nhật sản phẩm</button>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
</div>
</body>
</html>