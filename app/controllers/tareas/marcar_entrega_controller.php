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
    $id_tarea = $_POST['id_tarea'] ?? null;
    $registros_a_actualizar = $_POST['registros'] ?? [];

    $docente_id_sesion = $_SESSION['docente_id'];

    // Validaciones
    if (!$id_tarea || empty($registros_a_actualizar)) {
        $_SESSION['mensaje'] = "Datos incompletos para actualizar el estado.";
        $_SESSION['icono'] = "error";
        header('Location: ' . APP_URL . '/admin/tareas/show.php?id_tarea=' . $id_tarea);
        exit;
    }

    $pdo->beginTransaction();
    try {
        foreach ($registros_a_actualizar as $id_registro_tarea => $datos_registro) {
            // Depuración: Loguear los datos recibidos para este registro
            error_log("[MARCAR_ENTREGA] Procesando registro ID: $id_registro_tarea");
            error_log("[MARCAR_ENTREGA] Datos recibidos: " . print_r($datos_registro, true));
            if (!isset($_SESSION['docente_id']) || empty($_SESSION['docente_id'])) {
                error_log("[MARCAR_ENTREGA] ERROR CRÍTICO: docente_id no está en la sesión.");
                // Considera detener la ejecución o manejar este error de forma más robusta
            } else if ($_SESSION['docente_id'] != $docente_id_sesion) {
                 error_log("[MARCAR_ENTREGA] Discrepancia en docente_id de sesión. Sesión: ".$_SESSION['docente_id']." vs Controlador: ".$docente_id_sesion);
            }

            $calificacion = $datos_registro['calificacion'] ?? null;
            // Si la calificación está vacía, la convertimos a NULL para la BD
            if (trim($calificacion) === '') {
                $calificacion = null;
            }
            $observaciones_docente = $datos_registro['observaciones_docente'] ?? null;
            $estado_recibido_del_form = $datos_registro['estado_entrega'] ?? 'pendiente'; // Valor por defecto si no se envía

            // Asegurar que el estado coincida con los valores del ENUM (minúsculas)
            $nuevo_estado_entrega_db = strtolower(trim($estado_recibido_del_form));
            $estados_validos_enum = ['pendiente', 'entregado', 'evaluado', 'no_entregado']; // Estos son los valores de tu ENUM

            if (!in_array($nuevo_estado_entrega_db, $estados_validos_enum)) {
                error_log("[MARCAR_ENTREGA] Estado no válido para ENUM: '$nuevo_estado_entrega_db' (original: '$estado_recibido_del_form') para registro ID: $id_registro_tarea. Se usará 'pendiente'.");
                $nuevo_estado_entrega_db = 'pendiente'; // Valor por defecto del ENUM si el enviado no es válido
            }
            error_log("[MARCAR_ENTREGA] Intentando guardar estado en BD: '$nuevo_estado_entrega_db' para registro ID: $id_registro_tarea");

            // Verificar que el docente tenga permiso sobre esta tarea (indirectamente, a través del id_registro_tarea)
            $sql_check = "SELECT rt.id_registro FROM registro_tareas as rt 
                          INNER JOIN tareas as t ON rt.tarea_id = t.id_tarea
                          WHERE rt.id_registro = :id_registro_tarea AND t.docente_id = :docente_id";
            $query_check = $pdo->prepare($sql_check);
            $query_check->bindParam(':id_registro_tarea', $id_registro_tarea, PDO::PARAM_INT);
            $query_check->bindParam(':docente_id', $docente_id_sesion, PDO::PARAM_INT);
            $query_check->execute();

            if ($query_check->rowCount() == 0) {
                // Opcional: Podrías continuar con los demás registros o detener todo.
                // Por ahora, solo saltamos este registro.
                error_log("[MARCAR_ENTREGA] Intento de modificación no autorizada o registro no encontrado para registro_tareas ID: $id_registro_tarea por docente ID: $docente_id_sesion");
                continue; 
            }

            // Actualizar la base de datos
            $fyh_actualizacion = date('Y-m-d H:i:s');

            $sql_update_parts = [
                "estado = :estado",
                "calificacion = :calificacion",
                "observaciones = :observaciones", /* Asumiendo que tu columna se llama 'observaciones' */
                "fyh_actualizacion = :fyh_actualizacion"
            ];

            // Si el estado es 'entregado' o 'no_entregado', actualizamos la fecha_entrega con la fecha actual del sistema
            if (in_array($nuevo_estado_entrega_db, ['entregado', 'no_entregado'])) {
                $sql_update_parts[] = "fecha_entrega = :fecha_entrega";
            }

            $sql_update = "UPDATE registro_tareas SET " . implode(", ", $sql_update_parts) . " WHERE id_registro = :id_registro_tarea";
            
            $stmt = $pdo->prepare($sql_update);
            $stmt->bindParam(':estado', $nuevo_estado_entrega_db);
            $stmt->bindParam(':calificacion', $calificacion);
            $stmt->bindParam(':observaciones', $observaciones_docente);
            $stmt->bindParam(':fyh_actualizacion', $fyh_actualizacion);
            $stmt->bindParam(':id_registro_tarea', $id_registro_tarea, PDO::PARAM_INT);

            if (in_array($nuevo_estado_entrega_db, ['entregado', 'no_entregado'])) {
                $stmt->bindParam(':fecha_entrega', $fyh_actualizacion);
            }
            
            $stmt->execute();
            $filas_afectadas = $stmt->rowCount();
            error_log("[MARCAR_ENTREGA] Filas afectadas para registro ID $id_registro_tarea: $filas_afectadas");

            if ($filas_afectadas == 0) {
                error_log("[MARCAR_ENTREGA] ADVERTENCIA: La actualización no afectó filas para el registro ID $id_registro_tarea. Verifica el WHERE (id_registro) o si los datos (estado, calificación, observaciones) ya eran los mismos.");
            }
        }

        $pdo->commit();
        $_SESSION['mensaje'] = "Calificaciones y estados actualizados correctamente.";
        $_SESSION['icono'] = "success";

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['mensaje'] = "Error de base de datos: " . $e->getMessage();
        $_SESSION['icono'] = "error";
        // Considera loguear el error $e->getMessage()
    }

    header('Location: ' . APP_URL . '/admin/tareas/show.php?id_tarea=' . $id_tarea);
    exit;

} else {
    $_SESSION['mensaje'] = "Método no permitido.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/admin/tareas/');
    exit;
}
?>
                                estado = :estado, 
                                calificacion = :calificacion,
                                observaciones = :observaciones, /* Asumiendo que tu columna se llama 'observaciones' */
                                fyh_actualizacion = :fyh_actualizacion
                           WHERE id_registro = :id_registro_tarea";
            $stmt = $pdo->prepare($sql_update);
            $stmt->bindParam(':estado', $nuevo_estado_entrega_db);
            $stmt->bindParam(':calificacion', $calificacion);
            $stmt->bindParam(':observaciones', $observaciones_docente);
            $stmt->bindParam(':fyh_actualizacion', $fyh_actualizacion);
            $stmt->bindParam(':id_registro_tarea', $id_registro_tarea, PDO::PARAM_INT);
            
            $stmt->execute();
            $filas_afectadas = $stmt->rowCount();
            error_log("[MARCAR_ENTREGA] Filas afectadas para registro ID $id_registro_tarea: $filas_afectadas");

            if ($filas_afectadas == 0) {
                error_log("[MARCAR_ENTREGA] ADVERTENCIA: La actualización no afectó filas para el registro ID $id_registro_tarea. Verifica el WHERE (id_registro) o si los datos (estado, calificación, observaciones) ya eran los mismos.");
            }
        }

        $pdo->commit();
        $_SESSION['mensaje'] = "Calificaciones y estados actualizados correctamente.";
        $_SESSION['icono'] = "success";

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['mensaje'] = "Error de base de datos: " . $e->getMessage();
        $_SESSION['icono'] = "error";
        // Considera loguear el error $e->getMessage()
    }

    header('Location: ' . APP_URL . '/admin/tareas/show.php?id_tarea=' . $id_tarea);
    exit;

} else {
    $_SESSION['mensaje'] = "Método no permitido.";
    $_SESSION['icono'] = "error";
    header('Location: ' . APP_URL . '/admin/tareas/');
    exit;
}
?>