<?php
require_once '../config/database.php';
require_once '../includes/funciones.php';
requerirLogin();

$pdo = conectarDB();
$busqueda = trim($_GET['q'] ?? '');

// Se consulta directamente la VISTA vista_matriculas (ya contiene los JOIN necesarios)
if ($busqueda !== '') {
    $stmt = $pdo->prepare("SELECT * FROM vista_matriculas WHERE estudiante LIKE ? OR curso LIKE ? ORDER BY matricula_id DESC");
    $like = '%' . $busqueda . '%';
    $stmt->execute([$like, $like]);
} else {
    $stmt = $pdo->query("SELECT * FROM vista_matriculas ORDER BY matricula_id DESC");
}
$matriculas = $stmt->fetchAll();

$tituloPagina = 'Gestión de Matrículas';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-clipboard-check"></i> Gestión de Matrículas</h3>
  <a href="form.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Nueva Matrícula</a>
</div>

<div class="card card-dashboard mb-3">
  <div class="card-body">
    <form method="GET" class="row g-2">
      <div class="col-md-8">
        <input type="text" name="q" class="form-control" placeholder="Buscar por estudiante o curso..." value="<?= limpiar($busqueda) ?>">
      </div>
      <div class="col-md-2">
        <button class="btn btn-outline-secondary w-100" type="submit"><i class="bi bi-search"></i> Buscar</button>
      </div>
      <div class="col-md-2">
        <a href="index.php" class="btn btn-outline-danger w-100"><i class="bi bi-x-circle"></i> Limpiar</a>
      </div>
    </form>
  </div>
</div>

<div class="card card-dashboard">
  <div class="table-responsive">
    <table class="table table-hover mb-0 align-middle">
      <thead>
        <tr>
          <th>#</th>
          <th>Estudiante</th>
          <th>DNI</th>
          <th>Curso</th>
          <th>Docente</th>
          <th>Fecha</th>
          <th>Estado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($matriculas): ?>
          <?php foreach ($matriculas as $m): ?>
            <tr>
              <td><?= (int)$m['matricula_id'] ?></td>
              <td><?= limpiar($m['estudiante']) ?></td>
              <td><?= limpiar($m['dni_estudiante']) ?></td>
              <td><?= limpiar($m['curso']) ?> <span class="text-muted small">(<?= limpiar($m['codigo_curso']) ?>)</span></td>
              <td><?= limpiar($m['docente']) ?></td>
              <td><?= limpiar($m['fecha_matricula']) ?></td>
              <td>
                <?php
                  $colores = ['matriculado' => 'bg-success', 'retirado' => 'bg-danger', 'culminado' => 'bg-primary'];
                  $color = $colores[$m['estado_matricula']] ?? 'bg-secondary';
                ?>
                <span class="badge <?= $color ?>"><?= limpiar($m['estado_matricula']) ?></span>
              </td>
              <td class="text-end">
                <a href="form.php?id=<?= (int)$m['matricula_id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i></a>
                <form action="eliminar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta matrícula?');">
                  <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                  <input type="hidden" name="id" value="<?= (int)$m['matricula_id'] ?>">
                  <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="8" class="text-center text-muted py-3">No se encontraron matrículas.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
