<?php
/**
 * Controlador para listar las tareas asignadas por un docente específico.
 */

// config.php ya incluye session_start() y la conexión $pdo

if (!isset($_SESSION['docente_id'])) {
    // Si no hay un docente_id en la sesión, no se pueden listar tareas.
    // Esto podría manejarse con un mensaje o redirigiendo,
    // pero la página que incluye este script (index.php de tareas)
    // ya debería haber verificado la sesión del docente.
    // Si se llega aquí sin docente_id, es un error de flujo.
    $_SESSION['mensaje'] = "Acceso no autorizado o sesión de docente no válida.";
    $_SESSION['icono'] = "error";
    // Podríamos redirigir, pero es mejor que la página principal lo maneje.
    // header('Location: ' . APP_URL . '/login');
    // exit;
    $tareas_del_docente = []; // Devolver un array vacío para evitar errores en la vista
} else {
    $docente_id_sesion = $_SESSION['docente_id'];

    $sql_tareas = "SELECT
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
                   WHERE t.docente_id = :docente_id
                   ORDER BY t.fecha_asignacion DESC";

    $query_tareas = $pdo->prepare($sql_tareas);
    $query_tareas->bindParam(':docente_id', $docente_id_sesion, PDO::PARAM_INT);
    $query_tareas->execute();
    $tareas_del_docente = $query_tareas->fetchAll(PDO::FETCH_ASSOC);
}
?>