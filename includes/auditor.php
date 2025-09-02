<?php
function auditor_dashboard() {
    $pdo = Database::getInstance();

    // ✅ Consulta con tablas y campos en español
    $logs = $pdo->query("
        SELECT b.*, u.nombre 
        FROM bitacora b 
        LEFT JOIN usuarios u ON u.id = b.usuario_id 
        ORDER BY b.creado_en DESC 
        LIMIT 100
    ")->fetchAll();
    ?>
    <div class="container mx-auto px-6 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h4 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-shield-alt"></i>
                🕵️ Auditor - Reportes y Seguridad
            </h4>

            <!-- Botón de logout -->
            <a href="auth/logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                <i class="fas fa-sign-out-alt"></i> Salir
            </a>
        </div>

        <!-- Botones de exportación -->
        <div class="flex flex-wrap gap-3 mb-6">
            <a href="export.php?type=transactions" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition shadow-sm">
                <i class="fas fa-file-export"></i> Exportar Movimientos
            </a>
            <a href="export.php?type=accounts" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition shadow-sm">
                <i class="fas fa-file-export"></i> Exportar Cuentas
            </a>
            <a href="export.php?type=loans" class="bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition shadow-sm">
                <i class="fas fa-file-export"></i> Exportar Préstamos
            </a>
        </div>

        <!-- Título de la tabla -->
        <h6 class="text-lg font-semibold text-gray-800 mb-4">Registro de Actividad</h6>

        <!-- Tabla responsive -->
        <div class="overflow-x-auto bg-white rounded-xl shadow-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detalles</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($logs as $log): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= h($log['nombre'] ?? 'Sistema') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= h($log['accion']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?= h($log['detalles']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= h($log['direccion_ip']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= h($log['creado_en']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <footer class="mt-12 py-6 text-center text-gray-500 text-sm border-t border-gray-200">
            <p>&copy; <?= date('Y') ?> <?= h(APP_NAME) ?>. Sistema educativo para secundaria.</p>
        </footer>
    </div>
    <?php
}