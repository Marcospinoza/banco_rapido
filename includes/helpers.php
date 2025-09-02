<?php

// -----------------------------
// Funciones de seguridad y sesión
// -----------------------------

/**
 * Escapa texto para evitar XSS
 */
function h($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Verifica si el usuario está logueado
 */
function is_logged() {
    return isset($_SESSION['user']);
}

/**
 * Devuelve los datos del usuario actual
 */
function user() {
    return $_SESSION['user'] ?? null;
}

/**
 * Devuelve el ID del usuario actual
 */
function uid() {
    return user()['id'] ?? null;
}

/**
 * Devuelve el rol del usuario actual
 */
function role() {
    return user()['rol'] ?? 'cliente'; // ✅ 'rol' en español
}

// -----------------------------
// Gestión de usuarios
// -----------------------------

/**
 * Lista todos los usuarios (para gerente/auditor)
 */
function listar_usuarios() {
    $pdo = Database::getInstance();
    // ✅ Tabla y campo en español
    $stmt = $pdo->query("SELECT id, nombre, rol, creado_en FROM usuarios ORDER BY creado_en DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// -----------------------------
// Control de acceso
// -----------------------------

/**
 * Redirige si no está logueado
 */
function require_login() {
    if (!is_logged()) {
        header('Location: index.php');
        exit;
    }
}

/**
 * Verifica si el usuario tiene un rol específico
 */
function has_role($r) {
    return role() === $r;
}

/**
 * Verifica si el usuario tiene alguno de los roles dados
 */
function any_role($roles) {
    return in_array(role(), $roles, true);
}

// -----------------------------
// Generación de cuentas
// -----------------------------

/**
 * Genera un número de cuenta único
 */
function random_account_number() {
    return 'BR' . date('y') . substr(str_pad(mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT), 0, 7);
}

// -----------------------------
// Auditoría (bitácora)
// -----------------------------

/**
 * Registra una acción en la bitácora
 */
function log_action($accion, $detalles = '') {
    if (!is_logged()) return;
    $pdo = Database::getInstance();
    // ✅ Tabla y campos en español
    $stmt = $pdo->prepare("INSERT INTO bitacora (usuario_id, accion, detalles, direccion_ip) VALUES (?, ?, ?, ?)");
    $stmt->execute([uid(), $accion, $detalles, $_SERVER['REMOTE_ADDR']]);
}

// -----------------------------
// Protección CSRF
// -----------------------------

/**
 * Genera un token CSRF
 */
function generate_csrf() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valida el token CSRF
 */
function validate_csrf($token) {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token ?? '');
}