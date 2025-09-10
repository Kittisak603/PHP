<?php
session_start();
require_once '../config.php';
require_once 'auth_admin.php';



// ลบสมาชิก
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    // ป้องกันลบตัวเอง
    if ($user_id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role = 'member'");
        $stmt->execute([$user_id]);
    }
    header("Location: users.php");
    exit;
}

// ดึงข้อมูลสมาชิก
    $stmt = $conn->prepare("SELECT * FROM users WHERE role = 'member' ORDER BY created_at DESC");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

  <!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการสมาชิก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f1f3f5;
            font-family: "Prompt", sans-serif;
        }
        .page-header {
            background: linear-gradient(135deg, #198754, #20c997);
            color: white;
            padding: 1.5rem 2rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
        .page-header h2 {
            font-weight: 600;
        }
        table {
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 6px 15px rgba(0,0,0,0.05);
        }
        thead {
            background: #198754;
            color: white;
        }
        th, td {
            vertical-align: middle !important;
        }
        tbody tr:hover {
            background: #f8f9fa;
        }
        .btn {
            border-radius: 30px;
            padding: 0.35rem 0.9rem;
            font-size: 0.9rem;
        }
        .btn-warning {
            color: #fff;
        }
        .empty-box {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            padding: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body class="container py-4">

    <!-- Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <h2 class="mb-0">👥 จัดการสมาชิก</h2>
        <a href="index.php" class="btn btn-light shadow-sm">← กลับหน้าผู้ดูแล</a>
    </div>

    <!-- Users Table -->
    <?php if (count($users) === 0): ?>
        <div class="empty-box text-center">
            <h5 class="mb-0">⚠️ ยังไม่มีสมาชิกในระบบ</h5>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ชื่อผู้ใช้</th>
                        <th>ชื่อ - นามสกุล</th>
                        <th>อีเมล</th>
                        <th>วันที่สมัคร</th>
                        <th class="text-center">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['created_at']) ?></td>
                        <td class="text-center">
                            <a href="edit_user.php?id=<?= $user['user_id'] ?>" class="btn btn-sm btn-warning shadow-sm">✏️ แก้ไข</a>
                            <a href="users.php?delete=<?= $user['user_id'] ?>" class="btn btn-sm btn-danger shadow-sm"
                               onclick="return confirm('คุณต้องการลบสมาชิกคนนี้หรือไม่?')">🗑️ ลบ</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</body>
</html>

