<?php
session_start();
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOremail = trim($_POST['username_or_email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$usernameOremail, $usernameOremail]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: admin/index.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>เข้าสู่ระบบ</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

  <style>
    body {
      background: #e9f7ef; /* เขียวอ่อน สบายตา */
      font-family: "Prompt", sans-serif;
    }
    .card {
      border-radius: 0.75rem;
      border: none;
      background: #fff;
    }
    h2 {
      font-weight: 600;
      color: #198754; /* เขียว Bootstrap */
    }
    .btn {
      border-radius: 0.5rem;
    }
    .btn-link {
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-sm p-4 w-100" style="max-width: 450px;">
      <h2 class="text-center mb-4">เข้าสู่ระบบ</h2>

      <?php if (isset($_GET['register']) && $_GET['register'] === 'register'): ?>
        <div class="alert alert-success">สมัครสมาชิกสำเร็จ กรุณาเข้าสู่ระบบ</div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post">
        <div class="mb-3">
          <label for="username_or_email" class="form-label">ชื่อผู้ใช้หรืออีเมล</label>
          <input type="text" name="username_or_email" id="username_or_email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">รหัสผ่าน</label>
          <input type="password" name="password" id="password" class="form-control" required>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-3">
          <button type="submit" class="btn btn-success">เข้าสู่ระบบ</button>
          <a href="register.php" class="btn btn-outline-secondary">สมัครสมาชิก</a>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

