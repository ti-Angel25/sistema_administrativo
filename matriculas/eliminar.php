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

$stmt = $pdo->prepare('DELETE FROM matriculas WHERE id = ?');
$stmt->execute([$id]);

setMensaje('success', 'Matrícula eliminada correctamente.');
header('Location: index.php');
exit;
