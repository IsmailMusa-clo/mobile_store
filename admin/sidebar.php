
<div x-data="{ openSidebar: false }" :class="{'block': openSidebar, 'hidden': !openSidebar}" class="fixed inset-0 flex z-40 md:hidden">
    <div @click="openSidebar =false" class="fixed inset-0 bg-gray-600 opacity-75"></div>
    <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
        <div class="absolute top-0 right-0 -mr-12 pt-2">
            <button @click="openSidebar = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:bg-gray-600">
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="mt-5 flex-1 h-0 overflow-y-auto">
            <nav class="px-2 space-y-1">
                <a href="dashboard.php" class="block px-4 py-2 mt-2 text-sm font-semibold text-gray-900 bg-gray-200 rounded-lg hover:bg-gray-300">الداشبورد</a>
                <a href="users.php" class="block px-4 py-2 mt-2 text-sm font-semibold text-gray-900 rounded-lg hover:bg-gray-200">المستخدمين</a>
                <a href="categories.php" class="block px-4 py-2 mt-2 text-sm font-semibold text-gray-900 rounded-lg hover:bg-gray-200">الأصناف</a>
                <a href="products.php" class="block px-4 py-2 mt-2 text-sm font-semibold text-gray-900 rounded-lg hover:bg-gray-200">المنتجات</a>
                <a href="subscriptions.php" class="block px-4 py-2 mt-2 text-sm font-semibold text-gray-900 rounded-lg hover:bg-gray-200">الاشتراكات</a>
                <a href="orders.php" class="block px-4 py-2 mt-2 text-sm font-semibold text-gray-900 rounded-lg hover:bg-gray-200">الطلبات</a>
                <a href="ratings.php" class="block px-4 py-2 mt-2 text-sm font-semibold text-gray-900 rounded-lg hover:bg-gray-200">التقييمات</a>
                <a href="contact_messages.php" class="block px-4 py-2 mt-2 text-sm font-semibold text-gray-900 rounded-lg hover:bg-gray-200">رسائل التواصل</a>
            </nav>
        </div>
    </div>
    <div class="flex-shrink-0 w-14"></div>
</div>

<!-- Sidebar الدائم للشاشات الكبيرة -->
<div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0">
    <!-- Sidebar content -->
    <div class="flex flex-col flex-grow border-r border-gray-200 bg-white overflow-y-auto">
        <div class="flex items-center justify-center h-16 bg-gray-100">
            <span class="text-xl font-bold text-blue-600">لوحة التحكم</span>
        </div>
        <nav class="flex-1 px-2 py-4 space-y-1">
            <a href="dashboard.php" class="block px-4 py-2 text-sm font-semibold text-gray-900 bg-gray-200 rounded-lg">الداشبورد</a>
            <a href="users.php" class="block px-4 py-2 mt-2 text-sm font-semibold text-gray-900 rounded-lg hover:bg-gray-100">المستخدمين</a>
            <a href="categories.php" class="block px-4 py-2 mt-2 text-sm font-semibold text-gray-900 rounded-lg hover:bg-gray-100">الأصناف</a>
            <a href="products.php" class="block px-4 py-2 mt-2 text-sm font-semibold text-gray-900 rounded-lg hover:bg-gray-100">المنتجات</a>
            <a href="subscriptions.php" class="block px-4 py-2 mt-2 text-sm font-semibold text-gray-900 rounded-lg hover:bg-gray-100">الاشتراكات</a>
            <a href="orders.php" class="block px-4 py-2 mt-2 text-sm font-semibold text-gray-900 rounded-lg hover:bg-gray-100">الطلبات</a>
            <a href="ratings.php" class="block px-4 py-2 mt-2 text-sm font-semibold text-gray-900 rounded-lg hover:bg-gray-100">التقييمات</a>
            <a href="contact_messages.php" class="block px-4 py-2 mt-2 text-sm font-semibold text-gray-900 rounded-lg hover:bg-gray-100">رسائل التواصل</a>
        </nav>
    </div>
</div>
