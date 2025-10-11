<?php
require '../config.php'; // เชื่อมต่อฐานข้อมูล PDO
require 'auth_admin.php'; // ตรวจสอบสิทธิ์ Admin

// ตรวจสอบว่ามี id ของสินค้า
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}
$product_id = intval($_GET['id']);

// ดึงข้อมูลสินค้า
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "สินค้าไม่พบ";
    exit;
}

// ดึงหมวดหมู่ทั้งหมด
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// ประมวลผลเมื่อกดบันทึก
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = trim($_POST['product_name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category_id = intval($_POST['category_id']);
    $description = trim($_POST['description']);
    $image = $product['image'];

    // ลบรูปเดิม
    if (isset($_POST['remove_image']) && $_POST['remove_image'] == 1) {
        if (!empty($image) && file_exists("../product_images/$image")) {
            unlink("../product_images/$image");
        }
        $image = null;
    }

    // อัปโหลดรูปใหม่
    if (!empty($_FILES['product_image']['name'])) {
        $file = $_FILES['product_image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png'];
        if (in_array($ext, $allowed)) {
            $new_name = uniqid() . "." . $ext;
            move_uploaded_file($file['tmp_name'], "../product_images/$new_name");
            // ลบรูปเดิมถ้ามี
            if (!empty($image) && file_exists("../product_images/$image")) {
                unlink("../product_images/$image");
            }
            $image = $new_name;
        }
    }

    // อัปเดตฐานข้อมูล
    $stmt = $conn->prepare("UPDATE products SET product_name=?, price=?, stock=?, category_id=?, description=?, image=? WHERE product_id=?");
    $stmt->execute([$product_name, $price, $stock, $category_id, $description, $image, $product_id]);

    header("Location: products.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>แก้ไขสินค้า</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
        background: linear-gradient(135deg, #e8edf5, #f8f9fb);
        font-family: "Prompt", sans-serif;
        color: #1e2a47;
    }

    h2 {
        font-weight: 700;
        color: #2b3b63;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .card {
        border: none;
        border-radius: 1rem;
        background: #ffffff;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease-in-out;
    }
    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }

    .form-control, .form-select {
        border-radius: 0.75rem;
        border: 1px solid #ccc;
        transition: border 0.3s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #b88a44;
        box-shadow: 0 0 6px rgba(184,138,68,0.4);
    }

    .btn-back {
        border-radius: 50px;
        background: linear-gradient(90deg, #1e2a47, #2b3b63);
        color: #fff;
        font-weight: 500;
        transition: all 0.3s ease;
        padding: 0.5rem 1.5rem;
    }
    .btn-back:hover {
        background: linear-gradient(90deg, #b88a44, #d4a74f);
        color: #fff;
    }

    .btn-save {
        border-radius: 50px;
        background: linear-gradient(90deg, #1e2a47, #2b3b63, #b88a44);
        color: #fff;
        font-weight: 600;
        padding: 0.7rem 2rem;
        border: none;
        transition: 0.3s;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .btn-save:hover {
        background: linear-gradient(90deg, #b88a44, #d4a74f);
        transform: translateY(-2px);
    }

    img.img-preview {
        border-radius: 0.75rem;
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        border: 2px solid #dee2e6;
    }

    .label-strong {
        font-weight: 600;
        color: #2b3b63;
    }

    .header-bar {
        background: linear-gradient(90deg, #1e2a47, #2b3b63, #b88a44);
        color: white;
        padding: 1.25rem 2rem;
        border-radius: 1rem;
        box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .header-bar h2 {
        color: white;
        text-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
</style>
</head>
<body>
<div class="container py-5">

    <!-- Header -->
    <div class="header-bar">
        <h2>✏️ แก้ไขสินค้า</h2>
        <a href="products.php" class="btn btn-back shadow-sm">← กลับรายการสินค้า</a>
    </div>

    <!-- Edit Card -->
    <div class="card shadow-lg p-4">
        <form method="post" enctype="multipart/form-data" class="row g-3">

            <div class="col-md-6">
                <label class="form-label label-strong">ชื่อสินค้า</label>
                <input type="text" name="product_name" class="form-control" 
                       value="<?= htmlspecialchars($product['product_name']) ?>" required>
            </div>

            <div class="col-md-3">
                <label class="form-label label-strong">ราคา</label>
                <input type="number" step="0.01" name="price" class="form-control" 
                       value="<?= htmlspecialchars($product['price']) ?>" required>
            </div>

            <div class="col-md-3">
                <label class="form-label label-strong">จำนวนในคลัง</label>
                <input type="number" name="stock" class="form-control" 
                       value="<?= htmlspecialchars($product['stock']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label label-strong">หมวดหมู่</label>
                <select name="category_id" class="form-select" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['category_id'] ?>" 
                            <?= $product['category_id']==$cat['category_id']?'selected':'' ?>>
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12">
                <label class="form-label label-strong">รายละเอียดสินค้า</label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>

            <div class="col-md-6">
                <label class="form-label label-strong d-block">รูปปัจจุบัน</label>
                <?php if (!empty($product['image']) && file_exists("../product_images/".$product['image'])): ?>
                    <img src="../product_images/<?= htmlspecialchars($product['image']) ?>" 
                         class="img-thumbnail img-preview mb-2" width="180">
                <?php else: ?>
                    <span class="text-muted d-block mb-2">ไม่มีรูป</span>
                <?php endif; ?>
                <input type="hidden" name="old_image" value="<?= htmlspecialchars($product['image']) ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label label-strong">อัปโหลดรูปใหม่ (jpg, png)</label>
                <input type="file" name="product_image" class="form-control">
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                    <label class="form-check-label" for="remove_image">ลบรูปเดิม</label>
                </div>
            </div>

            <div class="col-12 text-end">
                <button type="submit" class="btn btn-save btn-lg">💾 บันทึกการแก้ไข</button>
            </div>

        </form>
    </div>
</div>
</body>
</html>


