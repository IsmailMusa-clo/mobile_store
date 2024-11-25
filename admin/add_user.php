<?php
// admin/add_user.php
require 'config.php';
 
$message = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = 'customer'; // يمكن تعديلها لاحقاً إذا كنت تريد إضافة مستخدمين بأدوار مختلفة

    // التحقق من صحة البيانات
    if(empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = "الرجاء ملء جميع الحقول.";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "البريد الإلكتروني غير صالح.";
    } elseif($password !== $confirm_password) {
        $message = "كلمتا المرور غير متطابقتين.";
    } else {
        // التحقق مما إذا كان البريد الإلكتروني مسجلاً مسبقاً
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows > 0) {
            $message = "البريد الإلكتروني مسجل بالفعل.";
        } else {
            $stmt->close();
            // تشفير كلمة المرور
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // إدخال المستخدم الجديد
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $role);
            if($stmt->execute()) {
                $message = "تم إضافة المستخدم بنجاح.";
            } else {
                $message = "حدث خطأ أثناء إضافة المستخدم.";
            }
            $stmt->close();
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

    <!-- Add User Section -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-8">إضافة مستخدم جديد</h2>
            <div class="bg-white p-8 rounded-lg shadow-lg max-w-lg mx-auto">
                <?php if($message): ?>
                    <div class="bg-<?php echo strpos($message, 'خطأ') !== false ? 'red' : 'green'; ?>-100 text-<?php echo strpos($message, 'خطأ') !== false ? 'red' : 'green'; ?>-700 p-4 rounded mb-6">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="add_user.php" class="space-y-6">
                    <div>
                        <label class="block text-gray-700">الاسم الكامل</label>
                        <input type="text" name="full_name" class="w-full px-4 py-2 border rounded" placeholder="أدخل الاسم الكامل" required>
                    </div>
                    <div>
                        <label class="block text-gray-700">البريد الإلكتروني</label>
                        <input type="email" name="email" class="w-full px-4 py-2 border rounded" placeholder="أدخل البريد الإلكتروني" required>
                    </div>
                    <div>
                        <label class="block text-gray-700">كلمة المرور</label>
                        <input type="password" name="password" class="w-full px-4 py-2 border rounded" placeholder="أدخل كلمة المرور" required>
                    </div>
                    <div>
                        <label class="block text-gray-700">تأكيد كلمة المرور</label>
                        <input type="password" name="confirm_password" class="w-full px-4 py-2 border rounded" placeholder="أعد إدخال كلمة المرور" required>
                    </div>
                    <button type="submit" class="w-full bg-green-500 text-white px-4 py-2 rounded">إضافة المستخدم</button>
                </form>
            </div>
        </div>
    </section>
    </div>
    <!-- تذييل الموقع -->
 </body>
</html>
