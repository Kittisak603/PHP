<?php
require '../config.php'; // เชื่อมต่อฐานข้อมูลด้วย PDO
require 'auth_admin.php'; // ตรวจสอบสิทธิ์ admin

// --- เพิ่มสินค้าใหม่ ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']); 
    $stock = intval($_POST['stock']); 
    $category_id = intval($_POST['category_id']);

    if ($name && $price > 0 && $stock >= 0 && $category_id > 0) {
        $stmt = $conn->prepare("INSERT INTO products(product_name, description, price, stock, category_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $stock, $category_id]);
        header("Location: products.php");
        exit;
    }
}

// --- ลบสินค้า ---
if (isset($_GET['delete'])) {
    $product_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    header("Location: products.php");
    exit;
}

// --- ดึงรายการสินค้า ---
$stmt = $conn->query("SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id ORDER BY p.created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- ดึงหมวดหมู่ ---
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
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    font-family: "Prompt", sans-serif;
    color: #fff;
}
.header-box {
    background: linear-gradient(135deg, #7c838fff, #0c2a69ff);
    padding: 1.5rem 2rem;
    border-radius: 1rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 25px rgba(0,0,0,0.2);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.card-custom {
    border-radius: 1rem;
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(15px);
    box-shadow: 0 6px 25px rgba(0,0,0,0.3);
    margin-bottom: 2rem;
    transition: transform 0.3s, box-shadow 0.3s;
}
.card-custom:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.5);
}
table {
    background: rgba(255,255,255,0.05);
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    backdrop-filter: blur(10px);
}
thead {
    background: linear-gradient(90deg, rgba(25,135,84,0.8), rgba(32,201,151,0.8));
    color: #fff;
}
tbody tr {
    background: rgba(255,255,255,0.02);
    transition: background 0.3s;
}
tbody tr:hover {
    background: rgba(255,255,255,0.1);
}
th, td {
    vertical-align: middle !important;
    color: #fff;
}
.btn {
    border-radius: 25px;
    font-size: 0.85rem;
    padding: 0.35rem 0.9rem;
    transition: all 0.3s;
}
.btn-primary {
    background: linear-gradient(90deg, #ffd369, #ff8c42);
    color: #1e2a47;
    border: none;
    box-shadow: 0 6px 20px rgba(255,216,105,0.4);
}
.btn-primary:hover {
    background: linear-gradient(90deg, #ff8c42, #ffd369);
    box-shadow: 0 8px 25px rgba(255,216,105,0.6);
}
.btn-warning {
    background: linear-gradient(90deg, #b88a44, #d4a74f);
    border: none;
    color: #1e2a47;
    box-shadow: 0 4px 12px rgba(212,167,79,0.4);
}
.btn-warning:hover {
    background: linear-gradient(90deg, #d4a74f, #b88a44);
}
.btn-danger {
    background: linear-gradient(90deg, #c34b4b, #e07a7a);
    border: none;
    color: #fff;
    box-shadow: 0 4px 12px rgba(224,122,122,0.4);
}
.btn-danger:hover {
    background: linear-gradient(90deg, #e07a7a, #c34b4b);
}
.img-thumbnail {
    border-radius: 0.5rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    object-fit: cover;
}
</style>
</head>
<body class="container py-4">

<!-- Header -->
<div class="header-box">
    <h2>🛒 จัดการสินค้า</h2>
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
        <?php if (!empty($products)): ?>
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
                             class="img-thumbnail" style="width:60px;height:60px;">
                    <?php else: ?>
                        <span class="text-muted">—</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <a href="edit_products.php?id=<?= $p['product_id'] ?>" class="btn btn-warning btn-sm shadow-sm">✏️ แก้ไข</a>
                    <a href="products.php?delete=<?= $p['product_id'] ?>" class="btn btn-danger btn-sm shadow-sm" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบสินค้านี้?')">🗑️ ลบ</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center text-muted">— ยังไม่มีสินค้า —</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
</div>

</body>
</html>
