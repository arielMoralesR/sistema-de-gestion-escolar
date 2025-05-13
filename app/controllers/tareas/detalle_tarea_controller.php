<?php
/**
 * Controlador para obtener los detalles de una tarea específica.
 * Este script es incluido por la vista que muestra los detalles de la tarea.
 */

// La sesión ya debería estar iniciada por config.php o el layout.
// config.php se incluye desde la vista que llama a este controlador.

// Variables para almacenar los datos de la tarea y mensajes
$tarea_data = null;
$error_message = '';
$id_tarea_get = null;

// Verificar si se proporcionó id_tarea y es un número válido
if (isset($_GET['id_tarea'])) {
    if (filter_var($_GET['id_tarea'], FILTER_VALIDATE_INT) && $_GET['id_tarea'] > 0) {
        $id_tarea_get = (int)$_GET['id_tarea'];
    } else {
        $error_message = "El ID de la tarea proporcionado no es válido.";
    }
} else {
    $error_message = "No se especificó un ID de tarea.";
}

if (empty($error_message) && $id_tarea_get) {
    try {
        // La conexión PDO $pdo ya debería estar disponible si config.php se incluyó correctamente
        // en la vista que llama a este controlador.
        if (!isset($pdo)) {
            // Esto es un fallback, idealmente $pdo ya existe.
            // Asegúrate de que config.php define $pdo globalmente o lo retorna.
            // Si config.php usa 'new PDO(...)' sin asignarlo a $pdo, necesitarás ajustar.
            // Por ahora, asumimos que $pdo está disponible.
            // Si no, deberías instanciarlo aquí como en otros controladores:
            // $pdo = new PDO($servidor, $usuario, $password, $opciones);
            // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Pero esto es redundante si config.php lo hace.
        }

        // Consulta para obtener los detalles de la tarea
        // Ajusta los JOINs y campos según tu esquema de base de datos.
       $sql_tarea = "SELECT
                        t.id_tarea,
                        t.titulo AS titulo_tarea,          -- Alias para coincidir con la vista
                        t.descripcion AS descripcion_tarea,  -- Alias para coincidir con la vista
                        DATE_FORMAT(t.fecha_asignacion, '%d/%m/%Y %H:%i') as fecha_asignacion_formato,
                         DATE_FORMAT(t.fecha_entrega, '%d/%m/%Y %H:%i') as fecha_limite_formato, -- 'fecha_entrega' en DB es la fecha límite
                        t.estado AS estado_tarea,          -- Estado de la tarea en sí (ej. activa, borrador, etc.)
                        m.nombre_materia,
                        CONCAT(p_docente.nombres, ' ', p_docente.apellidos) as nombre_docente,
                        n.nivel AS nombre_nivel,            -- 'nivel' es el nombre en la tabla niveles
                        g.curso AS nombre_grado,            -- 'curso' es el nombre en la tabla grados
                        g.paralelo AS paralelo_descripcion  -- 'paralelo' en la tabla grados
                    FROM tareas AS t
                    INNER JOIN materias AS m ON t.materia_id = m.id_materia
                    INNER JOIN docentes AS doc ON t.docente_id = doc.id_docente
                    INNER JOIN personas AS p_docente ON doc.persona_id = p_docente.id_persona
                    INNER JOIN niveles AS n ON t.nivel_id = n.id_nivel
                    INNER JOIN grados AS g ON t.grado_id = g.id_grado
                    WHERE t.id_tarea = :id_tarea AND t.estado = '1'"; // Asumiendo t.estado = '1' para activas/visibles


        $query_tarea = $pdo->prepare($sql_tarea);
        $query_tarea->bindParam(':id_tarea', $id_tarea_get, PDO::PARAM_INT);
        $query_tarea->execute();
        $tarea_data = $query_tarea->fetch(PDO::FETCH_ASSOC);

        if (!$tarea_data) {
            $error_message = "Tarea no encontrada o no está disponible.";
        } else {
            // Opcional: Cargar archivos adjuntos si existen
             // -- INICIO SECCIÓN ARCHIVOS ADJUNTOS (COMENTADA POR FALTA DE TABLA EN DB.SQL) --
            // Si tienes una tabla para archivos adjuntos, descomenta y ajusta esta sección.
            // Ejemplo:
            // $sql_archivos = "SELECT id_archivo_tarea, nombre_archivo, ruta_archivo, tipo_archivo, tamanio_archivo
            //                  FROM tu_tabla_de_archivos_tareas 
            //                  WHERE tarea_id = :id_tarea AND estado = '1'";
            // $query_archivos = $pdo->prepare($sql_archivos);
            // $query_archivos->bindParam(':id_tarea', $id_tarea_get, PDO::PARAM_INT);
            // $query_archivos->execute();
            // $tarea_data['archivos_adjuntos'] = $query_archivos->fetchAll(PDO::FETCH_ASSOC);
            // -- FIN SECCIÓN ARCHIVOS ADJUNTOS --
            $tarea_data['archivos_adjuntos'] = []; // Inicializar como array vacío si no hay tabla
        
        }

    } catch (PDOException $e) {
        $error_message = "Error al conectar o consultar la base de datos: " . $e->getMessage();
        // En un entorno de producción, es mejor no mostrar $e->getMessage() directamente.
        // Se recomienda registrar el error en un archivo de log.
        error_log("PDOException en detalle_tarea_controller.php: " . $e->getMessage());
    } catch (Exception $e) {
        $error_message = "Error general: " . $e->getMessage();
        error_log("Exception en detalle_tarea_controller.php: " . $e->getMessage());
    }
}

// Las variables $tarea_data y $error_message estarán disponibles para la vista
// que incluya este controlador.
?>