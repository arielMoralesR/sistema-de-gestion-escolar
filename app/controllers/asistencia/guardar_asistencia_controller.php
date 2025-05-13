<?php
include ('../../../app/config.php');

// Verificar sesión y rol de docente
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol_id'])) {
    $_SESSION['mensaje'] = "Acceso no autorizado. Debe iniciar sesión.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/login');
    exit;
}

$rol_id_sesion = $_SESSION['rol_id'];
$id_usuario_que_registra = $_SESSION['usuario_id'];

// Permitir acceso a Docentes (rol_id = 6) y Regentes (rol_id = 10)
if ($rol_id_sesion != 6 && $rol_id_sesion != 10) {
    $_SESSION['mensaje'] = "Acceso no autorizado. Permiso denegado para este rol.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/admin/'); // Redirigir al dashboard principal o página de error
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // --- INICIO DE DEPURACIÓN ---
    // var_dump($_POST);
    
    error_log("[GUARDAR_ASISTENCIA] Datos POST recibidos: " . print_r($_POST, true));
    // --- FIN DE DEPURACIÓN ---
    $fecha_asistencia = $_POST['fecha_asistencia_form'] ?? null; // Corregido para coincidir con el form
    $grado_id_asistencia = $_POST['grado_id_form'] ?? null;     // Corregido para coincidir con el form
    $datos_asistencias_post = $_POST['asistencias'] ?? [];   // Recibe el array principal de asistencias
    // Las observaciones ahora vienen dentro de $datos_asistencias_post

    error_log("[GUARDAR_ASISTENCIA] Fecha: " . ($fecha_asistencia ?? 'NO RECIBIDA'));
    error_log("[GUARDAR_ASISTENCIA] Grado ID: " . ($grado_id_asistencia ?? 'NO RECIBIDO'));
    error_log("[GUARDAR_ASISTENCIA] Asistencias Array (antes de empty check): " . print_r($asistencias_estudiantes, true));

    // Validaciones básicas
    if (empty($fecha_asistencia) || empty($grado_id_asistencia) || empty($datos_asistencias_post)) { // Validar $datos_asistencias_post
        $_SESSION['mensaje'] = "Datos incompletos para registrar la asistencia.";
        $_SESSION['icono'] = "error";
        error_log("[GUARDAR_ASISTENCIA] ERROR: Datos incompletos. Fecha: '$fecha_asistencia', GradoID: '$grado_id_asistencia', Asistencias empty: " . (empty($datos_asistencias_post) ? 'true' : 'false'));
        // Redirigir de vuelta a la página de toma de asistencia, idealmente con los parámetros para recargar el grado y fecha
        header('Location: ' . APP_URL . '/admin/asistencia/index.php?grado_id=' . $grado_id_asistencia . '&fecha=' . $fecha_asistencia);
        exit;
    }

    $pdo->beginTransaction();
    try {
        // Opcional: Verificar si el docente tiene permiso para tomar asistencia para este grado_id
        // Esto podría implicar verificar si el docente imparte alguna materia en ese grado.
        // Por simplicidad, lo omitiremos por ahora, pero es una buena práctica de seguridad.

        foreach ($datos_asistencias_post as $estudiante_id => $datos_estudiante) {
            $estado_asistencia = $datos_estudiante['estado'] ?? 'presente'; // Valor por defecto si no se envía
            $observacion = $datos_estudiante['observaciones'] ?? null;
            // Verificar si ya existe un registro para este estudiante en esta fecha
            $sql_check = "SELECT id_asistencia FROM asistencia_estudiantes 
                          WHERE estudiante_id = :estudiante_id AND fecha = :fecha";
            $query_check = $pdo->prepare($sql_check);
            $query_check->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
            $query_check->bindParam(':fecha', $fecha_asistencia);
            $query_check->execute();
            $registro_existente = $query_check->fetch(PDO::FETCH_ASSOC);

            if ($registro_existente) {
                // Actualizar el registro existente
                $sql_update = "UPDATE asistencia_estudiantes 
                               SET estado_asistencia = :estado_asistencia, 
                                   observaciones = :observaciones,
                                   fyh_actualizacion = :fyh_actualizacion,
                                   estado = '1' -- Asegurar que esté activo si se está actualizando
                               WHERE id_asistencia = :id_asistencia";
                $stmt = $pdo->prepare($sql_update);
                $stmt->bindParam(':id_asistencia', $registro_existente['id_asistencia'], PDO::PARAM_INT);
            } else {
                // Insertar un nuevo registro
                $sql_insert = "INSERT INTO asistencia_estudiantes 
                                (estudiante_id, fecha, estado_asistencia, observaciones, fyh_creacion, estado) 
                               VALUES 
                                (:estudiante_id, :fecha, :estado_asistencia, :observaciones, :fyh_creacion, '1')";
                $stmt = $pdo->prepare($sql_insert);
                $stmt->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
                $stmt->bindParam(':fecha', $fecha_asistencia);
                $stmt->bindParam(':fyh_creacion', $fechaHora); // $fechaHora de config.php
            }

            $stmt->bindParam(':estado_asistencia', $estado_asistencia);
            $stmt->bindParam(':observaciones', $observacion);
            if ($registro_existente) { // Solo para UPDATE
                 $stmt->bindParam(':fyh_actualizacion', $fechaHora); // $fechaHora de config.php
            }
            $stmt->execute();
        }

        $pdo->commit();
        $_SESSION['mensaje'] = "Asistencia registrada/actualizada correctamente para la fecha " . date("d/m/Y", strtotime($fecha_asistencia)) . ".";
        $_SESSION['icono'] = "success";

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['mensaje'] = "Error al registrar la asistencia: " . $e->getMessage();
        $_SESSION['icono'] = "error";
        error_log("Error en guardar_asistencia_controller: " . $e->getMessage());
    }

    // Redirigir de vuelta a la página de toma de asistencia, idealmente con los parámetros para recargar el grado y fecha
    header('Location: ' . APP_URL . '/admin/asistencia/index.php?grado_id=' . $grado_id_asistencia . '&fecha=' . $fecha_asistencia);
    exit;

} else {
    $_SESSION['mensaje'] = "Método no permitido.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/admin/asistencia/');
    exit;
}
?>