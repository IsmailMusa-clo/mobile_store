<?php
// search.php
session_start();
require 'config.php';

// الحصول على استعلام البحث من النموذج
$search_query = '';
if(isset($_GET['q'])) {
    $search_query = trim($_GET['q']);
}

// استرجاع المنتجات المطابقة من قاعدة البيانات
$products = [];
if($search_query !== '') {
    // استخدام prepared statements لمنع هجمات SQL Injection
    $stmt = $conn->prepare("SELECT id, name, price, image_url FROM products WHERE name LIKE ? OR description LIKE ? LIMIT 20");
    $like_query = '%' . $search_query . '%';
    $stmt->bind_param("ss", $like_query, $like_query);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>نتائج البحث - متجر الجوال</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <!-- Swiper.js for سلايدر النتائج (إذا كنت ترغب في استخدامه) -->
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
</head>
<body class="bg-gray-100">

  <!-- الشريط العلوي -->
  <?php include 'navbar.php'; ?>

  <!-- Search Results Section -->
  <section class="py-12">
    <div class="container mx-auto px-4">
      <?php if($search_query !== ''): ?>
        <h2 class="text-3xl font-bold mb-8">نتائج البحث عن "<?php echo htmlspecialchars($search_query); ?>"</h2>
      <?php else: ?>
        <h2 class="text-3xl font-bold mb-8">الرجاء إدخال كلمة بحث.</h2>
      <?php endif; ?>

      <?php if(!empty($products)): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
          <?php foreach($products as $product): ?>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
              <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-48 object-cover">
              <div class="p-4">
                <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                <p class="text-gray-600 mb-4">$<?php echo number_format($product['price'], 2); ?></p>
                <a href="product.php?id=<?php echo $product['id']; ?>" class="bg-blue-500 text-white px-4 py-2 rounded-full inline-block">تفاصيل المنتج</a>
                <a href="add_to_cart.php?id=<?php echo $product['id']; ?>" class="bg-green-500 text-white px-4 py-2 rounded-full inline-block mt-2">أضف إلى السلة</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php elseif($search_query !== ''): ?>
        <p class="text-center">لا توجد نتائج مطابقة لبحثك.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- تذييل الموقع -->
  <?php include 'footer.php'; ?>

  <!-- Scripts لإضافة Swiper.js للسلايدر (إذا كنت ترغب في استخدامه) -->
  <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
  <script>
    // تهيئة Swiper (إذا كنت تستخدمه في الصفحة)
    /*
    const swiper = new Swiper('.swiper-container', {
      loop: true,
      autoplay: {
        delay: 5000,
        disableOnInteraction: false,
      },
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
    });
    */
  </script>
</body>
</html>
