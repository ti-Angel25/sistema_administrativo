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

$id               = (int)($_POST['id'] ?? 0);
$dni              = trim($_POST['dni'] ?? '');
$nombres          = trim($_POST['nombres'] ?? '');
$apellidos        = trim($_POST['apellidos'] ?? '');
$email            = trim($_POST['email'] ?? '');
$telefono         = trim($_POST['telefono'] ?? '');
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?: null;
$direccion        = trim($_POST['direccion'] ?? '');
$estado           = $_POST['estado'] ?? 'activo';

if ($dni === '' || $nombres === '' || $apellidos === '') {
    setMensaje('danger', 'DNI, nombres y apellidos son obligatorios.');
    header('Location: index.php');
    exit;
}

try {
    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE estudiantes SET dni=?, nombres=?, apellidos=?, email=?, telefono=?, fecha_nacimiento=?, direccion=?, estado=? WHERE id=?');
        $stmt->execute([$dni, $nombres, $apellidos, $email, $telefono, $fecha_nacimiento, $direccion, $estado, $id]);
        setMensaje('success', 'Estudiante actualizado correctamente.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO estudiantes (dni, nombres, apellidos, email, telefono, fecha_nacimiento, direccion, estado) VALUES (?,?,?,?,?,?,?,?)');
        $stmt->execute([$dni, $nombres, $apellidos, $email, $telefono, $fecha_nacimiento, $direccion, $estado]);
        setMensaje('success', 'Estudiante creado correctamente.');
    }
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        setMensaje('danger', 'Ya existe un estudiante con ese DNI.');
    } else {
        setMensaje('danger', 'Ocurrió un error al guardar el estudiante.');
    }
}

header('Location: index.php');
exit;
