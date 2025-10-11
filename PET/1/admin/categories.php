<?php

require '../config.php';// TODO: เชื่อมต่อฐานข้อมูลด้วย PDO
require 'auth_admin.php';// TODO: การ์ดสิทธิ์ (Admin Guard)
// แนวทาง: ถ้า !isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' -> redirect ไป ../login.php แล้ว exit;

// เพิ่มหมวดหมู่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    if ($category_name) {
        $stmt = $conn->prepare("INSERT INTO categories(category_name) VALUES (?)");
        $stmt->execute([$category_name]);
        header("Location: category.php");
        exit;
    }
}
// ลบหมวดหมู่ (แบบไม่มีการตรวจสอบว่ายังมีสินค้าหมวดหมู่นี้หรือไม่)
// if (isset($_GET['delete'])) {
//     $category_id = $_GET['delete'];
//     $stmt = $pdo->prepare("DELETE FROM categories WHERE category_id = ?");
//     $stmt->execute([$category_id]);
//     header("Location: categories.php");
//     exit;
// }
// ลบหมวดหมู่
// ตรวจสอบว่าหมวดหมู่นี้ยังถูกใช้อยู่หรือไม่
if (isset($_GET['delete'])) {
    $category_id = $_GET['delete'];
    // ตรวจสอบว่าหมวดหมู่นี้ยังถูกใช้อยู่หรือไม่
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $productCount = $stmt->fetchColumn();
    
    if ($productCount > 0) {
        // ถ้ามีสินค้าอยู่ในหมวดหมู่นี้
        $_SESSION['error'] = "ไม่สามารถลบหมวดหมู่นี้ได้เนื่องจากยังมีสินค้าที่ใช้งานหมวดหมู่นี้อยู่";
    } else {
        // ถ้าไม่มีสินค้าให้ลบได้
        $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
        $stmt->execute([$category_id]);
        $_SESSION['success'] = "ลบหมวดหมู่เรียบร้อยแล้ว";
    }
    header("Location: categories.php");
    exit;
}
// แก้ไขหมวดหมู่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    $category_id = $_POST['category_id'];
    $category_name = trim($_POST['new_name']);
    if ($category_name) {
        $stmt = $conn->prepare("UPDATE categories SET category_name = ? WHERE category_id = ?");
        $stmt->execute([$category_name, $category_id]);
        header("Location: categories.php");
        exit;
    }
}
// ดึงหมวดหมู่ทั้งหมด
$categories = $conn->query("SELECT * FROM categories ORDER BY category_id ASC")->fetchAll(PDO::FETCH_ASSOC);

// โค้ดนี้เขียนต่อกันยาวบรรทัดเดียวได้เพราะผลลัพธ์จากเมธอดหนึ่งสามารถส่งต่อ (chaining) ให้เมธอดถัดไปทันที โดยไม่ต้อง
// แยกตัวแปรเก็บไว้ก่อน
// $pdo->query("...")->fetchAll(...);
// หากเขียนแยกเป็นหลายบรรทัดจะเป็นแบบนี้:
// $stmt = $pdo->query("SELECT * FROM categories ORDER BY category_id ASC");
// $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
// ควรเขียนแยกบรรทัดเมื่อจะใช้ $stmt ซ้ำหลายครั้ง (เช่น fetch ทีละ row, ตรวจจำนวนแถว)
// หรือเขียนแบบ prepare , execute
// $stmt = $pdo->prepare("SELECT * FROM categories ORDER BY category_id ASC");
// $stmt->execute();
// $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการหมวดหมู่สินค้า</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
@import url('https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600;700&display=swap');

