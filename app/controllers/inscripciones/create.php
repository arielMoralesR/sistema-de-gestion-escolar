<?php
include ('../../../app/config.php');

/// Datos del estudiante
// $rol_id =$_POST['rol_id']; // Eliminado, se gestionará internamente
$nombres =$_POST['nombres'];
$apellidos =$_POST['apellidos'];
$ci =$_POST['ci'];
$fecha_nacimiento =$_POST['fecha_nacimiento'];
$celular =$_POST['celular'];
$email_estudiante =$_POST['email_estudiante'];
$direccion =$_POST['direccion'];
$nivel_id =$_POST['nivel_id'];
$grado_id =$_POST['grado_id'];
$rude =$_POST['rude'];
$crear_usuario_est = $_POST['crear_usuario_estudiante'];
/// Datos del padre
$apellidos_ppff = $_POST['apellidos_ppff'];
$nombres_ppff = $_POST['nombres_ppff'];
$email_ppff = $_POST['email_ppff'];
$fecha_nacimiento_ppff = $_POST['fecha_nacimiento_ppff'];
$ci_ppff =$_POST['ci_ppff'];
$celular_ppff =$_POST['celular_ppff'];
$ocupacion_ppff =$_POST['ocupacion_ppff'];
$direccion_ppff = $_POST['direccion_ppff']; // Nueva dirección para el padre
$ref_nombre =$_POST['ref_nombre'];
$ref_parentesco =$_POST['ref_parentesco'];
$ref_celular =$_POST['ref_celular'];
$profesion ="ESTUDIANTE";

$fechaHora = date('Y-m-d H:i:s'); // Asegúrate de que esta variable esté definida

// session_start(); // Asegúrate de que la sesión esté iniciada, usualmente en config.php

