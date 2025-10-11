<?php
session_start();
require_once 'config.php'; // เชื่อมต่อ DB

// ตรวจสอบผู้ใช้ login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// ตรวจสอบว่ามีข้อมูลส่งมาหรือไม่
if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {

    foreach ($_POST['quantity'] as $cart_id => $qty) {
        $cart_id = (int)$cart_id;
        $qty = (int)$qty;
        $sweet = isset($_POST['sweet'][$cart_id]) ? $_POST['sweet'][$cart_id] : '';

        if ($qty <= 0) {
            // ลบสินค้าถ้าจำนวน <= 0
            $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
            $stmt->execute([$cart_id, $user_id]);
        } else {
            // อัปเดตจำนวนและความหวาน
            $stmt = $conn->prepare("UPDATE cart SET quantity = ?, sweet = ? WHERE cart_id = ? AND user_id = ?");
            $stmt->execute([$qty, $sweet, $cart_id, $user_id]);
        }
    }

    $_SESSION['success'] = "อัปเดตตะกร้าสินค้าเรียบร้อย!";
}

header('Location: cart.php');
exit;
?>

