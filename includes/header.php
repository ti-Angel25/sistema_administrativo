<?php
// $tituloPagina puede definirse antes de incluir este archivo
$base = rutaBase();
$msg = obtenerMensaje();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($tituloPagina) ? limpiar($tituloPagina) . ' - ' : '' ?>Sistema Administrativo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="<?= $base ?>assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
  <a class="navbar-brand fw-bold" href="<?= $base ?>index.php">
    <i class="bi bi-mortarboard-fill me-1"></i> Sistema Administrativo
  </a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navMenu">
    <ul class="navbar-nav me-auto">
      <li class="nav-item"><a class="nav-link" href="<?= $base ?>index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
      <li class="nav-item"><a class="nav-link" href="<?= $base ?>usuarios/index.php"><i class="bi bi-people-fill"></i> Usuarios</a></li>
      <li class="nav-item"><a class="nav-link" href="<?= $base ?>estudiantes/index.php"><i class="bi bi-person-badge"></i> Estudiantes</a></li>
      <li class="nav-item"><a class="nav-link" href="<?= $base ?>cursos/index.php"><i class="bi bi-journal-bookmark"></i> Cursos</a></li>
      <li class="nav-item"><a class="nav-link" href="<?= $base ?>matriculas/index.php"><i class="bi bi-clipboard-check"></i> Matrículas</a></li>
    </ul>
    <ul class="navbar-nav">
      <li class="nav-item">
        <span class="nav-link text-light-emphasis">
          <i class="bi bi-person-circle"></i> <?= limpiar($_SESSION['usuario_nombre'] ?? '') ?>
          <span class="badge bg-secondary ms-1"><?= limpiar($_SESSION['usuario_rol'] ?? '') ?></span>
        </span>
      </li>
      <li class="nav-item"><a class="nav-link" href="<?= $base ?>logout.php"><i class="bi bi-box-arrow-right"></i> Salir</a></li>
    </ul>
  </div>
</nav>
<div class="container-fluid py-4 px-md-4">
  <?php if ($msg): ?>
    <div class="alert alert-<?= limpiar($msg['tipo']) ?> alert-dismissible fade show" role="alert">
      <?= limpiar($msg['texto']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
</div>
<div class="container-fluid px-md-4">
