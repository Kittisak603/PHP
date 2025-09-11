<?php

require_once '../config.php';  // เชื่อมต่อฐานข้อมูล
require_once 'auth_admin.php';  


?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แผงควบคุมผู้ดูแลระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
            font-family: "Prompt", sans-serif;
        }
        .dashboard-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .dashboard-header h2 {
            font-weight: 700;
            color: #212529;
        }
        .dashboard-header p {
            color: #6c757d;
        }
        .card-option {
            border: none;
            border-radius: 1rem;
            padding: 2rem;
            color: #fff;
            text-align: center;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .card-option:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .card-option i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .logout-btn {
            margin-top: 2rem;
            display: block;
            text-align: center;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="container py-5">

    <!-- Header -->
    <div class="dashboard-header">
        <h2>📊 แผงควบคุมผู้ดูแลระบบ</h2>
        <p>ยินดีต้อนรับ, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>
    </div>

    <!-- Options -->
    <div class="row g-4">
        <div class="col-md-3">
            <a href="products.php" class="card-option bg-primary d-block text-decoration-none">
                <i class="bi bi-box-seam"></i>
                <h5 class="mt-2">จัดการสินค้า</h5>
            </a>
        </div>
        <div class="col-md-3">
            <a href="orders.php" class="card-option bg-success d-block text-decoration-none">
                <i class="bi bi-cart-check"></i>
                <h5 class="mt-2">จัดการคำสั่งซื้อ</h5>
            </a>
        </div>
        <div class="col-md-3">
            <a href="users.php" class="card-option bg-warning d-block text-decoration-none">
                <i class="bi bi-people"></i>
                <h5 class="mt-2">จัดการสมาชิก</h5>
            </a>
        </div>
        <div class="col-md-3">
            <a href="categories.php" class="card-option bg-dark d-block text-decoration-none">
                <i class="bi bi-tags"></i>
                <h5 class="mt-2">จัดการหมวดหมู่</h5>
            </a>
        </div>
    </div>

    <!-- Logout -->
    <div class="logout-btn">
        <a href="../logout.php" class="btn btn-danger px-4">ออกจากระบบ</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
