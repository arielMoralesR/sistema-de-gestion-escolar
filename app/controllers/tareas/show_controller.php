<?php
/**
 * Controlador para mostrar los detalles de una tarea específica y los estudiantes asignados.
 */

// config.php ya incluye session_start() y la conexión $pdo

$tarea_data = null;
$estudiantes_asignados = [];
$estadisticas_tarea = [
    'total_asignados' => 0,
    'total_entregados' => 0,    // Tareas en estado 'entregado' o 'evaluado'
    'total_evaluados' => 0,     // Tareas en estado 'evaluado'
    // 'pendientes' => 0,       // Si necesitas estas otras estadísticas, puedes mantenerlas
    // 'no_entregados' => 0,
    'porcentaje_entregas' => 0.0 // Coincide con la vista
];

if (!isset($_SESSION['docente_id'])) {
    $_SESSION['mensaje'] = "Acceso no autorizado o sesión de docente no válida.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/login');
    exit;
}

if (!isset($_GET['id_tarea']) || !filter_var($_GET['id_tarea'], FILTER_VALIDATE_INT)) {
    $_SESSION['mensaje'] = "ID de tarea no válido.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/admin/tareas/');
    exit;
}

$id_tarea_get = $_GET['id_tarea'];
$docente_id_sesion = $_SESSION['docente_id'];

// Obtener datos de la tarea
$sql_tarea = "SELECT
                    t.id_tarea, t.titulo, t.descripcion,
                    DATE_FORMAT(t.fecha_asignacion, '%d/%m/%Y %H:%i') as fecha_asignacion_formato,
                    DATE_FORMAT(t.fecha_entrega, '%d/%m/%Y %H:%i') as fecha_entrega_formato,
                    t.estado as estado_tarea,
                    m.nombre_materia,
                    n.nivel as nombre_nivel, n.turno,
                    g.curso as nombre_grado, g.paralelo
               FROM tareas as t
               INNER JOIN materias as m ON t.materia_id = m.id_materia
               INNER JOIN niveles as n ON t.nivel_id = n.id_nivel
               INNER JOIN grados as g ON t.grado_id = g.id_grado
               WHERE t.id_tarea = :id_tarea AND t.docente_id = :docente_id";

$query_tarea = $pdo->prepare($sql_tarea);
$query_tarea->bindParam(':id_tarea', $id_tarea_get, PDO::PARAM_INT);
$query_tarea->bindParam(':docente_id', $docente_id_sesion, PDO::PARAM_INT);
$query_tarea->execute();
$tarea_data = $query_tarea->fetch(PDO::FETCH_ASSOC);

if (!$tarea_data) {
    $_SESSION['mensaje'] = "Tarea no encontrada o no tiene permiso para verla.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/admin/tareas/');
    exit;
}

// Obtener estudiantes asignados a esta tarea
$sql_estudiantes = $sql_estudiantes = "SELECT rt.id_registro as id_registro_tarea, 
p.nombres as nombres_estudiante, 
p.apellidos as apellidos_estudiante, 
rt.estado as estado_tarea_estudiante, 
DATE_FORMAT(rt.fecha_entrega, '%d/%m/%Y %H:%i') as fecha_entrega_estudiante_formato, 
rt.calificacion, 
rt.observaciones as observaciones_docente,
rt.archivo_entrega as archivo_entrega_estudiante
                    FROM registro_tareas as rt
                    INNER JOIN estudiantes as e ON rt.estudiante_id = e.id_estudiante
                    INNER JOIN personas as p ON e.persona_id = p.id_persona
                    WHERE rt.tarea_id = :id_tarea
                    ORDER BY p.apellidos, p.nombres";
$query_estudiantes = $pdo->prepare($sql_estudiantes);
$query_estudiantes->bindParam(':id_tarea', $id_tarea_get, PDO::PARAM_INT);
$query_estudiantes->execute();
$estudiantes_asignados = $query_estudiantes->fetchAll(PDO::FETCH_ASSOC);

// Calcular estadísticas de entrega
 // Calcular estadísticas - Asegúrate que el alias para rt.estado en $sql_estudiantes_tarea sea 'estado_tarea_estudiante'
 if (!empty($estudiantes_asignados)) {
    $estadisticas_tarea['total_asignados'] = count($estudiantes_asignados);
    
    // Reiniciar contadores específicos para este cálculo
    $estadisticas_tarea['total_entregados'] = 0;
    $estadisticas_tarea['total_evaluados'] = 0;

    foreach ($estudiantes_asignados as $estudiante) { // Usamos $estudiante como variable de iteración
        // Obtener el estado del estudiante, convertir a minúsculas y quitar espacios
        // Asegúrate que $estudiante['estado_tarea_estudiante'] es el índice correcto
        $estado_actual_db = isset($estudiante['estado_tarea_estudiante']) ? strtolower(trim($estudiante['estado_tarea_estudiante'])) : 'pendiente';

        // Cuenta como entregado si el estado es 'entregado' o 'evaluado'
        // Estos deben coincidir con los valores de tu ENUM: ('pendiente', 'entregado', 'evaluado', 'no_entregado')
        if (in_array($estado_actual_db, ['entregado', 'evaluado'])) {
            $estadisticas_tarea['total_entregados']++;
        }
        // Cuenta como evaluado si el estado es 'evaluado'
        if ($estado_actual_db == 'evaluado') {
            $estadisticas_tarea['total_evaluados']++;
        }
        // error_log("Procesando estudiante para stats: Estado DB = '$estado_actual_db', Total Entregados ahora = {$estadisticas_tarea['total_entregados']}, Total Evaluados ahora = {$estadisticas_tarea['total_evaluados']}");
    }
    if ($estadisticas_tarea['total_asignados'] > 0) {
        $estadisticas_tarea['porcentaje_entregas'] = ($estadisticas_tarea['total_entregados'] / $estadisticas_tarea['total_asignados']) * 100;
        
     }
 }
?>
