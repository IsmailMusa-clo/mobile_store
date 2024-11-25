<?php
// subscribe.php
session_start();
require 'config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['email'])) {
        $email = trim($_POST['email']);
        
        // التحقق من صحة البريد الإلكتروني
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // التحقق مما إذا كان البريد الإلكتروني مسجلاً مسبقاً
            $stmt = $conn->prepare("SELECT id FROM subscribers WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            
            if($stmt->num_rows > 0) {
                $_SESSION['subscribe_message'] = "هذا البريد الإلكتروني مسجل بالفعل.";
            } else {
                // إدخال المشترك الجديد
                $stmt = $conn->prepare("INSERT INTO subscribers (email) VALUES (?)");
                $stmt->bind_param("s", $email);
                
                if($stmt->execute()) {
                    $_SESSION['subscribe_message'] = "تم الاشتراك بنجاح!";
                } else {
                    $_SESSION['subscribe_message'] = "حدث خطأ أثناء الاشتراك. حاول مرة أخرى.";
                }
            }
            
            $stmt->close();
        } else {
            $_SESSION['subscribe_message'] = "البريد الإلكتروني غير صالح.";
        }
    } else {
        $_SESSION['subscribe_message'] = "يرجى إدخال بريد إلكتروني.";
    }
    
    $conn->close();
    header("Location: index.php");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>
