<?php
// admin/delete_subscription.php
require 'config.php';

// التحقق من وجود معرف الاشتراك
if(!isset($_GET['id'])) {
    header("Location: subscriptions.php");
    exit();
}

$subscription_id = intval($_GET['id']);

// حذف الاشتراك
$stmt = $conn->prepare("DELETE FROM subscriptions WHERE id = ?");
if ($stmt === false) {
    die("خطأ في إعداد الاستعلام: " . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $subscription_id);
$stmt->execute();
$stmt->close();

$conn->close();

// إعادة التوجيه إلى صفحة الاشتراكات مع رسالة نجاح (يمكنك استخدام الجلسة لعرض الرسالة)
header("Location: subscriptions.php");
exit();
?>
