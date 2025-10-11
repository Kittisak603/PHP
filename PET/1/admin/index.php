<?php
require_once '../config.php';
require_once 'auth_admin.php';
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>แผงควบคุมผู้ดูแลระบบ</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600;700&display=swap');

    body {
        font-family: "Prompt", sans-serif;
        background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
        min-height: 100vh;
        color: #1e2a47;
    }

    /* 🔹 Navbar */
    .navbar {
        backdrop-filter: blur(12px);
        background: rgba(30, 42, 71, 0.85);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }
    .navbar-brand {
        font-weight: 700;
        color: #ffd166 !important;
    }
    .nav-link {
        color: #f0f0f0 !important;
        transition: 0.3s;
        font-weight: 500;
    }
    .nav-link:hover {
        color: #ffd166 !important;
        transform: translateY(-2px);
    }

    /* 🔹 Dashboard Header */
    .dashboard-header {
        text-align: center;
        margin: 3rem 0;
    }
    .dashboard-header h2 {
        font-weight: 700;
        color: #2b3b63;
        text-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .dashboard-header p {
        color: #6c7a91;
    }

    /* 🔹 Card Option */
    .card-option {
        border: none;
        border-radius: 1.2rem;
        padding: 2rem;
        color: #fff;
        text-align: center;
        transition: all 0.4s ease;
        font-weight: 600;
        position: relative;
        overflow: hidden;
    }
    .card-option i {
        font-size: 2.8rem;
        margin-bottom: 1rem;
    }
    .card-option:hover {
        transform: translateY(-8px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }
    .card-option::after {
        content: "";
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(255, 255, 255, 0.1);
        opacity: 0;
        transition: 0.4s;
    }
    .card-option:hover::after {
        opacity: 1;
    }

    /* 🔹 Gradient Animations */
    .bg-primary {
        background: linear-gradient(45deg, #1e2a47, #2b3b63, #3b5998);
        background-size: 200% 200%;
        animation: gradientMove 5s ease infinite;
    }
    .bg-success {
        background: linear-gradient(45deg, #b88a44, #d4a74f, #ffce66);
        background-size: 200% 200%;
        animation: gradientMove 6s ease infinite;
    }
    .bg-warning {
        background: linear-gradient(45deg, #f6b93b, #e58e26, #ffbe76);
        background-size: 200% 200%;
        animation: gradientMove 6s ease infinite;
    }
    .bg-dark {
        background: linear-gradient(45deg, #6c5ce7, #341f97, #4834d4);
        background-size: 200% 200%;
        animation: gradientMove 7s ease infinite;
    }

    @keyframes gradientMove {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* 🔹 Logout */
    .logout-btn {
        margin-top: 3rem;
        text-align: center;
    }
    .logout-btn .btn {
        border-radius: 50px;
        padding: 0.6rem 2rem;
        font-weight: 600;
        transition: 0.3s;
    }
    .logout-btn .btn:hover {
        transform: scale(1.05);
    }
</style>
</head>
<body>

<!-- 🔸 Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
  <div class="container">
    <a class="navbar-brand" href="index.php">
        <i class="bi bi-speedometer2 me-1"></i> Admin Panel
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarAdmin">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="products.php"><i class="bi bi-box-seam me-1"></i>สินค้า</a></li>
        <li class="nav-item"><a class="nav-link" href="orders.php"><i class="bi bi-cart-check me-1"></i>คำสั่งซื้อ</a></li>
        <li class="nav-item"><a class="nav-link" href="users.php"><i class="bi bi-people me-1"></i>สมาชิก</a></li>
        <li class="nav-item"><a class="nav-link" href="categories.php"><i class="bi bi-tags me-1"></i>หมวดหมู่</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['username']) ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow">
            <li><a class="dropdown-item" href="../logout.php"><i class="bi bi-box-arrow-right me-1"></i> ออกจากระบบ</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container" style="padding-top: 100px;">

    <!-- Header -->
    <div class="dashboard-header">
        <h2>📊 แผงผู้ดูแลระบบ</h2>
        <p>ยินดีต้อนรับ, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>
    </div>

    <!-- Options -->
    <div class="row g-4">
        <div class="col-md-3">
            <a href="products.php" class="card-option bg-primary d-block text-decoration-none">
                <i class="bi bi-box-seam"></i>
                <h5 class="mt-2">จัดการสินค้า</h5>
            </a>
        </div>
        <div class="col-md-3">
            <a href="orders.php" class="card-option bg-success d-block text-decoration-none">
                <i class="bi bi-cart-check"></i>
                <h5 class="mt-2">จัดการคำสั่งซื้อ</h5>
            </a>
        </div>
        <div class="col-md-3">
            <a href="users.php" class="card-option bg-warning d-block text-decoration-none">
                <i class="bi bi-people"></i>
                <h5 class="mt-2">จัดการสมาชิก</h5>
            </a>
        </div>
        <div class="col-md-3">
            <a href="categories.php" class="card-option bg-dark d-block text-decoration-none">
                <i class="bi bi-tags"></i>
                <h5 class="mt-2">จัดการหมวดหมู่</h5>
            </a>
        </div>
    </div>

    <!-- Logout -->
    <div class="logout-btn">
        <a href="../logout.php" class="btn btn-danger">
            <i class="bi bi-box-arrow-right"></i> ออกจากระบบ
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
