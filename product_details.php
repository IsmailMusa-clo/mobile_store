<?php
// product_details.php
require 'config.php';

// التحقق من وجود معرف المنتج في الرابط
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("معرف المنتج غير موجود أو غير صالح.");
}

$product_id = intval($_GET['id']);

// جلب بيانات المنتج
$query = "SELECT p.*, COALESCE(AVG(r.rating), 0) AS average_rating, COUNT(r.id) AS total_reviews 
          FROM products p
          LEFT JOIN ratings r ON p.id = r.product_id
          WHERE p.id = ?
          GROUP BY p.id";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("المنتج غير موجود.");
}

$product = $result->fetch_assoc();

// جلب التقييمات
$reviews_query = "SELECT r.*, u.full_name AS user_name 
                  FROM ratings r
                  JOIN users u ON r.user_id = u.id
                  WHERE r.product_id = ?";
$reviews_stmt = $conn->prepare($reviews_query);
$reviews_stmt->bind_param("i", $product_id);
$reviews_stmt->execute();
$reviews_result = $reviews_stmt->get_result();

$conn->close();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['name']); ?> - تفاصيل المنتج</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <!-- الشريط العلوي -->
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <!-- تفاصيل المنتج -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex flex-col md:flex-row">
                <!-- صورة المنتج -->
                <div class="md:w-1/2">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="w-full h-auto object-cover rounded-lg shadow">
                </div>
                <!-- تفاصيل المنتج -->
                <div class="md:w-1/2 md:pl-8 mt-6 md:mt-0">
                    <h1 class="text-3xl font-bold mb-4"><?php echo htmlspecialchars($product['name']); ?></h1>
                    <p class="text-xl text-gray-700 mb-4">$<?php echo number_format($product['price'], 2); ?></p>
                    <p class="text-gray-600 mb-6"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    <p class="text-yellow-500 mb-4">
                        التقييم: <?php echo number_format($product['average_rating'], 1); ?> نجمة 
                        (<?php echo $product['total_reviews']; ?> تقييم)
                    </p>
                    <p class="text-gray-500 mb-4">المخزون: <?php echo $product['stock'] > 0 ? $product['stock'] . " متوفر" : "غير متوفر"; ?></p>
                    <a href="add_to_cart.php?id=<?php echo $product['id']; ?>" 
                       class="bg-blue-500 text-white px-6 py-3 rounded-full font-semibold hover:bg-blue-600">
                        أضف إلى السلة
                    </a>
                </div>
            </div>
        </div>

        <!-- التقييمات -->
        <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-4">التقييمات</h2>
            <?php if ($reviews_result->num_rows > 0): ?>
                <div class="space-y-4">
                    <?php while ($review = $reviews_result->fetch_assoc()): ?>
                        <div class="border-b pb-4">
                            <h3 class="text-lg font-semibold text-gray-700"><?php echo htmlspecialchars($review['user_name']); ?></h3>
                            <p class="text-yellow-500">التقييم: <?php echo str_repeat('⭐', $review['rating']); ?></p>
                            <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($review['review'])); ?></p>
                            <p class="text-gray-400 text-sm"><?php echo htmlspecialchars($review['created_at']); ?></p>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500">لا توجد تقييمات لهذا المنتج بعد.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- تذييل الموقع -->
    <?php include 'footer.php'; ?>
</body>

</html>
