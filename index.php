<?php
// index.php - Página de inicio (login)
require_once 'includes/config.php';

// Si ya está logueado, redirige al dashboard
if (is_logged()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$logged_out = $_GET['logged_out'] ?? false;

// Procesar login
if ($_POST) {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    if (!validate_csrf($csrf)) {
        $error = 'Token CSRF inválido.';
    } elseif ($email && $password) {
        $pdo = Database::getInstance();
        // ✅ Tabla y campos en español
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['contraseña_hash'])) {
            // Iniciar sesión
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['nombre'], // ✅ nombre en español
                'email' => $user['email'],
                'rol' => $user['rol']     // ✅ rol en español
            ];
            log_action('login', 'Inicio de sesión');
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Email o contraseña incorrectos.';
        }
    } else {
        $error = 'Completa todos los campos.';
    }
}

// Incluir header (con Tailwind)
include 'includes/header.php';
?>

<!-- Contenedor principal -->
<div class="min-h-screen bg-gray-50 flex flex-col justify-center p-4">
    <!-- Formulario de login -->
    <div class="w-full max-w-md mx-auto bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <!-- Header -->
        <div class="bg-blue-600 text-white px-6 py-4 text-center">
            <h4 class="text-xl font-semibold flex items-center justify-center gap-2">
                <i class="fas fa-university"></i>
                Iniciar Sesión
            </h4>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-6">
            <!-- Mensaje de error -->
            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <?= h($error) ?>
                </div>
            <?php endif; ?>

            <!-- Mensaje de sesión cerrada -->
            <?php if ($logged_out): ?>
                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg text-sm">
                    Sesión cerrada. Inicia sesión nuevamente.
                </div>
            <?php endif; ?>

            <!-- Formulario -->
            <form method="post" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input
                        type="email"
                        name="email"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-600"
                        placeholder="tu@correo.com"
                        value="<?= h($_POST['email'] ?? '') ?>"
                        required
                    >
                </div>

                <!-- Contraseña -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
                    <input
                        type="password"
                        name="password"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-600"
                        placeholder="••••••••"
                        required
                    >
                </div>

                <!-- Botón -->
                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition duration-200 shadow-sm hover:shadow"
                >
                    Entrar
                </button>
            </form>

            <!-- Texto de acceso -->
            <div class="text-center text-sm text-gray-500">
                Acceso: solo usuarios registrados por el gerente.
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="mt-0.1 py-0.1 text-center text-gray-500 text-sm border-t border-gray-200">
    <p>&copy; <?= date('Y') ?> <?= h(APP_NAME) ?>. Sistema educativo para secundaria.</p>
</footer>

<?php include 'includes/footer.php'; ?>