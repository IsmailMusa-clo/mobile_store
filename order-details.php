  <!-- الشريط العلوي -->
  <?php include 'navbar.php';
    
    if(!isset($_SESSION['user_id'])) {
      header("Location: login.php");
      exit();
  }
  
    
    ?>
<?php
// order-details.php
 require 'config.php';

// التحقق مما إذا كان المستخدم مسجلاً دخوله

// التحقق من وجود `order_id` في الرابط
if(!isset($_GET['order_id'])) {
    header("Location: orders.php");
    exit();
}

$order_id = intval($_GET['order_id']);

// استرجاع بيانات الطلب من قاعدة البيانات
$stmt = $conn->prepare("SELECT id, created_at, status, total_amount, full_name, shipping_address, phone_number, payment_method FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

// التحقق مما إذا كان الطلب موجودًا
if(!$order) {
    echo "الطلب غير موجود أو لا يمكنك الوصول إليه.";
    exit();
}

// استرجاع عناصر الطلب من قاعدة البيانات
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
    <title>تفاصيل الطلب #<?php echo htmlspecialchars($order['id']); ?> - متجر الجوال</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">

  

    <!-- Order Details Section -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-8">تفاصيل الطلب #<?php echo htmlspecialchars($order['id']); ?></h2>
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <!-- معلومات الطلب -->
                <div class="mb-6">
                    <h3 class="text-2xl font-semibold mb-4">معلومات الطلب</h3>
                    <p><strong>التاريخ:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>
                    <p><strong>الحالة:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
                    <p><strong>الإجمالي:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
                    <p><strong>اسم المستلم:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
                    <p><strong>عنوان الشحن:</strong> <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                    <p><strong>رقم الهاتف:</strong> <?php echo htmlspecialchars($order['phone_number']); ?></p>
                    <p><strong>طريقة الدفع:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                </div>

                <!-- عناصر الطلب -->
                <div>
                    <h3 class="text-2xl font-semibold mb-4">عناصر الطلب</h3>
                    <table class="w-full">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-2">المنتج</th>
                                <th class="py-2">الكمية</th>
                                <th class="py-2">السعر</th>
                                <th class="py-2">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($order_items as $item): ?>
                                <tr class="border-t">
                                    <td class="py-4"><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td class="py-4"><?php echo htmlspecialchars($item['quantity']); ?></td>
                                    <td class="py-4">$<?php echo number_format($item['price'], 2); ?></td>
                                    <td class="py-4">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="text-center mt-6">
                <a href="orders.php" class="bg-blue-500 text-white px-6 py-2 rounded">عودة إلى طلباتي</a>
            </div>
        </div>
    </section>

    <!-- تذييل الموقع -->
    <?php include 'footer.php'; ?>

</body>
</html>
