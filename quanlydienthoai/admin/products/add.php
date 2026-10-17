<?php
include "../../config/database.php";
include "../../includes/session.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$message = "";

if(isset($_POST['add'])){

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

    $image = "";

    if(isset($_FILES['image']) && $_FILES['image']['error']==0){

        $image = time()."_".$_FILES['image']['name'];

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../../assets/uploads/".$image
        );
    }

    $sql = $conn->prepare("
        INSERT INTO products(
            category_id,name,price,old_price,stock,image,
            description,screen,cpu,ram,storage,battery,camera,color
        )
        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    ");

    $sql->execute([
        $category,$name,$price,$old_price,$stock,$image,
        $description,$screen,$cpu,$ram,$storage,$battery,$camera,$color
    ]);

    header("Location:index.php?success=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thêm sản phẩm</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../../assets/css/style.css">

</head>

<body class="bg-light">

<div class="container py-4">

<div class="card shadow p-4">

<h2 class="text-danger mb-4">➕ Thêm sản phẩm</h2>

<form method="POST" enctype="multipart/form-data">

<div class="row">

<div class="col-md-6 mb-3">
<label>Tên sản phẩm</label>
<input class="form-control" name="name" required>
</div>

<div class="col-md-6 mb-3">
<label>Danh mục</label>

<select class="form-select" name="category_id" required>

<?php
$list=$conn->query("SELECT * FROM categories");
while($row=$list->fetch()){
?>
<option value="<?=$row['id']?>"><?=$row['name']?></option>
<?php } ?>

</select>

</div>

<div class="col-md-4 mb-3">
<label>Giá bán</label>
<input class="form-control" type="number" name="price">
</div>

<div class="col-md-4 mb-3">
<label>Giá cũ</label>
<input class="form-control" type="number" name="old_price">
</div>

<div class="col-md-4 mb-3">
<label>Số lượng</label>
<input class="form-control" type="number" name="stock">
</div>

<div class="col-md-6 mb-3">
<label>RAM</label>
<input class="form-control" name="ram">
</div>

<div class="col-md-6 mb-3">
<label>Bộ nhớ</label>
<input class="form-control" name="storage">
</div>

<div class="col-md-6 mb-3">
<label>Màn hình</label>
<input class="form-control" name="screen">
</div>

<div class="col-md-6 mb-3">
<label>Chip</label>
<input class="form-control" name="cpu">
</div>

<div class="col-md-6 mb-3">
<label>Pin</label>
<input class="form-control" name="battery">
</div>

<div class="col-md-6 mb-3">
<label>Camera</label>
<input class="form-control" name="camera">
</div>

<div class="col-md-6 mb-3">
<label>Màu sắc</label>
<input class="form-control" name="color">
</div>

<div class="col-md-6 mb-3">
<label>Ảnh sản phẩm</label>
<input type="file" class="form-control" name="image" required>
</div>

<div class="col-12 mb-3">
<label>Mô tả</label>
<textarea class="form-control" rows="4" name="description"></textarea>
</div>

</div>

<button name="add" class="btn btn-danger">
Lưu sản phẩm
</button>

<a href="index.php" class="btn btn-secondary">
Quay lại
</a>

</form>

</div>

</div>

</body>
</html>