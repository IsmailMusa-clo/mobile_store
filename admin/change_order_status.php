<?php
// admin/change_order_status.php
require 'config.php';

if (!isset($_GET['id'])) {
  header("Location: orders.php");
  exit();
}

$order_id = intval($_GET['id']);
$message = '';

// جلب بيانات الطلب
$stmt = $conn->prepare("SELECT status FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->bind_result($current_status);
if (!$stmt->fetch()) {
  $stmt->close();
  header("Location: orders.php");
  exit();
}
$stmt->close();

// معالجة تحديث حالة الطلب
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $new_status = trim($_POST['status']);

  // التحقق من صحة البيانات
  $valid_statuses = ['قيد المعالجة', 'مكتمل', 'ملغى'];
  if (!in_array($new_status, $valid_statuses)) {
    $message = "حالة الطلب غير صالحة.";
  } else {
    // تحديث حالة الطلب
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    if ($stmt->execute()) {
      $message = "تم تحديث حالة الطلب بنجاح.";
      $current_status = $new_status;
    } else {
      $message = "حدث خطأ أثناء تحديث حالة الطلب.";
    }
    $stmt->close();
  }
}

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

    <!-- Change Order Status Section -->
    <section class="py-12">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-8">تغيير حالة الطلب</h2>
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md mx-auto">
          <?php if ($message): ?>
            <div class="bg-<?php echo strpos($message, 'خطأ') !== false ? 'red' : 'green'; ?>-100 text-<?php echo strpos($message, 'خطأ') !== false ? 'red' : 'green'; ?>-700 p-4 rounded mb-6">
              <?php echo htmlspecialchars($message); ?>
            </div>
          <?php endif; ?>
          <form method="POST" action="change_order_status.php?id=<?php echo $order_id; ?>" class="space-y-6">
            <div>
              <label class="block text-gray-700">الحالة الحالية: <strong><?php echo htmlspecialchars($current_status); ?></strong></label>
            </div>
            <div>
              <label class="block text-gray-700">تغيير الحالة إلى:</label>
              <select name="status" class="w-full px-4 py-2 border rounded" required>
                <option value="">اختر الحالة</option>
                <?php
                $statuses = ['قيد المعالجة', 'مكتمل', 'ملغى'];
                foreach ($statuses as $status):
                ?>
                  <option value="<?php echo $status; ?>" <?php echo ($current_status === $status) ? 'selected' : ''; ?>>
                    <?php echo $status; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded">تحديث الحالة</button>
          </form>
        </div>
      </div>
    </section>

    <!-- تذييل الموقع -->
  </div>
</body>

</html>