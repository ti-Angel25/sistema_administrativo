<?php
/**
 * Funciones auxiliares comunes a todo el sistema.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Verifica que exista una sesión activa; si no, redirige al login */
function requerirLogin() {
    if (empty($_SESSION['usuario_id'])) {
        header('Location: ' . rutaBase() . 'login.php');
        exit;
    }
}

/** Calcula la ruta base relativa según la profundidad del script actual (para módulos en subcarpetas) */
function rutaBase() {
    // Los scripts dentro de /usuarios, /estudiantes, /cursos, /matriculas están 1 nivel debajo de la raíz
    $enSubcarpeta = strpos($_SERVER['SCRIPT_NAME'], '/usuarios/') !== false
        || strpos($_SERVER['SCRIPT_NAME'], '/estudiantes/') !== false
        || strpos($_SERVER['SCRIPT_NAME'], '/cursos/') !== false
        || strpos($_SERVER['SCRIPT_NAME'], '/matriculas/') !== false;
    return $enSubcarpeta ? '../' : '';
}

/** Limpia una cadena de entrada básica */
function limpiar($valor) {
    return htmlspecialchars(trim($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

/** Genera (o reutiliza) un token CSRF para el formulario actual */
function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Valida el token CSRF recibido por POST */
function validarCsrf() {
    $token = $_POST['csrf_token'] ?? '';
    if (empty($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        die('Token de seguridad inválido. Recargue la página e intente nuevamente.');
    }
}

/** Guarda un mensaje flash para mostrarlo tras una redirección */
function setMensaje($tipo, $texto) {
    $_SESSION['mensaje'] = ['tipo' => $tipo, 'texto' => $texto];
}

/** Obtiene y limpia el mensaje flash actual */
function obtenerMensaje() {
    if (!empty($_SESSION['mensaje'])) {
        $m = $_SESSION['mensaje'];
        unset($_SESSION['mensaje']);
        return $m;
    }
    return null;
}

/** Devuelve el rol del usuario autenticado */
function rolActual() {
    return $_SESSION['usuario_rol'] ?? null;
}

/** Restringe una acción solo al rol admin */
function requerirAdmin() {
    if (rolActual() !== 'admin') {
        setMensaje('danger', 'No tiene permisos para realizar esta acción.');
        header('Location: ' . rutaBase() . 'index.php');
        exit;
    }
}
