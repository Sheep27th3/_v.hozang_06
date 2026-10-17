<?php
include "../config/database.php";
include "../includes/session.php";

$message = "";

if(isset($_POST['register'])){

    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = $_POST['password'];
    $address  = trim($_POST['address']);

    // Kiểm tra email đã tồn tại chưa
    $check = $conn->prepare("SELECT * FROM users WHERE email=?");
    $check->execute([$email]);

    if($check->rowCount() > 0){

        $message = "Email đã tồn tại!";

    }else{

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = $conn->prepare("
            INSERT INTO users(fullname,email,phone,password,address)
            VALUES(?,?,?,?,?)
        ");

        $sql->execute([
            $fullname,
            $email,
            $phone,
            $hash,
            $address
        ]);

        header("Location: login.php?success=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - CLICKBUY</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS của website -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <!-- Font Awesome -->
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card shadow-lg border-0 rounded-4 p-4">

                <h2 class="text-center text-danger fw-bold mb-4">
                    <i class="fa-solid fa-user-plus"></i> ĐĂNG KÝ
                </h2>

                <?php if($message!=""){ ?>
                    <div class="alert alert-danger"><?=$message?></div>
                <?php } ?>

                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <textarea name="address" rows="3" class="form-control"></textarea>
                    </div>

                    <button type="submit" name="register"
                        class="btn btn-danger w-100 py-2 fw-bold">
                        Đăng ký
                    </button>

                </form>

                <div class="text-center mt-3">
                    Đã có tài khoản?
                    <a href="login.php" class="text-danger fw-bold text-decoration-none">
                        Đăng nhập
                    </a>
                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>