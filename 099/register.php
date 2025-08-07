<?php
require 'config.php';
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
     //รับค่าจากฟอร์ม
        $username = trim($_POST['username']);
        $fullname = trim($_POST['fullname']);
        $email = trim($_POST['email']);
        $password = ($_POST['password']);
        $confirm_password = ($_POST['confirm_password']);

        //นำข้อมูลไปบันทึกในฐานข้อมูล
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
                $sql = "INSERT INTO users (username, full_name, email, password, role)VALUES (?, ?, ?, ?, 'admin')";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$username, $fullname,$email,$hashedPassword]);
    }

            
            
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5  ">
        <h2>สมัครสมาชิก</h2>
        <form action="" method="post">
            <div class="row">

                <div class="col-md-6">
                    <label for="username">ชื่อผู้ใช้</label>
                    <input type="text" name="username" id="username" class="form-control"
                    placeholder="ชื่อผู้ใช้" required>
                </div>
            <div class="col-md-6"> 
                <label for="fullname">ชื่อ-นามสกุล</label>
                <input type="text" name="fullname" id="fullname" class="form-control"
                placeholder="ชื่อ-นามสกุล" required>
            </div>
            <div class="col-md-6">
                <label for="email">E-mail</label>
                <input type="text" name="email" id="email" class="form-control"
                placeholder="E-mail" required>
            </div>
            <div class="col-md-6">
                <label for="password">รหัสผ่าน</label>
                <input type="text" name="password" id="password" class="form-control"
                placeholder="รหัสผ่าน" required>
                
            </div>
            <div class="col-md-6">
            <label for="confirm_password">ยืนยันรหัสผ่าน</label>
                <input type="text" name="confirm_password" id="confirm_password" class="form-control"
                placeholder="ยืนยันรหัสผ่าน" required>
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
</html>