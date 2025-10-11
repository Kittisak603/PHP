<?php
session_start();
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$errors = [];
$success = "";

// ดึงข้อมูลสมาชิก
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// เมื่อตรวจสอบฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // ตรวจสอบชื่อ-นามสกุลและอีเมลว่ามีข้อมูลหรือไม่
    if (empty($full_name) || empty($email)) {
        $errors[] = "กรุณากรอกชื่อ-นามสกุลและอีเมล";
    }

    // ตรวจสอบอีเมลซ้ำ
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND user_id != ?");
    $stmt->execute([$email, $user_id]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "อีเมลนี้ถูกใช้งานแล้ว";
    }

    // ตรวจสอบการเปลี่ยนรหัสผ่าน (ถ้ามี)
    if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
        if (!password_verify($current_password, $user['password'])) {
            $errors[] = "รหัสผ่านเดิมไม่ถูกต้อง";
        } elseif (strlen($new_password) < 6) {
            $errors[] = "รหัสผ่านใหม่ต้องมีอย่างน้อย 6 ตัวอักษร";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "รหัสผ่านใหม่และการยืนยันรหัสผ่านไม่ตรงกัน";
        } else {
            $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        }
    }

    // อัปเดตข้อมูลหากไม่มี error
    if (empty($errors)) {
        if (!empty($new_hashed)) {
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, password = ? WHERE user_id = ?");
            $stmt->execute([$full_name, $email, $new_hashed, $user_id]);
        } else {
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE user_id = ?");
            $stmt->execute([$full_name, $email, $user_id]);
        }
        $success = "บันทึกข้อมูลเรียบร้อยแล้ว";
        
        // อัปเดต session หากจำเป็น
        $_SESSION['username'] = $user['username'];
        $user['full_name'] = $full_name;
        $user['email'] = $email;
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>โปรไฟล์สมาชิก</title>
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
    margin-bottom: 1rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

.card {
    border: none;
    border-radius: 1rem;
    background: var(--beige);
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    padding: 2rem;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
}

label {
    font-weight: 500;
    color: var(--brown-warm);
}

input.form-control {
    border-radius: 50px;
    border: 1px solid var(--green-leaf);
    padding: 0.6rem 1rem;
    transition: 0.2s ease;
}
input.form-control:focus {
    border-color: var(--green-leaf);
    box-shadow: 0 0 6px rgba(107,163,104,0.3);
}

hr {
    border-top: 2px dashed #d8dee9;
}

.btn-gradient {
    background: linear-gradient(90deg, var(--brown-warm), #d4a74f);
    color: #fff;
    border: none;
    border-radius: 50px;
    padding: 0.6rem 1.6rem;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-gradient:hover {
    background: linear-gradient(90deg, #d4a74f, var(--brown-warm));
    box-shadow: 0 0 10px rgba(212,167,79,0.4);
    color: #fff;
}

.btn-outline-navy {
    border-color: var(--green-leaf);
    color: var(--green-leaf);
    border-radius: 50px;
    padding: 0.5rem 1.2rem;
    transition: 0.3s;
}
.btn-outline-navy:hover {
    background-color: var(--green-leaf);
    color: #fff;
}

.alert {
    border-radius: 12px;
    font-weight: 500;
}
</style>
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>👤 โปรไฟล์ของคุณ</h2>
        <a href="index.php" class="btn btn-outline-navy">← กลับหน้าหลัก</a>
    </div>

    <div class="card">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post" class="row g-3">
            <div class="col-md-6">
                <label for="full_name" class="form-label">ชื่อ - นามสกุล</label>
                <input type="text" name="full_name" class="form-control" required 
                       value="<?= htmlspecialchars($user['full_name']) ?>">
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">อีเมล</label>
                <input type="email" name="email" class="form-control" required 
                       value="<?= htmlspecialchars($user['email']) ?>">
            </div>

            <div class="col-12"><hr><h5 class="text-secondary">🔐 เปลี่ยนรหัสผ่าน (ไม่จำเป็น)</h5></div>

            <div class="col-md-6">
                <label for="current_password" class="form-label">รหัสผ่านเดิม</label>
                <input type="password" name="current_password" id="current_password" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="new_password" class="form-label">รหัสผ่านใหม่ (≥ 6 ตัวอักษร)</label>
                <input type="password" name="new_password" id="new_password" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="confirm_password" class="form-label">ยืนยันรหัสผ่านใหม่</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control">
            </div>

            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-gradient">💾 บันทึกการเปลี่ยนแปลง</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>