body {
  font-family: "Prompt", sans-serif;
  background: linear-gradient(135deg, #1e2a47, #2b3b63);
  color: #1e2a47;
  min-height: 100vh;
  padding-bottom: 3rem;
}

/* 🔹 Header */
.header-box {
  background: linear-gradient(90deg, #1e2a47, #2b3b63);
  color: #fff;
  padding: 1.5rem 2rem;
  border-radius: 1rem;
  margin-bottom: 2rem;
  box-shadow: 0 5px 20px rgba(0,0,0,0.15);
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.header-box h2 {
  font-weight: 700;
}
.header-box a.btn {
  border-radius: 50px;
  background: linear-gradient(90deg, #b88a44, #d4a74f);
  color: #fff;
  border: none;
  font-weight: 500;
  transition: 0.3s;
}
.header-box a.btn:hover {
  opacity: 0.9;
  transform: translateY(-2px);
}

/* 🔹 Card */
.card-custom {
  border: none;
  border-radius: 1rem;
  box-shadow: 0 6px 20px rgba(0,0,0,0.08);
  background: #fff;
}
.card-title {
  color: #2b3b63;
  font-weight: 600;
}

/* 🔹 Buttons */
.btn {
  border-radius: 30px;
  font-size: 0.9rem;
  padding: 0.45rem 1rem;
  font-weight: 500;
}
.btn-primary {
  background: linear-gradient(90deg, #1e2a47, #2b3b63);
  border: none;
}
.btn-primary:hover {
  background: linear-gradient(90deg, #2b3b63, #1e2a47);
}
.btn-warning {
  background: linear-gradient(90deg, #f6b93b, #e58e26);
  border: none;
  color: #fff;
}
.btn-warning:hover {
  opacity: 0.9;
}
.btn-danger {
  background: linear-gradient(90deg, #c0392b, #e74c3c);
  border: none;
}
.btn-danger:hover {
  opacity: 0.9;
}

/* 🔹 Table */
.table {
  background: #fff;
  border-radius: 0.75rem;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0,0,0,0.07);
}
thead {
  background: linear-gradient(90deg, #b88a44, #d4a74f);
  color: #fff;
  font-weight: 600;
}
tbody tr:hover {
  background: #f8f9fc;
}
th, td {
  vertical-align: middle !important;
}

/* 🔹 Form */
.form-control {
  border-radius: 0.6rem;
  border: 1px solid #ccc;
}
.form-control:focus {
  border-color: #2b3b63;
  box-shadow: 0 0 0 0.2rem rgba(43, 59, 99, 0.25);
}

/* 🔹 Alerts */
.alert {
  border-radius: 0.75rem;
  font-weight: 500;
  box-shadow: 0 3px 12px rgba(0,0,0,0.08);
}

/* Responsive fix */
@media (max-width: 768px) {
  .header-box {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
}
</style>
</head>
<body class="container py-4">

  <!-- Header -->
  <div class="header-box">
    <h2 class="mb-0"><i class="bi bi-tags me-2"></i>จัดการหมวดหมู่สินค้า</h2>
    <a href="index.php" class="btn"><i class="bi bi-arrow-left-circle"></i> กลับหน้าผู้ดูแล</a>
  </div>

  <!-- Alerts -->
  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <!-- Add Category -->
  <div class="card card-custom mb-4">
    <div class="card-body">
      <h5 class="card-title mb-3"><i class="bi bi-plus-circle"></i> เพิ่มหมวดหมู่</h5>
      <form method="post" class="row g-3">
        <div class="col-md-6">
          <input type="text" name="category_name" class="form-control" placeholder="ชื่อหมวดหมู่" required>
        </div>
        <div class="col-md-3">
          <button type="submit" name="add_category" class="btn btn-primary w-100">
            <i class="bi bi-plus"></i> เพิ่มหมวดหมู่
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Category List -->
  <h5 class="mb-3 text-primary fw-semibold"><i class="bi bi-list-ul me-2"></i>รายการหมวดหมู่</h5>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th>ชื่อหมวดหมู่</th>
          <th style="width:40%">แก้ไขชื่อ</th>
          <th class="text-center">การจัดการ</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($categories as $cat): ?>
          <tr>
            <td><?= htmlspecialchars($cat['category_name']) ?></td>
            <td>
              <form method="post" class="d-flex">
                <input type="hidden" name="category_id" value="<?= $cat['category_id'] ?>">
                <input type="text" name="new_name" class="form-control me-2" placeholder="ชื่อใหม่" required>
                <button type="submit" name="update_category" class="btn btn-sm btn-warning">
                  <i class="bi bi-pencil-square"></i> แก้ไข
                </button>
              </form>
            </td>
            <td class="text-center">
              <a href="categories.php?delete=<?= $cat['category_id'] ?>" 
                 class="btn btn-sm btn-danger"
                 onclick="return confirm('คุณต้องการลบหมวดหมู่นี้หรือไม่?')">
                 <i class="bi bi-trash3"></i> ลบ
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($categories)): ?>
          <tr>
            <td colspan="3" class="text-center text-muted py-4">— ยังไม่มีหมวดหมู่ —</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</body>
</html>


