<?php
// login.php
session_start();
require 'config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // التحقق من صحة الإدخال
    if (empty($email) || empty($password)) {
        $error = "الرجاء ملء جميع الحقول.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "البريد الإلكتروني غير صالح.";
    } else {
        // تحضير الاستعلام لمنع هجمات SQL Injection
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
        if ($stmt === false) {
            die("خطأ في إعداد الاستعلام: " . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // التحقق مما إذا كان المستخدم موجودًا
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed_password, $role);
            $stmt->fetch();

            // التحقق من كلمة المرور
            if (password_verify($password, $hashed_password)) {
                // تعيين بيانات الجلسة
                $_SESSION['user_id'] = $id;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role;

                // إعادة التوجيه بناءً على دور المستخدم
                if ($role === 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: index.php"); // صفحة العملاء الرئيسية
                }
                exit();
            } else {
                $error = "كلمة المرور غير صحيحة.";
            }
        } else {
            $error = "البريد الإلكتروني غير مسجل.";
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
  <title>تسجيل الدخول - متجر الجوال</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

  <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
    <h2 class="text-3xl font-bold text-center mb-6">تسجيل الدخول</h2>
    <?php if($error): ?>
      <div class="mb-4 text-red-500 text-center">
        <?php echo $error; ?>
      </div>
    <?php endif; ?>
    <form class="space-y-6" method="POST" action="">
      <div>
        <label class="block text-gray-700">البريد الإلكتروني</label>
        <input type="email" name="email" class="w-full px-4 py-2 border rounded" placeholder="أدخل بريدك الإلكتروني" required>
      </div>
      <div>
        <label class="block text-gray-700">كلمة المرور</label>
        <input type="password" name="password" class="w-full px-4 py-2 border rounded" placeholder="أدخل كلمة المرور" required>
      </div>
      <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded">تسجيل الدخول</button>
    </form>
    <div class="mt-4 text-center">
      <a href="#" class="text-blue-500 hover:underline">نسيت كلمة المرور؟</a>
    </div>
    <div class="mt-2 text-center">
      <span>ليس لديك حساب؟</span>
      <a href="register.php" class="text-blue-500 hover:underline">إنشاء حساب</a>
    </div>
  </div>

</body>
</html>
