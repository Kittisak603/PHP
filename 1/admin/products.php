<?php
require '../config.php';// TODO: เชื่อมต่อฐานข้อมูลด้วย PDO
require 'auth_admin.php';// TODO: การ์ดสิทธิ์ (Admin Guard)
// แนวทาง: ถ้า !isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' -> redirect ไป ../login.php แล้ว exit;
// เพิ่มสินค้าใหม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['product_name']);
    $description = trim($_POST['description ']);
    $price = floatval($_POST['price']); // floatval() ใช้แปลงเป็น float
    $stock = intval($_POST['stock']); // intval() ใช้แปลงเป็น integer
    $category_id = intval($_POST['category_id']);

    // ค่าที่ได้จากฟอร์มเป็น string เสมอ

    if ($name && $price > 0) { // ตรวจสอบชื่อ และราคาสินค้า
        $stmt = $conn->prepare("INSERT INTO products(product_id,product_name,description,price,stock) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name,$description,$price,$stock,$category_id ]);
        header("Location: products.php");
        exit;
    }
}

// ลบสินค้า
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->execute([$product_id ]);
    header("Location: products.php");
    exit;
}

// ดึงรายการสินค้า
$stmt = $conn->query("SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id ORDER BY p.created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงหมวดหมู่

$categories = $conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>จัดการสินค้า</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: #f1f3f5;
      font-family: "Prompt", sans-serif;
    }

    .header-box {
      background: linear-gradient(135deg, #0d6efd, #20c997);
      color: #fff;
      padding: 1.5rem 2rem;
      border-radius: 1rem;
      margin-bottom: 2rem;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .card-custom {
      border-radius: 1rem;
      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
      margin-bottom: 2rem;
    }

    table {
      background: #fff;
      border-radius: 0.75rem;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    thead {
      background: linear-gradient(90deg, #198754, #20c997);
      color: #fff;
    }

    tbody tr:hover {
      background: #f1fdf5;
    }

    th, td {
      vertical-align: middle !important;
    }

    .btn {
      border-radius: 25px;
      font-size: 0.85rem;
      padding: 0.35rem 0.9rem;
    }

    .form-control, .form-select, textarea {
      border-radius: 0.6rem;
    }
  </style>
</head>
<body class="container py-4">

  <!-- Header -->
  <div class="header-box d-flex justify-content-between align-items-center">
    <h2 class="mb-0">🛒 จัดการสินค้า</h2>
    <a href="index.php" class="btn btn-light shadow-sm">← กลับหน้าผู้ดูแล</a>
  </div>

  <!-- ฟอร์ม เพิ่มสินค้าใหม่ -->
  <div class="card card-custom">
    <div class="card-body">
      <h5 class="card-title mb-3">➕ เพิ่มสินค้าใหม่</h5>
      <form method="post" class="row g-3">
        <div class="col-md-4">
          <input type="text" name="product_name" class="form-control" placeholder="ชื่อสินค้า" required>
        </div>
        <div class="col-md-2">
          <input type="number" step="0.01" name="price" class="form-control" placeholder="ราคา" required>
        </div>
        <div class="col-md-2">
          <input type="number" name="stock" class="form-control" placeholder="จำนวน" required>
        </div>
        <div class="col-md-2">
          <select name="category_id" class="form-select" required>
            <option value="">เลือกหมวดหมู่</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12">
          <textarea name="description" class="form-control" placeholder="รายละเอียดสินค้า" rows="2"></textarea>
        </div>
        <div class="col-12">
          <button type="submit" name="add_product" class="btn btn-primary">เพิ่มสินค้า</button>
        </div>
      </form>
    </div>
  </div>

  <!-- รายการสินค้า -->
  <h5 class="mb-3">📋 รายการสินค้า</h5>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th>ชื่อสินค้า</th>
          <th>หมวดหมู่</th>
          <th>ราคา</th>
          <th>คงเหลือ</th>
          <th>รูป</th>
          <th class="text-center">การจัดการ</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
          <td><?= htmlspecialchars($p['product_name']) ?></td>
          <td><?= htmlspecialchars($p['category_name']) ?></td>
          <td><?= number_format($p['price'],2) ?> บาท</td>
          <td><?= $p['stock'] ?></td>
          <td>
            <?php if (!empty($p['image'])): ?>
              <img src="../product_images/<?= htmlspecialchars($p['image']) ?>" 
                   alt="<?= htmlspecialchars($p['product_name']) ?>" 
                   class="img-thumbnail" style="width:60px;height:60px;object-fit:cover;">
            <?php else: ?>
              <span class="text-muted">—</span>
            <?php endif; ?>
          </td>
          <td class="text-center">
            <a href="edit_products.php?id=<?= $p['product_id'] ?>" class="btn btn-warning btn-sm shadow-sm">✏️ แก้ไข</a>
            <a href="products.php?delete=<?= $p['product_id'] ?>" class="btn btn-danger btn-sm shadow-sm"
               onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบสินค้านี้?')">🗑️ ลบ</a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($products)): ?>
          <tr>
            <td colspan="6" class="text-center text-muted">— ยังไม่มีสินค้า —</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</body>
</html>
