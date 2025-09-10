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
      background: linear-gradient(135deg, #00c6ff, #0072ff);
      font-family: "Prompt", sans-serif;
    }
    .card {
      border-radius: 1.2rem;
      border: none;
    }
    .form-control {
      border-radius: 0.6rem;
      padding-left: 2.5rem;
    }
    .form-label {
      font-weight: 500;
    }
    .input-group-text {
      border-radius: 0.6rem 0 0 0.6rem;
    }
    h2 {
      font-weight: 700;
    }
    .btn {
      border-radius: 50px;
      padding: 0.5rem 1.5rem;
    }
  </style>
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center min-vh-100"> 
    <div class="card shadow-lg p-4 w-100 bg-white" style="max-width: 500px;">   
      <h2 class="text-center text-primary mb-4">✨ สมัครสมาชิก ✨</h2>

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
          <button type="submit" class="btn btn-primary">✅ สมัครสมาชิก</button>
          <a href="login.php" class="btn btn-outline-secondary">🔑 Login</a>
        </div>
      </form>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

