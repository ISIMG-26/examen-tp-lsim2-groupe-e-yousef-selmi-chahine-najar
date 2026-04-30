<?php
require_once __DIR__ . '/db.php';

// PHP 5.4 compatibility for password_* helpers.
if (!defined('PASSWORD_BCRYPT')) {
    define('PASSWORD_BCRYPT', 1);
}
if (!defined('PASSWORD_DEFAULT')) {
    define('PASSWORD_DEFAULT', PASSWORD_BCRYPT);
}
if (!function_exists('password_hash')) {
    function password_hash($password, $algo, $options = array()) {
        if ($algo !== PASSWORD_BCRYPT) {
            trigger_error('Only PASSWORD_BCRYPT is supported in this environment.', E_USER_WARNING);
            return false;
        }

        $cost = isset($options['cost']) ? (int)$options['cost'] : 10;
        $cost = max(4, min(31, $cost));

        $saltCharset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789./';
        $saltBody = '';

        if (function_exists('openssl_random_pseudo_bytes')) {
            $saltBody = substr(strtr(base64_encode(openssl_random_pseudo_bytes(16)), '+', '.'), 0, 22);
        } else {
            for ($i = 0; $i < 22; $i++) {
                $saltBody .= $saltCharset[mt_rand(0, strlen($saltCharset) - 1)];
            }
        }

        $salt = '$2y$' . str_pad((string)$cost, 2, '0', STR_PAD_LEFT) . '$' . $saltBody;

        return crypt($password, $salt);
    }
}
if (!function_exists('password_verify')) {
    function password_verify($password, $hash) {
        return crypt($password, $hash) === $hash;
    }
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Redirect helper
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Display flash messages
function showFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $type = isset($_SESSION['flash_type']) ? $_SESSION['flash_type'] : 'info';
        $safeType = preg_replace('/[^a-zA-Z0-9_-]/', '', $type);
        echo '<div class="alert alert-' . $safeType . '">' . sanitize($_SESSION['flash_message']) . '</div>';
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }
}

// Set flash message
function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

// Format price
function formatPrice($price) {
    return number_format($price, 3) . ' DT';
}

// Get cart count
function getCartCount() {
    if (!isset($_SESSION['cart'])) return 0;

    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count += isset($item['quantity']) ? (int)$item['quantity'] : 0;
    }

    return $count;
}

// Upload image helper
function uploadImage($file, $directory = '../images/products/') {
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $filename = $file['name'];
    $filetmp = $file['tmp_name'];
    $fileext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!in_array($fileext, $allowed)) {
        return false;
    }

    $newname = uniqid() . '.' . $fileext;
    $destination = $directory . $newname;

    if (move_uploaded_file($filetmp, $destination)) {
        return $newname;
    }
    return false;
}
?>
