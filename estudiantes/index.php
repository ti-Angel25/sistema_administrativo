<?php
require_once '../config/database.php';
require_once '../includes/funciones.php';
requerirLogin();

$pdo = conectarDB();
$busqueda = trim($_GET['q'] ?? '');

if ($busqueda !== '') {
    $stmt = $pdo->prepare("SELECT * FROM estudiantes WHERE nombres LIKE ? OR apellidos LIKE ? OR dni LIKE ? ORDER BY id DESC");
    $like = '%' . $busqueda . '%';
    $stmt->execute([$like, $like, $like]);
} else {
    $stmt = $pdo->query("SELECT * FROM estudiantes ORDER BY id DESC");
}
$estudiantes = $stmt->fetchAll();

$tituloPagina = 'Gestión de Estudiantes';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-person-badge"></i> Gestión de Estudiantes</h3>
  <a href="form.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Nuevo Estudiante</a>
</div>

<div class="card card-dashboard mb-3">
  <div class="card-body">
    <form method="GET" class="row g-2">
      <div class="col-md-8">
        <input type="text" name="q" class="form-control" placeholder="Buscar por nombre, apellido o DNI..." value="<?= limpiar($busqueda) ?>">
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
          <th>DNI</th>
          <th>Nombres</th>
          <th>Apellidos</th>
          <th>Correo</th>
          <th>Teléfono</th>
          <th>Estado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($estudiantes): ?>
          <?php foreach ($estudiantes as $e): ?>
            <tr>
              <td><?= (int)$e['id'] ?></td>
              <td><?= limpiar($e['dni']) ?></td>
              <td><?= limpiar($e['nombres']) ?></td>
              <td><?= limpiar($e['apellidos']) ?></td>
              <td><?= limpiar($e['email']) ?></td>
              <td><?= limpiar($e['telefono']) ?></td>
              <td>
                <span class="badge <?= $e['estado'] === 'activo' ? 'bg-success' : 'bg-secondary' ?>">
                  <?= limpiar($e['estado']) ?>
                </span>
              </td>
              <td class="text-end">
                <a href="form.php?id=<?= (int)$e['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i></a>
                <form action="eliminar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este estudiante?');">
                  <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                  <input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
                  <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="8" class="text-center text-muted py-3">No se encontraron estudiantes.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
