<?php
// cart.php
require 'config.php';
 
// التحقق مما إذا كان المستخدم مسجلاً دخوله

// متغيرات لتخزين رسائل النجاح أو الخطأ
$message = '';

// معالجة تحديث الكميات
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
    foreach($_POST['quantities'] as $product_id => $quantity) {
        $quantity = intval($quantity);
        if($quantity > 0) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    $message = "تم تحديث السلة بنجاح.";
}

// معالجة حذف عنصر من السلة
if(isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    if(isset($_SESSION['cart'][$remove_id])) {
        unset($_SESSION['cart'][$remove_id]);
        $message = "تم حذف العنصر من السلة.";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>سلة المشتريات - متجر الجوال</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">

  <!-- الشريط العلوي -->
  <?php include 'navbar.php';
    if(!isset($_SESSION['user_id'])) {
      header("Location: login.php");
      exit();
  }
  
  
  ?>


  <!-- Cart Section -->
  <section class="py-12">
    <div class="container mx-auto px-4">
      <h2 class="text-3xl font-bold text-center mb-8">سلة المشتريات</h2>
      
      <?php if($message): ?>
        <div class="bg-green-100 text-green-700 p-4 rounded mb-6 text-center">
          <?php echo htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>

      <?php if(!empty($_SESSION['cart'])): ?>
        <form method="POST" action="cart.php">
          <table class="w-full mb-6">
            <thead>
              <tr class="text-left border-b">
                <th class="py-2">المنتج</th>
                <th class="py-2">الكمية</th>
                <th class="py-2">السعر</th>
                <th class="py-2">الإجمالي</th>
                <th class="py-2">إجراء</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $total = 0;
              foreach($_SESSION['cart'] as $product_id => $item):
                  $quantity = $item['quantity'];

                  // استرجاع بيانات المنتج من قاعدة البيانات
                  $stmt = $conn->prepare("SELECT name, price, image_url FROM products WHERE id = ?");
                  $stmt->bind_param("i", $product_id);
                  $stmt->execute();
                  $stmt->store_result();

                  if($stmt->num_rows > 0){
                      $stmt->bind_result($name, $price, $image_url);
                      $stmt->fetch();
                      $stmt->close();

                      $subtotal = $price * $quantity;
                      $total += $subtotal;
              ?>
              <tr class="border-t">
                <td class="py-4 flex items-center">
                  <img src="<?php echo htmlspecialchars($image_url ?? 'default_image.png'); ?>" alt="<?php echo htmlspecialchars($name ?? ''); ?>" class="w-16 h-16 object-cover rounded mr-4">
                  <span><?php echo htmlspecialchars($name ?? 'غير معروف'); ?></span>
                </td>
                <td class="py-4">
                  <input type="number" name="quantities[<?php echo $product_id; ?>]" value="<?php echo $quantity; ?>" min="1" class="w-16 px-2 py-1 border rounded">
                </td>
                <td class="py-4">$<?php echo number_format($price, 2); ?></td>
                <td class="py-4">$<?php echo number_format($subtotal, 2); ?></td>
                <td class="py-4">
                  <a href="cart.php?remove=<?php echo $product_id; ?>" class="text-red-500 hover:underline">حذف</a>
                </td>
              </tr>
              <?php
                  } else {
                      // المنتج غير موجود، قم بإزالته من السلة
                      unset($_SESSION['cart'][$product_id]);
                      $message = "تم حذف منتج غير موجود من السلة.";
                  }
              endforeach;
              ?>
              <tr>
                <td colspan="3" class="text-right font-bold">الإجمالي:</td>
                <td colspan="2" class="font-bold">$<?php echo number_format($total, 2); ?></td>
              </tr>
            </tbody>
          </table>
          <div class="flex flex-col md:flex-row justify-between items-center">
            <button type="submit" name="update_cart" class="bg-blue-500 text-white px-6 py-2 rounded">تحديث السلة</button>
            <div class="mt-4 md:mt-0 text-right">
              <a href="checkout.php" class="bg-green-500 text-white px-6 py-2 rounded">إتمام الشراء</a>
              <a href="index.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded ml-2">متابعة التسوق</a>
            </div>
          </div>
        </form>
      <?php else: ?>
        <div class="bg-white p-8 rounded-lg shadow-lg text-center">
          <p>سلتك فارغة. <a href="index.php" class="text-blue-500 hover:underline">تسوق الآن</a></p>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- تذييل الموقع -->
  <?php include 'footer.php'; ?>

</body>
</html>
