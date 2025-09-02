<?php
function ejecutivo_dashboard() {
    $pdo = Database::getInstance();
    $message = '';

    // Procesar formulario
    if (($_POST['do'] ?? '') === 'open_account') {
        $email = strtolower(trim($_POST['email'] ?? ''));

        if (!$email) {
            $message = '<div class="bg-yellow-100 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Email es requerido.</strong>
            </div>';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user) {
                    $acc_num = random_account_number();
                    $insert = $pdo->prepare("INSERT INTO cuentas (usuario_id, numero_cuenta) VALUES (?, ?)");
                    $insert->execute([$user['id'], $acc_num]);

                    log_action('open_account', "Cuenta $acc_num creada para $email");
                    $message = '<div class="bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <strong>✅ Cuenta creada: <span class="font-semibold">' . h($acc_num) . '</span></strong>
                    </div>';
                } else {
                    $message = '<div class="bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
                        <i class="fas fa-times-circle"></i>
                        <strong>❌ No se encontró un usuario con ese email.</strong>
                    </div>';
                }
            } catch (Exception $e) {
                $message = '<div class="bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
                    <i class="fas fa-bug"></i>
                    <strong>Error: ' . h($e->getMessage()) . '</strong>
                </div>';
            }
        }
    }
    ?>
    <div class="container mx-auto px-6 py-8 max-w-3xl">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h4 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-user-tie"></i>
                👨‍💼 Ejecutivo - Apertura de Cuentas
            </h4>

            <!-- Botón de logout -->
            <a href="auth/logout.php"
               class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm flex items-center gap-1"
               onclick="return confirm('¿Seguro que deseas cerrar sesión?')">
                <i class="fas fa-sign-out-alt"></i>
                Salir
            </a>
        </div>

        <!-- Mensaje -->
        <?php if ($message): ?>
            <div class="mb-6">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- Formulario -->
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
            <form method="post" class="space-y-5">
                <input type="hidden" name="do" value="open_account">

                <h6 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-blue-600"></i>
                    Crear cuenta a cliente existente
                </h6>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">📧 Email del cliente</label>
                    <input
                        type="email"
                        name="email"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-600"
                        placeholder="cliente@banco.com"
                        value="<?= h($_POST['email'] ?? '') ?>"
                        required
                    >
                </div>

                <button
                    type="submit"
                    class="bg-gray-800 hover:bg-gray-900 text-white font-medium py-2.5 px-6 rounded-lg transition shadow-sm hover:shadow"
                >
                    Crear Cuenta
                </button>
            </form>
        </div>

        <!-- Footer -->
        <footer class="mt-12 py-6 text-center text-gray-500 text-sm border-t border-gray-200">
            <p>&copy; <?= date('Y') ?> <?= h(APP_NAME) ?>. Sistema educativo para secundaria.</p>
        </footer>
    </div>
    <?php
}