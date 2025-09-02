<?php
// auth/logout.php
session_start();
$user_name = $_SESSION['user']['name'] ?? 'Usuario';

// Registrar en el log
if (isset($_SESSION['user'])) {
    require_once '../includes/config.php';
    log_action('logout', 'Cierre de sesión');
}

session_destroy();
header('Location: ../index.php?logged_out=1');
exit;