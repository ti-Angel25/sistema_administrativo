<?php
require_once '../config/database.php';
require_once '../includes/funciones.php';
requerirLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
validarCsrf();

$pdo = conectarDB();

$id              = (int)($_POST['id'] ?? 0);
$estudiante_id   = (int)($_POST['estudiante_id'] ?? 0);
$curso_id        = (int)($_POST['curso_id'] ?? 0);
$fecha_matricula = $_POST['fecha_matricula'] ?? date('Y-m-d');
$estado          = $_POST['estado'] ?? 'matriculado';
$observaciones   = trim($_POST['observaciones'] ?? '');

if ($estudiante_id === 0 || $curso_id === 0) {
    setMensaje('danger', 'Debe seleccionar un estudiante y un curso.');
    header('Location: index.php');
    exit;
}

try {
    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE matriculas SET estudiante_id=?, curso_id=?, fecha_matricula=?, estado=?, observaciones=? WHERE id=?');
        $stmt->execute([$estudiante_id, $curso_id, $fecha_matricula, $estado, $observaciones, $id]);
        setMensaje('success', 'Matrícula actualizada correctamente.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO matriculas (estudiante_id, curso_id, fecha_matricula, estado, observaciones) VALUES (?,?,?,?,?)');
        $stmt->execute([$estudiante_id, $curso_id, $fecha_matricula, $estado, $observaciones]);
        setMensaje('success', 'Matrícula creada correctamente.');
    }
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        setMensaje('danger', 'Este estudiante ya está matriculado en el curso seleccionado.');
    } else {
        setMensaje('danger', 'Ocurrió un error al guardar la matrícula.');
    }
}

header('Location: index.php');
exit;
