<?php
require_once 'config/database.php';
require_once 'includes/funciones.php';
requerirLogin();

$pdo = conectarDB();

// --- Funciones agregadas (COUNT) para el dashboard ---
$totalUsuarios    = $pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
$totalEstudiantes = $pdo->query("SELECT COUNT(*) FROM estudiantes WHERE estado = 'activo'")->fetchColumn();
$totalCursos      = $pdo->query("SELECT COUNT(*) FROM cursos WHERE estado = 'activo'")->fetchColumn();
$totalMatriculas  = $pdo->query('SELECT COUNT(*) FROM matriculas')->fetchColumn();

// Matrículas agrupadas por estado
$stmtEstados = $pdo->query("SELECT estado, COUNT(*) AS total FROM matriculas GROUP BY estado");
$matriculasPorEstado = $stmtEstados->fetchAll();

// Top 5 cursos con más matriculados (JOIN + COUNT + GROUP BY)
$stmtTopCursos = $pdo->query("
    SELECT c.nombre, COUNT(m.id) AS total_matriculados
    FROM cursos c
    LEFT JOIN matriculas m ON m.curso_id = c.id
    GROUP BY c.id, c.nombre
    ORDER BY total_matriculados DESC
    LIMIT 5
");
$topCursos = $stmtTopCursos->fetchAll();

// Últimas 5 matrículas (usando la VISTA vista_matriculas)
$ultimasMatriculas = $pdo->query("
    SELECT * FROM vista_matriculas
    ORDER BY matricula_id DESC
    LIMIT 5
")->fetchAll();

$tituloPagina = 'Dashboard';
require_once 'includes/header.php';
?>

<h3 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard</h3>

<div class="row g-3 mb-4">
  <div class="col-md-3 col-sm-6">
    <div class="card card-dashboard text-white bg-primary">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <div class="fs-2 fw-bold"><?= (int)$totalUsuarios ?></div>
          <div>Usuarios</div>
        </div>
        <i class="bi bi-people-fill fs-1 opacity-75"></i>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="card card-dashboard text-white bg-success">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <div class="fs-2 fw-bold"><?= (int)$totalEstudiantes ?></div>
          <div>Estudiantes activos</div>
        </div>
        <i class="bi bi-person-badge fs-1 opacity-75"></i>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="card card-dashboard text-white bg-warning">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <div class="fs-2 fw-bold"><?= (int)$totalCursos ?></div>
          <div>Cursos activos</div>
        </div>
        <i class="bi bi-journal-bookmark fs-1 opacity-75"></i>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="card card-dashboard text-white bg-info">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <div class="fs-2 fw-bold"><?= (int)$totalMatriculas ?></div>
          <div>Matrículas totales</div>
        </div>
        <i class="bi bi-clipboard-check fs-1 opacity-75"></i>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-4">
    <div class="card card-dashboard h-100">
      <div class="card-header bg-white fw-semibold">Matrículas por estado</div>
      <div class="card-body">
        <?php if ($matriculasPorEstado): ?>
          <ul class="list-group list-group-flush">
            <?php foreach ($matriculasPorEstado as $fila): ?>
              <li class="list-group-item d-flex justify-content-between">
                <span class="text-capitalize"><?= limpiar($fila['estado']) ?></span>
                <span class="badge bg-dark rounded-pill"><?= (int)$fila['total'] ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="text-muted mb-0">Sin datos aún.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card card-dashboard h-100">
      <div class="card-header bg-white fw-semibold">Cursos con más matriculados</div>
      <div class="card-body">
        <?php if ($topCursos): ?>
          <ul class="list-group list-group-flush">
            <?php foreach ($topCursos as $fila): ?>
              <li class="list-group-item d-flex justify-content-between">
                <span><?= limpiar($fila['nombre']) ?></span>
                <span class="badge bg-primary rounded-pill"><?= (int)$fila['total_matriculados'] ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="text-muted mb-0">Sin datos aún.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card card-dashboard h-100">
      <div class="card-header bg-white fw-semibold">Últimas matrículas</div>
      <div class="card-body">
        <?php if ($ultimasMatriculas): ?>
          <ul class="list-group list-group-flush">
            <?php foreach ($ultimasMatriculas as $fila): ?>
              <li class="list-group-item">
                <div class="fw-semibold"><?= limpiar($fila['estudiante']) ?></div>
                <div class="text-muted small"><?= limpiar($fila['curso']) ?> &middot; <?= limpiar($fila['fecha_matricula']) ?></div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="text-muted mb-0">Sin datos aún.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
