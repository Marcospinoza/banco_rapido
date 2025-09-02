<?php
function gerente_dashboard() {
    $pdo = Database::getInstance();
    $message = '';

    // =================== PROCESAR ACCIONES ===================
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_logged() && role() === 'gerente') {
        $action = $_POST['do'] ?? '';

        // --- Aprobar préstamo ---
        if ($action === 'approve_loan') {
            $loan_id = (int)($_POST['loan_id'] ?? 0);

            $stmt = $pdo->prepare("SELECT * FROM prestamos WHERE id=? AND estado='pending'");
            $stmt->execute([$loan_id]);
            $loan = $stmt->fetch();

            if ($loan) {
                $upd = $pdo->prepare("UPDATE prestamos SET estado='approved', aprobado_por=? WHERE id=?");
                $upd->execute([uid(), $loan_id]);

                $insert = $pdo->prepare("
                    INSERT INTO transacciones (cuenta_id, tipo, monto, descripcion, creado_por)
                    VALUES (?, 'loan_disbursement', ?, 'Desembolso de préstamo aprobado', ?)
                ");
                $insert->execute([$loan['cuenta_solicitante_id'], $loan['monto_principal'], uid()]);

                $upd2 = $pdo->prepare("UPDATE cuentas SET saldo = saldo + ? WHERE id=?");
                $upd2->execute([$loan['monto_principal'], $loan['cuenta_solicitante_id']]);

                $message = "<div class='bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2 text-sm'>
                    <i class='fas fa-check-circle'></i> <strong>✅ Préstamo aprobado y desembolsado.</strong>
                </div>";
                log_action('approve_loan', "Préstamo ID $loan_id aprobado");
            } else {
                $message = "<div class='bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center gap-2 text-sm'>
                    <i class='fas fa-times-circle'></i> <strong>❌ Préstamo no encontrado o ya procesado.</strong>
                </div>";
            }
        }

        // --- Rechazar préstamo ---
        if ($action === 'reject_loan') {
            $loan_id = (int)($_POST['loan_id'] ?? 0);

            $upd = $pdo->prepare("UPDATE prestamos SET estado='rejected', aprobado_por=? WHERE id=? AND estado='pending'");
            $upd->execute([uid(), $loan_id]);

            if ($upd->rowCount()) {
                $message = "<div class='bg-yellow-100 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg flex items-center gap-2 text-sm'>
                    <i class='fas fa-ban'></i> <strong>🚫 Préstamo rechazado.</strong>
                </div>";
                log_action('reject_loan', "Préstamo ID $loan_id rechazado");
            } else {
                $message = "<div class='bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center gap-2 text-sm'>
                    <i class='fas fa-exclamation-triangle'></i> <strong>❌ No se pudo rechazar el préstamo.</strong>
                </div>";
            }
        }

        // --- Asignar rol ---
        if ($action === 'assign_role') {
            $email = trim($_POST['email'] ?? '');
            $role  = $_POST['role'] ?? 'cliente';

            $valid_roles = ['cliente','cajero','ejecutivo','gerente','auditor'];
            if (!in_array($role, $valid_roles, true)) {
                $message = "<div class='bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center gap-2 text-sm'>
                    <i class='fas fa-times-circle'></i> <strong>❌ Rol inválido.</strong>
                </div>";
            } else {
                $upd = $pdo->prepare("UPDATE usuarios SET rol=? WHERE email=?");
                $upd->execute([$role, $email]);

                if ($upd->rowCount()) {
                    $message = "<div class='bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2 text-sm'>
                        <i class='fas fa-user-shield'></i> <strong>✅ Rol actualizado a <span class='font-semibold'>$role</span> para $email.</strong>
                    </div>";
                    log_action('assign_role', "Rol de $email cambiado a $role");
                } else {
                    $message = "<div class='bg-yellow-100 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg flex items-center gap-2 text-sm'>
                        <i class='fas fa-user'></i> <strong>⚠️ Usuario no encontrado o rol ya asignado.</strong>
                    </div>";
                }
            }
        }

        // --- Configurar interés ---
        if (isset($_POST['set_interest'])) {
            $acc_num = trim($_POST['account_number']);
            $type = $_POST['interest_type'];
            $rate = floatval($_POST['interest_rate']);

            $stmt = $pdo->prepare("UPDATE cuentas SET tipo_interes = ?, tasa_interes = ? WHERE numero_cuenta = ?");
            $stmt->execute([$type, $rate, $acc_num]);

            $message = "<div class='bg-blue-100 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg flex items-center gap-2 text-sm'>
                <i class='fas fa-percent'></i> <strong>ℹ️ Interés actualizado para la cuenta <span class='font-semibold'>$acc_num</span>.</strong>
            </div>";
            log_action('set_interest', "Interés de $acc_num configurado a $rate%");
        }
    }

    // Verificar acceso
    require_login();
    if (!has_role('gerente')) {
        echo "<div class='p-8 text-center text-red-700 font-semibold text-xl'>🚫 Acceso denegado</div>";
        return;
    }

    // Obtener datos
    $loans = $pdo->query("
        SELECT l.*, a.numero_cuenta, u.nombre as borrower
        FROM prestamos l
        JOIN cuentas a ON a.id = l.cuenta_solicitante_id
        JOIN usuarios u ON u.id = a.usuario_id
        ORDER BY l.id DESC
    ")->fetchAll();

    $usuarios = listar_usuarios();
    ?>
    <div class="container mx-auto px-6 py-8 max-w-7xl">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8 p-6 bg-white rounded-xl shadow-lg border border-gray-200">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                    <i class="fas fa-user-tie text-blue-600"></i>
                    Panel del Gerente
                </h1>
                <p class="text-gray-600 text-sm mt-1">
                    Bienvenido, <strong><?= h(user()['name']) ?></strong>
                </p>
            </div>

            <!-- Botón de logout -->
            <a href="auth/logout.php"
               class="inline-flex items-center gap-2 px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition shadow-sm hover:shadow"
               onclick="return confirm('¿Seguro que deseas cerrar sesión?')">
                <i class="fas fa-sign-out-alt"></i>
                Cerrar sesión
            </a>
        </div>

        <!-- Descripción -->
        <div class="text-center mb-8">
            <p class="text-gray-500">Gestión de préstamos, roles y configuración de intereses</p>
        </div>

        <!-- Mensaje -->
        <?php if ($message): ?>
            <div class="mb-6">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- Préstamos Pendientes -->
        <section class="bg-white p-6 rounded-xl shadow-lg border border-gray-200 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-hand-holding-usd text-yellow-500"></i>
                Préstamos Pendientes
            </h2>

            <?php if (count(array_filter($loans, fn($l) => $l['estado'] === 'pending')) === 0): ?>
                <p class="text-gray-500 italic">No hay préstamos pendientes.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cuenta</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tasa</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meses</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($loans as $l): if ($l['estado'] !== 'pending') continue; ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900"><?= $l['id'] ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium"><?= h($l['borrower']) ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?= h($l['numero_cuenta']) ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold">S/ <?= number_format($l['monto_principal'], 2) ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?= $l['tasa_interes'] ?>%</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?= $l['plazo_meses'] ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm space-x-2">
                                        <form method="post" class="inline">
                                            <input type="hidden" name="do" value="approve_loan">
                                            <input type="hidden" name="loan_id" value="<?= $l['id'] ?>">
                                            <button class="bg-green-600 hover:bg-green-700 text-white text-xs font-medium px-3 py-1.5 rounded transition flex items-center gap-1">
                                                <i class="fas fa-check"></i> Aprobar
                                            </button>
                                        </form>
                                        <form method="post" class="inline">
                                            <input type="hidden" name="do" value="reject_loan">
                                            <input type="hidden" name="loan_id" value="<?= $l['id'] ?>">
                                            <button class="bg-red-600 hover:bg-red-700 text-white text-xs font-medium px-3 py-1.5 rounded transition flex items-center gap-1">
                                                <i class="fas fa-times"></i> Rechazar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <!-- Asignar Rol -->
        <section class="bg-white p-6 rounded-xl shadow-lg border border-gray-200 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-user-tag text-purple-500"></i>
                Asignar Rol a Usuario
            </h2>
            <form method="post" class="space-y-5 max-w-lg">
                <input type="hidden" name="do" value="assign_role">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">📧 Email del usuario</label>
                    <input
                        type="email"
                        name="email"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-600"
                        placeholder="usuario@banco.com"
                        required
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">💼 Rol</label>
                    <select
                        name="role"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-600"
                    >
                        <option value="cliente">Cliente</option>
                        <option value="cajero">Cajero</option>
                        <option value="ejecutivo">Ejecutivo</option>
                        <option value="gerente">Gerente</option>
                        <option value="auditor">Auditor</option>
                    </select>
                </div>

                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition shadow-sm hover:shadow"
                >
                    <i class="fas fa-save"></i> Actualizar Rol
                </button>
            </form>
        </section>

        <!-- Configurar Interés -->
        <section class="bg-white p-6 rounded-xl shadow-lg border border-gray-200 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-percent text-green-500"></i>
                Configurar Interés de Cuenta
            </h2>
            <form method="post" class="space-y-5 max-w-lg">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">🔢 Número de cuenta</label>
                    <input
                        type="text"
                        name="account_number"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-600"
                        placeholder="CTA-12345"
                        required
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">📈 Tipo de interés</label>
                    <select
                        name="interest_type"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-600"
                    >
                        <option value="simple">Interés Simple</option>
                        <option value="compuesto">Interés Compuesto</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">🎯 Tasa anual (%)</label>
                    <input
                        type="number"
                        name="interest_rate"
                        step="0.01"
                        min="0"
                        max="100"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-600"
                        placeholder="10.00"
                        required
                    >
                </div>

                <input type="hidden" name="set_interest" value="1">
                <button
                    type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 px-6 rounded-lg transition shadow-sm hover:shadow flex items-center gap-2"
                >
                    <i class="fas fa-sync-alt"></i> Aplicar Interés
                </button>
            </form>
        </section>

        <!-- Lista de Usuarios -->
        <section class="bg-white p-6 rounded-xl shadow-lg border border-gray-200 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-users text-indigo-500"></i>
                Lista de Usuarios
            </h2>

            <?php if (!empty($usuarios)): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Registro</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($usuarios as $u): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900"><?= h($u['id']) ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium"><?= h($u['nombre']) ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            <?php
                                            echo match($u['rol']) {
                                                'gerente' => 'bg-red-100 text-red-800',
                                                'auditor' => 'bg-purple-100 text-purple-800',
                                                'ejecutivo' => 'bg-blue-100 text-blue-800',
                                                'cajero' => 'bg-green-100 text-green-800',
                                                'cliente' => 'bg-gray-100 text-gray-800',
                                                default => 'bg-yellow-100 text-yellow-800'
                                            };
                                            ?>
                                        ">
                                            <?= h($u['rol']) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?= h($u['creado_en']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500 italic">No hay usuarios registrados.</p>
            <?php endif; ?>
        </section>

        <!-- Acciones rápidas -->
        <div class="text-center mt-8">
            <a href="auth/register.php" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm hover:shadow transition">
                <i class="fas fa-plus"></i> Registrar Nuevo Usuario
            </a>
        </div>

        <!-- Footer -->
        <footer class="mt-12 py-6 text-center text-gray-500 text-sm border-t border-gray-200">
            <p>&copy; <?= date('Y') ?> <?= h(APP_NAME) ?>. Sistema educativo para secundaria.</p>
        </footer>
    </div>
    <?php
}