$estado_de_registro = '1'; // Para nuevos registros activos
// Si se solicita crear usuario para estudiante, validar campos
if ($crear_usuario_est == 1) {
    if (empty($email_estudiante) || empty($ci) ) {
        //session_start();
        //$_SESSION['mensaje'] = "Debe completar todos los campos para crear usuario del estudiante";
        // session_start(); // Asumimos que config.php ya lo hace
        $_SESSION['mensaje'] = "Debe completar el Email y CI del estudiante para crear su usuario.";
        $_SESSION['icono'] = "error";
        //header('Location:'.APP_URL."/admin/estudiantes/create.php");
        header('Location:'.APP_URL."/admin/inscripciones/create.php"); // Corregida la URL
        exit();
    }
}
$pdo->beginTransaction();
///////// INSERTAR A LA TABLA USUARIOS al padre
/*$password = password_hash($ci_ppff, PASSWORD_DEFAULT);
$rol_id_ppff = 9; // Asumiendo que 9 es el ID del rol "PADRE DE FAMILIA"
    $sentencia = $pdo->prepare('INSERT INTO usuarios
                (rol_id,email,password, fyh_creacion, estado)
                VALUES ( :rol_id,:email,:password,:fyh_creacion,:estado)');*/

                try {
                    // --- OBTENER IDs DE ROLES ---
                    // Rol PADRE DE FAMILIA
                    $stmt_rol_ppff = $pdo->prepare("SELECT id_rol FROM roles WHERE nombre_rol = 'PADRE DE FAMILIA' LIMIT 1");
                    $stmt_rol_ppff->execute();
                    $rol_ppff_data = $stmt_rol_ppff->fetch(PDO::FETCH_ASSOC);
                    if (!$rol_ppff_data) {
                        throw new Exception("Error crítico: El rol 'PADRE DE FAMILIA' no se encuentra en la base de datos.");
                    }
                    $rol_id_ppff_fijo = $rol_ppff_data['id_rol'];
                
                    // Rol ESTUDIANTE
                    $stmt_rol_est = $pdo->prepare("SELECT id_rol FROM roles WHERE nombre_rol = 'ESTUDIANTE' LIMIT 1");
                    $stmt_rol_est->execute();
                    $rol_est_data = $stmt_rol_est->fetch(PDO::FETCH_ASSOC);
                    if (!$rol_est_data) {
                        throw new Exception("Error crítico: El rol 'ESTUDIANTE' no se encuentra en la base de datos.");
                    }
                    $rol_id_estudiante_fijo = $rol_est_data['id_rol'];
                
                    // --- MANEJO DEL PADRE/MADRE DE FAMILIA (PPFF) ---
                    $id_usuario_ppff_final = null;
                    $id_persona_ppff_final = null;
                    $id_ppff_final = null;

                    // 1. Verificar si el usuario del PPFF ya existe por email
                    $stmt_check_ppff_user = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = :email");
                    $stmt_check_ppff_user->bindParam(':email', $email_ppff);
                    $stmt_check_ppff_user->execute();
                    $usuario_ppff_existente = $stmt_check_ppff_user->fetch(PDO::FETCH_ASSOC);

                    if ($usuario_ppff_existente) {
                        $id_usuario_ppff_final = $usuario_ppff_existente['id_usuario'];

                        // 2. Obtener id_persona e id_ppff del PPFF existente
                        $stmt_get_ppff_details = $pdo->prepare("
                            SELECT p.id_persona, pf.id_ppff 
                            FROM personas p 
                            JOIN ppffs pf ON p.id_persona = pf.persona_id 
                            WHERE p.usuario_id = :usuario_id
                        ");
                        $stmt_get_ppff_details->bindParam(':usuario_id', $id_usuario_ppff_final);
                        $stmt_get_ppff_details->execute();
                        $ppff_details = $stmt_get_ppff_details->fetch(PDO::FETCH_ASSOC);

                        if ($ppff_details) {
                            $id_persona_ppff_final = $ppff_details['id_persona'];
                            $id_ppff_final = $ppff_details['id_ppff'];
                            // Opcional: Actualizar datos del PPFF si es necesario
                            // Ejemplo:
                            // $update_persona_ppff = $pdo->prepare("UPDATE personas SET nombres = :nombres, apellidos = :apellidos, ci = :ci, fecha_nacimiento = :fn, celular = :cel, profesion = :prof, direccion = :dir WHERE id_persona = :id_p");
                            // $update_persona_ppff->execute([...]);
                            // $update_ppffs = $pdo->prepare("UPDATE ppffs SET nombres_apellidos_ppff = :nom_ape, ci_ppff = :ci, ... WHERE id_ppff = :id_ppff_reg");
                            // $update_ppffs->execute([...]);

                        } else {
                            throw new Exception("Error de consistencia: Usuario del padre/madre existe (ID: $id_usuario_ppff_final) pero no sus detalles en 'personas' o 'ppffs'.");
                        }
                    } else {
                        // El usuario del PPFF NO existe, procedemos a crearlo
                        $password_ppff = password_hash($ci_ppff, PASSWORD_DEFAULT);
                        $sentencia_usuario_ppff = $pdo->prepare('INSERT INTO usuarios
                                    (rol_id,email,password, fyh_creacion, estado)
                                    VALUES ( :rol_id,:email,:password,:fyh_creacion,:estado)');
                        $sentencia_usuario_ppff->bindParam(':rol_id',$rol_id_ppff_fijo);
                        $sentencia_usuario_ppff->bindParam(':email',$email_ppff);
                        $sentencia_usuario_ppff->bindParam(':password',$password_ppff);
                        $sentencia_usuario_ppff->bindParam(':fyh_creacion',$fechaHora);
                        $sentencia_usuario_ppff->bindParam(':estado',$estado_de_registro);
                        $sentencia_usuario_ppff->execute();
                        $id_usuario_ppff_final = $pdo->lastInsertId();

                        if (empty(trim($direccion_ppff))) {
                            $direccion_ppff = $direccion; // Usar dirección del estudiante si la del padre está vacía
                        }

                        $sentencia_persona_ppff = $pdo->prepare('INSERT INTO personas
                                (usuario_id,nombres,apellidos,ci,fecha_nacimiento,celular,profesion,direccion, fyh_creacion, estado)
                                VALUES ( :usuario_id,:nombres,:apellidos,:ci,:fecha_nacimiento,:celular,:profesion,:direccion,:fyh_creacion,:estado)');
                        $sentencia_persona_ppff->bindParam(':usuario_id',$id_usuario_ppff_final);
                        $sentencia_persona_ppff->bindParam(':nombres',$nombres_ppff);
                        $sentencia_persona_ppff->bindParam(':apellidos',$apellidos_ppff);
                        $sentencia_persona_ppff->bindParam(':ci',$ci_ppff);
                        $sentencia_persona_ppff->bindParam(':fecha_nacimiento',$fecha_nacimiento_ppff);
                        $sentencia_persona_ppff->bindParam(':celular',$celular_ppff);
                        $sentencia_persona_ppff->bindParam(':profesion',$ocupacion_ppff);
                        $sentencia_persona_ppff->bindParam(':direccion',$direccion_ppff);
                        $sentencia_persona_ppff->bindParam(':fyh_creacion',$fechaHora);
                        $sentencia_persona_ppff->bindParam(':estado',$estado_de_registro);
                        $sentencia_persona_ppff->execute();
                        $id_persona_ppff_final = $pdo->lastInsertId();

                        $nombres_apellidos_completos_ppff = $nombres_ppff . ' ' . $apellidos_ppff;
                        $sentencia_ppffs = $pdo->prepare('INSERT INTO ppffs
                                (persona_id,nombres_apellidos_ppff,ci_ppff,celular_ppff,ocupacion_ppff,ref_nombre,ref_parentesco,ref_celular,fyh_creacion, estado)
                                VALUES (:persona_id,:nombres_apellidos_ppff,:ci_ppff,:celular_ppff,:ocupacion_ppff,:ref_nombre,:ref_parentesco,:ref_celular,:fyh_creacion, :estado)');
                        $sentencia_ppffs->bindParam(':persona_id',$id_persona_ppff_final);
                        $sentencia_ppffs->bindParam(':nombres_apellidos_ppff',$nombres_apellidos_completos_ppff);
                        $sentencia_ppffs->bindParam(':ci_ppff',$ci_ppff);
                        $sentencia_ppffs->bindParam(':celular_ppff',$celular_ppff);
                        $sentencia_ppffs->bindParam(':ocupacion_ppff',$ocupacion_ppff);
                        $sentencia_ppffs->bindParam(':ref_nombre',$ref_nombre);
                        $sentencia_ppffs->bindParam(':ref_parentesco',$ref_parentesco);
                        $sentencia_ppffs->bindParam(':ref_celular',$ref_celular);
                        $sentencia_ppffs->bindParam(':fyh_creacion',$fechaHora);
                        $sentencia_ppffs->bindParam(':estado',$estado_de_registro);
                        $sentencia_ppffs->execute();
                        $id_ppff_final = $pdo->lastInsertId();
                    }
                    // Al final de este bloque, $id_ppff_final tiene el ID del registro PPFF (existente o nuevo)
                    // y $id_usuario_ppff_final tiene el ID del usuario PPFF.
                    // $id_persona_ppff_final tiene el ID de la persona PPFF.

                    // --- MANEJO DEL ESTUDIANTE ---
                    $id_usuario_estudiante_creado = null; // Inicializar. Será NULL si no se crea usuario para el estudiante.
                
                    // 4. Registro del estudiante en la tabla personas
                    // El campo usuario_id será NULL inicialmente o si no se crea usuario para el estudiante.
                    // Si se crea usuario, se actualizará después.
                    $sentencia_persona_est = $pdo->prepare('INSERT INTO personas
                            (usuario_id, nombres, apellidos, ci, fecha_nacimiento, celular, profesion, direccion, fyh_creacion, estado)
                            VALUES (:usuario_id, :nombres, :apellidos, :ci, :fecha_nacimiento, :celular, 
                                    :profesion, :direccion, :fyh_creacion, :estado)');
                    $sentencia_persona_est->bindParam(':usuario_id',$id_usuario_estudiante_creado); // Será NULL inicialmente
                    $sentencia_persona_est->bindParam(':nombres',$nombres);
                    $sentencia_persona_est->bindParam(':apellidos',$apellidos);
                    $sentencia_persona_est->bindParam(':ci',$ci);
                    $sentencia_persona_est->bindParam(':fecha_nacimiento',$fecha_nacimiento);
                    $sentencia_persona_est->bindParam(':celular',$celular);
                    $sentencia_persona_est->bindParam(':profesion',$profesion); // "ESTUDIANTE"
                    $sentencia_persona_est->bindParam(':direccion',$direccion);
                    $sentencia_persona_est->bindParam(':fyh_creacion',$fechaHora);
                    $sentencia_persona_est->bindParam(':estado',$estado_de_registro);
                    $sentencia_persona_est->execute();
                    $id_persona_estudiante_creada = $pdo->lastInsertId();

                    // 5. Si se solicitó crear usuario para el estudiante
                    if ($crear_usuario_est == 1) {
                        // Crear usuario para el estudiante
                        $password_estudiante = password_hash($ci, PASSWORD_DEFAULT); // CI del estudiante como contraseña
                        $sentencia_usuario_est = $pdo->prepare('INSERT INTO usuarios
                                    (rol_id, email, password, fyh_creacion, estado)
                                    VALUES (:rol_id, :email, :password, :fyh_creacion, :estado)');
                        $sentencia_usuario_est->bindParam(':rol_id',$rol_id_estudiante_fijo);
                        $sentencia_usuario_est->bindParam(':email',$email_estudiante); // Email único del estudiante
                        $sentencia_usuario_est->bindParam(':password',$password_estudiante);
                        $sentencia_usuario_est->bindParam(':fyh_creacion',$fechaHora);
                        $sentencia_usuario_est->bindParam(':estado',$estado_de_registro);
                        $sentencia_usuario_est->execute();
                        $id_usuario_estudiante_creado = $pdo->lastInsertId(); // Actualizamos la variable

                        // Actualizar la persona del estudiante con el usuario_id recién creado
                        $sentencia_update_persona_est = $pdo->prepare('UPDATE personas SET usuario_id = :usuario_id WHERE id_persona = :id_persona');
                        $sentencia_update_persona_est->bindParam(':usuario_id',$id_usuario_estudiante_creado);
                        $sentencia_update_persona_est->bindParam(':id_persona',$id_persona_estudiante_creada);
                        $sentencia_update_persona_est->execute();
                    }

                    // 6. Registrar estudiante en la tabla estudiantes
                    $sentencia_estudiante = $pdo->prepare('INSERT INTO estudiantes
                            (persona_id, nivel_id, grado_id, rude, fyh_creacion, estado)
                            VALUES (:persona_id, :nivel_id, :grado_id, :rude, :fyh_creacion, :estado)');
                    $sentencia_estudiante->bindParam(':persona_id',$id_persona_estudiante_creada);
                    $sentencia_estudiante->bindParam(':nivel_id',$nivel_id);
                    $sentencia_estudiante->bindParam(':grado_id',$grado_id);
                    $sentencia_estudiante->bindParam(':rude',$rude);    
                    $sentencia_estudiante->bindParam(':fyh_creacion',$fechaHora);
                    $sentencia_estudiante->bindParam(':estado',$estado_de_registro);
                    $sentencia_estudiante->execute();
                    $id_estudiante_creado = $pdo->lastInsertId();

                    // 7. Relacionar estudiante con padre en la tabla estudiante_ppff
                    $parentesco = "Padre/Madre"; // O podrías tener un campo en el formulario para esto
                    $sentencia_est_ppff = $pdo->prepare('INSERT INTO estudiante_ppff
                            (estudiante_id, ppff_id, parentesco, fyh_creacion, estado)
                            VALUES (:estudiante_id, :ppff_id, :parentesco, :fyh_creacion, :estado)');
                    $sentencia_est_ppff->bindParam(':estudiante_id',$id_estudiante_creado);
                    $sentencia_est_ppff->bindParam(':ppff_id',$id_ppff_final); // Usar el ID del PPFF (existente o nuevo)
                    $sentencia_est_ppff->bindParam(':parentesco',$parentesco);
                    $sentencia_est_ppff->bindParam(':fyh_creacion',$fechaHora);
                    $sentencia_est_ppff->bindParam(':estado',$estado_de_registro);
                    $sentencia_est_ppff->execute();
                
                    // --- ASIGNAR TAREAS EXISTENTES AL NUEVO ESTUDIANTE ---
                    // (Esta lógica se mantiene igual)
                    $sql_materias_grado = "SELECT DISTINCT m.id_materia 
                                           FROM materias m
                                           INNER JOIN grados_materias gm ON m.id_materia = gm.materia_id
                                           WHERE gm.grado_id = :grado_id AND m.estado = '1'";
                    $query_materias = $pdo->prepare($sql_materias_grado);
                    $query_materias->bindParam(':grado_id', $grado_id, PDO::PARAM_INT);
                    $query_materias->execute();
                    $materias_del_grado = $query_materias->fetchAll(PDO::FETCH_COLUMN);

                    if (!empty($materias_del_grado)) {
                        $placeholders_materias = implode(',', array_fill(0, count($materias_del_grado), '?'));
                        $sql_tareas_existentes = "SELECT id_tarea FROM tareas 
                                                  WHERE grado_id = ? AND materia_id IN ($placeholders_materias) AND estado = '1'";
                        $query_tareas = $pdo->prepare($sql_tareas_existentes);
                        $params_tareas = array_merge([$grado_id], $materias_del_grado);
                        $query_tareas->execute($params_tareas);
                        $tareas_a_asignar = $query_tareas->fetchAll(PDO::FETCH_COLUMN);

                        foreach ($tareas_a_asignar as $id_tarea_existente) {
                            $sql_insert_registro_tarea = "INSERT INTO registro_tareas (estudiante_id, tarea_id, estado, fyh_creacion, estado_registro) 
                                                          VALUES (:estudiante_id, :tarea_id, :estado_tarea, :fyh_creacion, :estado_registro)";
                            $stmt_insert_rt = $pdo->prepare($sql_insert_registro_tarea);
                            $stmt_insert_rt->execute([
                                ':estudiante_id' => $id_estudiante_creado,
                                ':tarea_id' => $id_tarea_existente,
                                ':estado_tarea' => 'Pendiente',
                                ':fyh_creacion' => $fechaHora,
                                ':estado_registro' => '1'
                            ]);
                        }
                    }

                    $pdo->commit();
                    $_SESSION['mensaje'] = "Se registró al estudiante de manera correcta en la base de datos";
                    $_SESSION['icono'] = "success";
                    header('Location:'.APP_URL."/admin/estudiantes");
                    exit();
                
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $_SESSION['mensaje'] = "Error: " . $e->getMessage();
                    $_SESSION['icono'] = "error";
                    // Redirigir de vuelta al formulario de creación para que el usuario pueda corregir
                    // header('Location:'.APP_URL."/admin/inscripciones/create.php"); // Esta es la forma recomendada
                    ?><script>window.history.back();</script><?php // Manteniendo tu redirección actual si es preferida, pero header() es mejor.
                    exit();
                }
        ?>
