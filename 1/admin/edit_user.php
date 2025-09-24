<?php
require '../config.php';  // TODO-1: เชื่อมต่อฐานข้อมูลด้วย PDO (แก้ชื่อไฟล์ตามจริง)
require 'auth_admin.php'; // TODO-2: การ์ดสิทธิ์(Admin Guard)
// แนวทาง: ถ้า !isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' -> redirect ไป ../login.php แล้ว exit;
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
// TODO-3: ตรวจว่ามีพารามิเตอร์ id มาจริงไหม (ผ่าน GET)
// แนวทาง: ถ้าไม่มี -> redirect ไป users.php
if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit;
}
// TODO-4: ดึงค่า id และ "แคสต์เป็น int" เพื่อความปลอดภัย
$user_id = (int)$_GET['id'];

// TODO-5: เตรียม/รัน SELECT (เฉพาะ role = 'member')
// SQL แนะนำ:
// SELECT * FROM users WHERE user_id = ? AND role = 'member'
// - ใช้ prepare + execute([$user_id])
// - fetch(PDO::FETCH_ASSOC) แล้วเก็บใน $user
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ? AND role = 'member'");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// TODO-6: ถ้าไม่พบข้อมูล -> แสดงข้อความและ exit;
if (!$user) {
    echo "<h3>ไม่พบสมาชิก</h3>";
    exit;
}

// ========== เมื่อผู้ใช้กด Submit ฟอร์ม ==========
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO-7: รับค่า POST + trim
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // TODO-8: ตรวจความครบถ้วน และตรวจรูปแบบ email
    if ($username === '' || $email === '') {
        $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "รูปแบบอีเมลไม่ถูกต้อง";
    }

    // TODO-9: ถ้า validate ผ่าน ให้ตรวจสอบซ้ำ (username/email ซ้ำกับคนอื่นที่ไม่ใช่ตัวเองหรือไม่)
    // SQL แนะนำ:
    // SELECT 1 FROM users WHERE (username = ? OR email = ?) AND user_id != ?
    if (!$error) {
        $chk = $conn->prepare("SELECT 1 FROM users WHERE (username = ? OR email = ?) AND user_id != ?");
        $chk->execute([$username, $email, $user_id]);
        if ($chk->fetch()) {
            $error = "ชื่อผู้ใช้หรืออีเมลมีอยู่แล้วในระบบ";
        }
    }

    // ตรวจรหัสผ่าน (กรณีต้องการเปลี่ยน)
    // เงื่อนไข: อนุญาตให้ปล่อยว่างได้ (คือไม่เปลี่ยนรหัสผ่าน)
    $updatePassword = false;
    $hashed = null;
    if (!$error && ($password !== '' || $confirm !== '')) {
        // TODO: นักศึกษาตกกิริยา เช่น ยาว >= 6 และรหัสผ่านตรงกัน
        if (strlen($password) < 6) {
            $error = "รหัสผ่านต้องยาวอย่างน้อย 6 อักขระ";
        } elseif ($password !== $confirm) {
            $error = "รหัสผ่านใหม่กับยืนยันรหัสผ่านไม่ตรงกัน";
        } else {
            // แฮชรหัสผ่าน
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $updatePassword = true;
        }
    }

    // สร้าง SQL UPDATE แบบยืดหยุ่น (ถ้าไม่เปลี่ยนรหัสผ่านจะไม่แตะ field password)
    if (!$error) {
        if ($updatePassword) {
            // อัปเดตรวมรหัสผ่าน
            $sql = "UPDATE users
                    SET username = ?, full_name = ?, email = ?, password = ?
                    WHERE user_id = ?";
            $args = [$username, $full_name, $email, $hashed, $user_id];
        } else {
            // อัปเดตเฉพาะข้อมูลทั่วไป
            $sql = "UPDATE users
                    SET username = ?, full_name = ?, email = ?
                    WHERE user_id = ?";
            $args = [$username, $full_name, $email, $user_id];
        }
        $upd = $conn->prepare($sql);
        $upd->execute($args);
        // TODO-11: redirect กลับหน้า users.php หลังอัปเดตสำเร็จ
        header("Location: users.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขสมาชิก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f1f3f6;
            font-family: "Prompt", sans-serif;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #0d6efd, #20c997);
            color: #fff;
            font-size: 1.25rem;
            font-weight: bold;
            border-radius: 1rem 1rem 0 0;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13,110,253,.25);
        }
        .btn-primary {
            border-radius: .5rem;
            padding: .6rem 1.5rem;
            transition: all .3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13,110,253,.4);
        }
        .btn-secondary {
            border-radius: .5rem;
            transition: all .3s ease;
        }
        .btn-secondary:hover {
            transform: scale(1.05);
        }
        label {
            font-weight: 500;
        }
        .input-group-text {
            background: #e9ecef;
        }
    </style>
</head>
<body class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-person-gear"></i> แก้ไขข้อมูลสมาชิก
                </div>
                <div class="card-body">
                    <a href="users.php" class="btn btn-secondary mb-3">
                        <i class="bi bi-arrow-left-circle"></i> กลับหน้ารายชื่อสมาชิก
                    </a>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="post" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">ชื่อผู้ใช้</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($user['username']) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ชื่อ-นามสกุล</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">อีเมล</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($user['email']) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">รหัสผ่านใหม่ <small class="text-muted">(ถ้าไม่เปลี่ยนให้เว้นว่าง)</small></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                                <input type="password" name="confirm_password" class="form-control">
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> บันทึกการแก้ไข
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

