<?php
// navbar.php
?>
<nav class="bg-white shadow-lg">
  <div class="container mx-auto px-4 py-4 flex justify-between items-center">
    <!-- الشعار -->
    <a href="index.php" class="text-2xl font-bold text-blue-600 flex items-center space-x-2 rtl:space-x-reverse">
      <img class="w-8 h-8" src="assetes/images/logo.png" alt="">
      <span>متجري</span>
    </a>

    <!-- قائمة التنقل الرئيسية -->
    <ul class="hidden md:flex space-x-4 rtl:space-x-reverse ml-8">
      <li><a href="index.php" class="hover:text-blue-500">الرئيسية</a></li>
      <li>
        <a href="product.php" class="hover:text-blue-500">المنتجات</a>
      </li>
      <li><a href="#" class="hover:text-blue-500">عروضنا</a></li>
      <li><a href="#" class="hover:text-blue-500">خدماتنا</a></li>
      <li><a href="#" class="hover:text-blue-500">من نحن</a></li>
    </ul>

    <!-- أيقونات البحث والسلة والحساب -->
    <div class="flex items-center space-x-4 rtl:space-x-reverse">
      <!-- نموذج البحث -->
      <form action="search.php" method="GET" class="hidden md:flex items-center">
        <div class="flex">
          <!-- زر البحث -->
          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r-md flex items-center">
            <!-- أيقونة البحث -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16.65 16.65a7.5 7.5 0 111.06-1.06 7.5 7.5 0 01-1.06 1.06z" />
            </svg>
          </button>

          <!-- حقل الإدخال -->
          <input type="text" name="q" placeholder="بحث..."
            class="px-4 py-2 border rounded-l-md focus:outline-none focus:ring focus:ring-blue-300">
        </div>
      </form>

      <!-- السلة -->
      <a href="cart.php" class="text-gray-700 hover:text-blue-500 relative">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m5-9v9m4-9v9m5-9l2 9" />
        </svg>
         <!-- عدد العناصر في السلة -->
         <?php
        // حساب عدد العناصر في السلة
        $cart_count = 0;
        if (isset($_SESSION['cart'])) {
          foreach ($_SESSION['cart'] as $item) {
            $cart_count += $item['quantity'];
          }
        }
        ?>
        <span class="absolute top-0 right-0 bg-red-500 text-white rounded-full h-5 w-5 flex items-center justify-center text-xs">
          <?php echo $cart_count; ?>
        </span>
      </a>

      <!-- الحساب -->
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="profile.php" class="text-gray-700 hover:text-blue-500">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.487 0 4.785.695 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </a>
        <a href="logout.php" class="text-gray-700 hover:text-blue-500">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
          </svg>
        </a>
      <?php else: ?>
        <a href="login.php" class="text-gray-700 hover:text-blue-500">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.487 0 4.785.695 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </a>
      <?php endif; ?>
    </div>
  </div>
</nav>
