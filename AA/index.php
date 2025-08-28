<?php
session_start();
if(!isset($_SESSION['username']) || !isset($_SESSION['role'])){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>หน้าหลัก</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-info">
  <div class="container py-5">
    <div class="card shadow-sm p-4 bg-primary-subtle border-0 rounded-4">
      <h1 class="text-center text-primary mb-3">ยินดีต้อนรับเข้าหน้าหลัก</h1>
      <p class="text-center fs-5">
        ผู้ใช้ <span class="fw-bold text-dark"><?= htmlspecialchars($_SESSION['username']); ?></span> 
        (<span class="text-secondary"><?= htmlspecialchars($_SESSION['role']); ?></span>)
      </p>
      <div class="text-center mt-4">
        <a href="logout.php" class="btn btn-danger">ออกจากระบบ</a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
