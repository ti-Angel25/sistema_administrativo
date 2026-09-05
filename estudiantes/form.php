<?php
require_once '../config/database.php';
require_once '../includes/funciones.php';
requerirLogin();

$pdo = conectarDB();
$estudiante = ['id' => '', 'dni' => '', 'nombres' => '', 'apellidos' => '', 'email' => '', 'telefono' => '', 'fecha_nacimiento' => '', 'direccion' => '', 'estado' => 'activo'];
$esEdicion = false;

if (!empty($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM estudiantes WHERE id = ?');
    $stmt->execute([(int)$_GET['id']]);
    $encontrado = $stmt->fetch();
    if ($encontrado) {
        $estudiante = $encontrado;
        $esEdicion = true;
    }
}

$tituloPagina = $esEdicion ? 'Editar Estudiante' : 'Nuevo Estudiante';
require_once '../includes/header.php';
?>

<h3 class="mb-4"><i class="bi bi-person-badge"></i> <?= $esEdicion ? 'Editar' : 'Nuevo' ?> Estudiante</h3>

<div class="card card-dashboard">
  <div class="card-body">
    <form action="guardar.php" method="POST">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <input type="hidden" name="id" value="<?= limpiar($estudiante['id']) ?>">

      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">DNI</label>
          <input type="text" name="dni" class="form-control" required value="<?= limpiar($estudiante['dni']) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Nombres</label>
          <input type="text" name="nombres" class="form-control" required value="<?= limpiar($estudiante['nombres']) ?>">
        </div>
        <div class="col-md-5">
          <label class="form-label">Apellidos</label>
          <input type="text" name="apellidos" class="form-control" required value="<?= limpiar($estudiante['apellidos']) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Correo</label>
          <input type="email" name="email" class="form-control" value="<?= limpiar($estudiante['email']) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Teléfono</label>
          <input type="text" name="telefono" class="form-control" value="<?= limpiar($estudiante['telefono']) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Fecha de nacimiento</label>
          <input type="date" name="fecha_nacimiento" class="form-control" value="<?= limpiar($estudiante['fecha_nacimiento']) ?>">
        </div>
        <div class="col-md-8">
          <label class="form-label">Dirección</label>
          <input type="text" name="direccion" class="form-control" value="<?= limpiar($estudiante['direccion']) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Estado</label>
          <select name="estado" class="form-select">
            <option value="activo" <?= $estudiante['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
            <option value="inactivo" <?= $estudiante['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
          </select>
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
