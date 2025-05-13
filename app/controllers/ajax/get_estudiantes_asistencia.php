<?php
include ('../../../app/config.php');

header('Content-Type: application/json');

$response = ['estudiantes' => [], 'error' => null];

if (isset($_GET['grado_id']) && filter_var($_GET['grado_id'], FILTER_VALIDATE_INT) && isset($_GET['fecha'])) {
    $grado_id = $_GET['grado_id'];
    $fecha = $_GET['fecha']; // Validar formato de fecha si es necesario

    // Validar formato de fecha YYYY-MM-DD
    if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fecha)) {
        $response['error'] = "Formato de fecha no válido.";
        echo json_encode($response);
        exit;
    }

    try {
        $sql_estudiantes = "SELECT 
                                e.id_estudiante, 
                                p.nombres, 
                                p.apellidos,
                                ae.id_asistencia as id_asistencia_existente,
                                ae.estado_asistencia as estado_asistencia_actual,
                                ae.observaciones as observaciones_actuales
                            FROM estudiantes as e
                            INNER JOIN personas as p ON e.persona_id = p.id_persona
                            LEFT JOIN asistencia_estudiantes as ae ON e.id_estudiante = ae.estudiante_id AND ae.fecha = :fecha
                            WHERE e.grado_id = :grado_id AND e.estado = '1' AND p.estado = '1'
                            ORDER BY p.apellidos, p.nombres";
        
        $query_estudiantes = $pdo->prepare($sql_estudiantes);
        $query_estudiantes->bindParam(':grado_id', $grado_id, PDO::PARAM_INT);
        $query_estudiantes->bindParam(':fecha', $fecha);
        $query_estudiantes->execute();
        $estudiantes_result = $query_estudiantes->fetchAll(PDO::FETCH_ASSOC);

        foreach ($estudiantes_result as $estudiante) {
            $response['estudiantes'][] = [
                'id_estudiante' => $estudiante['id_estudiante'],
                'nombre_completo' => htmlspecialchars($estudiante['apellidos'] . ', ' . $estudiante['nombres']),
                'id_asistencia_existente' => $estudiante['id_asistencia_existente'],
                'estado_asistencia_actual' => $estudiante['estado_asistencia_actual'] ?? 'presente', // Default a 'presente' si no hay registro
                'observaciones_actuales' => htmlspecialchars($estudiante['observaciones_actuales'] ?? '')
            ];
        }
    } catch (PDOException $e) {
        $response['error'] = "Error de base de datos: " . $e->getMessage();
    }
} else {
    $response['error'] = "Parámetros incompletos (grado_id o fecha).";
}

echo json_encode($response);
exit;
?>