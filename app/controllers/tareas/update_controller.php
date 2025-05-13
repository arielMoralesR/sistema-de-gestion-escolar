<?php
include ('../../../app/config.php');

// Verificar sesión y rol de docente
if (!isset($_SESSION['docente_id'])) {
    $_SESSION['mensaje'] = "Acceso no autorizado.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger datos del formulario
    $id_tarea = filter_input(INPUT_POST, 'id_tarea', FILTER_VALIDATE_INT);
    $docente_id_form = filter_input(INPUT_POST, 'docente_id', FILTER_VALIDATE_INT); // Viene del campo oculto
    $titulo = trim($_POST['titulo'] ?? '');
    $materia_id = filter_input(INPUT_POST, 'materia_id', FILTER_VALIDATE_INT);
    // Nivel y Grado se toman de los campos ocultos ya que no son editables en el form
    $nivel_id = filter_input(INPUT_POST, 'nivel_id', FILTER_VALIDATE_INT); 
    $grado_id = filter_input(INPUT_POST, 'grado_id', FILTER_VALIDATE_INT);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $fecha_entrega = $_POST['fecha_entrega'] ?? '';
    $estado_tarea = $_POST['estado_tarea'] ?? '0'; // Default a inactiva si no se envía

    $docente_id_sesion = $_SESSION['docente_id'];

    // Validaciones básicas
    if (!$id_tarea || !$docente_id_form || $docente_id_form != $docente_id_sesion) {
        $_SESSION['mensaje'] = "Error: ID de tarea o docente no válido, o intento de modificación no autorizada.";
        $_SESSION['icono'] = "error";
        header('Location: ' . APP_URL . '/admin/tareas/');
        exit;
    }

    if (empty($titulo) || empty($descripcion) || !$materia_id || empty($fecha_entrega) || !$nivel_id || !$grado_id) {
        $_SESSION['mensaje'] = "Todos los campos marcados con * son obligatorios (excepto Nivel/Grado que son fijos).";
        $_SESSION['icono'] = "error";
        // Redirigir de vuelta al formulario de edición
        header('Location: ' . APP_URL . '/admin/tareas/edit.php?id_tarea=' . $id_tarea);
        exit;
    }

    // Preparar la actualización
    $fyh_actualizacion = date('Y-m-d H:i:s');

    try {
        $sql_update = "UPDATE tareas SET
                            titulo = :titulo,
                            materia_id = :materia_id,
                            nivel_id = :nivel_id, /* Aunque no se edite en form, lo incluimos por si se cambia la lógica después */
                            grado_id = :grado_id, /* Idem */
                            descripcion = :descripcion,
                            fecha_entrega = :fecha_entrega,
                            estado = :estado_tarea,
                            fyh_actualizacion = :fyh_actualizacion
                       WHERE id_tarea = :id_tarea AND docente_id = :docente_id_sesion";
        
        $stmt = $pdo->prepare($sql_update);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);
        $stmt->bindParam(':nivel_id', $nivel_id, PDO::PARAM_INT);
        $stmt->bindParam(':grado_id', $grado_id, PDO::PARAM_INT);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':fecha_entrega', $fecha_entrega);
        $stmt->bindParam(':estado_tarea', $estado_tarea);
        $stmt->bindParam(':fyh_actualizacion', $fyh_actualizacion);
        $stmt->bindParam(':id_tarea', $id_tarea, PDO::PARAM_INT);
        $stmt->bindParam(':docente_id_sesion', $docente_id_sesion, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Tarea actualizada correctamente.";
            $_SESSION['icono'] = "success";
            header('Location: ' . APP_URL . '/admin/tareas/show.php?id_tarea=' . $id_tarea);
            exit;
        } else {
            $_SESSION['mensaje'] = "Error al actualizar la tarea.";
            $_SESSION['icono'] = "error";
            header('Location: ' . APP_URL . '/admin/tareas/edit.php?id_tarea=' . $id_tarea);
            exit;
        }

    } catch (PDOException $e) {
        // Log error $e->getMessage();
        $_SESSION['mensaje'] = "Error de base de datos al actualizar la tarea.";
        $_SESSION['icono'] = "error";
        header('Location: ' . APP_URL . '/admin/tareas/edit.php?id_tarea=' . $id_tarea);
        exit;
    }

} else {
    $_SESSION['mensaje'] = "Método no permitido.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/admin/tareas/');
    exit;
}
?>