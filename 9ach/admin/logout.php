<?php
require_once '../includes/functions.php';
unset($_SESSION['admin_id']);
unset($_SESSION['admin_name']);
unset($_SESSION['admin_email']);
setFlashMessage('Admin logged out', 'info');
redirect('login.php');
?>
