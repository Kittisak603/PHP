<?php
session_start();
require 'config.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือยัง
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// เก็บ user_id
$user_id = $_SESSION['user_id'];

// ดึงคำสั่งซื้อของผู้ใช้
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ฟังก์ชันดึงรายการสินค้าในคำสั่งซื้อ
function getOrderItems($conn, $order_id) {
    $stmt = $conn->prepare("SELECT oi.quantity, oi.price, p.product_name
                            FROM order_items oi
                            JOIN products p ON oi.product_id = p.product_id
                            WHERE oi.order_id = ?");
    $stmt->execute([$order_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ฟังก์ชันดึงข้อมูลจัดส่ง
function getShippingInfo($conn, $order_id) {
    $stmt = $conn->prepare("SELECT * FROM shipping WHERE order_id = ?");
    $stmt->execute([$order_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Mapping สถานะภาษาไทย
$order_status_th = [
    'pending'    => 'รอดำเนินการ',
    'processing' => 'กำลังดำเนินการ',
    'shipped'    => 'จัดส่งแล้ว',
    'completed'  => 'เสร็จสมบูรณ์',
    'cancelled'  => 'ยกเลิก'
];

$shipping_status_th = [
    'not_shipped' => 'ยังไม่จัดส่ง',
    'shipped'     => 'จัดส่งแล้ว',
    'delivered'   => 'ส่งมอบแล้ว'
];
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ประวัติการสั่งซื้อ</title>
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
    margin-bottom: 1.5rem;
    text-align: center;
    text-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

.btn-outline-navy {
    border-color: var(--green-leaf);
    color: var(--green-leaf);
    border-radius: 50px;
    padding: 0.5rem 1.2rem;
    transition: 0.3s;
    text-decoration: none;
    font-weight: 600;
}
.btn-outline-navy:hover {
    background-color: var(--green-leaf);
    color: #fff;
}

.card {
    border: none;
    border-radius: 1rem;
    background: var(--beige);
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
}

.card-header {
    font-weight: 600;
    background-color: var(--cream-light);
    border-bottom: 1px solid #e0e4eb;
    border-radius: 1rem 1rem 0 0;
    color: var(--green-leaf);
}

.list-group-item {
    border: none;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    color: #1e2a47;
    background: var(--beige);
    border-radius: 0.6rem;
    margin-bottom: 0.3rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.total {
    font-weight: 700;
    color: var(--brown-warm);
    font-size: 1.1rem;
    margin-top: 0.5rem;
}

.shipping-info p {
    margin-bottom: 0.3rem;
}

.badge-status {
    padding: 0.4em 0.7em;
    border-radius: 0.5rem;
    font-weight: 600;
}

.badge-pending { background-color: #ffc107; color: #000; }
.badge-processing { background-color: #0dcaf0; color: #000; }
.badge-shipped { background-color: #0d6efd; color: #fff; }
.badge-completed { background-color: #198754; color: #fff; }
.badge-cancelled { background-color: #dc3545; color: #fff; }

.badge-not_shipped { background-color: #ffc107; color: #000; }
.badge-delivered { background-color: #198754; color: #fff; }
</style>
</head>
<body class="container mt-5">

<h2>🧾 ประวัติการสั่งซื้อ</h2>
<div class="text-center mb-4">
    <a href="index.php" class="btn btn-outline-navy">← กลับหน้าหลัก</a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">✅ ทำรายการสั่งซื้อเรียบร้อยแล้ว</div>
<?php endif; ?>

<?php if (count($orders) === 0): ?>
    <div class="alert alert-warning">คุณยังไม่เคยสั่งซื้อสินค้า</div>
<?php else: ?>
    <?php foreach ($orders as $order): ?>
    <div class="card mb-4">
        <div class="card-header">
            <strong>รหัสคำสั่งซื้อ:</strong> #<?= htmlspecialchars($order['order_id']) ?> |
            <strong>วันที่:</strong> <?= htmlspecialchars($order['order_date']) ?> |
            <strong>สถานะ:</strong> 
            <span class="badge-status badge-<?= $order['status'] ?>">
                <?= $order_status_th[$order['status']] ?>
            </span>
        </div>
        <div class="card-body">
            <ul class="list-group mb-3">
                <?php foreach (getOrderItems($conn, $order['order_id']) as $item): ?>
                <li class="list-group-item">
                    <?= htmlspecialchars($item['product_name']) ?> × <?= (int)$item['quantity'] ?> 
                    = <?= number_format($item['quantity'] * $item['price'], 2) ?> บาท
                </li>
                <?php endforeach; ?>
            </ul>

            <p class="total">รวมทั้งสิ้น: <?= number_format($order['total_amount'], 2) ?> บาท</p>

            <?php $shipping = getShippingInfo($conn, $order['order_id']); ?>
            <?php if ($shipping): ?>
            <div class="shipping-info mt-2">
                <p><strong>ที่อยู่จัดส่ง:</strong> <?= htmlspecialchars($shipping['address']) ?>, <?= htmlspecialchars($shipping['city']) ?> <?= htmlspecialchars($shipping['postal_code']) ?></p>
                <p><strong>สถานะการจัดส่ง:</strong> 
                    <span class="badge-status badge-<?= $shipping['shipping_status'] ?>">
                        <?= $shipping_status_th[$shipping['shipping_status']] ?>
                    </span>
                </p>
                <p><strong>เบอร์โทร:</strong> <?= htmlspecialchars($shipping['phone']) ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
