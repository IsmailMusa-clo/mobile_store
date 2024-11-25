<?php
// admin/contact_messages.php
require 'config.php';
 
// جلب جميع رسائل التواصل مرتبة حسب التاريخ الأحدث
$stmt = $conn->prepare("SELECT id, name, email, message, created_at FROM contact_messages ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
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

  <!-- Contact Messages Section -->
  <section class="py-12">
    <div class="container mx-auto px-4">
      <h2 class="text-3xl font-bold text-center mb-8">رسائل التواصل</h2>
      <div class="overflow-x-auto">
        <table class="w-full bg-white rounded-lg shadow">
          <thead>
            <tr class="text-left border-b">
              <th class="py-2 px-4">#</th>
              <th class="py-2 px-4">الاسم</th>
              <th class="py-2 px-4">البريد الإلكتروني</th>
              <th class="py-2 px-4">الرسالة</th>
              <th class="py-2 px-4">تاريخ الإرسال</th>
              <th class="py-2 px-4">إجراءات</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($messages as $message): ?>
              <tr class="border-t">
                <td class="py-4 px-4"><?php echo htmlspecialchars($message['id']); ?></td>
                <td class="py-4 px-4"><?php echo htmlspecialchars($message['name']); ?></td>
                <td class="py-4 px-4"><?php echo htmlspecialchars($message['email']); ?></td>
                <td class="py-4 px-4"><?php echo nl2br(htmlspecialchars($message['message'])); ?></td>
                <td class="py-4 px-4"><?php echo htmlspecialchars($message['created_at']); ?></td>
                <td class="py-4 px-4">
                  <a href="reply_contact_message.php?id=<?php echo $message['id']; ?>" class="text-blue-500 hover:underline">رد</a> |
                  <a href="delete_contact_message.php?id=<?php echo $message['id']; ?>" class="text-red-500 hover:underline" onclick="return confirm('هل أنت متأكد من حذف هذه الرسالة؟');">حذف</a>
                </td>

              </tr>
            <?php endforeach; ?>
            <?php if (empty($messages)): ?>
              <tr>
                <td colspan="6" class="py-4 px-4 text-center">لا توجد رسائل للتواصل.</td>
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