<?php
require_once '../config/database.php';
require_once '../includes/funciones.php';
requerirLogin();

$pdo = conectarDB();

// Listas para los selects
$estudiantes = $pdo->query("SELECT id, nombres, apellidos, dni FROM estudiantes WHERE estado='activo' ORDER BY apellidos")->fetchAll();
$cursos      = $pdo->query("SELECT id, nombre, codigo FROM cursos WHERE estado='activo' ORDER BY nombre")->fetchAll();

$matricula = ['id' => '', 'estudiante_id' => '', 'curso_id' => '', 'fecha_matricula' => date('Y-m-d'), 'estado' => 'matriculado', 'observaciones' => ''];
$esEdicion = false;

if (!empty($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM matriculas WHERE id = ?');
    $stmt->execute([(int)$_GET['id']]);
    $encontrado = $stmt->fetch();
    if ($encontrado) {
        $matricula = $encontrado;
        $esEdicion = true;
    }
}

$tituloPagina = $esEdicion ? 'Editar Matrícula' : 'Nueva Matrícula';
require_once '../includes/header.php';
?>

<h3 class="mb-4"><i class="bi bi-clipboard-check"></i> <?= $esEdicion ? 'Editar' : 'Nueva' ?> Matrícula</h3>

<div class="card card-dashboard">
  <div class="card-body">
    <form action="guardar.php" method="POST">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <input type="hidden" name="id" value="<?= limpiar($matricula['id']) ?>">

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Estudiante</label>
          <select name="estudiante_id" class="form-select" required>
            <option value="">-- Seleccione --</option>
            <?php foreach ($estudiantes as $e): ?>
              <option value="<?= (int)$e['id'] ?>" <?= (int)$matricula['estudiante_id'] === (int)$e['id'] ? 'selected' : '' ?>>
                <?= limpiar($e['apellidos'] . ', ' . $e['nombres'] . ' — ' . $e['dni']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Curso</label>
          <select name="curso_id" class="form-select" required>
            <option value="">-- Seleccione --</option>
            <?php foreach ($cursos as $c): ?>
              <option value="<?= (int)$c['id'] ?>" <?= (int)$matricula['curso_id'] === (int)$c['id'] ? 'selected' : '' ?>>
                <?= limpiar($c['nombre'] . ' (' . $c['codigo'] . ')') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Fecha de matrícula</label>
          <input type="date" name="fecha_matricula" class="form-control" required value="<?= limpiar($matricula['fecha_matricula']) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Estado</label>
          <select name="estado" class="form-select">
            <?php foreach (['matriculado','retirado','culminado'] as $estado): ?>
              <option value="<?= $estado ?>" <?= $matricula['estado'] === $estado ? 'selected' : '' ?>><?= ucfirst($estado) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-12">
          <label class="form-label">Observaciones</label>
          <input type="text" name="observaciones" class="form-control" value="<?= limpiar($matricula['observaciones']) ?>">
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
