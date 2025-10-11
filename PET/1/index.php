<?php
session_start();
require_once 'config.php';
$isLoggedIn = isset($_SESSION['user_id']);

// ดึงหมวดหมู่ทั้งหมด
$categoriesStmt = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

// ตรวจสอบว่าผู้ใช้เลือกหมวดหมู่หรือไม่
$selectedCategory = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

// ดึงสินค้า (กรองหมวดหมู่ถ้ามีการเลือก)
if ($selectedCategory > 0) {
    $stmt = $conn->prepare("SELECT p.*, c.category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE p.category_id = :cat
        ORDER BY p.created_at DESC");
    $stmt->execute(['cat' => $selectedCategory]);
} else {
    $stmt = $conn->query("SELECT p.*, c.category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        ORDER BY p.created_at DESC");
}
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>หน้าหลัก - ร้านอาหารธรรมชาติ</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
:root {
    --green-leaf: #6BA368;
    --green-light: #A8D5BA;
    --brown-warm: #A9746E;
    --cream-light: #FFF1E6;
    --beige: #F5E9DA;
    --orange-light: #F4CDA5;
}
/* Dropdown in Navbar ขยายความกว้าง */
.form-select {
    border-radius: 50px;
    border-color: var(--green-light);
    height: calc(2rem + 6px);
    padding: 0.25rem 0.5rem;
    
    font-size: 0.85rem;
    min-width: 180px;  /* เพิ่มตรงนี้ให้กว้างขึ้น */
}
body { background: var(--cream-light); font-family: "Prompt", sans-serif; }
.navbar { background: var(--green-leaf); padding: 0.9rem 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
.navbar-brand { color: var(--cream-light) !important; font-weight: 700; font-size: 1.4rem; letter-spacing: 0.5px; }
.navbar-nav .nav-link, .navbar .btn { color: var(--cream-light) !important; border-radius: 50px; transition: 0.3s; }
.navbar-nav .nav-link:hover, .navbar .btn:hover { background-color: rgba(255,255,255,0.15); }
.form-select { border-radius: 50px; border-color: var(--green-light); height: calc(2rem + 6px); padding: 0.25rem 0.5rem; font-size: 0.85rem; }
.form-select:focus { border-color: var(--green-light); box-shadow: 0 0 5px rgba(168,213,186,0.5); outline: none; }
h2 { font-weight: 700; color: var(--green-leaf); text-align: center; margin: 1rem 0 2rem; text-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.card { border: none; border-radius: 1rem; overflow: hidden; background: var(--beige); box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: transform 0.25s ease, box-shadow 0.25s ease; }
.card:hover { transform: translateY(-6px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
.product-thumb { height: 220px; object-fit: cover; width: 100%; transition: transform 0.3s ease; }
.card:hover .product-thumb { transform: scale(1.06); }
.product-meta { font-size: 0.85rem; color: #6c7a91; }
.product-title { font-weight: 600; color: var(--brown-warm); font-size: 1.1rem; margin-bottom: 0.3rem; }
.price { font-weight: 700; color: var(--brown-warm); font-size: 1.1rem; }
.btn-gradient { background: linear-gradient(90deg, var(--green-leaf), var(--green-light)); color: #fff; border: none; border-radius: 50px; transition: all 0.3s ease; }
.btn-gradient:hover { background: linear-gradient(90deg, var(--green-light), var(--green-leaf)); box-shadow: 0 0 10px rgba(168,213,186,0.4); color: #fff; }
.btn-outline-primary { border-radius: 50px; border-color: var(--green-leaf); color: var(--green-leaf); }
.btn-outline-primary:hover { background: var(--green-leaf); color: #fff; }
footer { background: var(--green-leaf); color: var(--cream-light); text-align: center; padding: 1.2rem; margin-top: 3rem; border-top-left-radius: 1rem; border-top-right-radius: 1rem; font-size: 0.9rem; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg shadow-sm fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php"> 🍗 ร้านไก่ทอด </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end align-items-center" id="navbarNav">
      <!-- Category Filter in Navbar (auto submit) -->
      <form method="get" class="d-flex me-3" id="categoryForm">
        <select name="category_id" class="form-select form-select-sm" onchange="document.getElementById('categoryForm').submit()">
          <option value="0">ทุกหมวดหมู่</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= (int)$cat['category_id'] ?>" <?= $selectedCategory === (int)$cat['category_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['category_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </form>

      <?php if ($isLoggedIn): ?>
        <span class="me-3 text-light">👋 <?= htmlspecialchars($_SESSION['username']) ?> (<?= $_SESSION['role'] ?>)</span>
        <a href="profile.php" class="btn btn-outline-light btn-sm me-2">โปรไฟล์</a>
        <a href="cart.php" class="btn btn-outline-light btn-sm me-2">ตะกร้า</a>
        <a href="orders.php" class="btn btn-outline-light btn-sm me-2">คำสั่งซื้อ</a>
        <a href="logout.php" class="btn btn-sm text-dark">ออก</a>
      <?php else: ?>
        <a href="login.php" class="btn  btn-sm me-2 text-dark">เข้าสู่ระบบ</a>
        <a href="register.php" class="btn btn-outline-light btn-sm">สมัครสมาชิก</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- Content -->
<div class="container" style="margin-top:120px;">
  <h2>🛍️ รายการเมนู</h2>
  <div class="row g-4">
    <?php foreach ($products as $p): ?>
      <?php $img = !empty($p['image']) ? 'product_images/' . rawurlencode($p['image']) : 'product_images/no-image.jpg'; ?>
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card h-100">
          <a href="product_detail.php?id=<?= (int)$p['product_id'] ?>">
            <img src="<?= htmlspecialchars($img) ?>" class="product-thumb" alt="<?= htmlspecialchars($p['product_name']) ?>">
          </a>
          <div class="card-body d-flex flex-column">
            <span class="product-meta"><?= htmlspecialchars($p['category_name'] ?? 'หมวดหมู่ทั่วไป') ?></span>
            <h5 class="product-title"><?= htmlspecialchars($p['product_name']) ?></h5>
            <div class="price mb-3"><?= number_format((float)$p['price'], 2) ?> บาท</div>
            <div class="mt-auto d-flex gap-2">
              <?php if ($isLoggedIn): ?>
                <form action="cart.php" method="post" class="m-0 flex-fill">
                  <input type="hidden" name="product_id" value="<?= (int)$p['product_id'] ?>">
                  <input type="hidden" name="quantity" value="1">
                  <button type="submit" class="btn btn-gradient btn-sm w-100">
                    <i class="bi bi-cart-plus"></i> เพิ่มในตะกร้า
                  </button>
                </form>
              <?php else: ?>
                <small class="text-muted">เข้าสู่ระบบเพื่อสั่งซื้อ</small>
              <?php endif; ?>
              <a href="product_detail.php?id=<?= (int)$p['product_id'] ?>" 
                 class="btn btn-outline-primary btn-sm flex-fill">รายละเอียด</a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Footer -->
<footer>
  <p class="mb-0">© <?= date("Y") ?> ร้านอาหารธรรมชาติ | Designed by <strong>Kittisak</strong> ✨</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
