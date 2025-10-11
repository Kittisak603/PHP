<!--if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = ($_POST['password']);
    $confirm_password = ($_POST['confirm_password']);

ใช้ตัวนี้ในการสมัครแอดมิน
require 'config.php';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = ($_POST['password']);
    $confirm_password = ($_POST['confirm_password']);

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, full_name, email, password, role) VALUES (?, ?, ?, ?, 'admin')";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $fullname, $email, $hashedPassword]);
}-->





<?php
require_once 'config.php';

$error = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        $error[] = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
    } 
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error[] = "อีเมลไม่ถูกต้อง";
    } 
    elseif ($password !== $confirm_password) {
        $error[] = "รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน";
    } 
    else {
        $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            $error[] = "ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้ไปแล้ว";
        }
    }
    if (empty($error)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, full_name, email, password, role) VALUES (?, ?, ?, ?, 'member')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username, $fullname, $email, $hashedPassword]);

        header("Location: login.php?register=success");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>สมัครสมาชิก</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
    body {
        background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
        font-family: "Prompt", sans-serif;
        color: #fff;
    }

    .card {
        border-radius: 1.5rem;
        border: none;
        background: rgba(255,255,255,0.05);
        backdrop-filter: blur(15px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.3);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.5);
    }

    h2 {
        font-weight: 700;
        color: #e6b53aff;
        text-align: center;
        margin-bottom: 1.5rem;
        text-shadow: 0 2px 6px rgba(0,0,0,0.5);
    }

    .form-label {
        color: #fff;
        font-weight: 500;
    }

    .form-control {
        border-radius: 50px;
        padding-left: 2.5rem;
        border: none;
        background: rgba(255,255,255,0.15);
        color: #fff;
        transition: background 0.3s;
    }

    .form-control:focus {
        background: rgba(255,255,255,0.25);
        box-shadow: none;
        color: #fff;
    }

    .input-group-text {
        border-radius: 0.6rem 0 0 0.6rem;
        background: rgba(255,255,255,0.2);
        color: #ffd369;
        border: none;
    }

    .btn {
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(90deg, #ffd369, #ff8c42);
        border: none;
        color: #1e2a47;
        box-shadow: 0 6px 20px rgba(255,216,105,0.4);
    }

    .btn-primary:hover {
        background: linear-gradient(90deg, #ff8c42, #ffd369);
        box-shadow: 0 8px 25px rgba(255,216,105,0.6);
        color: #1e2a47;
    }

    .btn-outline-secondary {
        border-radius: 50px;
        border-color: #ffd369;
        color: #ffd369;
    }

    .btn-outline-secondary:hover {
        background: #ffd369;
        color: #1e2a47;
    }

    /* Left side text styling */
    .left-panel h2 {
        margin-bottom: 1rem;
    }

    .left-panel p, .left-panel li {
        line-height: 1.4;
        margin: 0.3rem 0;
    }

    .left-panel ul {
        list-style: none;
        padding-left: 0;
    }

</style>
</head>

<body>
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg p-0 w-100" style="max-width: 900px; display: flex; flex-direction: row; overflow: hidden;">
        
        <!-- Left side: ข้อความ -->
        <div class="left-panel p-4 d-flex flex-column justify-content-center flex:1"
             style="background: linear-gradient(135deg, #8a6125ff, #696350ff);">
            <h2>ยินดีต้อนรับสู่ร้านไก่ทอด! 🍗✨</h2>
        </div>

        <!-- Right side: ฟอร์ม -->
        <div class="p-4 flex:1">
            <h2 class="mb-4 text-center">สมัครสมาชิก</h2>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($error as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="" method="post">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="username" class="form-label">ชื่อผู้ใช้</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" id="username" class="form-control" placeholder="ชื่อผู้ใช้" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="fullname" class="form-label">ชื่อ-นามสกุล</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                            <input type="text" name="fullname" id="fullname" class="form-control" placeholder="ชื่อ-นามสกุล" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">E-mail</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" id="email" class="form-control" placeholder="E-mail" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">รหัสผ่าน</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control" placeholder="รหัสผ่าน" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label for="confirm_password" class="form-label">ยืนยันรหัสผ่าน</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-check2-circle"></i></span>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="ยืนยันรหัสผ่าน" required>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-primary px-4">สมัครสมาชิก</button>
                    <a href="login.php" class="btn btn-outline-secondary px-4">เข้าสู่ระบบ</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



