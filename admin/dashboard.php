<?php
// admin/dashboard.php
session_start();
require 'config.php';

// التحقق من صلاحية الوصول
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

// جلب بيانات إحصائيات الداشبورد

// عدد المستخدمين
$stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE role = 'customer'");
$stmt->execute();
$stmt->bind_result($user_count);
$stmt->fetch();
$stmt->close();

// عدد الأصناف
$stmt = $conn->prepare("SELECT COUNT(*) FROM categories");
$stmt->execute();
$stmt->bind_result($category_count);
$stmt->fetch();
$stmt->close();

// عدد المنتجات
$stmt = $conn->prepare("SELECT COUNT(*) FROM products");
$stmt->execute();
$stmt->bind_result($product_count);
$stmt->fetch();
$stmt->close();

// إجمالي المبيعات (مجموع الطلبات المكتملة)
$stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) AS total_sales FROM orders WHERE status = 'مكتمل'");
$stmt->execute();
$stmt->bind_result($total_sales);
$stmt->fetch();
$stmt->close();

// عدد الطلبات
$stmt = $conn->prepare("SELECT COUNT(*) FROM orders");
$stmt->execute();
$stmt->bind_result($order_count);
$stmt->fetch();
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

        <!-- Dashboard Content -->
        <main class="p-6">
            <h2 class="text-3xl font-bold text-center mb-8">الداشبورد</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- عدد المستخدمين -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-xl font-semibold mb-2">المستخدمين</h3>
                    <p class="text-3xl"><?php echo htmlspecialchars($user_count); ?></p>
                </div>
                <!-- عدد الأصناف -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-xl font-semibold mb-2">الأصناف</h3>
                    <p class="text-3xl"><?php echo htmlspecialchars($category_count); ?></p>
                </div>
                <!-- عدد المنتجات -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-xl font-semibold mb-2">المنتجات</h3>
                    <p class="text-3xl"><?php echo htmlspecialchars($product_count); ?></p>
                </div>
                <!-- إجمالي المبيعات -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-xl font-semibold mb-2">إجمالي المبيعات</h3>
                    <p class="text-3xl">$<?php echo number_format($total_sales, 2); ?></p>
                </div>
                <!-- عدد الطلبات -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-xl font-semibold mb-2">الطلبات</h3>
                    <p class="text-3xl"><?php echo htmlspecialchars($order_count); ?></p>
                </div>
            </div>
        </main>

      </div>

    <!-- JavaScript for Sidebar -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('sidebar', () => ({
                openSidebar: false,
            }));
        });
    </script>
</body>

</html>
