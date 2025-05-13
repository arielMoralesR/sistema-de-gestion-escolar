<?php
/**
 * Controlador para obtener el listado de tareas asignadas a un estudiante específico.
 */

$tareas_del_estudiante = [];

if (!isset($_SESSION['estudiante_id']) || !filter_var($_SESSION['estudiante_id'], FILTER_VALIDATE_INT)) {
    // Si no hay un estudiante_id en la sesión, no se pueden cargar tareas.
    // Esto podría manejarse con un mensaje en la vista o simplemente no mostrar nada.
    // Por ahora, el array $tareas_del_estudiante quedará vacío.
    // Podrías añadir un mensaje de error a la sesión si es un error inesperado.
    // $_SESSION['mensaje'] = "No se pudo identificar al estudiante para cargar las tareas.";
    // $_SESSION['icono'] = "warning";
    return; // Salir del script si no hay estudiante_id
}

$id_estudiante_actual = $_SESSION['estudiante_id'];

try {
    $sql_tareas_estudiante = "SELECT 
                                t.id_tarea,
                                t.titulo,
                                m.nombre_materia,
                                DATE_FORMAT(t.fecha_entrega, '%d/%m/%Y %H:%i') as fecha_entrega_formato,
                                rt.estado as estado_entrega_estudiante,
                                rt.calificacion,
                                rt.id_registro as id_registro_tarea
                              FROM registro_tareas rt
                              INNER JOIN tareas t ON rt.tarea_id = t.id_tarea
                              INNER JOIN materias m ON t.materia_id = m.id_materia
                              WHERE rt.estudiante_id = :id_estudiante 
                                AND t.estado = '1' -- Solo tareas activas del docente
                                AND rt.estado_registro = '1' -- Solo asignaciones activas
                              ORDER BY t.fecha_entrega DESC, t.titulo ASC";
    
    $query_tareas_estudiante = $pdo->prepare($sql_tareas_estudiante);
    $query_tareas_estudiante->bindParam(':id_estudiante', $id_estudiante_actual, PDO::PARAM_INT);
    $query_tareas_estudiante->execute();
    $tareas_del_estudiante = $query_tareas_estudiante->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error al cargar tareas del estudiante: " . $e->getMessage());
    // Podrías establecer un mensaje de error para la vista si lo deseas
    // $_SESSION['mensaje'] = "Ocurrió un error al cargar tus tareas.";
    // $_SESSION['icono'] = "error";
}
?>