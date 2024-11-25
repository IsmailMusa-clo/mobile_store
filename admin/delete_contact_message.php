<?php
// admin/delete_contact_message.php
require 'config.php';

// التحقق من وجود معرف الرسالة
if(!isset($_GET['id'])) {
    header("Location: contact_messages.php");
    exit();
}

$message_id = intval($_GET['id']);

// حذف الرسالة
$stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
if ($stmt === false) {
    die("خطأ في إعداد الاستعلام: " . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $message_id);
$stmt->execute();
$stmt->close();

$conn->close();

// إعادة التوجيه إلى صفحة رسائل التواصل
header("Location: contact_messages.php");
exit();
?>
