<?php
/**
 * Controlador para que un estudiante vea los detalles de una tarea específica
 * y su propio estado/entrega para esa tarea.
 */

$tarea_detalle_estudiante = null;
$registro_tarea_estudiante = null;

if (!isset($_SESSION['estudiante_id']) || !filter_var($_SESSION['estudiante_id'], FILTER_VALIDATE_INT)) {
    $_SESSION['mensaje'] = "Debe iniciar sesión como estudiante para ver esta página.";
    $_SESSION['icono'] = "warning";
    header('Location: ' . APP_URL . '/login');
    exit;
}

if (!isset($_GET['id_tarea']) || !filter_var($_GET['id_tarea'], FILTER_VALIDATE_INT) ||
    !isset($_GET['id_registro']) || !filter_var($_GET['id_registro'], FILTER_VALIDATE_INT)) {
    $_SESSION['mensaje'] = "Información de tarea no válida.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/admin/deberes/'); // Redirigir a la lista de deberes del estudiante
    exit;
}

$id_tarea_get = $_GET['id_tarea'];
$id_registro_tarea_get = $_GET['id_registro'];
$id_estudiante_sesion = $_SESSION['estudiante_id'];

try {
    // 1. Obtener detalles generales de la tarea
    $sql_tarea_detalle = "SELECT 
                                t.id_tarea, t.titulo, t.descripcion, 
                                DATE_FORMAT(t.fecha_asignacion, '%d/%m/%Y') as fecha_asignacion_formato,
                                DATE_FORMAT(t.fecha_entrega, '%d/%m/%Y %H:%i') as fecha_entrega_formato,
                                m.nombre_materia,
                                CONCAT(g.curso, ' ', g.paralelo) as grado_completo,
                                CONCAT(n.nivel, ' - ', n.turno) as nivel_completo,
                                CONCAT(p_doc.nombres, ' ', p_doc.apellidos) as nombre_docente
                          FROM tareas as t
                          INNER JOIN materias as m ON t.materia_id = m.id_materia
                          INNER JOIN grados as g ON t.grado_id = g.id_grado
                          INNER JOIN niveles as n ON g.nivel_id = n.id_nivel
                          INNER JOIN docentes as d ON t.docente_id = d.id_docente
                          INNER JOIN personas as p_doc ON d.persona_id = p_doc.id_persona
                          WHERE t.id_tarea = :id_tarea AND t.estado = '1'";
    
    $query_tarea_detalle = $pdo->prepare($sql_tarea_detalle);
    $query_tarea_detalle->bindParam(':id_tarea', $id_tarea_get, PDO::PARAM_INT);
    $query_tarea_detalle->execute();
    $tarea_detalle_estudiante = $query_tarea_detalle->fetch(PDO::FETCH_ASSOC);

    if (!$tarea_detalle_estudiante) {
        $_SESSION['mensaje'] = "Tarea no encontrada o ya no está activa.";
        $_SESSION['icono'] = "error";
        header('Location: ' . APP_URL . '/admin/deberes/');
        exit;
    }

    // 2. Obtener el registro específico del estudiante para esta tarea
    $sql_registro_tarea = "SELECT 
                                rt.id_registro,
                                rt.estado as estado_entrega,
                                DATE_FORMAT(rt.fecha_entrega, '%d/%m/%Y %H:%i') as fecha_entrega_realizada_formato,
                                rt.calificacion,
                                rt.observaciones as observaciones_docente
                                -- rt.archivo_entrega (si tienes esta columna en registro_tareas)
                           FROM registro_tareas rt
                           WHERE rt.id_registro = :id_registro_tarea 
                             AND rt.tarea_id = :id_tarea
                             AND rt.estudiante_id = :id_estudiante_sesion
                             AND rt.estado_registro = '1'";

    $query_registro_tarea = $pdo->prepare($sql_registro_tarea);
    $query_registro_tarea->bindParam(':id_registro_tarea', $id_registro_tarea_get, PDO::PARAM_INT);
    $query_registro_tarea->bindParam(':id_tarea', $id_tarea_get, PDO::PARAM_INT);
    $query_registro_tarea->bindParam(':id_estudiante_sesion', $id_estudiante_sesion, PDO::PARAM_INT);
    $query_registro_tarea->execute();
    $registro_tarea_estudiante = $query_registro_tarea->fetch(PDO::FETCH_ASSOC);

    if (!$registro_tarea_estudiante) {
        $_SESSION['mensaje'] = "No se encontró tu asignación para esta tarea o no tienes permiso para verla.";
        $_SESSION['icono'] = "warning";
        header('Location: ' . APP_URL . '/admin/deberes/');
        exit;
    }

} catch (PDOException $e) {
    error_log("Error en ver_tarea_estudiante_controller: " . $e->getMessage());
    $_SESSION['mensaje'] = "Ocurrió un error al cargar los detalles de la tarea.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/admin/deberes/');
    exit;
}
?>