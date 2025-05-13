<?php
/**
 * Controlador para mostrar los detalles de un estudiante específico.
 */

// config.php ya incluye session_start() y la conexión $pdo

$estudiante_data = null;
$padres_del_estudiante = [];

if (!isset($_GET['id_estudiante']) || !filter_var($_GET['id_estudiante'], FILTER_VALIDATE_INT)) {
    $_SESSION['mensaje'] = "ID de estudiante no válido.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/admin/estudiantes/');
    exit;
}

$id_estudiante_get = $_GET['id_estudiante'];

try {
    // Obtener datos del estudiante y su información académica
    $sql_estudiante = "SELECT 
                            est.id_estudiante, est.rude, est.estado as estado_estudiante,
                            per.nombres as nombres_estudiante,
                            per.apellidos as apellidos_estudiante,
                            per.ci as ci_estudiante,
                            per.fecha_nacimiento as fecha_nac_estudiante,
                            per.celular as celular_estudiante,
                            per.direccion as direccion_estudiante,
                            per.profesion as profesion_estudiante, /* Aunque 'profesion' para estudiante es raro, lo mantenemos si está en la tabla personas */
                            usu.email as email_estudiante, /* Si tiene usuario */
                            niv.nivel as nombre_nivel,
                            niv.turno as turno_nivel,
                            gra.curso as nombre_grado,
                            gra.paralelo as paralelo_grado
                       FROM estudiantes as est
                       INNER JOIN personas as per ON est.persona_id = per.id_persona
                       INNER JOIN niveles as niv ON est.nivel_id = niv.id_nivel
                       INNER JOIN grados as gra ON est.grado_id = gra.id_grado
                       LEFT JOIN usuarios as usu ON per.usuario_id = usu.id_usuario AND usu.estado = '1' /* Para obtener email si el estudiante tiene usuario */
                       WHERE est.id_estudiante = :id_estudiante AND est.estado = '1' AND per.estado = '1'";
    
    $query_estudiante = $pdo->prepare($sql_estudiante);
    $query_estudiante->bindParam(':id_estudiante', $id_estudiante_get, PDO::PARAM_INT);
    $query_estudiante->execute();
    $estudiante_data = $query_estudiante->fetch(PDO::FETCH_ASSOC);

    if (!$estudiante_data) {
        $_SESSION['mensaje'] = "Estudiante no encontrado o inactivo.";
        $_SESSION['icono'] = "error";
        header('Location: ' . APP_URL . '/admin/estudiantes/');
        exit;
    }

    // Obtener datos de los padres/madres de familia asociados al estudiante
    $sql_padres = "SELECT
                        pp.id_ppff,
                        per_ppff.nombres as nombres_ppff, /* Nombres del padre/madre desde la tabla personas */
                        per_ppff.apellidos as apellidos_ppff, /* Apellidos del padre/madre desde la tabla personas */
                        pp.ci_ppff,
                        pp.celular_ppff,
                        pp.ocupacion_ppff,
                        usu_ppff.email as email_ppff, /* Email del usuario del padre/madre */
                        ep.parentesco
                   FROM estudiante_ppff as ep
                   INNER JOIN ppffs as pp ON ep.ppff_id = pp.id_ppff
                   INNER JOIN personas as per_ppff ON pp.persona_id = per_ppff.id_persona
                   LEFT JOIN usuarios as usu_ppff ON per_ppff.usuario_id = usu_ppff.id_usuario /* Unimos con usuarios para obtener el email */
                   WHERE ep.estudiante_id = :id_estudiante AND ep.estado = '1' AND pp.estado = '1' AND per_ppff.estado = '1' 
                         AND (usu_ppff.estado = '1' OR usu_ppff.id_usuario IS NULL)"; /* Aseguramos que el usuario del padre esté activo si existe */
    
    $query_padres = $pdo->prepare($sql_padres);
    $query_padres->bindParam(':id_estudiante', $id_estudiante_get, PDO::PARAM_INT);
    $query_padres->execute();
    $padres_del_estudiante = $query_padres->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error en show_controller de estudiantes: " . $e->getMessage());
    $_SESSION['mensaje'] = "Ocurrió un error al cargar los datos del estudiante.";
    // Mostrar el error SQL real para depuración (¡NO USAR EN PRODUCCIÓN!)
    //$_SESSION['mensaje'] = "Error al cargar datos: " . htmlspecialchars($e->getMessage());
   
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/admin/estudiantes/');
    exit;
}
?>