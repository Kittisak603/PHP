<?php
require 'config.php';

$error = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //รับค่าจากฟอร์ม
    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    //ตรวจสอบว่ากรอกข้อมูลมาครบหรือไม่
    if (empty($username) || empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        $error[] = "กรุณากรอกให้ครบทุกช่อง";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error[] = "กรุณากรอกอีเมลให้ถูกต้อง";
    } elseif ($password !== $confirm_password) {
        $error[] = "รหัสผ่านไม่ตรงกัน";
    } else {
        //ตรวจสอบว่ามีชื่อผู้ใช้หรืออีเมลถูกใช้ไปแล้วหรือไม่
        $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username, $email]);

        if ($stmt->rowCount() > 0) {
            $error[] = "ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้แล้ว";
        }
    }

    //ถ้าไม่มี error บันทึกข้อมูล
    if (empty($error)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, full_name, email, password, role) VALUES (?, ?, ?, ?, 'member')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username, $fullname, $email, $hashedPassword]);

        // ถ้าบันทึกสำเร็จให้เปลี่ยนไปหน้า login
        header("Location: login.php?register=success");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>สมัครสมาชิก</h2>
        <form action="" method="post">
            <?php if (!empty($error)): // ถ้ามีข้อผิดพลาดให้แสดง ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($error as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <label for="username">ชื่อผู้ใช้</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="ชื่อผู้ใช้" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="fullname">ชื่อ-นามสกุล</label>
                    <input type="text" name="fullname" id="fullname" class="form-control" placeholder="ชื่อ-นามสกุล" value="<?= isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : '' ?>" required>
                </div>   
                <div class="col-md-6">
                    <label for="email">E-mail</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="E-mail" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="password">รหัสผ่าน</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="รหัสผ่าน" required>
                </div>
                <div class="col-md-6">
                    <label for="confirm_password">ยืนยันรหัสผ่าน</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="ยืนยันรหัสผ่าน" required>
                </div>
            </div>
            <br>
            <div class="col-md-6">
                <button type="submit" class="btn btn-info">สมัครสมาชิก</button>
                <a href="login.php" class="btn btn-link">LOGIN</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
