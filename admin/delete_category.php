<?php
// admin/delete_category.php
require 'config.php';

// التحقق من وجود معرف الصنف
if(!isset($_GET['id'])) {
    header("Location: categories.php");
    exit();
}

$category_id = intval($_GET['id']);

// حذف الصنف
$stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$stmt->close();

$conn->close();

// إعادة التوجيه إلى صفحة الأصناف مع رسالة نجاح (يمكنك استخدام الجلسة لعرض الرسالة)
header("Location: categories.php");
exit();
?>
