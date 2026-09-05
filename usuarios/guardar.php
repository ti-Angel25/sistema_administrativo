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

$id       = (int)($_POST['id'] ?? 0);
$nombre   = trim($_POST['nombre'] ?? '');
$email    = trim($_POST['email'] ?? '');
$rol      = $_POST['rol'] ?? 'secretaria';
$estado   = $_POST['estado'] ?? 'activo';
$password = $_POST['password'] ?? '';

if ($nombre === '' || $email === '') {
    setMensaje('danger', 'Nombre y correo son obligatorios.');
    header('Location: index.php');
    exit;
}

try {
    if ($id > 0) {
        // ACTUALIZAR
        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('UPDATE usuarios SET nombre = ?, email = ?, rol = ?, estado = ?, password = ? WHERE id = ?');
            $stmt->execute([$nombre, $email, $rol, $estado, $hash, $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE usuarios SET nombre = ?, email = ?, rol = ?, estado = ? WHERE id = ?');
            $stmt->execute([$nombre, $email, $rol, $estado, $id]);
        }
        setMensaje('success', 'Usuario actualizado correctamente.');
    } else {
        // CREAR
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT INTO usuarios (nombre, email, password, rol, estado) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$nombre, $email, $hash, $rol, $estado]);
        setMensaje('success', 'Usuario creado correctamente.');
    }
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        setMensaje('danger', 'Ya existe un usuario con ese correo electrónico.');
    } else {
        setMensaje('danger', 'Ocurrió un error al guardar el usuario.');
    }
}

header('Location: index.php');
exit;
