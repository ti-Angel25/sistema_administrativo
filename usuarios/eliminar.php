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

if ($id === (int)($_SESSION['usuario_id'] ?? 0)) {
    setMensaje('danger', 'No puede eliminar su propio usuario mientras está conectado.');
    header('Location: index.php');
    exit;
}

$pdo = conectarDB();
$stmt = $pdo->prepare('DELETE FROM usuarios WHERE id = ?');
$stmt->execute([$id]);

setMensaje('success', 'Usuario eliminado correctamente.');
header('Location: index.php');
exit;
