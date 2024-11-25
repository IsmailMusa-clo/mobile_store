<?php
// admin/delete_user.php
require 'config.php';

// التحقق من وجود معرف المستخدم
if(!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$user_id = intval($_GET['id']);

// التأكد من عدم حذف المدير نفسه أو المستخدمين الأساسيين (اختياري)
if($user_id == $_SESSION['user_id']) {
    // يمكنك إضافة رسالة هنا إذا كنت لا تريد السماح للمسؤول بحذف نفسه
    header("Location: users.php");
    exit();
}

// حذف المستخدم
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

$conn->close();

// إعادة التوجيه إلى صفحة المستخدمين مع رسالة نجاح (يمكنك استخدام الجلسة لعرض الرسالة)
header("Location: users.php");
exit();
?>
