<?php
session_start();
date_default_timezone_set('America/Lima');

define('APP_NAME', 'Banco Rápido Escolar');
define('BASE_URL', 'http://localhost/banco_rapido');

// DB Config
$DB_HOST = 'localhost';
$DB_NAME = 'banco_rapido';
$DB_USER = 'root';
$DB_PASS = '';

// Autoload functions
require_once 'helpers.php';
require_once 'db.php';