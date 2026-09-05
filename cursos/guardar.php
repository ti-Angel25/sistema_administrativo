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

$id          = (int)($_POST['id'] ?? 0);
$codigo      = trim($_POST['codigo'] ?? '');
$nombre      = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$docente     = trim($_POST['docente'] ?? '');
$creditos    = (int)($_POST['creditos'] ?? 0);
$horas       = (int)($_POST['horas'] ?? 0);
$estado      = $_POST['estado'] ?? 'activo';

if ($codigo === '' || $nombre === '') {
    setMensaje('danger', 'Código y nombre son obligatorios.');
    header('Location: index.php');
    exit;
}

try {
    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE cursos SET codigo=?, nombre=?, descripcion=?, docente=?, creditos=?, horas=?, estado=? WHERE id=?');
        $stmt->execute([$codigo, $nombre, $descripcion, $docente, $creditos, $horas, $estado, $id]);
        setMensaje('success', 'Curso actualizado correctamente.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO cursos (codigo, nombre, descripcion, docente, creditos, horas, estado) VALUES (?,?,?,?,?,?,?)');
        $stmt->execute([$codigo, $nombre, $descripcion, $docente, $creditos, $horas, $estado]);
        setMensaje('success', 'Curso creado correctamente.');
    }
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        setMensaje('danger', 'Ya existe un curso con ese código.');
    } else {
        setMensaje('danger', 'Ocurrió un error al guardar el curso.');
    }
}

header('Location: index.php');
exit;
