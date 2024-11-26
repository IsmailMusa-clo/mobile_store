<?php
// admin/order-details.php
require 'config.php';
 
if(!isset($_GET['id'])) {
    header("Location: orders.php");
    exit();
}

$order_id = intval($_GET['id']);
$message = '';

// جلب بيانات الطلب
$stmt = $conn->prepare("SELECT orders.id, users.full_name, orders.created_at, orders.status, orders.total_amount, orders.shipping_address, orders.phone_number, orders.payment_method FROM orders JOIN users ON orders.user_id = users.id WHERE orders.id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->bind_result($id, $full_name, $created_at, $status, $total_amount, $shipping_address, $phone_number, $payment_method);
if(!$stmt->fetch()) {
    $stmt->close();
    header("Location: orders.php");
    exit();
}
$stmt->close();

// جلب عناصر الطلب
$stmt = $conn->prepare("SELECT products.name, order_items.quantity, order_items.price FROM order_items JOIN products ON order_items.product_id = products.id WHERE order_items.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order_items = $result->fetch_all(MYSQLI_ASSOC);
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
  <!-- Order Details Section -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-8">تفاصيل الطلب #<?php echo htmlspecialchars($id); ?></h2>
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <!-- معلومات الطلب -->
                <div class="mb-6">
                    <h3 class="text-2xl font-semibold mb-4">معلومات الطلب</h3>
                    <p><strong>اسم العميل:</strong> <?php echo htmlspecialchars($full_name); ?></p>
                    <p><strong>التاريخ:</strong> <?php echo htmlspecialchars($created_at); ?></p>
                    <p><strong>الحالة:</strong> <?php echo htmlspecialchars($status); ?></p>
                    <p><strong>الإجمالي:</strong> $<?php echo number_format($total_amount, 2); ?></p>
                    <p><strong>عنوان الشحن:</strong> <?php echo nl2br(htmlspecialchars($shipping_address)); ?></p>
                    <p><strong>رقم الهاتف:</strong> <?php echo htmlspecialchars($phone_number); ?></p>
                    <p><strong>طريقة الدفع:</strong> <?php echo htmlspecialchars($payment_method); ?></p>
                </div>

                <!-- عناصر الطلب -->
                <div>
                    <h3 class="text-2xl font-semibold mb-4">عناصر الطلب</h3>
                    <table class="w-full">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-4 px-4">المنتج</th>
                                <th class="py-4 px-4">الكمية</th>
                                <th class="py-4 px-4">السعر</th>
                                <th class="py-4 px-4">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($order_items as $item): ?>
                                <tr class="border-t">
                                    <td class="py-4 px-4 text-center"><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td class="py-4 px-4 text-center"><?php echo htmlspecialchars($item['quantity']); ?></td>
                                    <td class="py-4 px-4 text-center">$<?php echo number_format($item['price'], 2); ?></td>
                                    <td class="py-4 px-4 text-center">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- رابط العودة -->
                <div class="text-center mt-6">
                    <a href="orders.php" class="bg-blue-500 text-white px-6 py-2 rounded">عودة إلى الطلبات</a>
                </div>
            </div>
        </div>
    </section>

    <!-- تذييل الموقع -->
    </div>
</body>
</html>
