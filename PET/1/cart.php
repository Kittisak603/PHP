<?php
session_start();
require_once 'config.php';
require 'session_timeout.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = (int)$_SESSION['user_id'];

// --- POST เพิ่ม/อัปเดตสินค้า ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // เพิ่มสินค้าใหม่
    if (isset($_POST['product_id'])) {
        $product_id = (int)$_POST['product_id'];
        $quantity   = max(1, (int)($_POST['quantity'] ?? 1));
        $sweet_id   = (int)($_POST['sweet_id'] ?? 3); // default 'หวานปกติ'

        // ตรวจสอบ stock
        $chk = $conn->prepare("SELECT stock FROM products WHERE product_id = ?");
        $chk->execute([$product_id]);
        $p = $chk->fetch(PDO::FETCH_ASSOC);
        if (!$p || (int)$p['stock'] <= 0) { header("Location: cart.php"); exit; }
        $stock = (int)$p['stock'];

        // เช็คว่ามีอยู่แล้วใน cart
        $stmt = $conn->prepare("SELECT cart_id, quantity FROM cart WHERE user_id=? AND product_id=?");
        $stmt->execute([$user_id, $product_id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            $newQty = min($stock, $item['quantity'] + $quantity);
            $upd = $conn->prepare("UPDATE cart SET quantity=?, sweet_id=? WHERE cart_id=? AND user_id=?");
            $upd->execute([$newQty, $sweet_id, $item['cart_id'], $user_id]);
        } else {
            $ins = $conn->prepare("INSERT INTO cart(user_id, product_id, quantity, sweet_id) VALUES(?,?,?,?)");
            $ins->execute([$user_id, $product_id, min($stock,$quantity), $sweet_id]);
        }
        header("Location: cart.php");
        exit;
    }

    // อัปเดตหลายรายการ
    if (isset($_POST['update_all']) && isset($_POST['qty']) && is_array($_POST['qty'])) {
        foreach ($_POST['qty'] as $cart_id => $q) {
            $cart_id = (int)$cart_id;
            $q = max(1, (int)$q);
            $sweet_id = (int)($_POST['sweet_id'][$cart_id] ?? 3);

            $st = $conn->prepare("
                SELECT c.product_id, p.stock 
                FROM cart c 
                JOIN products p ON c.product_id=p.product_id 
                WHERE c.cart_id=? AND c.user_id=?
            ");
            $st->execute([$cart_id,$user_id]);
            $row = $st->fetch(PDO::FETCH_ASSOC);
            if (!$row) continue;

            $stock = (int)$row['stock'];
            if ($stock <= 0) {
                $del = $conn->prepare("DELETE FROM cart WHERE cart_id=? AND user_id=?");
                $del->execute([$cart_id,$user_id]);
                continue;
            }

            $q = min($q,$stock);
            $upd = $conn->prepare("UPDATE cart SET quantity=?, sweet_id=? WHERE cart_id=? AND user_id=?");
            $upd->execute([$q,$sweet_id,$cart_id,$user_id]);
        }
        header("Location: cart.php");
        exit;
    }
}

// ลบสินค้า
if (isset($_GET['remove'])) {
    $cart_id = (int)$_GET['remove'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id=? AND user_id=?");
    $stmt->execute([$cart_id,$user_id]);
    header("Location: cart.php");
    exit;
}

// ล้างตะกร้า
if (isset($_GET['clear']) && $_GET['clear']==='1') {
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id=?");
    $stmt->execute([$user_id]);
    header("Location: cart.php");
    exit;
}

// ดึงรายการ cart join sweet และ categories
$stmt = $conn->prepare("
    SELECT c.cart_id, c.quantity, c.sweet_id, s.sweet_name,
           p.product_id, p.product_name, p.price, p.image, p.stock,
           cat.category_name
    FROM cart c
    JOIN products p ON c.product_id=p.product_id
    LEFT JOIN categories cat ON p.category_id = cat.category_id
    LEFT JOIN sweet s ON c.sweet_id=s.swee_id
    WHERE c.user_id=?
    ORDER BY c.cart_id DESC
");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// คำนวณรวม
$subtotal = 0;
$outOfStockExists = false;
foreach($items as $it){
    $qty = (int)$it['quantity'];
    $stock = (int)$it['stock'];
    $price = (float)$it['price'];
    if($stock<=0){ $outOfStockExists=true; continue; }
    $qty = max(1,min($qty,$stock));
    $subtotal += $price*$qty;
}
$shipping = 0;
$discount = 0;
$total = max(0,$subtotal+$shipping-$discount);

function product_img($image){
    return !empty($image)?'product_images/'.rawurlencode($image):'product_images/no-image.jpg';
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ตะกร้าสินค้า</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
:root {
  --green-leaf:#6BA368; 
  --green-light:#A8D5BA; 
  --brown-warm:#A9746E; 
  --cream-light:#FFF1E6; 
  --beige:#F5E9DA;
}
body { background: var(--cream-light); font-family: "Prompt", sans-serif; }
.card{ background: var(--beige); border-radius:1rem; padding:1rem; box-shadow:0 4px 15px rgba(0,0,0,0.08); }
.thumb{ width:80px;height:80px;object-fit:cover;border-radius:.5rem; }
.btn-gradient{ background: linear-gradient(90deg,var(--green-leaf),var(--green-light)); color:#fff; border:none; border-radius:50px; transition:0.3s; }
.btn-gradient:hover{ background: linear-gradient(90deg,var(--green-light),var(--green-leaf)); }
.select-sweet{ border-radius:50px; padding:.25rem .5rem; border:1px solid var(--green-leaf); color:var(--brown-warm);}
input[type=number]{ border-radius:50px; border:1px solid var(--green-leaf); padding:.2rem .5rem; width:60px;}
</style>
</head>
<body class="container py-4">

<h2 class="text-center mb-4">🛒 ตะกร้าสินค้า</h2>

<?php if(!$items): ?>
<div class="alert alert-warning text-center">ตะกร้าของคุณยังว่าง <a href="index.php">ไปเลือกซื้อสินค้า</a></div>
<?php else: ?>
<form method="post" action="cart.php">
<?php foreach($items as $it): 
    $img = product_img($it['image']);
    $qty = (int)$it['quantity'];
    $stock = (int)$it['stock'];
    $line = max(1,min($qty,$stock))*$it['price'];
?>
<div class="card mb-3 d-flex flex-row align-items-center gap-3">
    <img src="<?=htmlspecialchars($img)?>" class="thumb">
    <div class="flex-fill">
        <div class="fw-bold"><?=htmlspecialchars($it['product_name'])?></div>
        <div>ราคา: <?=number_format($it['price'],2)?> บาท</div>
        <div>จำนวน: <input type="number" name="qty[<?=$it['cart_id']?>]" value="<?=$qty?>" min="1" max="<?=$stock?>"></div>
        
        <?php if(isset($it['category_name']) && trim($it['category_name']) === 'เครื่องดื่ม'): ?>
<div>ความหวาน: 
    <select name="sweet_id[<?=$it['cart_id']?>]" class="select-sweet">
        <?php
        $sweets = $conn->query("SELECT * FROM sweet")->fetchAll(PDO::FETCH_ASSOC);
        foreach($sweets as $s){
            $sel = ($it['sweet_id'] == $s['swee_id']) ? 'selected' : '';
            echo "<option value='{$s['swee_id']}' $sel>{$s['sweet_name']}</option>";
        }
        ?>
    </select>
</div>
<?php endif; ?>

        
    </div>
    <div class="text-end">
        <div class="fw-bold"><?=number_format($line,2)?> บาท</div>
        <a href="cart.php?remove=<?=$it['cart_id']?>" class="btn btn-gradient btn-sm mt-1">ลบ</a>
    </div>
</div>
<?php endforeach; ?>
<div class="d-flex justify-content-between">
    <a href="index.php" class="btn btn-gradient">เลือกสินค้าต่อ</a>
    <button type="submit" name="update_all" class="btn btn-gradient">อัปเดตตะกร้า</button>
</div>
</form>

<div class="card mt-3 p-3 text-end">
    <div>รวมย่อย: <?=number_format($subtotal,2)?> บาท</div>
    <div>ค่าส่ง: <?=number_format($shipping,2)?> บาท</div>
    <div>ส่วนลด: <?=number_format($discount,2)?> บาท</div>
    <hr>
    <div class="fw-bold">รวมทั้งหมด: <?=number_format($total,2)?> บาท</div>
    <a href="checkout.php" class="btn btn-gradient mt-2 w-100">ไปชำระเงิน</a>
</div>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

