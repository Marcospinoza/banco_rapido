<?php
require_once 'includes/config.php';
require_login();

// Cargar funcionalidad por rol
$role_file = "includes/" . role() . ".php";
if (file_exists($role_file)) {
    require_once $role_file;
} else {
    $role_file = "includes/cliente.php";
    require_once $role_file;
}

include 'includes/header.php';
call_user_func(role() . '_dashboard');
include 'includes/footer.php';