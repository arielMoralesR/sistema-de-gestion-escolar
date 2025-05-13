<?php
include ('../../../app/config.php');

header('Content-Type: application/json');

$estudiantes_data = [];

if (isset($_GET['grado_id']) && filter_var($_GET['grado_id'], FILTER_VALIDATE_INT)) {
    $grado_id = $_GET['grado_id'];

    $sql_estudiantes = "SELECT e.id_estudiante, p.nombres, p.apellidos 
                        FROM estudiantes as e
                        INNER JOIN personas as p ON e.persona_id = p.id_persona
                        WHERE e.grado_id = :grado_id AND e.estado = '1' AND p.estado = '1'
                        ORDER BY p.apellidos, p.nombres";
    
    $query_estudiantes = $pdo->prepare($sql_estudiantes);
    $query_estudiantes->bindParam(':grado_id', $grado_id, PDO::PARAM_INT);
    $query_estudiantes->execute();
    $estudiantes_result = $query_estudiantes->fetchAll(PDO::FETCH_ASSOC);

    foreach ($estudiantes_result as $estudiante) {
        $estudiantes_data[] = [
            'id' => $estudiante['id_estudiante'],
            'nombre_completo' => htmlspecialchars($estudiante['apellidos'] . ', ' . $estudiante['nombres'])
        ];
    }
}

echo json_encode($estudiantes_data);
exit;
?>