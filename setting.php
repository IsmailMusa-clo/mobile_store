    <!-- الشريط العلوي -->
    <?php include 'navbar.php'; 
    // التحقق مما إذا كان المستخدم مسجلاً دخوله
if(!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}
    
    ?>


<?php
// settings.php
 require 'config.php';



// استرجاع معلومات المستخدم من قاعدة البيانات
$stmt = $conn->prepare("SELECT full_name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($full_name, $email);
$stmt->fetch();
$stmt->close();

// متغيرات لتخزين رسائل النجاح أو الخطأ
$error = '';
$success = '';
$password_error = '';
$password_success = '';

// معالجة تحديث معلومات المستخدم
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_info'])) {
    $new_full_name = trim($_POST['full_name']);
    $new_email = trim($_POST['email']);

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
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $new_full_name, $new_email, $_SESSION['user_id']);

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

// معالجة تغيير كلمة المرور
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // التحقق من صحة البيانات
    if(empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $password_error = "الرجاء ملء جميع الحقول.";
    } elseif($new_password !== $confirm_password) {
        $password_error = "كلمات المرور الجديدة غير متطابقة.";
    } elseif(strlen($new_password) < 6) {
        $password_error = "يجب أن تكون كلمة المرور الجديدة على الأقل 6 أحرف.";
    } else {
        // استرجاع كلمة المرور الحالية من قاعدة البيانات
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        $stmt->close();

        // التحقق من صحة كلمة المرور الحالية
        if(password_verify($current_password, $hashed_password)) {
            // تشفير كلمة المرور الجديدة
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // تحديث كلمة المرور في قاعدة البيانات
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new_hashed_password, $_SESSION['user_id']);

            if($stmt->execute()) {
                $password_success = "تم تغيير كلمة المرور بنجاح.";
            } else {
                $password_error = "حدث خطأ أثناء تغيير كلمة المرور.";
            }

            $stmt->close();
        } else {
            $password_error = "كلمة المرور الحالية غير صحيحة.";
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الإعدادات - متجر الجوال</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">


    <!-- Settings Section -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-8">الإعدادات</h2>
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <!-- نموذج تعديل المعلومات الشخصية -->
                <form method="POST" action="" class="space-y-6">
                    <input type="hidden" name="update_info" value="1">
                    <div>
                        <label class="block text-gray-700">الاسم الكامل</label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" class="w-full px-4 py-2 border rounded" required>
                    </div>
                    <div>
                        <label class="block text-gray-700">البريد الإلكتروني</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" class="w-full px-4 py-2 border rounded" required>
                    </div>
                    <?php if($error): ?>
                        <div class="bg-red-100 text-red-700 p-4 rounded">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <?php if($success): ?>
                        <div class="bg-green-100 text-green-700 p-4 rounded">
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded">حفظ التغييرات</button>
                </form>
                
                <!-- تغيير كلمة المرور -->
                <div class="mt-12">
                    <h3 class="text-2xl font-semibold mb-4">تغيير كلمة المرور</h3>
                    <form method="POST" action="" class="space-y-6">
                        <input type="hidden" name="change_password" value="1">
                        <div>
                            <label class="block text-gray-700">كلمة المرور الحالية</label>
                            <input type="password" name="current_password" class="w-full px-4 py-2 border rounded" required>
                        </div>
                        <div>
                            <label class="block text-gray-700">كلمة المرور الجديدة</label>
                            <input type="password" name="new_password" class="w-full px-4 py-2 border rounded" required>
                        </div>
                        <div>
                            <label class="block text-gray-700">تأكيد كلمة المرور الجديدة</label>
                            <input type="password" name="confirm_password" class="w-full px-4 py-2 border rounded" required>
                        </div>
                        <?php if($password_error): ?>
                            <div class="bg-red-100 text-red-700 p-4 rounded">
                                <?php echo $password_error; ?>
                            </div>
                        <?php endif; ?>
                        <?php if($password_success): ?>
                            <div class="bg-green-100 text-green-700 p-4 rounded">
                                <?php echo $password_success; ?>
                            </div>
                        <?php endif; ?>
                        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded">تغيير كلمة المرور</button>
                    </form>
                </div>
            </div>
        </div>
    </section>


    <!-- تذييل الموقع -->
    <?php include 'footer.php'; ?>

</body>
</html>
