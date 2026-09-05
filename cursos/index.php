<?php
require_once '../config/database.php';
require_once '../includes/funciones.php';
requerirLogin();

$pdo = conectarDB();
$busqueda = trim($_GET['q'] ?? '');

// JOIN con matriculas para contar inscritos por curso (agregado + GROUP BY)
$sqlBase = "
    SELECT c.*, COUNT(m.id) AS total_matriculados
    FROM cursos c
    LEFT JOIN matriculas m ON m.curso_id = c.id
";

if ($busqueda !== '') {
    $stmt = $pdo->prepare($sqlBase . " WHERE c.nombre LIKE ? OR c.codigo LIKE ? GROUP BY c.id ORDER BY c.id DESC");
    $like = '%' . $busqueda . '%';
    $stmt->execute([$like, $like]);
} else {
    $stmt = $pdo->query($sqlBase . " GROUP BY c.id ORDER BY c.id DESC");
}
$cursos = $stmt->fetchAll();

$tituloPagina = 'Gestión de Cursos';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-journal-bookmark"></i> Gestión de Cursos</h3>
  <a href="form.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Nuevo Curso</a>
</div>

<div class="card card-dashboard mb-3">
  <div class="card-body">
    <form method="GET" class="row g-2">
      <div class="col-md-8">
        <input type="text" name="q" class="form-control" placeholder="Buscar por nombre o código..." value="<?= limpiar($busqueda) ?>">
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
          <th>Código</th>
          <th>Nombre</th>
          <th>Docente</th>
          <th>Créditos</th>
          <th>Matriculados</th>
          <th>Estado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($cursos): ?>
          <?php foreach ($cursos as $c): ?>
            <tr>
              <td><?= (int)$c['id'] ?></td>
              <td><?= limpiar($c['codigo']) ?></td>
              <td><?= limpiar($c['nombre']) ?></td>
              <td><?= limpiar($c['docente']) ?></td>
              <td><?= (int)$c['creditos'] ?></td>
              <td><span class="badge bg-primary"><?= (int)$c['total_matriculados'] ?></span></td>
              <td>
                <span class="badge <?= $c['estado'] === 'activo' ? 'bg-success' : 'bg-secondary' ?>">
                  <?= limpiar($c['estado']) ?>
                </span>
              </td>
              <td class="text-end">
                <a href="form.php?id=<?= (int)$c['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i></a>
                <form action="eliminar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este curso?');">
                  <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                  <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                  <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="8" class="text-center text-muted py-3">No se encontraron cursos.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
