<?php
// admin/add_product.php
require 'config.php';

// جلب الأصناف لاستخدامها في قائمة الاختيار
$stmt = $conn->prepare("SELECT id, name FROM categories ORDER BY name ASC");
$stmt->execute();
$result = $stmt->get_result();
$categories = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $category_id = intval($_POST['category_id']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $image_path = '';

    // التحقق من صحة البيانات
    if (empty($name) || empty($category_id) || empty($price)) {
        $message = "الرجاء ملء الحقول الأساسية.";
    } else {
        // التحقق مما إذا كان المنتج مسجلاً مسبقاً
        $stmt = $conn->prepare("SELECT id FROM products WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $message = "هذا المنتج مسجل بالفعل.";
        } else {
            $stmt->close();

            // معالجة رفع الصورة
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../assetes/images/product/';
                $file_tmp = $_FILES['image']['tmp_name'];
                $file_name = basename($_FILES['image']['name']);
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($file_ext, $allowed_extensions)) {
                    $new_file_name = uniqid('product_') . '.' . $file_ext;
                    $image_path = $upload_dir . $new_file_name;

                    // نقل الملف إلى المجلد المطلوب
                    if (move_uploaded_file($file_tmp, $image_path)) {
                        $image_path = str_replace('../', '', $image_path); // تخزين المسار النسبي فقط
                    } else {
                        $message = "حدث خطأ أثناء رفع الصورة.";
                    }
                } else {
                    $message = "نوع الملف غير مدعوم. الرجاء رفع صورة بصيغة jpg أو png أو gif.";
                }
            }

            // إدخال المنتج الجديد إذا لم تكن هناك مشاكل
            if (empty($message)) {
                $stmt = $conn->prepare("INSERT INTO products (category_id, name, description, price, image_url, stock) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issdsi", $category_id, $name, $description, $price, $image_path, $stock);
                if ($stmt->execute()) {
                    $message = "تم إضافة المنتج بنجاح.";
                } else {
                    $message = "حدث خطأ أثناء إضافة المنتج.";
                }
                $stmt->close();
            }
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>إضافة منتج - لوحة التحكم</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

        <!-- Add Product Section -->
        <section class="py-12">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-8">إضافة منتج جديد</h2>
                <div class="bg-white p-8 rounded-lg shadow-lg max-w-lg mx-auto">
                    <?php if ($message): ?>
                        <div class="bg-<?php echo strpos($message, 'خطأ') !== false ? 'red' : 'green'; ?>-100 text-<?php echo strpos($message, 'خطأ') !== false ? 'red' : 'green'; ?>-700 p-4 rounded mb-6">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="add_product.php" enctype="multipart/form-data" class="space-y-6">
                        <div>
                            <label class="block text-gray-700">اسم المنتج</label>
                            <input type="text" name="name" class="w-full px-4 py-2 border rounded" placeholder="أدخل اسم المنتج" required>
                        </div>
                        <div>
                            <label class="block text-gray-700">الصنف</label>
                            <select name="category_id" class="w-full px-4 py-2 border rounded" required>
                                <option value="">اختر الصنف</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700">الوصف</label>
                            <textarea name="description" class="w-full px-4 py-2 border rounded" placeholder="أدخل وصف المنتج"></textarea>
                        </div>
                        <div>
                            <label class="block text-gray-700">السعر</label>
                            <input type="number" step="0.01" name="price" class="w-full px-4 py-2 border rounded" placeholder="أدخل سعر المنتج" required>
                        </div>
                        <div>
                            <label class="block text-gray-700">المخزون</label>
                            <input type="number" name="stock" class="w-full px-4 py-2 border rounded" placeholder="أدخل كمية المخزون" required>
                        </div>
                        <div>
                            <label class="block text-gray-700">صورة المنتج</label>
                            <input type="file" name="image" class="w-full px-4 py-2 border rounded" accept=".jpg,.jpeg,.png,.gif" required>
                        </div>
                        <button type="submit" class="w-full bg-green-500 text-white px-4 py-2 rounded">إضافة المنتج</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</body>

</html>
