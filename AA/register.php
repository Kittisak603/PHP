<?php
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
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>สมัครสมาชิก</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-info">
  <div class="container d-flex justify-content-center align-items-center min-vh-100"> 
    <div class="card shadow-lg p-4 w-100 bg-primary-subtle rounded-4" style="max-width: 500px;">   
      <h2 class="text-center text-primary mb-4">สมัครสมาชิก</h2>
      <?php if (!empty($error)): // ถ้ามีข้อผิดพลาดให้แสดง ?>
                <div class="alert alert-danger">
                    <ul>
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
            <input type="text" name="username" id="username" class="form-control" placeholder="ชื่อผู้ใช้" required>
          </div>
          <div class="col-md-6"> 
            <label for="fullname" class="form-label">ชื่อ-นามสกุล</label>
            <input type="text" name="fullname" id="fullname" class="form-control" placeholder="ชื่อ-นามสกุล" required>
          </div>
          <div class="col-md-6">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="E-mail" required>
          </div>
          <div class="col-md-6">
            <label for="password" class="form-label">รหัสผ่าน</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="รหัสผ่าน" required>
          </div>
          <div class="col-md-6">
            <label for="confirm_password" class="form-label">ยืนยันรหัสผ่าน</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="ยืนยันรหัสผ่าน" required>
          </div>
        </div>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
          <button type="submit" class="btn btn-primary">สมัครสมาชิก</button>
          <a href="login.php" class="btn btn-outline-secondary">Login</a>
        </div>
      </form>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
