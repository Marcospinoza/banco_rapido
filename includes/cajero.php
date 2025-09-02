<?php
function cajero_dashboard() {
    $pdo = Database::getInstance();
    $message = '';

    // Procesar depósito
    if (($_POST['do'] ?? '') === 'deposit') {
        $acc_num = trim($_POST['account_number'] ?? '');
        $amount = (float)($_POST['amount'] ?? 0);

        if (!$acc_num || $amount <= 0) {
            $message = '<div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg text-sm">Por favor, ingresa una cuenta válida y un monto mayor a 0.</div>';
        } else {
            try {
                $pdo->beginTransaction();

                $stmt = $pdo->prepare("SELECT id FROM cuentas WHERE numero_cuenta = ?");
                $stmt->execute([$acc_num]);
                $account = $stmt->fetch();

                if (!$account) {
                    throw new Exception("Cuenta no existe.");
                }

                $stmt = $pdo->prepare("UPDATE cuentas SET saldo = saldo + ? WHERE numero_cuenta = ?");
                $stmt->execute([$amount, $acc_num]);

                $stmt = $pdo->prepare("INSERT INTO transacciones (cuenta_id, tipo, monto, creado_por, descripcion) VALUES (?, 'deposito', ?, ?, 'Depósito en ventanilla')");
                $stmt->execute([$account['id'], $amount, uid()]);

                $pdo->commit();
                $message = "<div class='bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm'>✅ Depósito de S/ " . number_format($amount, 2) . " realizado en la cuenta <strong>$acc_num</strong></div>";
                log_action('deposit', "Depósito de S/ $amount en $acc_num");

            } catch (Exception $e) {
                $pdo->rollback();
                $message = '<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">❌ Error: ' . h($e->getMessage()) . '</div>';
            }
        }
    }

    // Procesar retiro
    if (($_POST['do'] ?? '') === 'withdraw') {
        $acc_num = trim($_POST['account_number'] ?? '');
        $amount = (float)($_POST['amount'] ?? 0);

        if (!$acc_num || $amount <= 0) {
            $message = '<div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg text-sm">Por favor, ingresa una cuenta válida y un monto mayor a 0.</div>';
        } else {
            try {
                $pdo->beginTransaction();

                $stmt = $pdo->prepare("SELECT id, saldo FROM cuentas WHERE numero_cuenta = ?");
                $stmt->execute([$acc_num]);
                $account = $stmt->fetch();

                if (!$account) {
                    throw new Exception("Cuenta no existe.");
                }

                if ($account['saldo'] < $amount) {
                    throw new Exception("Fondos insuficientes. Saldo actual: S/ " . number_format($account['saldo'], 2));
                }

                $stmt = $pdo->prepare("UPDATE cuentas SET saldo = saldo - ? WHERE numero_cuenta = ?");
                $stmt->execute([$amount, $acc_num]);

                $stmt = $pdo->prepare("INSERT INTO transacciones (cuenta_id, tipo, monto, creado_por, descripcion) VALUES (?, 'retiro', ?, ?, 'Retiro en ventanilla')");
                $stmt->execute([$account['id'], $amount, uid()]);

                $pdo->commit();
                $message = "<div class='bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm'>✅ Retiro de S/ " . number_format($amount, 2) . " realizado en la cuenta <strong>$acc_num</strong></div>";
                log_action('withdraw', "Retiro de S/ $amount en $acc_num");

            } catch (Exception $e) {
                $pdo->rollback();
                $message = '<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">❌ Error: ' . h($e->getMessage()) . '</div>';
            }
        }
    }
    ?>
    <div class="container mx-auto px-6 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h4 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-cash-register"></i>
                💼 Cajero - Operaciones en Ventanilla
            </h4>

            <!-- Botón de logout -->
            <a href="auth/logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                <i class="fas fa-sign-out-alt"></i> Salir
            </a>
        </div>

        <!-- Mensaje -->
        <?php if ($message): ?>
            <div class="mb-6">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- Formularios -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Depósito -->
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                <h6 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-wallet text-green-600"></i>
                    Depósito
                </h6>
                <form method="post" class="space-y-4">
                    <input type="hidden" name="do" value="deposit">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número de cuenta</label>
                        <input
                            type="text"
                            name="account_number"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-600"
                            value="<?= h($_POST['account_number'] ?? '') ?>"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Monto (S/)</label>
                        <input
                            type="number"
                            step="0.01"
                            name="amount"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-600"
                            value="<?= h($_POST['amount'] ?? '') ?>"
                            required
                        >
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 px-4 rounded-lg transition shadow-sm hover:shadow"
                    >
                        Registrar Depósito
                    </button>
                </form>
            </div>

            <!-- Retiro -->
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                <h6 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-hand-holding-usd text-red-600"></i>
                    Retiro
                </h6>
                <form method="post" class="space-y-4">
                    <input type="hidden" name="do" value="withdraw">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número de cuenta</label>
                        <input
                            type="text"
                            name="account_number"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500/30 focus:border-red-600"
                            value="<?= h($_POST['account_number'] ?? '') ?>"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Monto (S/)</label>
                        <input
                            type="number"
                            step="0.01"
                            name="amount"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500/30 focus:border-red-600"
                            value="<?= h($_POST['amount'] ?? '') ?>"
                            required
                        >
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2.5 px-4 rounded-lg transition shadow-sm hover:shadow"
                    >
                        Registrar Retiro
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <footer class="mt-12 py-6 text-center text-gray-500 text-sm border-t border-gray-200">
            <p>&copy; <?= date('Y') ?> <?= h(APP_NAME) ?>. Sistema educativo para secundaria.</p>
        </footer>
    </div>
    <?php
}