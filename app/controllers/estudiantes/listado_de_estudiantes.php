<?php
// Consulta para listar todos los estudiantes activos y su información relevante
$sql_estudiantes = "SELECT 
                        est.id_estudiante,
                        per.nombres as nombres_estudiante,
                        per.apellidos as apellidos_estudiante,
                        per.ci as ci_estudiante,
                        est.rude,
                        niv.nivel as nombre_nivel,
                        niv.turno as turno_nivel,
                        gra.curso as nombre_grado,
                        gra.paralelo as paralelo_grado,
                        est.estado as estado_estudiante
                    FROM estudiantes as est
                    INNER JOIN personas as per ON est.persona_id = per.id_persona
                    INNER JOIN niveles as niv ON est.nivel_id = niv.id_nivel
                    INNER JOIN grados as gra ON est.grado_id = gra.id_grado
                    WHERE est.estado = '1' AND per.estado = '1' -- Aseguramos que la persona también esté activa
                    ORDER BY per.apellidos, per.nombres";
$query_estudiantes = $pdo->prepare($sql_estudiantes);
$query_estudiantes->execute();
$estudiantes = $query_estudiantes->fetchAll(PDO::FETCH_ASSOC);