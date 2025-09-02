<?php
// auth/register.php
require_once '../includes/config.php';
require_login();

if (!has_role('gerente')) {
    echo '<div class="p-8 text-center">';
    echo '<h3 class="text-2xl font-bold text-red-700 mb-4">🚫 Acceso denegado</h3>';
    echo '<p class="text-gray-600">Solo el gerente puede registrar usuarios.</p>';
    echo '<a href="../dashboard.php" class="inline-block mt-4 px-5 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm transition">← Volver al panel</a>';
    echo '</div>';
    exit;
}

$error = '';
$success = '';

if ($_POST) {
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'cliente';
    $csrf = $_POST['csrf_token'] ?? '';

    if (!validate_csrf($csrf)) {
        $error = 'Token de seguridad inválido.';
    } elseif (!$name || !$email || !$password) {
        $error = 'Por favor, completa todos los campos.';
    } elseif (!in_array($role, ['cliente', 'cajero', 'ejecutivo', 'gerente', 'auditor'])) {
        $error = 'Rol no válido.';
    } else {
        $pdo = Database::getInstance();
        try {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $pdo->beginTransaction();

            // Insertar usuario
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, contraseña_hash, rol) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hash, $role]);
            $user_id = $pdo->lastInsertId();

            // Crear cuenta bancaria automáticamente
            $account_number = random_account_number();
            $stmt = $pdo->prepare("INSERT INTO cuentas (usuario_id, numero_cuenta) VALUES (?, ?)");
            $stmt->execute([$user_id, $account_number]);

            $pdo->commit();
            $success = "✅ Usuario <strong>$name</strong> creado con rol <strong>$role</strong>. Cuenta: <code class='font-mono bg-gray-100 px-1 rounded'>$account_number</code>";
            log_action('register_user', "Gerente creó usuario: $email (rol: $role)");
        } catch (Exception $e) {
            $pdo->rollback();
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $error = '❌ Este email ya está registrado.';
            } else {
                $error = '❌ Error al crear usuario: ' . $e->getMessage();
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="container mx-auto px-4 py-8 max-w-3xl">
  <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">

    <h4 class="text-xl font-semibold text-gray-800 flex items-center gap-2 mb-2">
      <i class="fas fa-user-plus text-blue-600"></i>
      Registrar Nuevo Usuario
    </h4>
    <p class="text-gray-600 mb-6">Solo el gerente puede crear nuevos usuarios y asignar roles.</p>

    <?php if ($error): ?>
      <div class="p-4 mb-6 text-red-800 bg-red-100 border border-red-200 rounded-lg">
        <?= $error ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="p-4 mb-6 text-green-800 bg-green-100 border border-green-200 rounded-lg">
        <?= $success ?>
      </div>
    <?php endif; ?>

    <form method="post" class="space-y-5">
      <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">

      <!-- Nombre -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
        <input type="text" name="name"
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
               value="<?= h($_POST['name'] ?? '') ?>" required>
      </div>

      <!-- Email -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email"
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
               value="<?= h($_POST['email'] ?? '') ?>" required>
      </div>

      <!-- Contraseña -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
        <input type="password" name="password"
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
               required>
      </div>

      <!-- Rol -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
        <select name="role"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                required>
          <option value="cliente">Cliente</option>
          <option value="cajero">Cajero</option>
          <option value="ejecutivo">Ejecutivo</option>
          <option value="gerente">Gerente</option>
          <option value="auditor">Auditor</option>
        </select>
      </div>

      <!-- Botones -->
      <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mt-6">
        <a href="../dashboard.php"
           class="px-5 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition text-center">
          ← Cancelar
        </a>
        <button type="submit"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow">
          <i class="fas fa-save mr-1"></i> Crear Usuario
        </button>
      </div>
    </form>

  </div>
</div>

<?php include '../includes/footer.php'; ?>