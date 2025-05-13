<?php
include ('../../../app/config.php');

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['mensaje'] = "Debe iniciar sesión para realizar esta acción.";
    $_SESSION['icono'] = "warning";
    header('Location: ' . APP_URL . '/login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger los datos del formulario
    $usuario_remitente_id = $_POST['usuario_remitente_id'] ?? null;
    $tipo_comunicacion = $_POST['tipo_comunicacion'] ?? '';
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';

    // Campos opcionales para reportes
    $estudiante_afectado_nombre = $_POST['estudiante_afectado_nombre'] ?? null;
    $estudiante_afectado_id = null; // Lo buscaremos si se proporciona el nombre
    $fecha_incidente = !empty($_POST['fecha_incidente']) ? $_POST['fecha_incidente'] : null;
    $lugar_incidente = !empty($_POST['lugar_incidente']) ? $_POST['lugar_incidente'] : null;
    $testigos_descripcion = !empty($_POST['testigos_descripcion']) ? $_POST['testigos_descripcion'] : null;

    // Validaciones básicas
    if (empty($usuario_remitente_id) || empty($tipo_comunicacion) || empty($titulo) || empty($descripcion)) {
        $_SESSION['mensaje'] = "Por favor, complete todos los campos obligatorios (*).";
        $_SESSION['icono'] = "error";
        header('Location: ' . APP_URL . '/admin/comunicaciones/create.php');
        exit;
    }

    $pdo->beginTransaction();
    try {
        // Si se proporcionó un nombre de estudiante afectado, intentar buscar su ID
        // Esta es una lógica simplificada. En un sistema real, podrías necesitar una búsqueda más robusta
        // o un selector en el formulario.
        if (!empty($estudiante_afectado_nombre) && ($tipo_comunicacion == 'reporte_bullying' || $tipo_comunicacion == 'otro_reporte')) {
            // Dividir el nombre en nombres y apellidos para una búsqueda más flexible
            $partes_nombre = explode(' ', $estudiante_afectado_nombre, 2);
            $nombre_busqueda = $partes_nombre[0];
            $apellido_busqueda = isset($partes_nombre[1]) ? $partes_nombre[1] : '';

            $sql_find_student = "SELECT e.id_estudiante FROM estudiantes e 
                                 INNER JOIN personas p ON e.persona_id = p.id_persona 
                                 WHERE (p.nombres LIKE :nombre OR p.apellidos LIKE :apellido) 
                                 AND e.estado = '1' AND p.estado = '1' LIMIT 1";
            $query_find_student = $pdo->prepare($sql_find_student);
            $query_find_student->bindValue(':nombre', '%' . $nombre_busqueda . '%');
            $query_find_student->bindValue(':apellido', '%' . $apellido_busqueda . '%');
            $query_find_student->execute();
            $estudiante_encontrado = $query_find_student->fetch(PDO::FETCH_ASSOC);

            if ($estudiante_encontrado) {
                $estudiante_afectado_id = $estudiante_encontrado['id_estudiante'];
            } else {
                // Opcional: ¿Qué hacer si no se encuentra el estudiante?
                // Podrías guardar el nombre en un campo de texto en lugar del ID,
                // o simplemente no asignar un ID y dejar que la descripción lo aclare.
                // Por ahora, si no se encuentra, $estudiante_afectado_id permanecerá null.
                error_log("No se encontró estudiante con nombre: " . $estudiante_afectado_nombre);
            }
        }

        $sql_insert = "INSERT INTO comunicaciones_internas 
                        (usuario_remitente_id, tipo_comunicacion, titulo, descripcion, 
                         estudiante_afectado_id, fecha_incidente, lugar_incidente, testigos_descripcion, 
                         estado_seguimiento, fyh_creacion, estado)
                       VALUES 
                        (:usuario_remitente_id, :tipo_comunicacion, :titulo, :descripcion, 
                         :estudiante_afectado_id, :fecha_incidente, :lugar_incidente, :testigos_descripcion, 
                         'nuevo', :fyh_creacion, '1')";
        
        $stmt = $pdo->prepare($sql_insert);

        $stmt->bindParam(':usuario_remitente_id', $usuario_remitente_id, PDO::PARAM_INT);
        $stmt->bindParam(':tipo_comunicacion', $tipo_comunicacion);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':estudiante_afectado_id', $estudiante_afectado_id, PDO::PARAM_INT); // PDO maneja bien los NULL
        $stmt->bindParam(':fecha_incidente', $fecha_incidente);
        $stmt->bindParam(':lugar_incidente', $lugar_incidente);
        $stmt->bindParam(':testigos_descripcion', $testigos_descripcion);
        $stmt->bindParam(':fyh_creacion', $fechaHora); // $fechaHora debe estar definida en config.php

        $stmt->execute();
        $pdo->commit();

        $_SESSION['mensaje'] = "Su comunicación ha sido enviada correctamente. Gracias.";
        $_SESSION['icono'] = "success";
        //header('Location: ' . APP_URL . '/admin/reportes/'); // Redirigir a la página de reportes o a donde sea apropiado
        // Redirección condicional según el rol del remitente
        $rol_id_remitente = $_SESSION['rol_id'] ?? null; // Asumiendo que 'rol_id' está en sesión
        if ($rol_id_remitente == 8) { // Asumiendo que 8 es el rol_id de ESTUDIANTE
            header('Location: ' . APP_URL . '/admin/deberes/');
        } elseif ($rol_id_remitente == 9) { // Asumiendo que 9 es el rol_id de PADRE DE FAMILIA (ajusta este ID)
            header('Location: ' . APP_URL . '/admin/reportes/');
        } else {
            header('Location: ' . APP_URL . '/admin'); // Redirección general para otros roles (ej. admin dashboard)
        }
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['mensaje'] = "Error al enviar la comunicación: " . $e->getMessage();
        $_SESSION['icono'] = "error";
        error_log("Error en store_comunicacion_controller: " . $e->getMessage());
        header('Location: ' . APP_URL . '/admin/comunicaciones/create.php'); // Volver al formulario
        exit;
    }

} else {
    $_SESSION['mensaje'] = "Método no permitido.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/');
    exit;
}
?>