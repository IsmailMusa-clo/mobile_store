<?php
// checkout_success.php
 require 'config.php';

// التأكد من تسجيل الدخول

// جلب المنتجات التي تم شراؤها في الطلب الأخير
$user_id = $_SESSION['user_id'];

// افترض أن لديك جدول 'orders' وجدول 'order_items'
$stmt = $conn->prepare("
    SELECT oi.product_id, p.name 
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    JOIN products p ON oi.product_id = p.id
    WHERE o.user_id = ? 
    ORDER BY o.created_at DESC
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$latest_order = $result->fetch_assoc();
$stmt->close();

$product_id = $latest_order['product_id'];
$product_name = $latest_order['name'];

// معالجة التقييم
$message = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = intval($_POST['rating']);
    $review = trim($_POST['review']);
    
    // التحقق من صحة الإدخال
    if($rating < 1 || $rating > 5) {
        $message = "يرجى اختيار تقييم صحيح.";
    } else {
        // التحقق مما إذا كان المستخدم قد قيم هذا المنتج مسبقاً
        $stmt = $conn->prepare("SELECT id FROM ratings WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->num_rows > 0) {
            $message = "لقد قمت بتقييم هذا المنتج مسبقاً.";
        } else {
            $stmt->close();
            // إدخال التقييم الجديد
            $stmt = $conn->prepare("INSERT INTO ratings (user_id, product_id, rating, review) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiis", $user_id, $product_id, $rating, $review);
            
            if($stmt->execute()) {
                $message = "شكراً لتقييمك!";
            } else {
                $message = "حدث خطأ أثناء حفظ تقييمك.";
            }
        }
        
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تم الدفع بنجاح - متجر الجوال</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- الشريط العلوي -->
    <?php include 'navbar.php'; 
    if(!isset($_SESSION['user_id'])) {
      header("Location: login.php");
      exit();
  }
  
    
    ?>
    
    <!-- صفحة تأكيد الدفع -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-8">تم الدفع بنجاح!</h2>
            <p class="text-center mb-6">شكراً لشراءك من متجر الجوال. نأمل أن تكون راضياً عن منتجاتنا.</p>
            
            <!-- نموذج التقييم -->
            <div class="bg-white p-8 rounded-lg shadow-lg max-w-lg mx-auto">
                <?php if($message): ?>
                    <div class="bg-<?php echo strpos($message, 'شكراً') !== false ? 'green' : 'red'; ?>-100 text-<?php echo strpos($message, 'شكراً') !== false ? 'green' : 'red'; ?>-700 p-4 rounded mb-6">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="checkout_success.php" class="space-y-6">
                    <h3 class="text-2xl font-semibold">قيم منتجك</h3>
                    <p class="text-gray-700">منتجك: <strong><?php echo htmlspecialchars($product_name); ?></strong></p>
                    
                    <div>
                        <label class="block text-gray-700">التقييم</label>
                        <div class="flex">
    <?php for($i = 1; $i <= 5; $i++): ?>
        <label class="flex items-center cursor-pointer mr-2">
            <input type="radio" name="rating" value="<?php echo $i; ?>" class="hidden peer" required>
            <svg class="w-6 h-6 text-gray-400 peer-checked:text-yellow-500 transition-colors duration-200" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.172c.969 0 1.371 1.24.588 1.81l-3.37 2.447a1 1 0 00-.364 1.118l1.286 3.967c.3.921-.755 1.688-1.54 1.118L10 13.347l-3.37 2.447c-.785.57-1.838-.197-1.54-1.118l1.286-3.967a1 1 0 00-.364-1.118L2.98 9.394c-.783-.57-.38-1.81.588-1.81h4.172a1 1 0 00.95-.69l1.286-3.967z"/>
            </svg>
        </label>
    <?php endfor; ?>
</div>

                    </div>
                    
                    <div>
                        <label class="block text-gray-700">المراجعة (اختياري)</label>
                        <textarea name="review" class="w-full px-4 py-2 border rounded" placeholder="اكتب مراجعتك هنا" rows="4"></textarea>
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">إرسال التقييم</button>
                </form>
            </div>
        </div>
    </section>
    
    <!-- تذييل الموقع -->
    <?php include 'footer.php'; ?>
</body>
</html>
