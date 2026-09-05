<?php
require_once '../config/database.php';
require_once '../includes/funciones.php';
requerirLogin();

$pdo = conectarDB();

$busqueda = trim($_GET['q'] ?? '');

if ($busqueda !== '') {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nombre LIKE ? OR email LIKE ? ORDER BY id DESC");
    $like = '%' . $busqueda . '%';
    $stmt->execute([$like, $like]);
} else {
    $stmt = $pdo->query("SELECT * FROM usuarios ORDER BY id DESC");
}
$usuarios = $stmt->fetchAll();

$tituloPagina = 'Gestión de Usuarios';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-people-fill"></i> Gestión de Usuarios</h3>
  <a href="form.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Nuevo Usuario</a>
</div>

<div class="card card-dashboard mb-3">
  <div class="card-body">
    <form method="GET" class="row g-2">
      <div class="col-md-8">
        <input type="text" name="q" class="form-control" placeholder="Buscar por nombre o correo..." value="<?= limpiar($busqueda) ?>">
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
          <th>Nombre</th>
          <th>Correo</th>
          <th>Rol</th>
          <th>Estado</th>
          <th>Creado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($usuarios): ?>
          <?php foreach ($usuarios as $u): ?>
            <tr>
              <td><?= (int)$u['id'] ?></td>
              <td><?= limpiar($u['nombre']) ?></td>
              <td><?= limpiar($u['email']) ?></td>
              <td><span class="badge bg-secondary text-capitalize"><?= limpiar($u['rol']) ?></span></td>
              <td>
                <span class="badge <?= $u['estado'] === 'activo' ? 'bg-success' : 'bg-secondary' ?>">
                  <?= limpiar($u['estado']) ?>
                </span>
              </td>
              <td><?= limpiar($u['created_at']) ?></td>
              <td class="text-end">
                <a href="form.php?id=<?= (int)$u['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i></a>
                <form action="eliminar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este usuario?');">
                  <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                  <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                  <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" class="text-center text-muted py-3">No se encontraron usuarios.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
