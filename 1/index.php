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
    <title>รายการสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #f0f9ff, #cbebff);
            font-family: "Prompt", sans-serif;
        }
        h1 {
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
    </style>
</head>

<body class="container py-4">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4 px-3">
        <a class="navbar-brand fw-bold" href="#">ร้านค้าออนไลน์</a>
        <div class="ms-auto">
            <?php if ($isLoggedIn): ?>
                <span class="text-white me-3">
                    ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['username']) ?> (<?= $_SESSION['role'] ?>)
                </span>
                <a href="profile.php" class="btn btn-light btn-sm me-2">ข้อมูลส่วนตัว</a>
                <a href="cart.php" class="btn btn-warning btn-sm me-2">ดูตะกร้า</a>
                <a href="logout.php" class="btn btn-danger btn-sm">ออกจากระบบ</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-success btn-sm me-2">เข้าสู่ระบบ</a>
                <a href="register.php" class="btn btn-light btn-sm">สมัครสมาชิก</a>
            <?php endif ?>
        </div>
    </nav>

    <h1 class="mb-4 text-center">🛒 รายการสินค้า</h1>

    <!-- แสดงสินค้า -->
    <div class="row">
        <?php foreach ($products as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($product['category_name']) ?></h6>
                        <p class="card-text flex-grow-1"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                        <p><strong>ราคา:</strong> <?= number_format($product['price'], 2) ?> บาท</p>
                        
                        <?php if ($isLoggedIn): ?>
                            <form action="cart.php" method="post" class="d-inline">
                                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-success btn-sm">เพิ่มในตะกร้า</button>
                            </form>
                        <?php else: ?>
                            <small class="text-muted">เข้าสู่ระบบเพื่อสั่งซื้อ </small>
                        <?php endif; ?>
                        
                        <a href="product_detail.php?id=<?= $product['product_id'] ?>" class="btn btn-outline-primary btn-sm float-end">ดูรายละเอียด</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>