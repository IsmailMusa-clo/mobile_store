<?php
// admin/categories.php
require 'config.php';

// جلب جميع الأصناف
$stmt = $conn->prepare("SELECT id, name, description, created_at FROM categories ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$categories = $result->fetch_all(MYSQLI_ASSOC);
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

    <!-- Categories Management Section -->
    <section class="py-12">
      <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-3xl font-bold">إدارة الأصناف</h2>
          <a href="add_category.php" class="bg-green-500 text-white px-4 py-2 rounded">إضافة صنف جديد</a>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full bg-white rounded-lg shadow">
            <thead>
              <tr class="text-left border-b">
                <th class="py-4 px-4">#</th>
                <th class="py-4 px-4">الاسم</th>
                <th class="py-4 px-4">الوصف</th>
                <th class="py-4 px-4">تاريخ الإنشاء</th>
                <th class="py-4 px-4">إجراءات</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $category): ?>
                <tr class="border-t">
                  <td class="py-4 px-4 text-center"><?php echo htmlspecialchars($category['id']); ?></td>
                  <td class="py-4 px-4 text-center"><?php echo htmlspecialchars($category['name']); ?></td>
                  <td class="py-4 px-4 text-center"><?php echo htmlspecialchars($category['description']); ?></td>
                  <td class="py-4 px-4 text-center"><?php echo htmlspecialchars($category['created_at']); ?></td>
                  <td class="py-4 px-4 text-center">
                    <a href="edit_category.php?id=<?php echo $category['id']; ?>" class="text-blue-500 hover:underline">تعديل</a> |
                    <a href="delete_category.php?id=<?php echo $category['id']; ?>" class="text-red-500 hover:underline" onclick="return confirm('هل أنت متأكد من حذف هذا الصنف؟');">حذف</a>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($categories)): ?>
                <tr>
                  <td colspan="5" class="py-4 px-4 text-center ">لا يوجد أصناف لعرضها.</td>
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