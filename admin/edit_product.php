<?php
// admin/edit_product.php
require 'config.php';

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = intval($_GET['id']);
$message = '';

// جلب بيانات المنتج
$stmt = $conn->prepare("SELECT category_id, name, description, price, image_url, stock FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->bind_result($category_id, $name, $description, $price, $image_url, $stock);
if (!$stmt->fetch()) {
    $stmt->close();
    header("Location: products.php");
    exit();
}
$stmt->close();

// جلب الأصناف لاستخدامها في قائمة الاختيار
$stmt = $conn->prepare("SELECT id, name FROM categories ORDER BY name ASC");
$stmt->execute();
$result = $stmt->get_result();
$categories = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// معالجة تحديث المنتج
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = trim($_POST['name']);
    $new_category_id = intval($_POST['category_id']);
    $new_description = trim($_POST['description']);
    $new_price = floatval($_POST['price']);
    $new_stock = intval($_POST['stock']);
    $new_image_url = $image_url; // القيمة الافتراضية هي الرابط الحالي للصورة

    // التحقق من صحة البيانات
    if (empty($new_name) || empty($new_category_id) || empty($new_price)) {
        $message = "الرجاء ملء الحقول الأساسية.";
    } else {
        // التحقق مما إذا كان اسم المنتج مسجلاً مسبقاً لمنتج آخر
        $stmt = $conn->prepare("SELECT id FROM products WHERE name = ? AND id != ?");
        $stmt->bind_param("si", $new_name, $product_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $message = "هذا المنتج مسجل بالفعل.";
        } else {
            $stmt->close();

            // معالجة رفع الصورة الجديدة (إذا تم اختيارها)
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../assetes/images/product/';
                $file_tmp = $_FILES['image']['tmp_name'];
                $file_name = basename($_FILES['image']['name']);
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($file_ext, $allowed_extensions)) {
                    $new_file_name = uniqid('product_') . '.' . $file_ext;
                    $new_image_path = $upload_dir . $new_file_name;

                    // نقل الملف إلى المجلد المطلوب
                    if (move_uploaded_file($file_tmp, $new_image_path)) {
                        $new_image_url = str_replace('../', '', $new_image_path); // تخزين المسار النسبي فقط
                    } else {
                        $message = "حدث خطأ أثناء رفع الصورة.";
                    }
                } else {
                    $message = "نوع الملف غير مدعوم. الرجاء رفع صورة بصيغة jpg أو png أو gif.";
                }
            }

            // تحديث بيانات المنتج
            if (empty($message)) {
                $stmt = $conn->prepare("UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, image_url = ?, stock = ? WHERE id = ?");
                $stmt->bind_param("issdssi", $new_category_id, $new_name, $new_description, $new_price, $new_image_url, $new_stock, $product_id);
                if ($stmt->execute()) {
                    $message = "تم تحديث المنتج بنجاح.";
                    // تحديث المتغيرات المحلية
                    $category_id = $new_category_id;
                    $name = $new_name;
                    $description = $new_description;
                    $price = $new_price;
                    $image_url = $new_image_url;
                    $stock = $new_stock;
                } else {
                    $message = "حدث خطأ أثناء تحديث المنتج.";
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
    <title>تعديل منتج - لوحة التحكم</title>
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

        <!-- Edit Product Section -->
        <section class="py-12">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-8">تعديل منتج</h2>
                <div class="bg-white p-8 rounded-lg shadow-lg max-w-lg mx-auto">
                    <?php if ($message): ?>
                        <div class="bg-<?php echo strpos($message, 'خطأ') !== false ? 'red' : 'green'; ?>-100 text-<?php echo strpos($message, 'خطأ') !== false ? 'red' : 'green'; ?>-700 p-4 rounded mb-6">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="edit_product.php?id=<?php echo $product_id; ?>" enctype="multipart/form-data" class="space-y-6">
                        <div>
                            <label class="block text-gray-700">اسم المنتج</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" class="w-full px-4 py-2 border rounded" placeholder="أدخل اسم المنتج" required>
                        </div>
                        <div>
                            <label class="block text-gray-700">الصنف</label>
                            <select name="category_id" class="w-full px-4 py-2 border rounded" required>
                                <option value="">اختر الصنف</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo ($category['id'] == $category_id) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700">الوصف</label>
                            <textarea name="description" class="w-full px-4 py-2 border rounded" placeholder="أدخل وصف المنتج"><?php echo htmlspecialchars($description); ?></textarea>
                        </div>
                        <div>
                            <label class="block text-gray-700">السعر</label>
                            <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($price); ?>" class="w-full px-4 py-2 border rounded" placeholder="أدخل سعر المنتج" required>
                        </div>
                        <div>
                            <label class="block text-gray-700">المخزون</label>
                            <input type="number" name="stock" value="<?php echo htmlspecialchars($stock); ?>" class="w-full px-4 py-2 border rounded" placeholder="أدخل كمية المخزون" required>
                        </div>
                        <div>
                            <label class="block text-gray-700">صورة المنتج</label>
                            <input type="file" name="image" class="w-full px-4 py-2 border rounded" accept=".jpg,.jpeg,.png,.gif">
                            <?php if (!empty($image_url)): ?>
                                <img src="../<?php echo htmlspecialchars($image_url); ?>" alt="صورة المنتج" class="mt-4 w-32 h-32 object-cover rounded">
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded">تحديث المنتج</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</body>

</html>
