<?php
include ('../../../app/config.php');

// Datos estudiante
$id_usuario_estudiante = $_POST['id_usuario'] ?? null; // ID de usuario del estudiante
$id_persona = $_POST['id_persona'];
$id_estudiante = $_POST['id_estudiante'];

$nombres =$_POST['nombres'];
$apellidos =$_POST['apellidos'];
$ci =$_POST['ci'];
$fecha_nacimiento =$_POST['fecha_nacimiento'];
$celular =$_POST['celular'];
$email_estudiante =$_POST['email']; // Correo del estudiante (puede estar vacío)
$direccion =$_POST['direccion'];
$nivel_id =$_POST['nivel_id'];
$grado_id =$_POST['grado_id'];
$rude =$_POST['rude'];
//datos padre de familia
$nombres_ppff_persona =$_POST['nombres_ppff_persona']; // Nombres del padre/madre para tabla personas
$apellidos_ppff_persona =$_POST['apellidos_ppff_persona']; // Apellidos del padre/madre para tabla personas
$ci_ppff =$_POST['ci_ppff'];
$celular_ppff =$_POST['celular_ppff'];
$email_ppff = $_POST['email_ppff'] ?? null; // Correo del padre/madre (puede estar vacío)
$id_usuario_ppff = $_POST['id_usuario_ppff'] ?? null; // ID de usuario del padre/madre

$id_ppff = $_POST['id_ppff'] ?? null; // ID de la tabla ppffs
$id_persona_ppff = $_POST['id_persona_ppff'] ?? null; // ID de la persona del padre/madre
$ocupacion_ppff =$_POST['ocupacion_ppff'];
$ref_nombre =$_POST['ref_nombre'];
$ref_parentesco =$_POST['ref_parentesco'];
$ref_celular =$_POST['ref_celular'];
$profesion ="ESTUDIANTE";

$pdo->beginTransaction();

