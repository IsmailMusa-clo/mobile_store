<?php
// admin/products.php
require 'config.php';
 
// جلب جميع المنتجات مع معلومات الصنف
$stmt = $conn->prepare("SELECT products.id, products.name, categories.name as category, products.price, products.stock, products.created_at FROM products JOIN categories ON products.category_id = categories.id ORDER BY products.created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

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

    <!-- Products Management Section -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold">إدارة المنتجات</h2>
                <a href="add_product.php" class="bg-green-500 text-white px-4 py-2 rounded">إضافة منتج جديد</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full bg-white rounded-lg shadow">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="py-4 px-4">#</th>
                            <th class="py-4 px-4">الاسم</th>
                            <th class="py-4 px-4">الصنف</th>
                            <th class="py-4 px-4">السعر</th>
                            <th class="py-4 px-4">المخزون</th>
                            <th class="py-4 px-4">تاريخ الإنشاء</th>
                            <th class="py-4 px-4">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $product): ?>
                            <tr class="border-t">
                                <td class="py-4 px-4 text-center"><?php echo htmlspecialchars($product['id']); ?></td>
                                <td class="py-4 px-4 text-center"><?php echo htmlspecialchars($product['name']); ?></td>
                                <td class="py-4 px-4 text-center"><?php echo htmlspecialchars($product['category']); ?></td>
                                <td class="py-4 px-4 text-center">$<?php echo number_format($product['price'], 2); ?></td>
                                <td class="py-4 px-4 text-center"><?php echo htmlspecialchars($product['stock']); ?></td>
                                <td class="py-4 px-4 text-center"><?php echo htmlspecialchars($product['created_at']); ?></td>
                                <td class="py-4 px-4 text-center">
                                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="text-blue-500 hover:underline">تعديل</a> |
                                    <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="text-red-500 hover:underline" onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟');">حذف</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(empty($products)): ?>
                            <tr>
                                <td colspan="7" class="py-4 px-4 text-center">لا يوجد منتجات لعرضها.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- تذييل الموقع -->
    </div>
</body>
</html>
