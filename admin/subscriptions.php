<?php
// admin/subscriptions.php
require 'config.php';
// جلب جميع الاشتراكات
$stmt = $conn->prepare("SELECT id, email ,subscribed_at FROM subscribers ORDER BY subscribed_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$subscriptions = $result->fetch_all(MYSQLI_ASSOC);
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

    <!-- Subscriptions Management Section -->
    <section class="py-12">
      <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-3xl font-bold">إدارة الاشتراكات</h2>
          <a href="add_subscription.php" class="bg-green-500 text-white px-4 py-2 rounded">إضافة اشتراك جديد</a>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full bg-white rounded-lg shadow">
            <thead>
              <tr class="text-left border-b">
                <th class="py-2 px-4 text-center">#</th>
                <th class="py-2 px-4 text-center">الايميل</th>
                <th class="py-2 px-4 text-center">تاريخ الإنشاء</th>
                <th class="py-2 px-4 text-center">إجراءات</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($subscriptions as $subscription): ?>
                <tr class="border-t">
                  <td class="py-2 px-4 text-center"><?php echo htmlspecialchars($subscription['id']); ?></td>
                  <td class="py-2 px-4 text-center"><?php echo htmlspecialchars($subscription['email']); ?></td>
                  <td class="py-2 px-4 text-center"><?php echo htmlspecialchars($subscription['subscribed_at']); ?></td>
                  <td class="py-2 px-4 text-center">
                    <a href="delete_subscription.php?id=<?php echo $subscription['id']; ?>" class="text-red-500 hover:underline" onclick="return confirm('هل أنت متأكد من حذف هذا الاشتراك؟');">حذف</a>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($subscriptions)): ?>
                <tr>
                  <td colspan="7" class="py-2 px-4 text-center">لا توجد اشتراكات لعرضها.</td>
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