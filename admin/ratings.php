<?php
// admin/ratings.php
require 'config.php';
 
// جلب جميع التقييمات مع معلومات المستخدم والمنتج
$stmt = $conn->prepare("
    SELECT 
        r.id, 
        u.full_name, 
        u.email, 
        p.name AS product_name, 
        r.rating, 
        r.review, 
        r.created_at
    FROM 
        ratings r
    JOIN 
        users u ON r.user_id = u.id
    JOIN 
        products p ON r.product_id = p.id
    ORDER BY 
        r.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
$ratings = $result->fetch_all(MYSQLI_ASSOC);
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

    <!-- Ratings Management Section -->
    <section class="py-12">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-8">إدارة التقييمات</h2>
        <div class="overflow-x-auto">
          <table class="w-full bg-white rounded-lg shadow">
            <thead>
              <tr class="text-left border-b">
                <th class="py-2 px-4">#</th>
                <th class="py-2 px-4">اسم المستخدم</th>
                <th class="py-2 px-4">البريد الإلكتروني</th>
                <th class="py-2 px-4">اسم المنتج</th>
                <th class="py-2 px-4">التقييم</th>
                <th class="py-2 px-4">المراجعة</th>
                <th class="py-2 px-4">تاريخ الإرسال</th>
                <th class="py-2 px-4">إجراءات</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($ratings as $rating): ?>
                <tr class="border-t">
                  <td class="py-4 px-4"><?php echo htmlspecialchars($rating['id']); ?></td>
                  <td class="py-4 px-4"><?php echo htmlspecialchars($rating['full_name']); ?></td>
                  <td class="py-4 px-4"><?php echo htmlspecialchars($rating['email']); ?></td>
                  <td class="py-4 px-4"><?php echo htmlspecialchars($rating['product_name']); ?></td>
                  <td class="py-4 px-4">
                    <?php
                    for ($i = 1; $i <= 5; $i++):
                      if ($i <= $rating['rating']):
                    ?>
                        <svg class="w-5 h-5 text-yellow-400 inline" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.172c.969 0 1.371 1.24.588 1.81l-3.37 2.447a1 1 0 00-.364 1.118l1.286 3.967c.3.921-.755 1.688-1.54 1.118L10 13.347l-3.37 2.447c-.785.57-1.838-.197-1.54-1.118l1.286-3.967a1 1 0 00-.364-1.118L2.98 9.394c-.783-.57-.38-1.81.588-1.81h4.172a1 1 0 00.95-.69l1.286-3.967z" />
                        </svg>
                      <?php
                      else:
                      ?>
                        <svg class="w-5 h-5 text-gray-300 inline" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.172c.969 0 1.371 1.24.588 1.81l-3.37 2.447a1 1 0 00-.364 1.118l1.286 3.967c.3.921-.755 1.688-1.54 1.118L10 13.347l-3.37 2.447c-.785.57-1.838-.197-1.54-1.118l1.286-3.967a1 1 0 00-.364-1.118L2.98 9.394c-.783-.57-.38-1.81.588-1.81h4.172a1 1 0 00.95-.69l1.286-3.967z" />
                        </svg>
                    <?php
                      endif;
                    endfor;
                    ?>
                  </td>
                  <td class="py-4 px-4"><?php echo nl2br(htmlspecialchars($rating['review'])); ?></td>
                  <td class="py-4 px-4"><?php echo htmlspecialchars($rating['created_at']); ?></td>
                  <td class="py-4 px-4">
                    <a href="delete_rating.php?id=<?php echo $rating['id']; ?>" class="text-red-500 hover:underline" onclick="return confirm('هل أنت متأكد من حذف هذا التقييم؟');">حذف</a>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($ratings)): ?>
                <tr>
                  <td colspan="8" class="py-4 px-4 text-center">لا توجد تقييمات لعرضها.</td>
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