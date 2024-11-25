<?php
// index.php
require 'config.php';

// استرجاع المنتجات من قاعدة البيانات

$stmt = $conn->prepare("
    SELECT 
        p.id, 
        p.name, 
        p.description, 
        p.price, 
        p.image_url, 
        COALESCE(AVG(r.rating), 0) AS average_rating, 
        COUNT(r.id) AS rating_count
    FROM 
        products p
    LEFT JOIN 
        ratings r ON p.id = r.product_id
    GROUP BY 
        p.id
    ORDER BY 
        p.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
 <!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <title>متجر الجوال العصري</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- إضافة أيقونات مثل Font Awesome أو Heroicons -->
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <!-- إضافة CSS لـ Swiper.js -->
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
<style>
    @keyframes float {
        0% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-20px);
        }

        100% {
            transform: translateY(0);
        }
    }
    .float-animation {
        animation: float 2s infinite; 
    }
</style>
</head>

<body class="bg-gray-100 text-gray-800">

  <!-- الشريط العلوي -->
  <?php include 'navbar.php'; ?>

  <div
            class="content mx-auto px-6 lg:px-24 py-5 lg:py-10 md:py-5 flex-col-reverse md:flex-row flex justify-between">
            <div class="left">
                 <h1
                    class="text-black text-center md:text-start tracking-normal md:tracking-wider mt-5 font-Poppins-bold text-2xl md:text-3xl lg:text-4xl leading-normal md:leading-[2rem] lg:leading-[3rem]">
                    متجر 
                    <span class="block mb-3 text-3xl lg:text-5xl xl:text-6xl md:text-4xl leading-[4rem]">
                        موبيليا
                    </span>
                    لأحدث الأجهزة الذكية
                </h1>
                <p class="mt-7 text-center md:text-start text-black text-xl text-wrap w-full md:w-80 lg:w-96">
                    تجد كل ما تحتاج إليه
                </p>
                <div class="mt-10 justify-center flex align-middle lg:gap-6 md:gap-4 gap-2">
                    <a href="product.php"
                        class="btn rounded-full border border-black hover:text-black hover:bg-transparent transition ease-in-out delay-100 text-white bg-black lg:py-4 md:py-3 py-1 lg:px-24 md:px-16 px-10 lg:text-lg md:text-base text-sm">
                        اكتشف منتجاتنا
                    </a>
                    <button type="button" id="openModalButton"><img src="/images/icons8-circled-play-48.png"
                            class="border border-transparent rounded-full transition ease-in-out delay-100 hover:border-black md:w-16 w-10"
                            alt="" /></button>
                </div>
            </div>
            <div class="right">
                <img src="assetes/images/mobile.png" class="end-2/4 float-animation" />
            </div>
        </div>

  <!-- أقسام المنتجات المميزة -->
  <section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
      <h2 class="text-3xl font-bold text-center mb-8">منتجات مميزة</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        <?php if (!empty($products)): ?>
          <?php foreach ($products as $product): ?>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
              <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-48 object-cover">
              <div class="p-4">
                <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                <div class="flex items-center mt-2">
                  <?php
                  $average_rating = round($product['average_rating'], 1);
                  for ($i = 1; $i <= 5; $i++):
                    if ($i <= floor($average_rating)):
                  ?>
                      <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.172c.969 0 1.371 1.24.588 1.81l-3.37 2.447a1 1 0 00-.364 1.118l1.286 3.967c.3.921-.755 1.688-1.54 1.118L10 13.347l-3.37 2.447c-.785.57-1.838-.197-1.54-1.118l1.286-3.967a1 1 0 00-.364-1.118L2.98 9.394c-.783-.57-.38-1.81.588-1.81h4.172a1 1 0 00.95-.69l1.286-3.967z" />
                      </svg>
                    <?php
                    elseif ($i - 0.5 <= $average_rating):
                    ?>
                      <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.172c.969 0 1.371 1.24.588 1.81l-3.37 2.447a1 1 0 00-.364 1.118l1.286 3.967c.3.921-.755 1.688-1.54 1.118L10 13.347l-3.37 2.447c-.785.57-1.838-.197-1.54-1.118l1.286-3.967a1 1 0 00-.364-1.118L2.98 9.394c-.783-.57-.38-1.81.588-1.81h4.172a1 1 0 00.95-.69l1.286-3.967z" />
                      </svg>
                    <?php
                    else:
                    ?>
                      <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.172c.969 0 1.371 1.24.588 1.81l-3.37 2.447a1 1 0 00-.364 1.118l1.286 3.967c.3.921-.755 1.688-1.54 1.118L10 13.347l-3.37 2.447c-.785.57-1.838-.197-1.54-1.118l1.286-3.967a1 1 0 00-.364-1.118L2.98 9.394c-.783-.57-.38-1.81.588-1.81h4.172a1 1 0 00.95-.69l1.286-3.967z" />
                      </svg>
                  <?php endif;
                  endfor; ?>
                  <span class="text-gray-600 ml-2">(<?php echo $product['rating_count']; ?>)</span>
                </div>
                <p class="text-gray-600 mb-4">$<?php echo number_format($product['price'], 2); ?></p>
                <a href="product_details.php?id=<?php echo $product['id']; ?>" class="bg-blue-500 text-white px-4 py-2 rounded-full inline-block">تفاصيل المنتج</a>
                <a href="add_to_cart.php?id=<?php echo $product['id']; ?>" class="bg-green-500 text-white px-4 py-2 rounded-full inline-block mt-2">أضف إلى السلة</a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-center col-span-4">لا توجد منتجات متاحة حالياً.</p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Contact Form Section -->
  <section class="py-12 bg-gray-100">
    <div class="container mx-auto px-4">
      <h2 class="text-3xl font-bold text-right mb-8">تواصل معنا</h2>
      <div class="flex justify-between">
        <div class="bg-white p-8 rounded-lg shadow-lg w-2/5">
          <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="bg-green-100 text-green-700 p-4 rounded mb-6">
              تم إرسال رسالتك بنجاح. سنقوم بالرد عليك في أقرب وقت ممكن.
            </div>
          <?php elseif (isset($_GET['error'])): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
              حدث خطأ أثناء إرسال رسالتك. الرجاء المحاولة لاحقاً.
            </div>
          <?php endif; ?>
          <form method="POST" action="contact_process.php" enctype="multipart/form-data" class="space-y-6">
            <div>
              <label class="block text-gray-700">الاسم الكامل</label>
              <input type="text" name="name" class="w-full px-4 py-2 border rounded" placeholder="أدخل اسمك الكامل" required>
            </div>
            <div>
              <label class="block text-gray-700">البريد الإلكتروني</label>
              <input type="email" name="email" class="w-full px-4 py-2 border rounded" placeholder="أدخل بريدك الإلكتروني" required>
            </div>
            <div>
              <label class="block text-gray-700">الرسالة</label>
              <textarea name="message" class="w-full px-4 py-2 border rounded" placeholder="أدخل رسالتك" rows="5" required></textarea>
            </div>
            <!-- <div>
              <label class="block text-gray-700">المرفق</label>
              <input type="file" name="attachment" class="w-full px-4 py-2 border rounded">
            </div> -->
  
            <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">إرسال</button>
          </form>
        </div>
        <div class="w-2/5">
          <img src="assetes/images/contact.jfif" class="w-full" alt="">
        </div>
      </div>
    </div>
  </section>


  <!-- دعوة للاشتراك في النشرة الإخبارية -->
  <section class="py-12 bg-blue-600 text-white">
    <div class="container mx-auto px-4 text-center">
      <h2 class="text-3xl font-bold mb-4">اشترك في نشرتنا الإخبارية</h2>
      <p class="mb-6">احصل على أحدث العروض والأخبار مباشرة في بريدك الإلكتروني.</p>
      <?php if (isset($_SESSION['subscribe_message'])): ?>
        <div class="mb-4">
          <p class="text-green-300"><?php echo $_SESSION['subscribe_message']; ?></p>
        </div>
        <?php unset($_SESSION['subscribe_message']); ?>
      <?php endif; ?>
      <form action="subscribe.php" method="POST" class="flex flex-col sm:flex-row justify-center items-center">
        <button type="submit" class="mt-4 sm:mt-0 sm:ml-2 bg-white text-blue-600 px-6 py-2 rounded-md font-semibold">اشتراك</button>
        <input type="email" name="email" required placeholder="أدخل بريدك الإلكتروني" class="w-full sm:w-1/3 px-4 py-2 rounded-md text-gray-800">
      </form>
    </div>
  </section>

  <!-- تذييل الموقع -->
  <?php include 'footer.php'; ?>

  <!-- Scripts لإضافة Swiper.js للسلايدر -->
  <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
  <script>
    // تهيئة Swiper
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
  </script>
</body>

</html>