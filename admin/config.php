<?php
// admin/config.php

$servername = "localhost";
$username = "root";
$password = ""; // ضع كلمة مرور قاعدة البيانات هنا
$dbname = "mobile_store";

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// بدء الجلسة إذا لم تكن قد بدأت بالفعل
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
