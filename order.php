<?php
// orders.php
session_start();
require 'config.php';

// التحقق مما إذا كان المستخدم مسجلاً دخوله
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// استرجاع الطلبات الخاصة بالمستخدم من قاعدة البيانات
$stmt = $conn->prepare("SELECT id, created_at, status, total_amount FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
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
  <title>طلباتي - متجر الجوال</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">

  <!-- الشريط العلوي -->
  <?php include 'navbar.php'; ?>

  <!-- Orders Section -->
  <section class="py-12">
    <div class="container mx-auto px-4">
      <h2 class="text-3xl font-bold text-center mb-8">طلباتي</h2>
      <div class="bg-white p-8 rounded-lg shadow-lg">
        <?php if(!empty($orders)): ?>
          <table class="w-full">
            <thead>
              <tr class="text-left border-b">
                <th class="py-2">رقم الطلب</th>
                <th class="py-2">التاريخ</th>
                <th class="py-2">الحالة</th>
                <th class="py-2">الإجمالي</th>
                <th class="py-2">تفاصيل</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($orders as $order): ?>
                <tr class="border-t">
                  <td class="py-4">#<?php echo htmlspecialchars($order['id']); ?></td>
                  <td class="py-4"><?php echo htmlspecialchars($order['created_at']); ?></td>
                  <td class="py-4 <?php echo ($order['status'] == 'مكتمل') ? 'text-green-500' : 'text-yellow-500'; ?>">
                    <?php echo htmlspecialchars($order['status']); ?>
                  </td>
                  <td class="py-4">$<?php echo number_format($order['total_amount'], 2); ?></td>
                  <td class="py-4">
                    <a href="order-details.php?order_id=<?php echo $order['id']; ?>" class="text-blue-500 hover:underline">تفاصيل</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div class="text-center">
            <p>ليس لديك أي طلبات بعد. <a href="index.php" class="text-blue-500 hover:underline">تسوق الآن</a></p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- تذييل الموقع -->
  <?php include 'footer.php'; ?>

</body>
</html>
