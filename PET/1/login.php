<?php
session_start();
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOremail = trim($_POST['username_or_email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$usernameOremail, $usernameOremail]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: admin/index.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>เข้าสู่ระบบ</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

<style>
    body {
        background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
        font-family: "Prompt", sans-serif;
        color: #fff;
    }

    .card {
        border-radius: 1.5rem;
        border: none;
        background: rgba(255,255,255,0.05);
        backdrop-filter: blur(15px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.3);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.5);
    }

    h2 {
        font-weight: 700;
        color: #ffd369;
        text-align: center;
        margin-bottom: 2rem;
        text-shadow: 0 2px 6px rgba(0,0,0,0.5);
    }

    .form-label {
        color: #fff;
        font-weight: 500;
    }

    .form-control {
        border-radius: 50px;
        padding: 0.75rem 1.5rem;
        border: none;
        background: rgba(255,255,255,0.15);
        color: #fff;
        transition: background 0.3s;
    }

    .form-control:focus {
        background: rgba(255,255,255,0.25);
        box-shadow: none;
        color: #fff;
    }

    .btn {
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-success {
        background: linear-gradient(90deg, #ffd369, #ff8c42);
        border: none;
        color: #1e2a47;
        box-shadow: 0 6px 20px rgba(255,216,105,0.4);
    }

    .btn-success:hover {
        background: linear-gradient(90deg, #ff8c42, #ffd369);
        box-shadow: 0 8px 25px rgba(255,216,105,0.6);
        color: #1e2a47;
    }

    .btn-outline-secondary {
        border-radius: 50px;
        border-color: #ffd369;
        color: #ffd369;
    }

    .btn-outline-secondary:hover {
        background: #ffd369;
        color: #1e2a47;
    }
</style>
</head>

<body>
<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-sm p-5 w-100" style="max-width: 420px;">
        <h2>เข้าสู่ระบบ 🔑</h2>

        <?php if (isset($_GET['register']) && $_GET['register'] === 'register'): ?>
            <div class="alert alert-success">สมัครสมาชิกสำเร็จ กรุณาเข้าสู่ระบบ</div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="username_or_email" class="form-label">ชื่อผู้ใช้หรืออีเมล</label>
                <input type="text" name="username_or_email" id="username_or_email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">รหัสผ่าน</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-4">
                <button type="submit" class="btn btn-success px-4">เข้าสู่ระบบ</button>
                <a href="register.php" class="btn btn-outline-secondary px-4">สมัครสมาชิก</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

