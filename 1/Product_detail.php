<?php
    session_start();
    
    require_once 'config.php'; //เชื่อมต่อฐานข้อมูล

    if(!isset($_GET['id'])){ 
        header('Location: index.php');
        exit();
    }
    
    $product_id = $_GET['id'];


    $stmt = $conn->prepare("SELECT p.*, c.category_name
                        FROM products p
                        LEFT JOIN categories c ON p.category_id = c.category_id
                        WHERE p.product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    $isLoggedIn = isset($_SESSION['user_id']);

?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>รายละเอียดสินค้า</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
    }
    .card {
      border-radius: 0.75rem;
      overflow: hidden;
    }
    .card-body h3 {
      font-weight: 600;
      color: #198754; /* เขียว Bootstrap */
    }
    .price {
      font-size: 1.25rem;
      font-weight: bold;
      color: #dc3545; /* แดงราคาสินค้า */
    }
    .stock {
      font-size: 1rem;
      color: #6c757d;
    }
  </style>
</head>
<body class="container py-4">

  <a href="index.php" class="btn btn-secondary mb-3">← กลับหน้ารายการสินค้า</a>

  <div class="card shadow-sm">
    <div class="card-body">
      <h3 class="mb-3"><?= htmlspecialchars($product['product_name']) ?></h3>
      <h6 class="text-muted mb-3">หมวดหมู่: <?= htmlspecialchars($product['category_name']) ?></h6>

      <p class="price">ราคา: <?= htmlspecialchars($product['price']) ?> บาท</p>
      <p class="stock">คงเหลือ: <?= htmlspecialchars($product['stock']) ?> ชิ้น</p>

      <?php if ($isLoggedIn): ?>
        <form action="cart.php" method="post" class="mt-3">
          <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
          <div class="mb-3">
            <label for="quantity" class="form-label">จำนวน</label>
            <input type="number" name="quantity" id="quantity" 
                   value="1" min="1" max="<?= $product['stock'] ?>" 
                   class="form-control" style="max-width: 120px;" required>
          </div>
          <button type="submit" class="btn btn-success">เพิ่มในตะกร้า</button>
        </form>
      <?php else: ?>
        <div class="alert alert-info mt-3">
          กรุณาเข้าสู่ระบบเพื่อสั่งซื้อสินค้า
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

