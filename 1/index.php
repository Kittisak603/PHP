<?php
session_start();
require_once 'config.php';  //เชื่อมต่อฐานข้อมูล
    $isLoggedIn = isset($_SESSION['user_id']);

    $stmt = $conn->query("SELECT p.*, c.category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        ORDER BY p.created_at DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    




?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>หน้าหลัก - ร้านค้าออนไลน์</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body {
        background: linear-gradient(135deg, #f0f9ff, #cbebff);
        font-family: "Prompt", sans-serif;
    }
    h1, h2 {
        font-weight: 700;
        color: #0d6efd;
    }
    .card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: transform 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .btn {
        border-radius: 50px;
    }
    .navbar {
        border-radius: 0.75rem;
        margin-bottom: 2rem;
    }
    .product-thumb {
        height: 180px;
        object-fit: cover;
        width: 100%;
        border-top-left-radius: 1rem;
        border-top-right-radius: 1rem;
    }
    .product-title {
        font-weight: 600;
        color: #0d6efd;
    }
    .product-meta {
        font-size: 0.8rem;
        color: #0d6efd;
    }
    .price {
        font-weight: 700;
        color: #0d6efd;
    }
    .btn-primary-custom {
        background-color: #0d6efd;
        color: #fff;
        border: none;
    }
    .btn-primary-custom:hover {
        background-color: #0b5ed7;
    }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm px-4">
  <a class="navbar-brand fw-bold" href="index.php">🛒 ร้านค้าออนไลน์</a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
    <?php if ($isLoggedIn): ?>
      <span class="me-3">👋 <?= htmlspecialchars($_SESSION['username']) ?> (<?= $_SESSION['role'] ?>)</span>
      <a href="profile.php" class="btn btn-primary-custom btn-sm me-2">โปรไฟล์</a>
      <a href="cart.php" class="btn btn-primary-custom btn-sm me-2">ตะกร้า</a>
      <a href="logout.php" class="btn btn-outline-primary btn-sm">ออก</a>
    <?php else: ?>
      <a href="login.php" class="btn btn-primary-custom btn-sm me-2">เข้าสู่ระบบ</a>
      <a href="register.php" class="btn btn-outline-primary btn-sm">สมัครสมาชิก</a>
    <?php endif; ?>
  </div>
</nav>

<div class="container mt-4">
  <h2 class="text-center mb-4">🛍️ รายการสินค้า</h2>
  <div class="row g-4">
    <?php foreach ($products as $p): ?>
      <?php
        $img = !empty($p['image']) ? 'product_images/' . rawurlencode($p['image']) : 'product_images/no-image.jpg';
      ?>
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card h-100">
          <a href="product_detail.php?id=<?= (int)$p['product_id'] ?>">
            <img src="<?= htmlspecialchars($img) ?>" class="product-thumb" alt="<?= htmlspecialchars($p['product_name']) ?>">
          </a>
          <div class="card-body d-flex flex-column">
            <span class="product-meta"><?= htmlspecialchars($p['category_name'] ?? 'หมวดหมู่') ?></span>
            <h5 class="product-title"><?= htmlspecialchars($p['product_name']) ?></h5>
            <div class="price mb-3"><?= number_format((float)$p['price'], 2) ?> บาท</div>
            <div class="mt-auto d-flex gap-2">
              <?php if ($isLoggedIn): ?>
                <form action="cart.php" method="post" class="d-inline-flex m-0">
                  <input type="hidden" name="product_id" value="<?= (int)$p['product_id'] ?>">
                  <input type="hidden" name="quantity" value="1">
                  <button type="submit" class="btn btn-primary-custom btn-sm">เพิ่มในตะกร้า</button>
                </form>
              <?php else: ?>
                <small class="text-muted">เข้าสู่ระบบเพื่อสั่งซื้อ</small>
              <?php endif; ?>
              <a href="product_detail.php?id=<?= (int)$p['product_id'] ?>" class="btn btn-outline-primary btn-sm ms-auto">รายละเอียด</a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
