<?php
function cliente_dashboard() {
    $pdo = Database::getInstance();
    $user_id = uid();
    $message = '';

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'transferencia_realizada':
            $message = '<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">✅ Transferencia realizada con éxito.</div>';
            break;
        case 'pago_realizado':
            $message = '<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">✅ Pago de préstamo realizado.</div>';
            break;
    }
}

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'fondos_insuficientes':
            $message = '<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">❌ Fondos insuficientes.</div>';
            break;
        case 'datos_invalidos':
            $message = '<div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg text-sm">⚠️ Datos inválidos.</div>';
            break;
    }
}

    // =================== OBTENER DATOS ===================
    $stmt = $pdo->prepare("SELECT * FROM cuentas WHERE usuario_id = ? ORDER BY creado_en DESC");
    $stmt->execute([$user_id]);
    $my_accounts = $stmt->fetchAll();

    $stmt = $pdo->prepare("
        SELECT l.*, a.numero_cuenta 
        FROM prestamos l 
        JOIN cuentas a ON a.id = l.cuenta_solicitante_id 
        WHERE a.usuario_id = ? 
        ORDER BY l.id DESC
    ");
    $stmt->execute([$user_id]);
    $my_loans = $stmt->fetchAll();

    $stmt = $pdo->prepare("
        SELECT t.*, a.numero_cuenta, u.nombre AS por_usuario 
        FROM transacciones t 
        JOIN cuentas a ON a.id = t.cuenta_id 
        LEFT JOIN usuarios u ON u.id = t.creado_por 
        WHERE a.usuario_id = ? 
        ORDER BY t.id DESC 
        LIMIT 30
    ");
    $stmt->execute([$user_id]);
    $txs = $stmt->fetchAll();

    // =================== PROCESAR ACCIONES ===================
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['do'] ?? '';

        // --- Transferencia interna ---
        if ($action === 'transfer') {
            $from_acc = trim($_POST['from_account'] ?? '');
            $to_acc   = trim($_POST['to_account'] ?? '');
            $amount   = (float)($_POST['amount'] ?? 0);

            if (!$from_acc || !$to_acc || $amount <= 0) {
                $message = '<div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg text-sm">⚠️ Completa todos los campos con valores válidos.</div>';
            } elseif ($from_acc === $to_acc) {
                $message = '<div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg text-sm">⚠️ No puedes transferir a la misma cuenta.</div>';
            } else {
                try {
                    $pdo->beginTransaction();

                    // ✅ Cuenta origen
                    $stmt = $pdo->prepare("SELECT id, saldo FROM cuentas WHERE numero_cuenta = ? AND usuario_id = ?");
                    $stmt->execute([$from_acc, $user_id]);
                    $from = $stmt->fetch();

                    if (!$from) throw new Exception("Cuenta de origen inválida o no te pertenece.");
                    if ($from['saldo'] < $amount) throw new Exception("Fondos insuficientes (Saldo: S/ " . number_format($from['saldo'], 2) . ").");

                    // ✅ Cuenta destino
                    $stmt = $pdo->prepare("SELECT id FROM cuentas WHERE numero_cuenta = ? AND usuario_id = ?");
                    $stmt->execute([$to_acc, $user_id]);
                    $to = $stmt->fetch();
                    if (!$to) throw new Exception("Cuenta destino inválida o no te pertenece.");

                    // ✅ Actualizar saldos
                    $pdo->prepare("UPDATE cuentas SET saldo = saldo - ? WHERE id = ?")->execute([$amount, $from['id']]);
                    $pdo->prepare("UPDATE cuentas SET saldo = saldo + ? WHERE id = ?")->execute([$amount, $to['id']]);

                    // ✅ Registrar transacciones
                    $pdo->prepare("INSERT INTO transacciones (cuenta_id, tipo, monto, descripcion, creado_por) VALUES (?, 'transfer_out', ?, ?, ?)")
                        ->execute([$from['id'], $amount, "Transferencia a $to_acc", $user_id]);

                    $pdo->prepare("INSERT INTO transacciones (cuenta_id, tipo, monto, descripcion, creado_por) VALUES (?, 'transfer_in', ?, ?, ?)")
                        ->execute([$to['id'], $amount, "Transferencia de $from_acc", $user_id]);

                    $pdo->commit();
                    $message = "<div class='bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm'>✅ Transferencia de <b>S/ " . number_format($amount, 2) . "</b> realizada de <b>$from_acc</b> a <b>$to_acc</b>.</div>";
                    log_action('transfer', "Transferencia interna: S/ $amount de $from_acc a $to_acc");
                } catch (Exception $e) {
                    $pdo->rollback();
                    $message = "<div class='bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm'>❌ " . h($e->getMessage()) . "</div>";
                }
            }
        }

        // --- Solicitar préstamo ---
        if ($action === 'request_loan') {
            $acc_num   = trim($_POST['account_number'] ?? '');
            $principal = (float)($_POST['principal'] ?? 0);
            $term      = (int)($_POST['term'] ?? 0);

            if (!$acc_num || $principal <= 0 || $term <= 0) {
                $message = '<div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg text-sm">⚠️ Completa todos los campos del préstamo.</div>';
            } else {
                try {
                    // ✅ Obtener cuenta y su configuración de interés
                    $stmt = $pdo->prepare("
                        SELECT id, tasa_interes, tipo_interes 
                        FROM cuentas 
                        WHERE numero_cuenta = ? AND usuario_id = ?
                    ");
                    $stmt->execute([$acc_num, $user_id]);
                    $account = $stmt->fetch();

                    if (!$account) {
                        throw new Exception("Cuenta no válida o no te pertenece.");
                    }

                    $rate = $account['tasa_interes'];

                    if ($account['tipo_interes'] === 'compuesto') {
                        $monthly_rate = $rate / 100 / 12;
                        $total_due = $principal * pow(1 + $monthly_rate, $term);
                    } else {
                        $total_due = $principal * (1 + ($rate / 100) * ($term / 12));
                    }

                    $stmt = $pdo->prepare("
                        INSERT INTO prestamos (
                            cuenta_solicitante_id, 
                            monto_principal, 
                            tasa_interes, 
                            plazo_meses, 
                            total_a_pagar, 
                            estado
                        ) VALUES (?, ?, ?, ?, ?, 'pending')
                    ");
                    $stmt->execute([
                        $account['id'], 
                        $principal, 
                        $rate, 
                        $term, 
                        $total_due
                    ]);

                    $message = "<div class='bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm'>
                        ✅ Solicitud enviada por <b>S/ " . number_format($principal, 2) . "</b> al <b>$rate%</b> anual. Estado: <b>Pendiente</b>.
                    </div>";
                    log_action('loan_request', "Préstamo solicitado: S/ $principal a $rate% ($account[tipo_interes]) por $term meses");
                } catch (Exception $e) {
                    $message = "<div class='bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm'>❌ " . h($e->getMessage()) . "</div>";
                }
            }
        }

       // --- Pagar préstamo ---
if ($action === 'pay_loan') {
    $loan_id = (int)($_POST['loan_id'] ?? 0);
    $amount  = (float)($_POST['amount'] ?? 0);

    if ($loan_id <= 0 || $amount <= 0) {
        // ❌ Error: redirigir con código
        header('Location: dashboard.php?error=pago_invalido');
        exit;
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                SELECT l.*, a.id as acc_id, a.saldo, a.numero_cuenta 
                FROM prestamos l 
                JOIN cuentas a ON a.id = l.cuenta_solicitante_id 
                WHERE l.id=? AND a.usuario_id=? AND l.estado='approved'
            ");
            $stmt->execute([$loan_id, $user_id]);
            $loan = $stmt->fetch();

            if (!$loan) throw new Exception("Préstamo no encontrado o no aprobado.");
            if ($loan['saldo'] < $amount) throw new Exception("Saldo insuficiente en la cuenta (S/ " . number_format($loan['saldo'], 2) . ").");

            $pdo->prepare("UPDATE cuentas SET saldo = saldo - ? WHERE id=?")->execute([$amount, $loan['acc_id']]);

            $pdo->prepare("INSERT INTO transacciones (cuenta_id, tipo, monto, descripcion, creado_por) 
                           VALUES (?, 'loan_payment', ?, ?, ?)")
                ->execute([$loan['acc_id'], $amount, "Pago préstamo ID $loan_id", $user_id]);

            $new_total = $loan['total_a_pagar'] - $amount;
            if ($new_total <= 0) {
                $pdo->prepare("UPDATE prestamos SET estado='paid', total_a_pagar=0 WHERE id=?")->execute([$loan_id]);
            } else {
                $pdo->prepare("UPDATE prestamos SET total_a_pagar=? WHERE id=?")->execute([$new_total, $loan_id]);
            }

            $pdo->commit();
            log_action('loan_payment', "Pago de préstamo ID $loan_id: S/ $amount");

            // ✅ Éxito: redirigir para evitar reenvío
            header('Location: dashboard.php?success=pago_realizado');
            exit;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollback();
            }
            // ❌ Error: redirigir con código
            header('Location: dashboard.php?error=pago_fallido');
            exit;
        }
    }
}
    }
    ?>
    <div class="container mx-auto px-6 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h4 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-university"></i>
                🏦 Mi Banco - Cliente
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Mis cuentas -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <h6 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-credit-card text-blue-600"></i>
                        Mis Cuentas
                    </h6>
                    <?php if ($my_accounts): ?>
                        <ul class="space-y-3">
                            <?php foreach ($my_accounts as $a): ?>
                                <li class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                                    <div class="font-medium text-gray-900"><?= h($a['numero_cuenta']) ?></div>
                                    <div class="text-sm text-gray-600">Creada: <?= date('d/m/Y', strtotime($a['creado_en'])) ?></div>
                                    <div class="mt-2">
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                            S/ <?= number_format($a['saldo'], 2) ?>
                                        </span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-sm text-gray-500">No tienes cuentas bancarias.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Operaciones -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Transferencia -->
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <h6 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-exchange-alt text-green-600"></i>
                        Transferencia entre mis cuentas
                    </h6>
                    <form method="post" class="space-y-4">
                        <input type="hidden" name="do" value="transfer">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                                <select name="from_account" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-600" required>
                                    <option value="">Selecciona cuenta origen</option>
                                    <?php foreach ($my_accounts as $a): ?>
                                        <option value="<?= h($a['numero_cuenta']) ?>">
                                            <?= h($a['numero_cuenta']) ?> (S/ <?= number_format($a['saldo'], 2) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Hacia</label>
                                <select name="to_account" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-600" required>
                                    <option value="">Selecciona cuenta destino</option>
                                    <?php foreach ($my_accounts as $a): ?>
                                        <option value="<?= h($a['numero_cuenta']) ?>">
                                            <?= h($a['numero_cuenta']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Monto (S/)</label>
                            <input
                                type="number"
                                step="0.01"
                                name="amount"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-600"
                                placeholder="0.00"
                                required
                            >
                        </div>

                        <div class="text-right">
                            <button
                                type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 px-6 rounded-lg transition shadow-sm hover:shadow"
                            >
                                Transferir
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Solicitar préstamo -->
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <h6 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-hand-holding-usd text-purple-600"></i>
                        Solicitar Préstamo
                    </h6>
                    <form method="post" class="space-y-4">
                        <input type="hidden" name="do" value="request_loan">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cuenta destino</label>
                            <select name="account_number" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500/30 focus:border-purple-600" required>
                                <option value="">Selecciona cuenta</option>
                                <?php foreach ($my_accounts as $a): ?>
                                    <option value="<?= h($a['numero_cuenta']) ?>">
                                        <?= h($a['numero_cuenta']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Monto</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    name="principal"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500/30 focus:border-purple-600"
                                    placeholder="1000"
                                    required
                                >
                            </div>

                            <!-- Mostrar tasa de interés desde la base de datos -->
                            <?php if (!empty($my_accounts)): ?>
                                <?php $first_account = $my_accounts[0]; ?>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tasa de interés</label>
                                    <p class="text-sm text-gray-600 bg-gray-50 px-4 py-2.5 rounded-lg">
                                        <?= $first_account['tasa_interes'] ?>% anual
                                    </p>
                                </div>
                            <?php else: ?>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tasa de interés</label>
                                    <p class="text-sm text-gray-500">No hay cuentas para mostrar tasa.</p>
                                </div>
                            <?php endif; ?>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Plazo (meses)</label>
                                <input
                                    type="number"
                                    name="term"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500/30 focus:border-purple-600"
                                    value="6"
                                    required
                                >
                            </div>
                        </div>

                        <div class="text-right">
                            <button
                                type="submit"
                                class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2.5 px-6 rounded-lg transition shadow-sm hover:shadow"
                            >
                                Enviar Solicitud
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Préstamos -->
        <div class="mt-8 bg-white p-6 rounded-xl shadow-lg border border-gray-200">
            <h6 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-list-alt text-yellow-600"></i>
                Mis Préstamos
            </h6>
            <?php if ($my_loans): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cuenta</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Principal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tasa</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plazo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Restante</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($my_loans as $l): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900"><?= $l['id'] ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?= h($l['numero_cuenta']) ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">S/ <?= number_format($l['monto_principal'], 2) ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?= $l['tasa_interes'] ?>%</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?= $l['plazo_meses'] ?> meses</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">S/ <?= number_format($l['total_a_pagar'], 2) ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            <?php
                                            echo match($l['estado']) {
                                                'approved' => 'bg-green-100 text-green-800',
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'paid' => 'bg-gray-100 text-gray-800',
                                                default => 'bg-red-100 text-red-800'
                                            };
                                            ?>
                                        ">
                                            <?= ucfirst(h($l['estado'])) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        <?php if ($l['estado'] === 'approved'): ?>
                                            <form method="post" class="inline">
                                                <input type="hidden" name="do" value="pay_loan">
                                                <input type="hidden" name="loan_id" value="<?= $l['id'] ?>">
                                                <div class="flex gap-2">
                                                    <input
                                                        type="number"
                                                        step="0.01"
                                                        name="amount"
                                                        placeholder="Monto"
                                                        class="w-24 px-3 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                        required
                                                    >
                                                    <button
                                                        type="submit"
                                                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-3 py-1.5 rounded transition"
                                                    >
                                                        Pagar
                                                    </button>
                                                </div>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-sm text-gray-500">No tienes préstamos activos.</p>
            <?php endif; ?>
        </div>

        <!-- Movimientos -->
        <div class="mt-8 bg-white p-6 rounded-xl shadow-lg border border-gray-200">
            <h6 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-history text-indigo-600"></i>
                Movimientos Recientes
            </h6>
            <?php if ($txs): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cuenta</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Por</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($txs as $t): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?= h($t['tipo']) ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?= h($t['numero_cuenta']) ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">S/ <?= number_format($t['monto'], 2) ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-700"><?= h($t['descripcion']) ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?= date('d/m H:i', strtotime($t['creado_en'])) ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?= h($t['por_usuario'] ?? 'Sistema') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-sm text-gray-500">No hay movimientos registrados.</p>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <footer class="mt-12 py-6 text-center text-gray-500 text-sm border-t border-gray-200">
            <p>&copy; <?= date('Y') ?> <?= h(APP_NAME) ?>. Sistema educativo para secundaria.</p>
        </footer>
    </div>
    <?php
}