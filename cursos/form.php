<?php
require_once '../config/database.php';
require_once '../includes/funciones.php';
requerirLogin();

$pdo = conectarDB();
$curso = ['id' => '', 'codigo' => '', 'nombre' => '', 'descripcion' => '', 'docente' => '', 'creditos' => 0, 'horas' => 0, 'estado' => 'activo'];
$esEdicion = false;

if (!empty($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM cursos WHERE id = ?');
    $stmt->execute([(int)$_GET['id']]);
    $encontrado = $stmt->fetch();
    if ($encontrado) {
        $curso = $encontrado;
        $esEdicion = true;
    }
}

$tituloPagina = $esEdicion ? 'Editar Curso' : 'Nuevo Curso';
require_once '../includes/header.php';
?>

<h3 class="mb-4"><i class="bi bi-journal-bookmark"></i> <?= $esEdicion ? 'Editar' : 'Nuevo' ?> Curso</h3>

<div class="card card-dashboard">
  <div class="card-body">
    <form action="guardar.php" method="POST">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <input type="hidden" name="id" value="<?= limpiar($curso['id']) ?>">

      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Código</label>
          <input type="text" name="codigo" class="form-control" required value="<?= limpiar($curso['codigo']) ?>">
        </div>
        <div class="col-md-9">
          <label class="form-label">Nombre del curso</label>
          <input type="text" name="nombre" class="form-control" required value="<?= limpiar($curso['nombre']) ?>">
        </div>
        <div class="col-md-12">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" class="form-control" rows="3"><?= limpiar($curso['descripcion']) ?></textarea>
        </div>
        <div class="col-md-4">
          <label class="form-label">Docente</label>
          <input type="text" name="docente" class="form-control" value="<?= limpiar($curso['docente']) ?>">
        </div>
        <div class="col-md-2">
          <label class="form-label">Créditos</label>
          <input type="number" name="creditos" min="0" class="form-control" value="<?= (int)$curso['creditos'] ?>">
        </div>
        <div class="col-md-2">
          <label class="form-label">Horas</label>
          <input type="number" name="horas" min="0" class="form-control" value="<?= (int)$curso['horas'] ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Estado</label>
          <select name="estado" class="form-select">
            <option value="activo" <?= $curso['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
            <option value="inactivo" <?= $curso['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
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
