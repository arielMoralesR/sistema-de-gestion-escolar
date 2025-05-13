<?php
include ('../../../app/config.php');

$rol_id = $_POST['rol_id'];
$materias_id = isset($_POST['materias_id']) ? $_POST['materias_id'] : [];
$nombres = $_POST['nombres'];
$apellidos = $_POST['apellidos'];
$ci = $_POST['ci'];
$email = $_POST['email'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$celular = $_POST['celular'];
$profesion = $_POST['profesion'];
$direccion = $_POST['direccion'];
$especialidad = $_POST['especialidad'];
// Validación más estricta
if(empty($materias_id) || !is_array($materias_id)) {
    session_start();
    $_SESSION['mensaje'] = "Debe seleccionar al menos una materia";
    $_SESSION['icono'] = "error";
    header('Location:'.APP_URL."/admin/docentes/create.php");
    exit;
}

// Iniciar transacción
$pdo->beginTransaction();
$success = true;

try {
    // 1. Insertar en usuarios
    $password = password_hash($ci, PASSWORD_DEFAULT);
    $stmt_usuario = $pdo->prepare('INSERT INTO usuarios (rol_id, email, password, fyh_creacion, estado) 
                                 VALUES (:rol_id, :email, :password, :fyh_creacion, :estado)');
    $stmt_usuario->execute([
        ':rol_id' => $_POST['rol_id'],
        ':email' => $_POST['email'],
        ':password' => $password,
        ':fyh_creacion' => $fechaHora,
        ':estado' => $estado_de_registro
    ]);
    $id_usuario = $pdo->lastInsertId();

    // 2. Insertar en personas
    $stmt_persona = $pdo->prepare('INSERT INTO personas 
                                 (usuario_id, nombres, apellidos, ci, fecha_nacimiento, celular, profesion, direccion, fyh_creacion, estado) 
                                 VALUES (:usuario_id, :nombres, :apellidos, :ci, :fecha_nacimiento, :celular, :profesion, :direccion, :fyh_creacion, :estado)');
    $stmt_persona->execute([
        ':usuario_id' => $id_usuario,
        ':nombres' => $_POST['nombres'],
        ':apellidos' => $_POST['apellidos'],
        ':ci' => $_POST['ci'],
        ':fecha_nacimiento' => $_POST['fecha_nacimiento'],
        ':celular' => $_POST['celular'],
        ':profesion' => $_POST['profesion'],
        ':direccion' => $_POST['direccion'],
        ':fyh_creacion' => $fechaHora,
        ':estado' => $estado_de_registro
    ]);
    $id_persona = $pdo->lastInsertId();

    // 3. Insertar en docentes
    $stmt_docente = $pdo->prepare('INSERT INTO docentes 
                                 (persona_id, especialidad, fyh_creacion, estado) 
                                 VALUES (:persona_id, :especialidad, :fyh_creacion, :estado)');
    $stmt_docente->execute([
        ':persona_id' => $id_persona,
        ':especialidad' => $_POST['especialidad'],
        ':fyh_creacion' => $fechaHora,
        ':estado' => $estado_de_registro
    ]);
    $id_docente = $pdo->lastInsertId();

    // 4. Insertar materias del docente (parte crítica)
    $stmt_materias = $pdo->prepare('INSERT INTO docente_materias 
                                  (docente_id, materia_id, fyh_creacion, estado) 
                                  VALUES (:docente_id, :materia_id, :fyh_creacion, :estado)');
    
    foreach($materias_id as $materia_id) {
        $stmt_materias->execute([
            ':docente_id' => $id_docente,
            ':materia_id' => $materia_id,
            ':fyh_creacion' => $fechaHora,
            ':estado' => $estado_de_registro
        ]);
        
        // Verificar si la inserción fue exitosa
        if($stmt_materias->rowCount() == 0) {
            throw new Exception("Error al insertar materia ID: $materia_id");
        }
    }

    // Confirmar todas las operaciones
    $pdo->commit();

    // Redireccionar con mensaje de éxito
    session_start();
    $_SESSION['mensaje'] = "Docente registrado con éxito con " . count($materias_id) . " materia(s)";
    $_SESSION['icono'] = "success";
    header('Location:'.APP_URL."/admin/docentes");
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    session_start();
    $_SESSION['mensaje'] = "Error en la base de datos: " . $e->getMessage();
    $_SESSION['icono'] = "error";
    header('Location:'.APP_URL."/admin/docentes/create.php");
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    session_start();
    $_SESSION['mensaje'] = $e->getMessage();
    $_SESSION['icono'] = "error";
    header('Location:'.APP_URL."/admin/docentes/create.php");
    exit;
}