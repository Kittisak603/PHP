<?php
require '../config.php';
require 'auth_admin.php';
require '../function.php';

// ดึงคำสั่งซื้อทั้งหมด
$stmt = $conn->query("
    SELECT o.*, u.username
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.user_id
    ORDER BY o.order_date DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// แก้ไขสถานะคำสั่งซื้อ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->execute([$_POST['status'], $_POST['order_id']]);
        header("Location: orders.php");
        exit;
    }
    if (isset($_POST['update_shipping'])) {
        $stmt = $conn->prepare("UPDATE shipping SET shipping_status = ? WHERE shipping_id = ?");
        $stmt->execute([$_POST['shipping_status'], $_POST['shipping_id']]);
        header("Location: orders.php");
        exit;
    }
}

// mapping สถานะภาษาไทย
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

$statuses = array_keys($order_status_th);
$s_statuses = array_keys($shipping_status_th);
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการคำสั่งซื้อ</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #1e2a47, #2b3b63);
    font-family: "Prompt", sans-serif;
    color: #1e2a47;
    min-height: 100vh;
}

/* Header */
.header-box {
  background: linear-gradient(90deg, #0d47a1, #1e88e5, #f5c542);
  color: #fff;
  padding: 1.5rem 2rem;
  border-radius: 1rem;
  margin-bottom: 2rem;
  box-shadow: 0 5px 15px rgba(13, 71, 161, 0.2);
}

.header-box h2 { font-weight: 600; }

/* Accordion */
.accordion-item {
  border: none;
  border-radius: 14px;
  margin-bottom: 1.2rem;
  overflow: hidden;
  background: #fff;
  box-shadow: 0 4px 10px rgba(0,0,0,0.06);
  transition: all 0.3s;
}

.accordion-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(0,0,0,0.08);
}

.accordion-button {
  background: #fff;
  border: none;
  font-weight: 500;
  color: #0d47a1;
}

.accordion-button:not(.collapsed) {
  background: linear-gradient(90deg, #1e88e520, #f5c54215);
  color: #0d47a1;
  font-weight: 600;
}

.accordion-body {
  background: #ffffff;
  border-top: 1px solid #e9ecef;
  padding: 1.5rem;
}

/* List Items */
.list-group-item {
  border: none;
  border-bottom: 1px solid #f0f0f0;
  font-size: 0.95rem;
}
.list-group-item:last-child { border-bottom: none; }

/* Buttons */
.btn { border-radius: 25px; font-size: 0.9rem; padding: 0.5rem 1rem; transition: all 0.25s; }
.btn-primary { background: linear-gradient(90deg, #0d47a1, #1e88e5); border: none; color: #fff; }
.btn-primary:hover { background: linear-gradient(90deg, #1e88e5, #0d47a1); }
.btn-success { background: linear-gradient(90deg, #1e88e5, #f5c542); border: none; color: #fff; }
.btn-success:hover { background: linear-gradient(90deg, #f5c542, #1e88e5); }
.btn-light { color: #0d47a1; border: 1px solid #fff; }

.form-select { border-radius: 0.6rem; }

/* Shipping Box */
.bg-light { background-color: #f8f9fc !important; border-left: 4px solid #1e88e5; }

footer { text-align: center; color: #777; font-size: 0.85rem; margin-top: 50px; }
</style>
</head>

<body class="container py-4">

<div class="header-box d-flex justify-content-between align-items-center">
  <h2 class="mb-0">📦 จัดการคำสั่งซื้อทั้งหมด</h2>
  <a href="index.php" class="btn btn-light shadow-sm">← กลับหน้าผู้ดูแล</a>
</div>

<div class="accordion" id="ordersAccordion">
<?php foreach ($orders as $index => $order): ?>
  <?php $shipping = getShippingInfo($conn, $order['order_id']); ?>

  <div class="accordion-item">
    <h2 class="accordion-header" id="heading<?= $index ?>">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
        data-bs-target="#collapse<?= $index ?>" aria-expanded="false" aria-controls="collapse<?= $index ?>">
        <div class="w-100 d-flex flex-wrap justify-content-between align-items-center">
          <span>🧾 <strong>คำสั่งซื้อ #<?= $order['order_id'] ?></strong></span>
          <span>👤 <?= htmlspecialchars($order['username']) ?></span>
          <span>🕒 <?= $order['order_date'] ?></span>
          <span>สถานะ: 
            <span class="badge bg-info text-dark"><?= $order_status_th[$order['status']] ?></span>
          </span>
        </div>
      </button>
    </h2>

    <div id="collapse<?= $index ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $index ?>"
      data-bs-parent="#ordersAccordion">
      <div class="accordion-body">

        <h5 class="mt-2 mb-3 text-primary fw-semibold">🛍️ รายการสินค้า</h5>
        <ul class="list-group mb-3">
          <?php foreach (getOrderItems($conn, $order['order_id']) as $item): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <?= htmlspecialchars($item['product_name']) ?> × <?= $item['quantity'] ?>
              <span class="fw-semibold"><?= number_format($item['quantity'] * $item['price'], 2) ?> บาท</span>
            </li>
          <?php endforeach; ?>
        </ul>

        <p><strong>💰 ยอดรวม:</strong>
          <span class="text-success fw-bold"><?= number_format($order['total_amount'], 2) ?> บาท</span>
        </p>

        <!-- อัปเดตสถานะคำสั่งซื้อ -->
        <form method="post" class="row g-2 mb-3">
          <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
          <div class="col-md-4">
            <select name="status" class="form-select">
              <?php foreach ($statuses as $status): 
                  $selected = ($order['status'] === $status) ? 'selected' : '';
              ?>
                <option value="<?= $status ?>" <?= $selected ?>><?= $order_status_th[$status] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <button type="submit" name="update_status" class="btn btn-primary w-100">อัปเดตสถานะ</button>
          </div>
        </form>

        <?php if ($shipping): ?>
          <h5 class="mt-4 text-primary fw-semibold">🚚 ข้อมูลการจัดส่ง</h5>
          <div class="bg-light p-3 rounded mb-3">
            <p class="mb-1"><strong>ที่อยู่:</strong>
              <?= htmlspecialchars($shipping['address']) ?>, <?= htmlspecialchars($shipping['city']) ?>
              <?= htmlspecialchars($shipping['postal_code']) ?>
            </p>
            <p class="mb-1"><strong>เบอร์โทร:</strong> <?= htmlspecialchars($shipping['phone']) ?></p>
          </div>

          <!-- อัปเดตสถานะการจัดส่ง -->
          <form method="post" class="row g-2">
            <input type="hidden" name="shipping_id" value="<?= $shipping['shipping_id'] ?>">
            <div class="col-md-4">
              <select name="shipping_status" class="form-select">
                <?php foreach ($s_statuses as $s): 
                    $selected = ($shipping['shipping_status'] === $s) ? 'selected' : '';
                ?>
                  <option value="<?= $s ?>" <?= $selected ?>><?= $shipping_status_th[$s] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <button type="submit" name="update_shipping" class="btn btn-success w-100">อัปเดตการจัดส่ง</button>
            </div>
          </form>
        <?php endif; ?>

      </div>
    </div>
  </div>
<?php endforeach; ?>
</div>

<footer>
  © <?= date('Y') ?> ระบบจัดการคำสั่งซื้อ | Nakhon Pathom Rajabhat University
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
