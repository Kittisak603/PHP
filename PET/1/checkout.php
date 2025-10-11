<?php
session_start();
require 'config.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) { // TODO: ใส่ session ของ user
    header("Location: login.php"); // TODO: หน้า login
    exit;
}

$user_id = $_SESSION['user_id']; // TODO: กำหนด user_id

// ดึงรายสินค้าจากตะกร้า
$stmt = $conn->prepare("SELECT cart.cart_id, cart.quantity, cart.product_id, products.product_name,
    products.price
    FROM cart
    JOIN products ON cart.product_id = products.product_id
    WHERE cart.user_id = ?");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// -----------------------------
// คำนวณราคารวม
// -----------------------------
$total = 0;
foreach ($items as $item) {
    $total += $item['quantity'] * $item['price']; // TODO: quantity * price
}
// เมอื่ ผใู้ชก้ดยนื ยันค ำสั่งซอื้ (method POST)
$error = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$address = trim($_POST['address']); // TODO: ชอ่ งกรอกทอี่ ยู่
$city = trim($_POST['city']); // TODO: ชอ่ งกรอกจังหวัด
$postal_code = trim($_POST['postal_code']); // TODO: ชอ่ งกรอกรหัสไปรษณีย์
$phone = trim($_POST['phone']); // TODO: ชอ่ งกรอกเบอรโ์ ทรศัพท์
// ตรวจสอบกำรกรอกข ้อมูล
if (empty($address) || empty($city) || empty($postal_code) || empty($phone)) {
$errors[] = "กรุณำกรอกข ้อมูลให้ครบถ ้วน"; // TODO: ข ้อควำมแจ้งเตือนกรอกไม่ครบ
}
if (empty($errors)) {
// เริ่ม transaction
$conn->beginTransaction();
try {
// บันทกึขอ้ มลู กำรสั่งซอื้
$stmt =$conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
$stmt->execute([$user_id, $total]);
$order_id = $conn->lastInsertId();
// บันทกึ รำยกำรสนิ คำ้ใน order_items
$stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?,
?, ?)");
foreach ($items as $item) {
$stmtItem->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
// TODO: product_id, quantity, price
}
// บันทกึขอ้ มลู กำรจัดสง่
$stmt = $conn->prepare("INSERT INTO shipping (order_id, address, city, postal_code, phone) VALUES (?,
?, ?, ?, ?)");
$stmt->execute([$order_id, $address, $city, $postal_code, $phone]);
// ลำ้งตะกรำ้สนิ คำ้
$stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
// ยืนยันกำรบันทึก
$conn->commit();
header("Location: orders.php?success=1"); // TODO: หนำ้แสดงผลค ำสั่งซอื้
exit;
} catch (Exception $e) {
$conn->rollBack();
$errors[] = "เกิดข ้อผิดพลำด: " . $e->getMessage();
}
}
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ยืนยันการสั่งซื้อ</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
:root {
    --green-leaf: #6BA368;
    --green-light: #A8D5BA;
    --brown-warm: #A9746E;
    --cream-light: #FFF1E6;
    --beige: #F5E9DA;
}

body {
    background: var(--cream-light);
    font-family: "Prompt", sans-serif;
    color: #1e2a47;
}

h2 {
    font-weight: 700;
    color: var(--green-leaf);
    text-align: center;
    margin-bottom: 1.5rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

.list-group-item {
    border-radius: 0.8rem;
    margin-bottom: 0.3rem;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    background: var(--beige);
}

.list-group-item.text-end {
    font-weight: 700;
    color: var(--brown-warm);
}

.form-control {
    border-radius: 50px;
    border: 1px solid var(--green-leaf);
    padding: 0.4rem 0.8rem;
}

.alert {
    border-radius: 12px;
    text-align: center;
    font-weight: 500;
}

.btn-success, .btn-secondary {
    border-radius: 50px;
    padding: 0.5rem 1.5rem;
    transition: 0.3s;
    font-weight: 600;
}

.btn-success {
    background: linear-gradient(90deg, var(--green-leaf), var(--green-light));
    color: #fff;
    border: none;
}
.btn-success:hover {
    background: linear-gradient(90deg, var(--green-light), var(--green-leaf));
    box-shadow: 0 0 10px rgba(107,163,104,0.4);
}

.btn-secondary {
    background-color: var(--brown-warm);
    color: #fff;
    border: none;
}
.btn-secondary:hover {
    background-color: #8f5b53;
    color: #fff;
}

form .row.g-3 {
    background: var(--beige);
    padding: 1.5rem;
    border-radius: 1rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}
</style>
</head>
<body class="container mt-5">

<h2>📝 ยืนยันการสั่งซื้อ</h2>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<h5 class="mt-4">รายการสินค้าของคุณ</h5>
<ul class="list-group mb-4">
    <?php foreach ($items as $item): ?>
        <li class="list-group-item">
            <?= htmlspecialchars($item['product_name']) ?> × <?= $item['quantity'] ?> =
            <?= number_format($item['price'] * $item['quantity'], 2) ?> บาท
        </li>
    <?php endforeach; ?>
    <li class="list-group-item text-end">
        รวมทั้งหมด: <?= number_format($total, 2) ?> บาท
    </li>
</ul>

<form method="post" class="row g-3">
    <div class="col-md-6">
        <label for="address" class="form-label">ที่อยู่</label>
        <input type="text" name="address" id="address" class="form-control" required>
    </div>

    <div class="col-md-4">
        <label for="city" class="form-label">จังหวัด</label>
        <input type="text" name="city" id="city" class="form-control" required>
    </div>

    <div class="col-md-2">
        <label for="postal_code" class="form-label">รหัสไปรษณีย์</label>
        <input type="text" name="postal_code" id="postal_code" class="form-control" required>
    </div>

    <div class="col-md-6">
        <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
        <input type="text" name="phone" id="phone" class="form-control">
    </div>

    <div class="col-12 d-flex gap-2 mt-3">
        <button type="submit" class="btn btn-success">✅ ยืนยันการสั่งซื้อ</button>
        <a href="cart.php" class="btn btn-secondary">← กลับไปที่ตะกร้า</a>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


