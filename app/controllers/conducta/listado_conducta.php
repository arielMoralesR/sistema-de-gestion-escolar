<?php
/**
 * Controlador para listar los registros de conducta.
 */

// config.php ya incluye session_start() y la conexión $pdo

// Opcional: Verificar permisos si es necesario.
// Por ejemplo, un docente podría ver solo los que él reportó o de sus estudiantes,
// mientras un administrador o director vería todos.
// Para esta versión inicial, listaremos todos.
/*
if (!isset($_SESSION['usuario_id'])) { // O una verificación de rol más específica
    $_SESSION['mensaje'] = "Acceso no autorizado.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/login');
    exit;
}
*/

$sql_registros_conducta = "SELECT
                                dr.id_registro_disciplina,
                                CONCAT(p_est.nombres, ' ', p_est.apellidos) as nombre_estudiante,
                                dti.nombre_tipo as tipo_incidente,
                                dti.naturaleza as naturaleza_incidente,
                                DATE_FORMAT(dr.fecha_hora_suceso, '%d/%m/%Y %H:%i') as fecha_suceso_formato,
                                IFNULL(CONCAT(p_rep.nombres, ' ', p_rep.apellidos), u_rep.email) as reportado_por,
                                dr.estado as estado_registro_conducta
                           FROM disciplina_registros as dr
                           INNER JOIN estudiantes as e ON dr.estudiante_id = e.id_estudiante
                           INNER JOIN personas as p_est ON e.persona_id = p_est.id_persona
                           INNER JOIN disciplina_tipos_incidentes as dti ON dr.tipo_incidente_id = dti.id_tipo_incidente
                           LEFT JOIN usuarios as u_rep ON dr.reportado_por_usuario_id = u_rep.id_usuario
                           LEFT JOIN personas as p_rep ON u_rep.id_usuario = p_rep.usuario_id 
                           ORDER BY dr.fecha_hora_suceso DESC";

$query_registros_conducta = $pdo->prepare($sql_registros_conducta);
$query_registros_conducta->execute();
$registros_conducta = $query_registros_conducta->fetchAll(PDO::FETCH_ASSOC);
?>