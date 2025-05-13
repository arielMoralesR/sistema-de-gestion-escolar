<?php
include ('../../../app/config.php');

// Verificar sesión y permisos (ej. solo docentes o administradores pueden registrar)
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol_id'])) {
    $_SESSION['mensaje'] = "Acceso no autorizado. Debe iniciar sesión."; 
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/login');
    exit;
}
$rol_id_sesion = $_SESSION['rol_id'];
$reportado_por_usuario_id = $_SESSION['usuario_id']; // Usaremos el usuario_id general

// Permitir acceso a Docentes (rol_id = 6) y Regentes (rol_id = 10)
if ($rol_id_sesion != 6 && $rol_id_sesion != 10) {
    $_SESSION['mensaje'] = "Acceso no autorizado. Permiso denegado para este rol.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/admin/'); // O una página de acceso denegado
    exit;
}

// Ejemplo de verificación de rol (ajusta según tus IDs de roles)
/*
$ROL_PERMITIDO_1 = 6; // ID del rol Docente
$ROL_PERMITIDO_2 = 1; // ID del rol Administrador
if (!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], [$ROL_PERMITIDO_1, $ROL_PERMITIDO_2])) {
    $_SESSION['mensaje'] = "No tiene los permisos necesarios para registrar incidentes.";
    $_SESSION['icono'] = "warning";
    header('Location: ' . APP_URL . '/admin/');
    exit;
}
*/

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger datos del formulario
    $estudiante_id = filter_input(INPUT_POST, 'estudiante_id', FILTER_VALIDATE_INT);
    $tipo_incidente_id = filter_input(INPUT_POST, 'tipo_incidente_id', FILTER_VALIDATE_INT);
    $fecha_hora_suceso = $_POST['fecha_hora_suceso'] ?? '';
    $lugar_suceso = trim($_POST['lugar_suceso'] ?? '');
    $descripcion_detallada = trim($_POST['descripcion_detallada'] ?? '');
    $medidas_tomadas = trim($_POST['medidas_tomadas'] ?? '');

    $reportado_por_usuario_id = $_SESSION['usuario_id']; // Usuario logueado que reporta
    $fyh_creacion = date('Y-m-d H:i:s');
    $estado_registro = '1'; // Activo por defecto

    // Validaciones básicas
    if (!$estudiante_id || !$tipo_incidente_id || empty($fecha_hora_suceso) || empty($descripcion_detallada)) {
        $_SESSION['mensaje'] = "Error: Los campos marcados con (*) son obligatorios.";
        $_SESSION['icono'] = "error";
        header('Location: ' . APP_URL . '/admin/conducta/create.php');
        exit;
    }

    try {
        $sql_insert = "INSERT INTO disciplina_registros 
                        (estudiante_id, tipo_incidente_id, fecha_hora_suceso, lugar_suceso, 
                         descripcion_detallada, reportado_por_usuario_id, medidas_tomadas, 
                         fyh_creacion, estado)
                       VALUES 
                        (:estudiante_id, :tipo_incidente_id, :fecha_hora_suceso, :lugar_suceso, 
                         :descripcion_detallada, :reportado_por_usuario_id, :medidas_tomadas, 
                         :fyh_creacion, :estado_registro)";
        
        $stmt = $pdo->prepare($sql_insert);
        $stmt->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
        $stmt->bindParam(':tipo_incidente_id', $tipo_incidente_id, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_hora_suceso', $fecha_hora_suceso);
        $stmt->bindParam(':lugar_suceso', $lugar_suceso);
        $stmt->bindParam(':descripcion_detallada', $descripcion_detallada);
        $stmt->bindParam(':reportado_por_usuario_id', $reportado_por_usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':medidas_tomadas', $medidas_tomadas);
        $stmt->bindParam(':fyh_creacion', $fyh_creacion);
        $stmt->bindParam(':estado_registro', $estado_registro);

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Incidente de conducta registrado correctamente.";
            $_SESSION['icono'] = "success";
            header('Location: ' . APP_URL . '/admin/conducta/');
            exit;
        } else {
            $_SESSION['mensaje'] = "Error al registrar el incidente de conducta.";
            $_SESSION['icono'] = "error";
            header('Location: ' . APP_URL . '/admin/conducta/create.php');
            exit;
        }
    } catch (PDOException $e) {
        // Log error $e->getMessage();
        $_SESSION['mensaje'] = "Error de base de datos al registrar el incidente: " . $e->getMessage();
        $_SESSION['icono'] = "error";
        header('Location: ' . APP_URL . '/admin/conducta/create.php');
        exit;
    }
} else {
    $_SESSION['mensaje'] = "Método no permitido.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/admin/conducta/');
    exit;
}
?>