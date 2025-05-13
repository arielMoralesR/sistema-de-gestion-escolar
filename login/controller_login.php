<?php
/**
 * Created by PhpStorm.
 * User: HILARIWEB
 * Date: 28/12/2023
 * Time: 19:57
 */

include ('../app/config.php');

$email = $_POST['email'];
$password = $_POST['password'];

// Usar sentencias preparadas para seguridad
$sql = "SELECT * FROM usuarios WHERE email = :email AND estado = '1' ";
$query = $pdo->prepare($sql);
$query->bindParam(':email', $email);
$query->execute();

$usuario = $query->fetch(PDO::FETCH_ASSOC); // Obtener solo un usuario

if ($usuario && password_verify($password, $usuario['password'])) {
    // echo "los datos son correctos";
    // session_start(); // Ya se llama en config.php

    $_SESSION['mensaje'] = "Bienvenido al sistema";
    $_SESSION['icono'] = "success";
    $_SESSION['sesion_email'] = $usuario['email']; // Usar el email de la BD por consistencia
    $_SESSION['usuario_id'] = $usuario['id_usuario'];
    $_SESSION['rol_id'] = $usuario['rol_id'];

    // Lógica para establecer IDs específicos según el rol
    // Según db.sql, el rol_id para DOCENTE es 6
    // Según tu var_dump, el rol_id para ESTUDIANTE es 8
    $ROL_DOCENTE_ID = 6; 
    if ($usuario['rol_id'] == $ROL_DOCENTE_ID) {
        // Para obtener id_docente: usuarios.id_usuario -> personas.id_persona -> docentes.id_docente
        $sql_docente_info = "SELECT d.id_docente 
                             FROM docentes as d
                             INNER JOIN personas as p ON d.persona_id = p.id_persona
                             WHERE p.usuario_id = :usuario_id AND d.estado = '1' AND p.estado = '1'";
        
        $query_docente_info = $pdo->prepare($sql_docente_info);
        $query_docente_info->bindParam(':usuario_id', $usuario['id_usuario']);
        $query_docente_info->execute();
        $docente_data = $query_docente_info->fetch(PDO::FETCH_ASSOC);

        if ($docente_data) {
            $_SESSION['docente_id'] = $docente_data['id_docente'];
        } else {
            // El usuario tiene rol de docente, pero no se encontró una entrada activa 
            // en la tabla 'docentes' vinculada a través de 'personas'.
            // Esto podría ser un error de configuración de datos para este docente.
            // Puedes decidir cómo manejarlo:
            // 1. No establecer $_SESSION['docente_id'] (las páginas de docente lo redirigirán).
            // 2. Mostrar un mensaje específico y/o redirigir a una página de error.
            // 3. Permitir el login pero con funcionalidades de docente limitadas.
            // Por ahora, si no se encuentra, $_SESSION['docente_id'] no se establecerá.
            // Esto es importante porque la página de crear tareas lo necesita.
            // Considera añadir un mensaje si este caso es crítico:
            // $_SESSION['mensaje'] = "Configuración de docente incompleta. Contacte al administrador.";
            // $_SESSION['icono'] = "warning";
        }
    } elseif ($usuario['rol_id'] == 8) { // Si el rol es ESTUDIANTE
        // Para obtener id_estudiante: usuarios.id_usuario -> personas.id_persona -> estudiantes.id_estudiante
        $sql_estudiante_info = "SELECT e.id_estudiante 
                                FROM estudiantes as e
                                INNER JOIN personas as p ON e.persona_id = p.id_persona
                                WHERE p.usuario_id = :usuario_id AND e.estado = '1' AND p.estado = '1'";
        
        $query_estudiante_info = $pdo->prepare($sql_estudiante_info);
        $query_estudiante_info->bindParam(':usuario_id', $usuario['id_usuario']);
        $query_estudiante_info->execute();
        $estudiante_data = $query_estudiante_info->fetch(PDO::FETCH_ASSOC);

        if ($estudiante_data) {
            $_SESSION['estudiante_id'] = $estudiante_data['id_estudiante'];
        } else {
            // El usuario tiene rol de estudiante, pero no se encontró una entrada activa 
            // en la tabla 'estudiantes' vinculada a través de 'personas'.
            // Esto podría ser un error de configuración de datos para este estudiante.
            error_log("LOGIN_ERROR: Usuario con rol estudiante (usuario_id: {$usuario['id_usuario']}) no tiene un registro de estudiante asociado.");
            // Considera añadir un mensaje de error para el usuario y no permitir el login completo.
        }
    }

    header('Location:'.APP_URL."/admin");
    exit;
}else{
    // echo "los datos son incorrectos";
    // session_start(); // Ya se llama en config.php
    $_SESSION['mensaje'] = "Los datos introducidos son incorrectos, vuelva a intentarlo";
    $_SESSION['icono'] = "error"; // Añadir icono para el mensaje de error
    header('Location:'.APP_URL."/login");
    exit;
}
