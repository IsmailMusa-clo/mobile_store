<?php
// product.php
require 'config.php';

// معالجة الفلاتر
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? intval($_GET['category']) : '';
$rating = isset($_GET['rating']) ? intval($_GET['rating']) : '';
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : PHP_INT_MAX;

// جلب الفئات
$categories = $conn->query("SELECT id, name FROM categories");

// إعداد استعلام الفلترة
$query = "
    SELECT p.*, COALESCE(AVG(r.rating), 0) AS average_rating
    FROM products p
    LEFT JOIN ratings r ON p.id = r.product_id
    WHERE 1=1
";
$params = [];
$types = "";

// تطبيق الفلترة على الاسم
if (!empty($search)) {
    $query .= " AND p.name LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

// تطبيق الفلترة على الفئة
if (!empty($category)) {
    $query .= " AND p.category_id = ?";
    $params[] = $category;
    $types .= "i";
}

// تطبيق الفلترة على التقييم
if (!empty($rating)) {
    $query .= " HAVING average_rating >= ?";
    $params[] = $rating;
    $types .= "i";
}

// تطبيق الفلترة على السعر
if ($min_price > 0) {
    $query .= " AND p.price >= ?";
    $params[] = $min_price;
    $types .= "d";
}

if ($max_price < PHP_INT_MAX) {
    $query .= " AND p.price <= ?";
    $params[] = $max_price;
    $types .= "d";
}

$query .= " GROUP BY p.id"; // جمع البيانات حسب المنتج

// تحضير الاستعلام وتنفيذه
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>المنتجات - متجر الجوال</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <!-- الشريط العلوي -->
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto px-4 gap-4 py-8 flex">
        <!-- قسم الفلترة -->
        <aside class="w-full md:w-1/4 bg-white p-4 rounded-lg shadow-md">
            <h2 class="text-lg font-bold mb-4">فلترة المنتجات</h2>
            <form method="GET" action="product.php" class="space-y-4">
                <!-- البحث -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">بحث</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>"
                        class="mt-1 block w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-300">
                </div>
                <!-- الفئة -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">الفئة</label>
                    <select id="category" name="category"
                        class="mt-1 block w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-300">
                        <option value="">كل الفئات</option>
                        <?php while ($row = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo $category == $row['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['name']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <!-- التقييم -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">التقييم (أعلى من)</label>
                    <div class="flex items-center space-x-2 rtl:space-x-reverse">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <label class="flex items-center space-x-1 rtl:space-x-reverse cursor-pointer">
                            <input type="radio" name="rating" value="<?php echo $i; ?>"
                                <?php echo $rating == $i ? 'checked' : ''; ?> class="hidden peer">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-gray-400 peer-checked:text-yellow-500 cursor-pointer"
                                fill="<?php echo $rating >= $i ? 'currentColor' : 'none'; ?>" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.964a1 1 0 00.95.69h4.173c.969 0 1.371 1.24.588 1.81l-3.375 2.455a1 1 0 00-.364 1.118l1.286 3.964c.3.921-.755 1.688-1.54 1.118l-3.375-2.455a1 1 0 00-1.176 0l-3.375 2.455c-.785.57-1.84-.197-1.54-1.118l1.286-3.964a1 1 0 00-.364-1.118L2.24 9.39c-.783-.57-.381-1.81.588-1.81h4.173a1 1 0 00.95-.69l1.286-3.964z" />
                            </svg>
                        </label>
                        <?php endfor; ?>
                    </div>
                </div>
                <!-- السعر -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">السعر</label>
                    <div class="flex items-center justify-between text-sm text-gray-500 mb-2">
                        <span>من: <span id="min-price-display"><?php echo $min_price ?: 0; ?></span></span>
                        <span>إلى: <span id="max-price-display"><?php echo $max_price ?: 5000; ?></span></span>
                    </div>
                    <div class="relative flex items-center space-x-4">
                        <input type="range" id="min-price" name="min_price" min="0" max="5000" step="10"
                            value="<?php echo htmlspecialchars($min_price ?: 0); ?>"
                            class="z-10 w-full appearance-none h-1 bg-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-300">
                        <input type="range" id="max-price" name="max_price" min="0" max="5000" step="10"
                            value="<?php echo htmlspecialchars($max_price ?: 5000); ?>"
                            class="z-10 w-full appearance-none h-1 bg-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-300">
                    </div>
                </div>
                <button type="submit"
                    class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-600">فلترة</button>
            </form>
        </aside>

        <!-- قائمة المنتجات -->
        <section class="w-full md:w-3/4 md:pl-6">
            <h2 class="text-2xl font-bold mb-4">المنتجات</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($product = $result->fetch_assoc()): ?>
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <img class='' src="<?php echo htmlspecialchars($product['image_url']); ?>"
                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                        class="w-full h-40 object-cover rounded-lg mb-4">
                    <h3 class="text-lg font-bold"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <div class="flex justify-between">
                      <p class="text-gray-700">$<?php echo number_format($product['price'], 2); ?></p>
                      <p class="text-yellow-500">التقييم: <?php echo number_format($product['average_rating'], 1); ?> نجمة</p>
                    </div>
                    <a href="product_details.php?id=<?php echo $product['id']; ?>"
                        class="block bg-blue-500 text-white text-center mt-4 px-4 py-2 rounded-lg">عرض المنتج</a>
                </div>
                <?php endwhile; ?>
            </div>
        </section>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>
