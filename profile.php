<?php
// profile.php
session_start();
require 'config.php';

// التحقق مما إذا كان المستخدم مسجلاً دخوله
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// استرجاع معلومات المستخدم من قاعدة البيانات
$stmt = $conn->prepare("SELECT full_name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($full_name, $email);
$stmt->fetch();
$stmt->close();

// تحديث معلومات المستخدم
$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_full_name = trim($_POST['full_name']);
    $new_email = trim($_POST['email']);
    $new_password = trim($_POST['password']);

    // التحقق من صحة البيانات
    if(empty($new_full_name) || empty($new_email)) {
        $error = "الرجاء ملء جميع الحقول.";
    } elseif(!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "البريد الإلكتروني غير صالح.";
    } else {
        // التحقق مما إذا كان البريد الإلكتروني مسجلاً مسبقاً
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $new_email, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows > 0) {
            $error = "البريد الإلكتروني مسجل بالفعل.";
        } else {
            $stmt->close();
            // تحديث بيانات المستخدم
            if(!empty($new_password)) {
                // تشفير كلمة المرور الجديدة
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, password = ? WHERE id = ?");
                $stmt->bind_param("sssi", $new_full_name, $new_email, $hashed_password, $_SESSION['user_id']);
            } else {
                $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
                $stmt->bind_param("ssi", $new_full_name, $new_email, $_SESSION['user_id']);
            }

            if($stmt->execute()) {
                $success = "تم تحديث معلوماتك بنجاح.";
                // تحديث المتغيرات المحلية
                $full_name = $new_full_name;
                $email = $new_email;
            } else {
                $error = "حدث خطأ أثناء التحديث.";
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
    <title>حسابي - متجر الجوال</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">

    <!-- الشريط العلوي -->
    <?php include 'navbar.php'; ?>

    <!-- Profile Section -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-8">حسابي</h2>
            <div class="bg-white p-8 rounded-lg shadow-lg flex flex-col md:flex-row">
                <!-- صورة ومعلومات المستخدم -->
                <div class="flex flex-col items-center md:items-start md:w-1/3">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="صورة المستخدم" class="w-32 h-32 rounded-full mb-4">
                    <h3 class="text-xl font-semibold"><?php echo htmlspecialchars($full_name); ?></h3>
                    <p class="text-gray-600"><?php echo htmlspecialchars($email); ?></p>
                    <a href="settings.php" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">الإعدادات</a>
                </div>
                
                <!-- قائمة الخيارات ونموذج تحديث المعلومات -->
                <div class="md:w-2/3 mt-6 md:mt-0 md:pl-8">
                    <div class="space-y-4">
                        <a href="orders.php" class="block bg-gray-100 hover:bg-gray-200 px-4 py-3 rounded">طلباتي</a>
                        <a href="cart.php" class="block bg-gray-100 hover:bg-gray-200 px-4 py-3 rounded">سلة المشتريات</a>
                        <a href="logout.php" class="block bg-red-500 hover:bg-red-600 text-white px-4 py-3 rounded">تسجيل الخروج</a>
                    </div>

                    <!-- نموذج تحديث معلومات المستخدم -->
                    <div class="mt-8">
                        <h3 class="text-2xl font-semibold mb-4">تحديث معلوماتي</h3>
                        <?php if($error): ?>
                            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($success): ?>
                            <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
                                <?php echo $success; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" class="bg-gray-50 p-6 rounded-lg shadow-md">
                            <div class="mb-4">
                                <label class="block text-gray-700">الاسم الكامل</label>
                                <input type="text" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required class="w-full px-4 py-2 border rounded">
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700">البريد الإلكتروني</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required class="w-full px-4 py-2 border rounded">
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700">كلمة المرور الجديدة (اختياري)</label>
                                <input type="password" name="password" placeholder="أدخل كلمة المرور الجديدة" class="w-full px-4 py-2 border rounded">
                            </div>
                            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded">تحديث المعلومات</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- تذييل الموقع -->
    <?php include 'footer.php'; ?>

</body>
</html>
