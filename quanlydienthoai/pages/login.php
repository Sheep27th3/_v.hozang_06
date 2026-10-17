<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập - CLICKBUY</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<?php
include "../config/database.php";
include "../includes/session.php";

$error = "";

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $sql = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $sql->execute([$email]);

    if ($sql->rowCount() == 1) {
        $user = $sql->fetch();

        if (password_verify($password, $user['password'])) {
            // Lưu Session khách hàng
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['fullname']  = $user['fullname'];
            $_SESSION['user_name'] = $user['fullname']; // Đồng bộ tên hiển thị cho navbar
            $_SESSION['role']      = $user['role'];

            // Chuyển hướng về trang chủ
            header("Location: ../index.php");
            exit();
        } else {
            $error = "Mật khẩu không chính xác!";
        }
    } else {
        $error = "Email này chưa được đăng ký!";
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow border-0 p-4 rounded-3">
                <h3 class="text-center text-danger fw-bold mb-4">
                    <i class="fa-solid fa-right-to-bracket me-2"></i>ĐĂNG NHẬP
                </h3>

                <!-- Hiển thị thông báo lỗi nếu có -->
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="Nhập email của bạn..." required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mật khẩu</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu..." required>
                        </div>
                    </div>

                    <button type="submit" name="login" class="btn btn-danger w-100 fw-bold py-2 mt-2">
                        Đăng nhập
                    </button>
                </form>

                <div class="text-center mt-4">
                    <p class="mb-0">Chưa có tài khoản? <a href="register.php" class="text-danger fw-bold text-decoration-none">Đăng ký ngay</a></p>
                    <p class="mt-2 mb-0"><a href="../index.php" class="text-secondary text-decoration-none"><i class="fa-solid fa-house me-1"></i>Quay lại trang chủ</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>