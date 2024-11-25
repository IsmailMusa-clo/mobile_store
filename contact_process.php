<?php
// contact_process.php
require 'config.php';
require 'vendor/autoload.php'; // تأكد من المسار الصحيح
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// التحقق من طريقة الطلب
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // جلب بيانات النموذج مع تنقية الإدخال
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);
     // التحقق من صحة الإدخال
    if (empty($name) || empty($email) || empty($message)) {
        header("Location: index.php?error=1");
        exit();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: index.php?error=1");
        exit();
    } else {
        // تحضير الاستعلام لمنع هجمات SQL Injection
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        if ($stmt === false) {
            // تسجيل الخطأ (اختياري)
            error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            header("Location: index.php?error=1");
            exit();
        }
        $stmt->bind_param("sss", $name, $email, $message);
        
        if ($stmt->execute()) {
            // إرسال بريد إلكتروني للمسؤول باستخدام PHPMailer
            $mail = new PHPMailer(true);
            try {
                // إعدادات الخادم
                $mail->isSMTP();
                $mail->Host       = 'smtp.example.com'; //  استبدل بخادم SMTP الخاص بك
                $mail->SMTPAuth   = true;
                $mail->Username   = $email; // استبدل ببريدك الإلكتروني
                $mail->Password   = ''; // استبدل بكلمة مرور بريدك الإلكتروني
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                
                // مرسل ومتلقي البريد
                $mail->setFrom('ismailnofal019@gmail.com', 'اسم المتجر');
                $mail->addAddress('ismailnofal019@gmail.com', 'مدير المتجر'); // استبدل ببريد المسؤول
                
                // محتوى البريد
                $mail->isHTML(true);
                $mail->Subject = "رسالة تواصل جديدة من $name";
                $mail->Body    = "
                    <h3>رسالة تواصل جديدة</h3>
                    <p><strong>الاسم:</strong> {$name}</p>
                    <p><strong>البريد الإلكتروني:</strong> {$email}</p>
                    <p><strong>الرسالة:</strong></p>
                    <p>" . nl2br(htmlspecialchars($message)) . "</p>
                ";
                
                $mail->send();
                header("Location: index.php?success=1");
                exit();
            } catch (Exception $e) {
                error_log("Mail Error: {$mail->ErrorInfo}");
                header("Location: index.php?error=1");
                exit();
            }
        } else {
            // تسجيل الخطأ (اختياري)
            error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
            header("Location: index.php?error=1");
            exit();
        }
        
        $stmt->close();
    }
} else {
    // إذا لم يكن الطلب POST، إعادة التوجيه إلى الصفحة الرئيسية
    header("Location: index.php");
    exit();
}
?>
