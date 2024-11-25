<?php
// admin/edit_user.php
require 'config.php';
 
if(!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$user_id = intval($_GET['id']);
$message = '';

// جلب بيانات المستخدم
$stmt = $conn->prepare("SELECT full_name, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($full_name, $email, $role);
if(!$stmt->fetch()) {
    $stmt->close();
    header("Location: users.php");
    exit();
}
$stmt->close();

// معالجة تحديث المستخدم
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_full_name = trim($_POST['full_name']);
    $new_email = trim($_POST['email']);
    $new_role = trim($_POST['role']);

    // التحقق من صحة البيانات
    if(empty($new_full_name) || empty($new_email) || empty($new_role)) {
        $message = "الرجاء ملء جميع الحقول.";
    } elseif(!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $message = "البريد الإلكتروني غير صالح.";
    } else {
        // التحقق مما إذا كان البريد الإلكتروني مسجلاً مسبقاً لمستخدم آخر
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $new_email, $user_id);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows > 0) {
            $message = "البريد الإلكتروني مسجل بالفعل.";
        } else {
            $stmt->close();
            // تحديث بيانات المستخدم
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, role = ? WHERE id = ?");
            $stmt->bind_param("sssi", $new_full_name, $new_email, $new_role, $user_id);
            if($stmt->execute()) {
                $message = "تم تحديث المستخدم بنجاح.";
                // تحديث المتغيرات المحلية
                $full_name = $new_full_name;
                $email = $new_email;
                $role = $new_role;
            } else {
                $message = "حدث خطأ أثناء تحديث المستخدم.";
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

    <!-- Edit User Section -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-8">تعديل مستخدم</h2>
            <div class="bg-white p-8 rounded-lg shadow-lg max-w-lg mx-auto">
                <?php if($message): ?>
                    <div class="bg-<?php echo strpos($message, 'خطأ') !== false ? 'red' : 'green'; ?>-100 text-<?php echo strpos($message, 'خطأ') !== false ? 'red' : 'green'; ?>-700 p-4 rounded mb-6">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="edit_user.php?id=<?php echo $user_id; ?>" class="space-y-6">
                    <div>
                        <label class="block text-gray-700">الاسم الكامل</label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" class="w-full px-4 py-2 border rounded" placeholder="أدخل الاسم الكامل" required>
                    </div>
                    <div>
                        <label class="block text-gray-700">البريد الإلكتروني</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" class="w-full px-4 py-2 border rounded" placeholder="أدخل البريد الإلكتروني" required>
                    </div>
                    <div>
                        <label class="block text-gray-700">الدور</label>
                        <select name="role" class="w-full px-4 py-2 border rounded" required>
                            <option value="customer" <?php echo ($role === 'customer') ? 'selected' : ''; ?>>عميل</option>
                            <option value="admin" <?php echo ($role === 'admin') ? 'selected' : ''; ?>>مسؤول</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded">تحديث المستخدم</button>
                </form>
            </div>
        </div>
    </section>

    <!-- تذييل الموقع -->
    </div>
</body>
</html>
