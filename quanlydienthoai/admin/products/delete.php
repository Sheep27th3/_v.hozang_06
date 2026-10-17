<?php
include "../../config/database.php";
include "../../includes/session.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

if(isset($_GET['id'])){

    $id = $_GET['id'];

    // Lấy tên ảnh để xóa
    $sql = $conn->prepare("SELECT image FROM products WHERE id=?");
    $sql->execute([$id]);

    $product = $sql->fetch();

    if($product){

        if($product['image'] != "" && file_exists("../../assets/uploads/".$product['image'])){
            unlink("../../assets/uploads/".$product['image']);
        }

        $delete = $conn->prepare("DELETE FROM products WHERE id=?");
        $delete->execute([$id]);

    }

    header("Location:index.php?delete=1");
    exit();
}
?>