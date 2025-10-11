<?php

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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    body {
         
        background: linear-gradient(135deg, #1e2a47, #2b3b63);
        font-family: "Prompt", sans-serif;
        color: #1e2a47;
        min-height: 100vh;
    
    }

    .page-header {
        background: linear-gradient(90deg, #1e2a47, #2b3b63, #b88a44);
        color: white;
        padding: 1.5rem 2rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-header h2 {
        font-weight: 700;
        text-shadow: 1px 1px 6px rgba(0,0,0,0.2);
        margin: 0;
    }

    .page-header .btn {
        background: #fff;
        color: #2b3b63;
        border: none;
        font-weight: 600;
        transition: all 0.3s;
    }
    .page-header .btn:hover {
        background: #b88a44;
        color: white;
    }

    table {
        background: white;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(0,0,0,0.05);
    }

    thead {
        background: linear-gradient(90deg, #1e2a47, #2b3b63, #b88a44);
        color: white;
    }

    th, td {
        vertical-align: middle !important;
    }

    tbody tr:hover {
        background: #f8f9fa;
        transition: background 0.3s;
    }

    .btn {
        border-radius: 30px;
        padding: 0.35rem 0.9rem;
        font-size: 0.9rem;
        font-weight: 500;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .btn-warning {
        background: linear-gradient(90deg, #f6b93b, #e58e26);
        border: none;
        color: #fff;
    }

    .btn-danger {
        background: linear-gradient(90deg, #c0392b, #e74c3c);
        border: none;
    }

    .btn-warning:hover, .btn-danger:hover {
        opacity: 0.85;
    }

    .empty-box {
        background: #fffaf3;
        border: 1px solid #ffecb5;
        padding: 2rem;
        border-radius: 0.75rem;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

</style>
</head>
<body class="container py-4">

<!-- Header -->
<div class="page-header">
    <h2>👥 จัดการสมาชิก</h2>
    <a href="index.php" class="btn shadow-sm">← กลับหน้าผู้ดูแล</a>
</div>

<!-- Users Table -->
<?php if (count($users) === 0): ?>
    <div class="empty-box">
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
                        <a href="edit_user.php?id=<?= $user['user_id'] ?>" class="btn btn-sm btn-warning">✏️ แก้ไข</a>
                        <form action="deluser_sweet.php" method="POST" style="display:inline;">
                            <input type="hidden" name="u_id" value="<?= $user['user_id'] ?>">
                            <button type="button" class="delete-button btn btn-danger btn-sm" data-user-id="<?= $user['user_id'] ?>">🗑️ ลบ</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>


<script>
function showDeleteConfirmation(userId) {
    Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: 'คุณจะไม่สามารถเรียกคืนข้อมูลนี้ได้!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#c0392b',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ลบ',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'deluser_sweet.php';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'u_id';
            input.value = userId;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

document.querySelectorAll('.delete-button').forEach((button) => {
    button.addEventListener('click', () => {
        const userId = button.getAttribute('data-user-id');
        showDeleteConfirmation(userId);
    });
});
</script>

</body>
</html>


