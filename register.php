<?php
// register.php
session_start();
require 'config.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password']; // إضافة حقل تأكيد كلمة المرور
    $full_name = trim($_POST['full_name']);

    // التحقق من صحة الإدخال
    if (empty($email) || empty($password) || empty($confirm_password) || empty($full_name)) {
        $error = "الرجاء ملء جميع الحقول.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "البريد الإلكتروني غير صالح.";
    } elseif ($password !== $confirm_password) {
        $error = "كلمتا المرور غير متطابقتين.";
    } elseif (strlen($password) < 6) {
        $error = "يجب أن تكون كلمة المرور مكونة من 6 أحرف على الأقل.";
    } else {
        // التحقق مما إذا كان البريد الإلكتروني مسجلاً مسبقاً
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if ($stmt === false) {
            die("خطأ في إعداد الاستعلام: " . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "البريد الإلكتروني مسجل بالفعل.";
        } else {
            // تشفير كلمة المرور
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // إدخال المستخدم الجديد مع تعيين role إلى 'customer'
            $stmt = $conn->prepare("INSERT INTO users (email, password, full_name, role) VALUES (?, ?, ?, 'customer')");
            if ($stmt === false) {
                die("خطأ في إعداد الاستعلام: " . htmlspecialchars($conn->error));
            }
            $stmt->bind_param("sss", $email, $hashed_password, $full_name);

            if ($stmt->execute()) {
                $success = "تم إنشاء الحساب بنجاح. يمكنك الآن <a href='login.php' class='text-blue-500'>تسجيل الدخول</a>.";
            } else {
                $error = "حدث خطأ أثناء إنشاء الحساب. الرجاء المحاولة لاحقاً.";
            }
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>إنشاء حساب - متجر الجوال</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

  <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
    <h2 class="text-3xl font-bold text-center mb-6">إنشاء حساب</h2>
    <?php if($error): ?>
      <div class="mb-4 text-red-500 text-center">
        <?php echo $error; ?>
      </div>
    <?php endif; ?>
    <?php if($success): ?>
      <div class="mb-4 text-green-500 text-center">
        <?php echo $success; ?>
      </div>
    <?php endif; ?>
    <form class="space-y-6" method="POST" action="">
      <div>
        <label class="block text-gray-700">الاسم الكامل</label>
        <input type="text" name="full_name" class="w-full px-4 py-2 border rounded" placeholder="أدخل اسمك الكامل" required>
      </div>
      <div>
        <label class="block text-gray-700">البريد الإلكتروني</label>
        <input type="email" name="email" class="w-full px-4 py-2 border rounded" placeholder="أدخل بريدك الإلكتروني" required>
      </div>
      <div>
        <label class="block text-gray-700">كلمة المرور</label>
        <input type="password" name="password" class="w-full px-4 py-2 border rounded" placeholder="أدخل كلمة المرور" required>
      </div>
      <div>
        <label class="block text-gray-700"> تأكيد كلمة المرور</label>
        <input type="password" name="confirm_password" class="w-full px-4 py-2 border rounded" placeholder="أدخل كلمة المرور" required>
      </div>
      <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded">إنشاء الحساب</button>
    </form>
    <div class="mt-2 text-center">
      <span>لديك حساب بالفعل؟</span>
      <a href="login.php" class="text-blue-500 hover:underline">تسجيل الدخول</a>
    </div>
  </div>

</body>
</html>