try {
    // Obtener el ID del rol 'ESTUDIANTE'
    $stmt_rol_estudiante = $pdo->prepare("SELECT id_rol FROM roles WHERE nombre_rol = 'ESTUDIANTE' LIMIT 1");
    $stmt_rol_estudiante->execute();
    $rol_estudiante_data = $stmt_rol_estudiante->fetch(PDO::FETCH_ASSOC);

    if (!$rol_estudiante_data) {
        $pdo->rollBack();
        $_SESSION['mensaje'] = "Error crítico: El rol 'ESTUDIANTE' no se encuentra en la base de datos.";
        $_SESSION['icono'] = "error";
        header('Location:'.APP_URL."/admin/estudiantes/edit.php?id_estudiante=".$id_estudiante);
        exit();
    }
    $rol_id_estudiante_fijo = $rol_estudiante_data['id_rol'];

    // Actualizar TABLA USUARIOS (del estudiante), si tiene un id_usuario
    if (!empty($id_usuario_estudiante)) {
        if (!empty($email_estudiante)) {
            // Si se proporciona email, actualizar email y contraseña
            $password_estudiante = password_hash($ci, PASSWORD_DEFAULT);
            $sql_update_usuario_est = 'UPDATE usuarios
                        SET rol_id=:rol_id,
                            email=:email,
                            password=:password,
                            fyh_actualizacion=:fyh_actualizacion
                        WHERE id_usuario=:id_usuario';
            $sentencia_usuario_est = $pdo->prepare($sql_update_usuario_est);
            $sentencia_usuario_est->bindParam(':rol_id', $rol_id_estudiante_fijo);
            $sentencia_usuario_est->bindParam(':email', $email_estudiante);
            $sentencia_usuario_est->bindParam(':password', $password_estudiante);
            $sentencia_usuario_est->bindParam(':fyh_actualizacion', $fechaHora);
            $sentencia_usuario_est->bindParam(':id_usuario', $id_usuario_estudiante);
            $sentencia_usuario_est->execute();
        } else {
            // Si no se proporciona email, solo actualizar rol_id (por si acaso) y fyh_actualizacion
            $sql_update_usuario_est_no_email = 'UPDATE usuarios
                        SET rol_id=:rol_id,
                            fyh_actualizacion=:fyh_actualizacion
                        WHERE id_usuario=:id_usuario';
            $sentencia_usuario_est_no_email = $pdo->prepare($sql_update_usuario_est_no_email);
            $sentencia_usuario_est_no_email->bindParam(':rol_id', $rol_id_estudiante_fijo);
            $sentencia_usuario_est_no_email->bindParam(':fyh_actualizacion', $fechaHora);
            $sentencia_usuario_est_no_email->bindParam(':id_usuario', $id_usuario_estudiante);
            $sentencia_usuario_est_no_email->execute();
        }
    }

    // ACTUALIZAR A LA TABLA PERSONAS (del estudiante)
    $sql_update_persona = 'UPDATE personas
               SET nombres=:nombres,
                apellidos=:apellidos,
                ci=:ci,
                fecha_nacimiento=:fecha_nacimiento,
                celular=:celular,
                profesion=:profesion,
                direccion=:direccion,
                fyh_actualizacion=:fyh_actualizacion
    WHERE id_persona=:id_persona';
    $sentencia_persona = $pdo->prepare($sql_update_persona);
    $sentencia_persona->bindParam(':nombres',$nombres);
    $sentencia_persona->bindParam(':apellidos',$apellidos);
    $sentencia_persona->bindParam(':ci',$ci);
    $sentencia_persona->bindParam(':fecha_nacimiento',$fecha_nacimiento);
    $sentencia_persona->bindParam(':celular',$celular);
    $sentencia_persona->bindParam(':profesion',$profesion);
    $sentencia_persona->bindParam(':direccion',$direccion);
    $sentencia_persona->bindParam(':fyh_actualizacion',$fechaHora);
    $sentencia_persona->bindParam(':id_persona',$id_persona);
    $sentencia_persona->execute();

    // ACTUALIZAR A LA TABLA ESTUDIANTES
    $sql_update_estudiante = 'UPDATE estudiantes
                SET nivel_id=:nivel_id,
                    grado_id=:grado_id,
                    rude=:rude,
                    fyh_actualizacion=:fyh_actualizacion
    WHERE id_estudiante=:id_estudiante';
    $sentencia_estudiante = $pdo->prepare($sql_update_estudiante);
    $sentencia_estudiante->bindParam(':nivel_id',$nivel_id);
    $sentencia_estudiante->bindParam(':grado_id',$grado_id);
    $sentencia_estudiante->bindParam(':rude',$rude);
    $sentencia_estudiante->bindParam(':fyh_actualizacion',$fechaHora);
    $sentencia_estudiante->bindParam(':id_estudiante',$id_estudiante);
    $sentencia_estudiante->execute();

    // ACTUALIZAR DATOS DEL PADRE/MADRE EN LA TABLA PERSONAS (si existe id_persona_ppff)
    if (!empty($id_persona_ppff) && (!empty($nombres_ppff_persona) || !empty($apellidos_ppff_persona)) ) {
        $sql_update_persona_ppff = 'UPDATE personas
            SET nombres = :nombres_ppff,
                apellidos = :apellidos_ppff,
                ci = :ci_ppff,
                celular = :celular_ppff,
                fyh_actualizacion = :fyh_actualizacion
            WHERE id_persona = :id_persona_ppff';
        $sentencia_persona_ppff = $pdo->prepare($sql_update_persona_ppff);
        $sentencia_persona_ppff->bindParam(':nombres_ppff', $nombres_ppff_persona);
        $sentencia_persona_ppff->bindParam(':apellidos_ppff', $apellidos_ppff_persona);
        $sentencia_persona_ppff->bindParam(':ci_ppff', $ci_ppff);
        $sentencia_persona_ppff->bindParam(':celular_ppff', $celular_ppff);
        $sentencia_persona_ppff->bindParam(':fyh_actualizacion', $fechaHora);
        $sentencia_persona_ppff->bindParam(':id_persona_ppff', $id_persona_ppff);
        $sentencia_persona_ppff->execute();
    }

    // ACTUALIZAR DATOS DEL PADRE/MADRE EN LA TABLA USUARIOS (si tiene id_usuario_ppff y se proporcionó email_ppff)
    if (!empty($id_usuario_ppff) && !empty($email_ppff)) {
        $sql_update_usuario_ppff = 'UPDATE usuarios
            SET email = :email_ppff,
                fyh_actualizacion = :fyh_actualizacion
            WHERE id_usuario = :id_usuario_ppff';
        $stmt_update_usuario_ppff = $pdo->prepare($sql_update_usuario_ppff);
        $stmt_update_usuario_ppff->bindParam(':email_ppff', $email_ppff);
        $stmt_update_usuario_ppff->bindParam(':fyh_actualizacion', $fechaHora);
        $stmt_update_usuario_ppff->bindParam(':id_usuario_ppff', $id_usuario_ppff);
        $stmt_update_usuario_ppff->execute();
    }



    // Actualizar A LA TABLA PPFFS (si existe id_ppff)
    if (!empty($id_ppff)) {
        $sql_update_ppffs = 'UPDATE ppffs
               SET  ocupacion_ppff=:ocupacion_ppff,
                    ref_nombre=:ref_nombre,
                    ref_parentesco=:ref_parentesco,
                    ref_celular=:ref_celular,
                    fyh_actualizacion=:fyh_actualizacion
                WHERE id_ppff=:id_ppff';
        $sentencia_ppffs = $pdo->prepare($sql_update_ppffs);
        $sentencia_ppffs->bindParam(':ocupacion_ppff',$ocupacion_ppff);
        $sentencia_ppffs->bindParam(':ref_nombre',$ref_nombre);
        $sentencia_ppffs->bindParam(':ref_parentesco',$ref_parentesco);
        $sentencia_ppffs->bindParam(':ref_celular',$ref_celular);
        $sentencia_ppffs->bindParam('fyh_actualizacion',$fechaHora);
        $sentencia_ppffs->bindParam(':id_ppff',$id_ppff);
        $sentencia_ppffs->execute();
    }

    $pdo->commit();
    $_SESSION['mensaje'] = "Se actualizó al estudiante de manera correcta en la base de datos";
    $_SESSION['icono'] = "success";
    header('Location:'.APP_URL."/admin/estudiantes");
    exit();
} catch (PDOException $e) {
    $pdo->rollBack();
    // error_log("Error al actualizar estudiante: " . $e->getMessage()); // Log the error
    $_SESSION['mensaje'] = "Error no se pudo actualizar en la base datos, comuniquese con el administrador";
    // $_SESSION['mensaje'] = "Error no se pudo actualizar en la base datos: " . $e->getMessage(); // For debugging only
    $_SESSION['icono'] = "error";
    ?><script>window.history.back();</script><?php
    exit();
}
