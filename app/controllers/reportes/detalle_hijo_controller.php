<?php
/**
 * Controlador para mostrar los detalles y reportes de un hijo específico para el padre logueado.
 */

$estudiante_seleccionado_data = null;
$reporte_asistencia = [];
$reporte_conducta = [];
$reporte_tareas = [];
$nombre_padre_logueado = $_SESSION['nombre_sesion_usuario'] ?? "Padre/Madre"; // Asumiendo que parte1.php ya la define

// Definir el ID del rol para Padres/Madres de Familia (ajusta según tu BD)
if (!defined('ROL_PADRE_ID')) { // Evitar redefinir si ya está en otro controlador incluido
    define('ROL_PADRE_ID', 9); // ID del rol PADRE DE FAMILIA
}

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != ROL_PADRE_ID) {
    $_SESSION['mensaje'] = "Acceso restringido. Debe iniciar sesión como padre/madre de familia.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/login');
    exit;
}

if (!isset($_GET['estudiante_id']) || !filter_var($_GET['estudiante_id'], FILTER_VALIDATE_INT)) {
    $_SESSION['mensaje'] = "ID de estudiante no válido.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/admin/reportes/'); // Volver a la lista de hijos
    exit;
}

$id_estudiante_get = $_GET['estudiante_id'];
$usuario_id_sesion_padre = $_SESSION['usuario_id'];

try {
    // 1. Verificar que el padre logueado tenga relación con este estudiante
    $sql_verificar_relacion = "SELECT ep.estudiante_id 
                               FROM estudiante_ppff as ep
                               INNER JOIN ppffs as pp ON ep.ppff_id = pp.id_ppff
                               INNER JOIN personas as per_ppff ON pp.persona_id = per_ppff.id_persona
                               WHERE per_ppff.usuario_id = :usuario_id_padre AND ep.estudiante_id = :id_estudiante_get AND ep.estado = '1'";
    $query_verificar = $pdo->prepare($sql_verificar_relacion);
    $query_verificar->bindParam(':usuario_id_padre', $usuario_id_sesion_padre, PDO::PARAM_INT);
    $query_verificar->bindParam(':id_estudiante_get', $id_estudiante_get, PDO::PARAM_INT);
    $query_verificar->execute();

    if ($query_verificar->rowCount() == 0) {
        $_SESSION['mensaje'] = "No tiene permiso para ver los reportes de este estudiante.";
        $_SESSION['icono'] = "warning";
        header('Location: ' . APP_URL . '/admin/reportes/');
        exit;
    }

    // 2. Obtener datos del estudiante seleccionado
    $sql_estudiante = "SELECT 
                            p.nombres, p.apellidos,
                            n.nivel as nombre_nivel, n.turno,
                            g.curso as nombre_grado, g.paralelo
                       FROM estudiantes as e
                       INNER JOIN personas as p ON e.persona_id = p.id_persona
                       INNER JOIN niveles as n ON e.nivel_id = n.id_nivel
                       INNER JOIN grados as g ON e.grado_id = g.id_grado
                       WHERE e.id_estudiante = :id_estudiante AND e.estado = '1' AND p.estado = '1'";
    $query_estudiante_data = $pdo->prepare($sql_estudiante);
    $query_estudiante_data->bindParam(':id_estudiante', $id_estudiante_get, PDO::PARAM_INT);
    $query_estudiante_data->execute();
    $estudiante_seleccionado_data = $query_estudiante_data->fetch(PDO::FETCH_ASSOC);

    if (!$estudiante_seleccionado_data) {
        $_SESSION['mensaje'] = "No se encontró información del estudiante seleccionado.";
        $_SESSION['icono'] = "error";
        header('Location: ' . APP_URL . '/admin/reportes/');
        exit;
    }

    // 3. Obtener reporte de asistencia
    $sql_asistencia = "SELECT DATE_FORMAT(fecha, '%d/%m/%Y') as fecha_formato, estado_asistencia, observaciones 
                       FROM asistencia_estudiantes 
                       WHERE estudiante_id = :id_estudiante AND estado = '1'
                       ORDER BY fecha DESC"; // LIMIT 30 para los últimos 30 registros, por ejemplo
    $query_asistencia = $pdo->prepare($sql_asistencia);
    $query_asistencia->bindParam(':id_estudiante', $id_estudiante_get, PDO::PARAM_INT);
    $query_asistencia->execute();
    $reporte_asistencia = $query_asistencia->fetchAll(PDO::FETCH_ASSOC);

    // 4. Obtener reporte de conducta
    $sql_conducta = "SELECT 
                        DATE_FORMAT(dr.fecha_hora_suceso, '%d/%m/%Y %H:%i') as fecha_suceso_formato,
                        dti.nombre_tipo, dti.naturaleza, dti.gravedad_nivel,
                        dr.descripcion_detallada, dr.medidas_tomadas
                     FROM disciplina_registros as dr
                     INNER JOIN disciplina_tipos_incidentes as dti ON dr.tipo_incidente_id = dti.id_tipo_incidente
                     WHERE dr.estudiante_id = :id_estudiante AND dr.estado = '1'
                     ORDER BY dr.fecha_hora_suceso DESC";
    $query_conducta = $pdo->prepare($sql_conducta);
    $query_conducta->bindParam(':id_estudiante', $id_estudiante_get, PDO::PARAM_INT);
    $query_conducta->execute();
    $reporte_conducta = $query_conducta->fetchAll(PDO::FETCH_ASSOC);

    // 5. Obtener reporte de tareas
    $sql_tareas = "SELECT 
                        t.id_tarea, -- <<< AÑADIR ESTA LÍNEA
                        t.titulo AS titulo_tarea,
                        m.nombre_materia,
                        DATE_FORMAT(t.fecha_asignacion, '%d/%m/%Y') as fecha_asignacion_tarea,
                        DATE_FORMAT(t.fecha_entrega, '%d/%m/%Y') as fecha_limite_tarea,
                        rt.estado as estado_entrega_estudiante,
                        DATE_FORMAT(rt.fecha_entrega, '%d/%m/%Y %H:%i') as fecha_entrega_estudiante,
                        rt.calificacion,
                        rt.observaciones as observaciones_tarea_estudiante
                   FROM registro_tareas as rt
                   INNER JOIN tareas as t ON rt.tarea_id = t.id_tarea
                   INNER JOIN materias as m ON t.materia_id = m.id_materia
                   WHERE rt.estudiante_id = :id_estudiante AND t.estado = '1' -- AND rt.estado_registro = '1' si tienes esa columna
                   ORDER BY t.fecha_asignacion DESC";
    $query_tareas = $pdo->prepare($sql_tareas);
    $query_tareas->bindParam(':id_estudiante', $id_estudiante_get, PDO::PARAM_INT);
    $query_tareas->execute();
    $reporte_tareas = $query_tareas->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error en detalle_hijo_controller: " . $e->getMessage());
    $_SESSION['mensaje'] = "Error al cargar los reportes del estudiante: " . htmlspecialchars($e->getMessage());
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/admin/reportes/');
    exit;
}
?>