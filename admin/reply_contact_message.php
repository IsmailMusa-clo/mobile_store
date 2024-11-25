<?php
// admin/reply_contact_message.php
require 'config.php';

// التحقق من وجود معرف الرسالة
if (!isset($_GET['id'])) {
    header("Location: contact_messages.php");
    exit();
}

$message_id = intval($_GET['id']);
$error = '';
$success = '';

// جلب بيانات الرسالة
$stmt = $conn->prepare("SELECT name, email, message FROM contact_messages WHERE id = ?");
if ($stmt === false) {
    die("خطأ في إعداد الاستعلام: " . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $message_id);
$stmt->execute();
$stmt->bind_result($name, $email, $original_message);
if (!$stmt->fetch()) {
    $stmt->close();
    header("Location: contact_messages.php");
    exit();
}
$stmt->close();

// استيراد PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

// معالجة الرد
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reply_message = trim($_POST['reply_message']);

    if (empty($reply_message)) {
        $error = "الرجاء كتابة رسالة الرد.";
    } else {
        // إعداد الرسالة
        $subject = "رد على رسالتك في متجر الجوال";
        $body = "مرحباً $name,\n\n";
        $body .= "شكراً لتواصلك معنا. نود أن نرد على رسالتك:\n\n";
        $body .= "رسالتك الأصلية:\n$original_message\n\n";
        $body .= "ردنا:\n$reply_message\n\n";
        $body .= "مع التحية,\nفريق متجر الجوال";

        $mail = new PHPMailer(true);

        try {
            // إعدادات SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.office365.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ismailnofal018@gmail.com';
            $mail->Password = 'ismail2638553##';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
        
            // تعطيل التحقق من SSL
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];
        
            // إعدادات البريد
            $mail->setFrom('your_email@gmail.com', 'متجر الجوال');
            $mail->addAddress($email, $name); // المرسل إليه
        
            // محتوى الرسالة
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body = $body;
        
            $mail->send();
            $success = "تم إرسال الرد بنجاح.";
        } catch (Exception $e) {
            $error = "حدث خطأ أثناء إرسال الرد: " . $mail->ErrorInfo;
        }
        
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <title>داشبورد - لوحة التحكم</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- إضافة Alpine.js للتفاعلية -->
  <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="flex bg-gray-100">
  <!-- Sidebar -->
  <div class="w-64 fixed inset-y-0 right-0 bg-white border-l shadow-lg z-50">
    <?php include 'sidebar.php'; ?>
  </div>

  <!-- Main Content -->
  <div class="flex-1 mr-64 min-h-screen">
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Reply Contact Message Section -->
    <section class="py-12">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-8">رد على رسالة</h2>
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-lg mx-auto">
          <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
              <?php echo htmlspecialchars($error); ?>
            </div>
          <?php endif; ?>
          <?php if ($success): ?>
            <div class="bg-green-100 text-green-700 p-4 rounded mb-6">
              <?php echo htmlspecialchars($success); ?>
            </div>
          <?php endif; ?>
          <div class="mb-6">
            <h3 class="text-xl font-semibold mb-2">تفاصيل الرسالة</h3>
            <p><strong>الاسم:</strong> <?php echo htmlspecialchars($name); ?></p>
            <p><strong>البريد الإلكتروني:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>الرسالة:</strong></p>
            <p class="whitespace-pre-line"><?php echo nl2br(htmlspecialchars($original_message)); ?></p>
          </div>
          <form method="POST" action="reply_contact_message.php?id=<?php echo $message_id; ?>" class="space-y-6">
            <div>
              <label class="block text-gray-700">رسالة الرد</label>
              <textarea name="reply_message" class="w-full px-4 py-2 border rounded" placeholder="اكتب رسالتك هنا" rows="5" required></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">إرسال الرد</button>
          </form>
          <div class="text-center mt-4">
            <a href="contact_messages.php" class="text-blue-500 hover:underline">عودة إلى رسائل التواصل</a>
          </div>
        </div>
      </div>
    </section>

    <!-- تذييل الموقع -->
  </div>
</body>

</html>