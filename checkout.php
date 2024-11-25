<?php
// checkout.php
session_start();
require 'config.php';

// التحقق مما إذا كان المستخدم مسجلاً دخوله
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// استرجاع عناصر السلة
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// التحقق مما إذا كانت السلة فارغة
if (empty($cart)) {
  header("Location: cart.php");
  exit();
}

// متغيرات لتخزين رسائل النجاح أو الخطأ
$message = '';
$error = '';

// معالجة عملية الشراء
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // جمع بيانات الشحن والدفع
  $full_name = trim($_POST['full_name']);
  $address = trim($_POST['address']);
  $phone = trim($_POST['phone']);
  $payment_method = trim($_POST['payment']);

  // التحقق من صحة البيانات
  if (empty($full_name) || empty($address) || empty($phone) || empty($payment_method)) {
    $error = "الرجاء ملء جميع الحقول.";
  } else {
    // بدء معاملة
    $conn->begin_transaction();

    try {
      // حساب الإجمالي
      $total_amount = 0;
      foreach ($cart as $item) {
        $product_id = intval($item['id']); // تأكد من تحويل القيمة إلى عدد صحيح
        $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
        if (!$stmt) {
          throw new Exception("خطأ في إعداد الاستعلام: " . $conn->error);
        }
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->bind_result($price);
        if (!$stmt->fetch()) {
          throw new Exception("المنتج غير موجود في قاعدة البيانات. Product ID: " . $product_id);
        }
        $stmt->close();
      }

      // إدخال الطلب
      $stmt = $conn->prepare("INSERT INTO orders (user_id, full_name, shipping_address, phone_number, payment_method, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, 'قيد المعالجة')");
      $stmt->bind_param("issssd", $_SESSION['user_id'], $full_name, $address, $phone, $payment_method, $total_amount);
      if (!$stmt->execute()) {
        throw new Exception("خطأ أثناء إدخال الطلب: " . $stmt->error);
      }
      $order_id = $stmt->insert_id;
      $stmt->close();

      // إدخال عناصر الطلب
      $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
      foreach ($cart as $item) {
        $stmt_price = $conn->prepare("SELECT price FROM products WHERE id = ?");
        $stmt_price->bind_param("i", $item['id']);
        $stmt_price->execute();
        $stmt_price->bind_result($price);
        if (!$stmt_price->fetch()) {
          throw new Exception("المنتج غير موجود: " . $item['id']);
        }
        $stmt_price->close();

        $stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $price);
        if (!$stmt->execute()) {
          throw new Exception("خطأ أثناء إدخال عناصر الطلب: " . $stmt->error);
        }
      }
      $stmt->close();

      // إتمام المعاملة
      $conn->commit();

      // مسح السلة
      unset($_SESSION['cart']);

      // إعداد رسالة النجاح
      $message = "تم إتمام طلبك بنجاح!";
      
      // إعادة التوجيه إلى صفحة النجاح
      header("Location: checkout_success.php");
      exit();
    } catch (Exception $e) {
      $conn->rollback();
      $error = "حدث خطأ أثناء إتمام الطلب. التفاصيل: " . $e->getMessage();
    }
  }
}


$conn->close();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <title>الدفع - متجر الجوال</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>

<body class="bg-gray-100">

  <!-- الشريط العلوي -->
  <?php include 'navbar.php'; ?>

  <!-- Checkout Section -->
  <section class="py-12">
    <div class="container mx-auto px-4">
      <h2 class="text-3xl font-bold text-center mb-8">الدفع</h2>

      <?php if ($message): ?>
        <div class="bg-green-100 text-green-700 p-4 rounded mb-6 text-center">
          <?php echo htmlspecialchars($message); ?>
        </div>
        <?php if ($message == "تم إتمام طلبك بنجاح!"): ?>
          <div class="text-center">
            <a href="index.php" class="bg-blue-500 text-white px-6 py-3 rounded">العودة إلى الرئيسية</a>
          </div>
        <?php endif; ?>
      <?php elseif ($error): ?>
        <div class="bg-red-100 text-red-700 p-4 rounded mb-6 text-center">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php else: ?>
        <div class="bg-white p-8 rounded-lg shadow-lg">
          <form method="POST" action="checkout.php" class="space-y-6">
            <!-- معلومات الشحن -->
            <div>
              <h3 class="text-2xl font-semibold mb-4">معلومات الشحن</h3>
              <div class="space-y-4">
                <div>
                  <label class="block text-gray-700">الاسم الكامل</label>
                  <input type="text" name="full_name" class="w-full px-4 py-2 border rounded" placeholder="أدخل اسمك الكامل" required>
                </div>
                <div>
                  <label class="block text-gray-700">عنوان الشحن</label>
                  <input type="text" name="address" class="w-full px-4 py-2 border rounded" placeholder="أدخل عنوان الشحن" required>
                </div>
                <div>
                  <label class="block text-gray-700">رقم الهاتف</label>
                  <input type="tel" name="phone" class="w-full px-4 py-2 border rounded" placeholder="أدخل رقم هاتفك" required>
                </div>
              </div>
            </div>

            <!-- خيارات الدفع -->
            <div>
              <h3 class="text-2xl font-semibold mb-4">طرق الدفع</h3>
              <div class="space-y-4">
                <label class="inline-flex items-center">
                  <input type="radio" name="payment" value="بطاقة ائتمان" class="form-radio" checked>
                  <span class="ml-2">بطاقة ائتمان</span>
                </label>
                <label class="inline-flex items-center">
                  <input type="radio" name="payment" value="PayPal" class="form-radio">
                  <span class="ml-2">PayPal</span>
                </label>
                <label class="inline-flex items-center">
                  <input type="radio" name="payment" value="الدفع عند الاستلام" class="form-radio">
                  <span class="ml-2">الدفع عند الاستلام</span>
                </label>
              </div>
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white px-6 py-3 rounded">إتمام الدفع</button>
          </form>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- تذييل الموقع -->
  <?php include 'footer.php'; ?>

</body>

</html>