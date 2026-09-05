<?php
require_once 'config/database.php';
require_once 'includes/funciones.php';

// Si ya hay sesión activa, ir directo al dashboard
if (!empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCsrf();

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Debe completar correo y contraseña.';
    } else {
        $pdo = conectarDB();
        // Prepared statement: previene SQL Injection
        $stmt = $pdo->prepare('SELECT id, nombre, email, password, rol, estado FROM usuarios WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && $usuario['estado'] === 'activo' && password_verify($password, $usuario['password'])) {
            // Regenerar id de sesión para prevenir fijación de sesión
            session_regenerate_id(true);
            $_SESSION['usuario_id']     = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_rol']    = $usuario['rol'];

            header('Location: index.php');
            exit;
        } else {
            $error = 'Credenciales inválidas o usuario inactivo.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Iniciar sesión - Sistema Administrativo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="login-wrapper">
  <div class="card login-card p-2">
    <div class="card-body p-4">
      <div class="text-center mb-4">
        <i class="bi bi-mortarboard-fill display-4 text-primary"></i>
        <h4 class="fw-bold mt-2">Sistema Administrativo</h4>
        <p class="text-muted mb-0">Ingrese sus credenciales para continuar</p>
      </div>

      <?php if ($error): ?>
        <div class="alert alert-danger py-2"><?= limpiar($error) ?></div>
      <?php endif; ?>

      <form method="POST" novalidate>
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
        <div class="mb-3">
          <label class="form-label">Correo electrónico</label>
          <input type="email" name="email" class="form-control" required autofocus value="<?= limpiar($_POST['email'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Contraseña</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">
          <i class="bi bi-box-arrow-in-right"></i> Ingresar
        </button>
      </form>

      <hr>
      <p class="text-muted small mb-0 text-center">
        Usuario de prueba: <strong>admin@sistema.com</strong> / <strong>admin123</strong>
      </p>
    </div>
  </div>
</div>
</body>
</html>
