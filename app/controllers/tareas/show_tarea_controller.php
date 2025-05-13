<?php
/**
 * Controlador para mostrar los detalles de una tarea específica y los estudiantes asignados.
 */

$tarea_data = null;
$estudiantes_asignados = [];
$estadisticas_tarea = [
    'total_asignados' => 0,
    'total_entregados' => 0,
    'total_evaluados' => 0,
    'porcentaje_entregas' => 0.0,
    'porcentaje_evaluados' => 0.0
];
$id_docente_sesion = $_SESSION['usuario_id'] ?? null; // Asumiendo que el ID del docente está en la sesión

if (!isset($_GET['id_tarea']) || !filter_var($_GET['id_tarea'], FILTER_VALIDATE_INT)) {
    $_SESSION['mensaje'] = "ID de tarea no válido.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/admin/tareas/'); // Ajusta la URL de redirección si es necesario
    exit;
}

$id_tarea_get = $_GET['id_tarea'];

try {
    // 1. Obtener detalles de la tarea
    $sql_tarea = "SELECT 
                        t.id_tarea, t.titulo, t.descripcion,
                        DATE_FORMAT(t.fecha_asignacion, '%d/%m/%Y') as fecha_asignacion_formato,
                        DATE_FORMAT(t.fecha_entrega, '%d/%m/%Y %H:%i') as fecha_entrega_formato,
                        m.nombre_materia,
                        g.curso as nombre_grado,  /* Ajustado para coincidir con la vista */
                        g.paralelo,               /* Ajustado para coincidir con la vista */
                        n.nivel as nombre_nivel,  /* Ajustado para coincidir con la vista */
                        n.turno,                  /* Ajustado para coincidir con la vista */
                        t.estado as estado_tarea, /* Añadido para el estado de la tarea */
                        t.docente_id 
                  FROM tareas as t
                  INNER JOIN materias as m ON t.materia_id = m.id_materia
                  INNER JOIN grados as g ON t.grado_id = g.id_grado
                  INNER JOIN niveles as n ON g.nivel_id = n.id_nivel
                  WHERE t.id_tarea = :id_tarea AND t.estado = '1'";
    
    $query_tarea = $pdo->prepare($sql_tarea);
    $query_tarea->bindParam(':id_tarea', $id_tarea_get, PDO::PARAM_INT);
    $query_tarea->execute();
    $tarea_data = $query_tarea->fetch(PDO::FETCH_ASSOC);

    if (!$tarea_data) {
        $_SESSION['mensaje'] = "Tarea no encontrada o inactiva.";
        $_SESSION['icono'] = "error";
        header('Location: ' . APP_URL . '/admin/tareas/'); // Ajusta la URL
        exit;
    }

    // Opcional: Verificar si el docente logueado es el que creó la tarea (si es necesario)
    // if ($id_docente_sesion != $tarea_data['docente_id']) {
    //     $_SESSION['mensaje'] = "No tiene permiso para ver esta tarea.";
    //     $_SESSION['icono'] = "warning";
    //     header('Location: ' . APP_URL . '/admin/tareas/');
    //     exit;
    // }

    // 2. Obtener estudiantes asignados a esta tarea desde registro_tareas
    $sql_estudiantes_tarea = "SELECT 
                                    rt.id_registro as id_registro_tarea, /* Reemplaza NOMBRE_CORRECTO_DE_TU_COLUMNA */
                                    rt.estado as estado_tarea_estudiante, 
                                    rt.calificacion, 
                                    rt.observaciones as observaciones_docente, 
                                    
                                    DATE_FORMAT(rt.fecha_entrega, '%d/%m/%Y %H:%i') as fecha_entrega_estudiante_formato,
                                    e.id_estudiante,
                                    p.nombres as nombres_estudiante,
                                    p.apellidos as apellidos_estudiante
                              FROM registro_tareas rt
                              INNER JOIN estudiantes e ON rt.estudiante_id = e.id_estudiante
                              INNER JOIN personas p ON e.persona_id = p.id_persona
                              WHERE rt.tarea_id = :id_tarea 
                                AND e.estado = '1' /* Estudiante activo */
                                AND p.estado = '1' /* Persona activa */
                                AND rt.estado_registro = '1' /* Registro de tarea activo */
                              ORDER BY p.apellidos, p.nombres";
    
    $query_estudiantes_tarea = $pdo->prepare($sql_estudiantes_tarea);
    $query_estudiantes_tarea->bindParam(':id_tarea', $id_tarea_get, PDO::PARAM_INT);
    $query_estudiantes_tarea->execute();
    $estudiantes_asignados = $query_estudiantes_tarea->fetchAll(PDO::FETCH_ASSOC);

    // Calcular estadísticas
    if (!empty($estudiantes_asignados)) {
        $estadisticas_tarea['total_asignados'] = count($estudiantes_asignados);
        // Asegurarse de reiniciar los contadores antes del bucle
        $estadisticas_tarea['total_entregados'] = 0;
        $estadisticas_tarea['total_evaluados'] = 0;
        $estadisticas_tarea['porcentaje_entregas'] = 0.0;
        $estadisticas_tarea['porcentaje_evaluados'] = 0.0;

        foreach ($estudiantes_asignados as $estudiante_tarea) {
            // Obtener el estado, convertir a minúsculas y quitar espacios
            $estado_actual_db = isset($estudiante_tarea['estado_tarea_estudiante']) ? strtolower(trim($estudiante_tarea['estado_tarea_estudiante'])) : 'pendiente';

            // Cuenta como entregado si el estado es 'entregado' o 'evaluado'
            // Estos deben coincidir con los valores de tu ENUM: ('pendiente', 'entregado', 'evaluado', 'no_entregado')
            if (in_array($estado_actual_db, ['entregado', 'evaluado'])) {
                $estadisticas_tarea['total_entregados']++;
            }
            // Cuenta como evaluado si el estado es 'evaluado'
            if ($estado_actual_db == 'evaluado') {
                $estadisticas_tarea['total_evaluados']++;
            }
            // Descomenta la siguiente línea para depurar los valores que se están procesando:
            // error_log("[SHOW_TAREA_STATS] Registro ID: {$estudiante_tarea['id_registro_tarea']}, Estado Leído: '$estado_actual_db', Total Entregados: {$estadisticas_tarea['total_entregados']}, Total Evaluados: {$estadisticas_tarea['total_evaluados']}");
        }
        if ($estadisticas_tarea['total_asignados'] > 0) {
            $estadisticas_tarea['porcentaje_entregas'] = ($estadisticas_tarea['total_entregados'] / $estadisticas_tarea['total_asignados']) * 100;
            $estadisticas_tarea['porcentaje_evaluados'] = ($estadisticas_tarea['total_evaluados'] / $estadisticas_tarea['total_asignados']) * 100;
        }
    }

} catch (PDOException $e) {
    error_log("Error en show_tarea_controller: " . $e->getMessage());
    //$_SESSION['mensaje'] = "Ocurrió un error al cargar los datos de la tarea.";
    // Para depuración (NO USAR EN PRODUCCIÓN):
     $_SESSION['mensaje'] = "Error al cargar datos: " . htmlspecialchars($e->getMessage());
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/admin/tareas/'); // Ajusta la URL
    exit;
}
?>