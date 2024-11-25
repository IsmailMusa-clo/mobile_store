<?php
// add_to_cart.php
session_start();
require 'config.php';

if(isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    // استرجاع المنتج من قاعدة البيانات
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if($product) {
        // إضافة المنتج إلى السلة
        if(isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$product_id] = [
                "id" => $product['id'],
                "name" => $product['name'],
                "price" => $product['price'],
                "image_url" => $product['image_url'],
                "quantity" => 1
            ];
        }
        header("Location: cart.php");
        exit();
    } else {
        echo "المنتج غير موجود.";
    }
} else {
    echo "معرف المنتج غير موجود.";
}

$conn->close();
?>
