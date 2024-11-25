<?php
// admin/orders.php
require 'config.php';
 
// جلب جميع الطلبات مع معلومات المستخدم
$stmt = $conn->prepare("SELECT orders.id, users.full_name, orders.created_at, orders.status, orders.total_amount FROM orders JOIN users ON orders.user_id = users.id ORDER BY orders.created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
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

    <!-- Orders Management Section -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-8">متابعة الطلبات</h2>
            <div class="overflow-x-auto">
                <table class="w-full bg-white rounded-lg shadow">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="py-2 px-4">#</th>
                            <th class="py-2 px-4">اسم العميل</th>
                            <th class="py-2 px-4">التاريخ</th>
                            <th class="py-2 px-4">الحالة</th>
                            <th class="py-2 px-4">الإجمالي</th>
                            <th class="py-2 px-4">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $order): ?>
                            <tr class="border-t">
                                <td class="py-4 px-4"><?php echo htmlspecialchars($order['id']); ?></td>
                                <td class="py-4 px-4"><?php echo htmlspecialchars($order['full_name']); ?></td>
                                <td class="py-4 px-4"><?php echo htmlspecialchars($order['created_at']); ?></td>
                                <td class="py-4 px-4 <?php echo ($order['status'] == 'مكتمل') ? 'text-green-500' : (($order['status'] == 'قيد المعالجة') ? 'text-yellow-500' : 'text-red-500'); ?>">
                                    <?php echo htmlspecialchars($order['status']); ?>
                                </td>
                                <td class="py-4 px-4">$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td class="py-4 px-4">
                                    <a href="order-details.php?id=<?php echo $order['id']; ?>" class="text-blue-500 hover:underline">تفاصيل</a> |
                                    <a href="change_order_status.php?id=<?php echo $order['id']; ?>" class="text-green-500 hover:underline">تغيير الحالة</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(empty($orders)): ?>
                            <tr>
                                <td colspan="6" class="py-4 px-4 text-center">لا توجد طلبات لعرضها.</td>
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
