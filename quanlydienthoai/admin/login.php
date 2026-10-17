<?php
include "../config/database.php";
include "../includes/session.php";

$error = "";

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = $conn->prepare("
        SELECT * FROM users
        WHERE email = ? AND role = 'admin'
    ");

    $sql->execute([$email]);

    if ($sql->rowCount()) {

        $admin = $sql->fetch();

        if ($password == $admin['password'] || md5($password) == $admin['password'] || password_verify($password, $admin['password'])) {

            $_SESSION['admin'] = $admin['fullname'];
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['role'] = "admin";

            header("Location: dashboard.php");
            exit();

        } else {
            $error = "Sai mật khẩu";
        }

    } else {
        $error = "Không phải Admin";
    }

}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card p-4 shadow">
                <h3 class="text-center text-danger mb-3">
                    ADMIN LOGIN
                </h3>

                <?php if ($error): ?>
                    <div class="alert alert-danger text-center py-2">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <input class="form-control mb-3"
                           type="email"
                           name="email"
                           placeholder="Email" required>

                    <input class="form-control mb-3"
                           type="password"
                           name="password"
                           placeholder="Mật khẩu" required>

                    <button class="btn btn-danger w-100"
                            name="login">
                        Đăng nhập
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>