<?php
require_once 'includes/functions.php';
session_destroy();
setFlashMessage('You have been logged out', 'info');
redirect('index.php');
?>
