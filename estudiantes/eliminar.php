<?php
require_once '../config/database.php';
require_once '../includes/funciones.php';
requerirLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
validarCsrf();

$id = (int)($_POST['id'] ?? 0);
$pdo = conectarDB();

try {
    $stmt = $pdo->prepare('DELETE FROM estudiantes WHERE id = ?');
    $stmt->execute([$id]);
    setMensaje('success', 'Estudiante eliminado correctamente.');
} catch (PDOException $e) {
    setMensaje('danger', 'No se pudo eliminar: el estudiante tiene matrículas asociadas.');
}

header('Location: index.php');
exit;
