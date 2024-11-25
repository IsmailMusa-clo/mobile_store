<?php
// admin/edit_category.php
require 'config.php';
 
if(!isset($_GET['id'])) {
    header("Location: categories.php");
    exit();
}

$category_id = intval($_GET['id']);
$message = '';

// جلب بيانات الصنف
$stmt = $conn->prepare("SELECT name, description FROM categories WHERE id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$stmt->bind_result($name, $description);
if(!$stmt->fetch()) {
    $stmt->close();
    header("Location: categories.php");
    exit();
}
$stmt->close();

// معالجة تحديث الصنف
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = trim($_POST['name']);
    $new_description = trim($_POST['description']);

    // التحقق من صحة البيانات
    if(empty($new_name)) {
        $message = "الرجاء إدخال اسم الصنف.";
    } else {
        // التحقق مما إذا كان اسم الصنف مسجلاً مسبقاً لمصنف آخر
        $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ? AND id != ?");
        $stmt->bind_param("si", $new_name, $category_id);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows > 0) {
            $message = "هذا الصنف مسجل بالفعل.";
        } else {
            $stmt->close();
            // تحديث بيانات الصنف
            $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
            $stmt->bind_param("ssi", $new_name, $new_description, $category_id);
            if($stmt->execute()) {
                $message = "تم تحديث الصنف بنجاح.";
                // تحديث المتغيرات المحلية
                $name = $new_name;
                $description = $new_description;
            } else {
                $message = "حدث خطأ أثناء تحديث الصنف.";
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

    <!-- Edit Category Section -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-8">تعديل صنف</h2>
            <div class="bg-white p-8 rounded-lg shadow-lg max-w-lg mx-auto">
                <?php if($message): ?>
                    <div class="bg-<?php echo strpos($message, 'خطأ') !== false ? 'red' : 'green'; ?>-100 text-<?php echo strpos($message, 'خطأ') !== false ? 'red' : 'green'; ?>-700 p-4 rounded mb-6">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="edit_category.php?id=<?php echo $category_id; ?>" class="space-y-6">
                    <div>
                        <label class="block text-gray-700">اسم الصنف</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" class="w-full px-4 py-2 border rounded" placeholder="أدخل اسم الصنف" required>
                    </div>
                    <div>
                        <label class="block text-gray-700">الوصف</label>
                        <textarea name="description" class="w-full px-4 py-2 border rounded" placeholder="أدخل وصف الصنف"><?php echo htmlspecialchars($description); ?></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded">تحديث الصنف</button>
                </form>
            </div>
        </div>
    </section>

    <!-- تذييل الموقع -->
    </div>
</body>
</html>
