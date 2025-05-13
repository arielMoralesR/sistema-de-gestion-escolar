<?php
/**
 * Controlador para obtener los datos de una tarea para el formulario de edición.
 */

$tarea_a_editar = null;
$materias_docente = [];
// $niveles_todos = []; // No los cargaremos si Nivel/Grado no son editables
// $grados_todos = [];  // No los cargaremos si Nivel/Grado no son editables

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

// Obtener datos de la tarea a editar
$sql_tarea_edit = "SELECT
                        t.id_tarea, t.titulo, t.descripcion, t.materia_id, t.nivel_id, t.grado_id,
                        t.fecha_entrega, /* Necesitamos la fecha en formato Y-m-d\TH:i para datetime-local */
                        t.estado as estado_tarea,
                        m.nombre_materia,
                        n.nivel as nombre_nivel, n.turno,
                        g.curso as nombre_grado, g.paralelo
                   FROM tareas as t
                   INNER JOIN materias as m ON t.materia_id = m.id_materia
                   INNER JOIN niveles as n ON t.nivel_id = n.id_nivel
                   INNER JOIN grados as g ON t.grado_id = g.id_grado
                   WHERE t.id_tarea = :id_tarea AND t.docente_id = :docente_id";

$query_tarea_edit = $pdo->prepare($sql_tarea_edit);
$query_tarea_edit->bindParam(':id_tarea', $id_tarea_get, PDO::PARAM_INT);
$query_tarea_edit->bindParam(':docente_id', $docente_id_sesion, PDO::PARAM_INT);
$query_tarea_edit->execute();
$tarea_a_editar = $query_tarea_edit->fetch(PDO::FETCH_ASSOC);

if (!$tarea_a_editar) {
    $_SESSION['mensaje'] = "Tarea no encontrada o no tiene permiso para editarla.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/admin/tareas/');
    exit;
}

// Convertir fecha_entrega al formato YYYY-MM-DDTHH:MM para el input datetime-local
if ($tarea_a_editar && !empty($tarea_a_editar['fecha_entrega'])) {
    $datetime = new DateTime($tarea_a_editar['fecha_entrega']);
    $tarea_a_editar['fecha_entrega_formato_input'] = $datetime->format('Y-m-d\TH:i');
} else {
    $tarea_a_editar['fecha_entrega_formato_input'] = '';
}


// Obtener materias del docente para el dropdown
$sql_materias_docente = "SELECT m.id_materia, m.nombre_materia
                         FROM docente_materias dm
                         JOIN materias m ON dm.materia_id = m.id_materia
                         WHERE dm.docente_id = :docente_id AND dm.estado = '1'";
$query_materias_docente = $pdo->prepare($sql_materias_docente);
$query_materias_docente->bindParam(':docente_id', $docente_id_sesion, PDO::PARAM_INT);
$query_materias_docente->execute();
$materias_docente = $query_materias_docente->fetchAll(PDO::FETCH_ASSOC);

// Nota: Nivel y Grado no se cargarán para edición en esta versión para simplificar.
// Si se hicieran editables, aquí se cargarían todos los niveles y grados.
?>