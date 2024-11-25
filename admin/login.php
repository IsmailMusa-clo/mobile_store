<?php
// admin/login.php
require 'config.php';

// إذا كان المستخدم مسجلاً دخوله بالفعل، إعادة توجيهه إلى الداشبورد
if(isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    header("Location: dashboard.php");
    exit();
}

$message = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if(empty($email) || empty($password)) {
        $message = "الرجاء ملء جميع الحقول.";
    } else {
        // استخدام prepared statements للبحث عن المستخدم
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($id, $hashed_password, $role);
        $stmt->fetch();
        $stmt->close();

        if($id && password_verify($password, $hashed_password) && $role === 'admin') {
            // تعيين الجلسة
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "البريد الإلكتروني أو كلمة المرور غير صحيحة.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول - لوحة التحكم</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">تسجيل الدخول للوحة التحكم</h2>
        <?php if($message): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4 text-center">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="mb-4">
                <label class="block text-gray-700">البريد الإلكتروني</label>
                <input type="email" name="email" class="w-full px-4 py-2 border rounded" placeholder="أدخل بريدك الإلكتروني" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700">كلمة المرور</label>
                <input type="password" name="password" class="w-full px-4 py-2 border rounded" placeholder="أدخل كلمة المرور" required>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded">تسجيل الدخول</button>
        </form>
        <a class="link text-sm mt-3 text-blue-500" href="../index.php">الرجوع للصفحة الرئيسية</a>
    </div>
</body>
</html>
