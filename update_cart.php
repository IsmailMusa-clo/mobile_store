<?php
// update_cart.php
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['id']) && isset($_POST['quantity'])) {
        $product_id = intval($_POST['id']);
        $quantity = intval($_POST['quantity']);
        
        if($quantity > 0) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    }
}

header("Location: cart.php");
exit();
?>
