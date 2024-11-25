<?php
// admin/delete_rating.php
require 'config.php';

// التحقق من وجود معرف التقييم
if(!isset($_GET['id'])) {
    header("Location: ratings.php");
    exit();
}

$rating_id = intval($_GET['id']);

// حذف التقييم
$stmt = $conn->prepare("DELETE FROM ratings WHERE id = ?");
if ($stmt === false) {
    die("خطأ في إعداد الاستعلام: " . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $rating_id);
$stmt->execute();
$stmt->close();

$conn->close();

// إعادة التوجيه إلى صفحة التقييمات
header("Location: ratings.php");
exit();
?>
