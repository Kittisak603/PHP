<?php
session_start();
require_once 'config.php';

if(!isset($_GET['id'])){
    header('Location: index.php');
    exit();
}

$product_id = $_GET['id'];
$isLoggedIn = isset($_SESSION['user_id']);

$stmt = $conn->prepare("SELECT p.*, c.category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE p.product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: index.php');
    exit();
} 
$img = !empty($product['image']) ? 'product_images/' . rawurlencode($product['image']) : 'product_images/no-image.jpg';
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>รายละเอียดสินค้า</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
    --green-leaf: #6BA368;
    --green-light: #A8D5BA;
    --cream-light: #FFF1E6;
    --brown-warm: #A9746E;
}

body {
    background: var(--cream-light);
    font-family: "Prompt", sans-serif;
    color: #1e2a47;
    min-height: 100vh;
}

.card {
    border-radius: 1rem;
    background: #fff;
    box-shadow: 0 6px 25px rgba(0,0,0,0.15);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.2);
}

h2 {
    font-weight: 700;
    color: var(--green-leaf);
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

h6 {
    color: #6c7a91;
    font-weight: 500;
}

.price {
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--brown-warm);
    margin-bottom: 0.5rem;
}

.stock {
    font-size: 1rem;
    color: #6c757d;
}

.product-img {
    border-radius: 1rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.product-img:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.input-group-text {
    background: #f8f9fb;
    border: 1px solid #dcdfe6;
    color: var(--green-leaf);
    font-weight: 600;
}

.btn-green {
    background: linear-gradient(90deg, var(--green-leaf), var(--green-light));
    color: #fff;
    border: none;
    border-radius: 50px;
    padding: 0.6rem 1.8rem;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.btn-green:hover {
    background: linear-gradient(90deg, var(--green-light), var(--green-leaf));
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(107,163,104,0.3);
    color: #fff;
}

.btn-outline-green {
    border-color: var(--green-leaf);
    color: var(--green-leaf);
    border-radius: 50px;
    transition: 0.3s;
}
.btn-outline-green:hover {
    background-color: var(--green-leaf);
    color: #fff;
}

.back-btn {
    border-radius: 50px;
    border-color: var(--green-leaf);
    color: var(--green-leaf);
    font-weight: 500;
    transition: all 0.3s ease;
}
.back-btn:hover {
    background: var(--green-leaf);
    color: #fff;
}

.product-description {
    background: #f1f6f2;
    border-radius: 0.75rem;
    color: #2b3b63;
    box-shadow: inset 0 0 5px rgba(0,0,0,0.05);
    padding: 1rem;
    margin-bottom: 1.5rem;
}
.product-description h5 {
    font-weight: 600;
    margin-bottom: 0.5rem;
}
.product-description p {
    margin: 0;
    font-size: 0.95rem;
    line-height: 1.5;
}

.alert {
    border-radius: 1rem;
}
</style>
</head>
<body class="container py-4">

<a href="index.php" class="btn back-btn mb-3">← กลับหน้ารายการสินค้า</a>

<div class="container mt-5">
    <div class="card p-4">
        <div class="row align-items-center">
            <div class="col-md-5 text-center">
                <img src="<?= $img ?>" class="img-fluid product-img" alt="<?= htmlspecialchars($product['product_name']) ?>">
            </div>
            <div class="col-md-7 mt-4 mt-md-0">
                <h2><?= htmlspecialchars($product['product_name']) ?></h2>
                <h6 class="mb-3">หมวดหมู่: <?= htmlspecialchars($product['category_name']) ?></h6>

                <p class="price">💰 <?= number_format($product['price'], 2) ?> บาท</p>
                <p class="stock"><strong>คงเหลือ:</strong> <?= htmlspecialchars($product['stock']) ?> ชิ้น</p>

                <!-- รายละเอียดสินค้า -->
                <?php if (!empty($product['description'])): ?>
                    <div class="product-description">
                        <h5>📋 รายละเอียดสินค้า</h5>
                        <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($isLoggedIn): ?>
                    <?php if ($product['stock'] > 0): ?>
                        <form action="cart.php" method="post" class="mt-2">
                            <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                            <div class="input-group mb-3" style="max-width: 240px;">
                                <label class="input-group-text" for="quantity">จำนวน</label>
                                <input type="number" name="quantity" id="quantity" value="1" min="1"
                                       max="<?= $product['stock'] ?>" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-green btn-lg"><i class="bi bi-cart-plus"></i> เพิ่มในตะกร้า</button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-danger mt-4">❌ สินค้าหมดชั่วคราว</div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-warning mt-4">⚠️ กรุณาเข้าสู่ระบบเพื่อสั่งซื้อสินค้า</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




