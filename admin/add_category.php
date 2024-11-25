<?php
// admin/add_category.php
require 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = trim($_POST['name']);
  $description = trim($_POST['description']);

  // التحقق من صحة البيانات
  if (empty($name)) {
    $message = "الرجاء إدخال اسم الصنف.";
  } else {
    // التحقق مما إذا كان الصنف مسجلاً مسبقاً
    $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
      $message = "هذا الصنف مسجل بالفعل.";
    } else {
      $stmt->close();
      // إدخال الصنف الجديد
      $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
      $stmt->bind_param("ss", $name, $description);
      if ($stmt->execute()) {
        $message = "تم إضافة الصنف بنجاح.";
      } else {
        $message = "حدث خطأ أثناء إضافة الصنف.";
      }
      $stmt->close();
    }
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

    <!-- Add Category Section -->
    <section class="py-12">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-8">إضافة صنف جديد</h2>
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-lg mx-auto">
          <?php if ($message): ?>
            <div class="bg-<?php echo strpos($message, 'خطأ') !== false ? 'red' : 'green'; ?>-100 text-<?php echo strpos($message, 'خطأ') !== false ? 'red' : 'green'; ?>-700 p-4 rounded mb-6">
              <?php echo htmlspecialchars($message); ?>
            </div>
          <?php endif; ?>
          <form method="POST" action="add_category.php" class="space-y-6">
            <div>
              <label class="block text-gray-700">اسم الصنف</label>
              <input type="text" name="name" class="w-full px-4 py-2 border rounded" placeholder="أدخل اسم الصنف" required>
            </div>
            <div>
              <label class="block text-gray-700">الوصف</label>
              <textarea name="description" class="w-full px-4 py-2 border rounded" placeholder="أدخل وصف الصنف"></textarea>
            </div>
            <button type="submit" class="w-full bg-green-500 text-white px-4 py-2 rounded">إضافة الصنف</button>
          </form>
        </div>
      </div>
    </section>

  </div>

  <!-- JavaScript for Sidebar -->
  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('sidebar', () => ({
        openSidebar: false,
      }));
    });
  </script>
  <!-- تذييل الموقع -->
</body>

</html>