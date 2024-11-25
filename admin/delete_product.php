<?php
// admin/delete_product.php
require 'config.php';

// التحقق من وجود معرف المنتج
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = intval($_GET['id']);

// جلب مسار الصورة المرتبطة بالمنتج
$stmt = $conn->prepare("SELECT image_url FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->bind_result($image_url);
if ($stmt->fetch()) {
    $stmt->close();

    // حذف الصورة من المجلد إذا كانت موجودة
    if (!empty($image_url)) {
        $file_path = '../' . $image_url;
        if (file_exists($file_path)) {
            unlink($file_path); // حذف الملف
        }
    }

    // حذف المنتج من قاعدة البيانات
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();
} else {
    $stmt->close();
}

$conn->close();

// إعادة التوجيه إلى صفحة المنتجات مع رسالة نجاح
header("Location: products.php");
exit();
?>
