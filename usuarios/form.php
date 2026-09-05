<?php
require_once '../config/database.php';
require_once '../includes/funciones.php';
requerirLogin();

$pdo = conectarDB();
$usuario = ['id' => '', 'nombre' => '', 'email' => '', 'rol' => 'secretaria', 'estado' => 'activo'];
$esEdicion = false;

if (!empty($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = ?');
    $stmt->execute([(int)$_GET['id']]);
    $encontrado = $stmt->fetch();
    if ($encontrado) {
        $usuario = $encontrado;
        $esEdicion = true;
    }
}

$tituloPagina = $esEdicion ? 'Editar Usuario' : 'Nuevo Usuario';
require_once '../includes/header.php';
?>

<h3 class="mb-4"><i class="bi bi-people-fill"></i> <?= $esEdicion ? 'Editar' : 'Nuevo' ?> Usuario</h3>

<div class="card card-dashboard">
  <div class="card-body">
    <form action="guardar.php" method="POST">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <input type="hidden" name="id" value="<?= limpiar($usuario['id']) ?>">

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Nombre completo</label>
          <input type="text" name="nombre" class="form-control" required value="<?= limpiar($usuario['nombre']) ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Correo electrónico</label>
          <input type="email" name="email" class="form-control" required value="<?= limpiar($usuario['email']) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Rol</label>
          <select name="rol" class="form-select">
            <?php foreach (['admin','secretaria','docente'] as $r): ?>
              <option value="<?= $r ?>" <?= $usuario['rol'] === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Estado</label>
          <select name="estado" class="form-select">
            <option value="activo" <?= $usuario['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
            <option value="inactivo" <?= $usuario['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Contraseña <?= $esEdicion ? '(dejar en blanco para no cambiar)' : '' ?></label>
          <input type="password" name="password" class="form-control" <?= $esEdicion ? '' : 'required' ?> minlength="6">
        </div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
        <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
