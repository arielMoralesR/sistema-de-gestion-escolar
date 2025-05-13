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
    $id_tarea = filter_input(INPUT_POST, 'id_tarea', FILTER_VALIDATE_INT);
    $docente_id_sesion = $_SESSION['docente_id'];

    if (!$id_tarea) {
        $_SESSION['mensaje'] = "ID de tarea no válido.";
        $_SESSION['icono'] = "error";
        header('Location: ' . APP_URL . '/admin/tareas/');
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Verificar que la tarea pertenece al docente logueado antes de "eliminarla"
        $sql_check = "SELECT docente_id FROM tareas WHERE id_tarea = :id_tarea AND estado = '1'";
        $query_check = $pdo->prepare($sql_check);
        $query_check->bindParam(':id_tarea', $id_tarea, PDO::PARAM_INT);
        $query_check->execute();
        $tarea_existente = $query_check->fetch(PDO::FETCH_ASSOC);

        if (!$tarea_existente) {
            $_SESSION['mensaje'] = "La tarea no existe o ya ha sido eliminada.";
            $_SESSION['icono'] = "warning";
            $pdo->rollBack();
            header('Location: ' . APP_URL . '/admin/tareas/');
            exit;
        }

        if ($tarea_existente['docente_id'] != $docente_id_sesion) {
            $_SESSION['mensaje'] = "No tiene permiso para eliminar esta tarea.";
            $_SESSION['icono'] = "error";
            $pdo->rollBack();
            header('Location: ' . APP_URL . '/admin/tareas/');
            exit;
        }

        // Borrado lógico: cambiar el estado de la tarea a '0' (inactiva/eliminada)
        $fyh_actualizacion = date('Y-m-d H:i:s');
        $sql_delete = "UPDATE tareas SET estado = '0', fyh_actualizacion = :fyh_actualizacion WHERE id_tarea = :id_tarea";
        $stmt_delete = $pdo->prepare($sql_delete);
        $stmt_delete->bindParam(':fyh_actualizacion', $fyh_actualizacion);
        $stmt_delete->bindParam(':id_tarea', $id_tarea, PDO::PARAM_INT);
        $stmt_delete->execute();

        $pdo->commit();
        $_SESSION['mensaje'] = "Tarea eliminada (marcada como inactiva) correctamente.";
        $_SESSION['icono'] = "success";

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['mensaje'] = "Error al eliminar la tarea: " . $e->getMessage();
        $_SESSION['icono'] = "error";
        // Considera loguear el error $e->getMessage()
    }

    header('Location: ' . APP_URL . '/admin/tareas/');
    exit;

} else {
    $_SESSION['mensaje'] = "Método no permitido.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/admin/tareas/');
    exit;
}
?>