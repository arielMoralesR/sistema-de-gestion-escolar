<?php
$sql_estudiantes = "SELECT 
    est.id_estudiante, est.persona_id, est.nivel_id, est.grado_id, est.rude, est.fyh_creacion AS fyh_creacion_estudiante, est.estado AS estado_estudiante,
    per.id_persona, per.usuario_id, per.nombres, per.apellidos, per.ci, per.fecha_nacimiento, per.celular, per.direccion, per.profesion, per.estado AS estado_persona,
    usu.id_usuario AS id_usuario_estudiante, usu.rol_id AS rol_id_estudiante, usu.email AS email_estudiante, usu.estado AS estado_usuario_estudiante,
    rol.nombre_rol, 
    niv.nivel, 
    niv.turno, 
    gra.curso, 
    gra.paralelo,

    pp.id_ppff, pp.ocupacion_ppff, pp.ref_nombre, pp.ref_parentesco, pp.ref_celular,
    
    per_pp.id_persona AS id_persona_ppff,
    per_pp.nombres AS nombres_persona_ppff,
    per_pp.apellidos AS apellidos_persona_ppff,
    per_pp.ci AS ci_persona_ppff,
    per_pp.celular AS celular_persona_ppff,    
    usu_pp.id_usuario AS id_usuario_ppff,
    usu_pp.email AS email_ppff,
    
    ep.parentesco

FROM estudiantes AS est
INNER JOIN personas AS per ON per.id_persona = est.persona_id
LEFT JOIN usuarios AS usu ON usu.id_usuario = per.usuario_id
LEFT JOIN roles AS rol ON rol.id_rol = usu.rol_id
INNER JOIN niveles AS niv ON niv.id_nivel = est.nivel_id
INNER JOIN grados AS gra ON gra.id_grado = est.grado_id

LEFT JOIN estudiante_ppff AS ep ON ep.estudiante_id = est.id_estudiante AND ep.estado = '1'
LEFT JOIN ppffs AS pp ON pp.id_ppff = ep.ppff_id AND pp.estado = '1'
LEFT JOIN personas AS per_pp ON per_pp.id_persona = pp.persona_id AND per_pp.estado = '1'
LEFT JOIN usuarios AS usu_pp ON usu_pp.id_usuario = per_pp.usuario_id AND usu_pp.estado = '1'

WHERE est.id_estudiante = :id_estudiante AND est.estado = '1' AND per.estado = '1'";

$query_estudiantes = $pdo->prepare($sql_estudiantes);
$query_estudiantes->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
$query_estudiantes->execute();
$estudiantes = $query_estudiantes->fetchAll(PDO::FETCH_ASSOC); // Should be fetch() if expecting one student

foreach ($estudiantes as $estudiante) {
    $id_usuario_estudiante = $estudiante['id_usuario_estudiante']; // Renamed for clarity
    $id_persona = $estudiante['id_persona'];
    $id_estudiante = $estudiante['id_estudiante'];
    $id_ppff = $estudiante['id_ppff'];
    $rol_id_estudiante = $estudiante['rol_id_estudiante']; // Renamed for clarity
    $id_persona_ppff = $estudiante['id_persona_ppff']; // For parent's persona record
    $nombre_rol = $estudiante['nombre_rol'];
    $nombres = $estudiante['nombres'];
    $apellidos = $estudiante['apellidos'];
    $ci = $estudiante['ci'];
    $fecha_nacimiento = $estudiante['fecha_nacimiento'];
    $celular = $estudiante['celular'];
    $email_estudiante = $estudiante['email_estudiante']; // Renamed for clarity
    $direccion = $estudiante['direccion'];
    $nivel_id = $estudiante['nivel_id'];
    $nivel = $estudiante['nivel'];
    $turno = $estudiante['turno'];
    $grado_id = $estudiante['grado_id'];
    $curso = $estudiante['curso'];
    $paralelo = $estudiante['paralelo'];
    $rude = $estudiante['rude'];
    // Parent's user details
    $id_usuario_ppff = $estudiante['id_usuario_ppff'];
    $email_ppff = $estudiante['email_ppff'];
    // Parent's details from their Persona record
    $nombres_persona_ppff = $estudiante['nombres_persona_ppff'];
    $apellidos_persona_ppff = $estudiante['apellidos_persona_ppff'];
    $ci_ppff = $estudiante['ci_persona_ppff']; // Used for parent's CI input
    $celular_ppff = $estudiante['celular_persona_ppff']; // Used for parent's Celular input
    // Parent's details from PPFFs table
    $ocupacion_ppff = $estudiante['ocupacion_ppff'];
    $ref_nombre = $estudiante['ref_nombre'];
    $ref_parentesco = $estudiante['ref_parentesco'];
    $ref_celular = $estudiante['ref_celular'];
    $fyh_creacion = $estudiante['fyh_creacion_estudiante']; // Clarify which fyh_creacion
    $estado = $estudiante['estado_estudiante']; // Clarify which estado
}