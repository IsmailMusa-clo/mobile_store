<?php
// admin/navbar.php
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// استدعاء اسم المستخدم من الجلسة أو قاعدة البيانات
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'المستخدم';
?>
<nav class="bg-white shadow-lg" x-data="{ open: false }">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
      
        <!-- شريط البحث -->
        <div class="flex-1 mx-3">
            <input type="text" placeholder="ابحث..." class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <!-- اسم المستخدم مع القائمة المنسدلة -->
        <div class="relative" x-data="{ dropdownOpen: false }">
            <button @click="dropdownOpen = !dropdownOpen" class="flex items-center text-gray-700 hover:text-blue-500 focus:outline-none">
                <span class="mr-2"><?php echo htmlspecialchars($username); ?></span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
            </button>
            <!-- القائمة المنسدلة -->
            <div x-show="dropdownOpen" @click.away="dropdownOpen = false" class="absolute right-[-30px] mt-2 w-48 bg-white border rounded-md shadow-lg z-20">
                <a href="logout.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">تسجيل الخروج</a>
            </div>
        </div>
    </div>
</nav>
