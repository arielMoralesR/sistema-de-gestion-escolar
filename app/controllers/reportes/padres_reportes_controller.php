<?php
/**
 * Controlador para la vista de reportes de los padres.
 * Obtiene los hijos del padre/madre logueado.
 */

// config.php ya incluye session_start() y la conexión $pdo

$hijos_del_padre = [];
$nombre_padre_logueado = "Padre/Madre"; // Valor por defecto

// Definir el ID del rol para Padres/Madres de Familia (ajusta según tu BD)
define('ROL_PADRE_ID', 9); // Asume que el rol de padre tiene id 7

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol_id'])) {
    $_SESSION['mensaje'] = "Debe iniciar sesión para ver los reportes.";
    $_SESSION['icono'] = "warning";
    header('Location: ' . APP_URL . '/login');
    exit;
}

if ($_SESSION['rol_id'] != ROL_PADRE_ID) {
    $_SESSION['mensaje'] = "Acceso restringido. Esta sección es solo para padres/madres de familia.";
    $_SESSION['icono'] = "error";
    // Redirigir a la página principal del admin o a una página de error apropiada
    header('Location: ' . APP_URL . '/admin/'); 
    exit;
}

$usuario_id_sesion = $_SESSION['usuario_id'];

try {
    // 1. Obtener la persona_id del padre/madre a partir del usuario_id
    $sql_persona_padre = "SELECT id_persona, nombres, apellidos FROM personas WHERE usuario_id = :usuario_id AND estado = '1'";
    $query_persona_padre = $pdo->prepare($sql_persona_padre);
    $query_persona_padre->bindParam(':usuario_id', $usuario_id_sesion, PDO::PARAM_INT);
    $query_persona_padre->execute();
    $persona_padre_data = $query_persona_padre->fetch(PDO::FETCH_ASSOC);

    if ($persona_padre_data) {
        $persona_id_padre = $persona_padre_data['id_persona'];
        $nombre_padre_logueado = htmlspecialchars($persona_padre_data['nombres'] . ' ' . $persona_padre_data['apellidos']);

        // 2. Obtener el id_ppff del padre/madre a partir de su persona_id
        $sql_ppff = "SELECT id_ppff FROM ppffs WHERE persona_id = :persona_id AND estado = '1'";
        $query_ppff = $pdo->prepare($sql_ppff);
        $query_ppff->bindParam(':persona_id', $persona_id_padre, PDO::PARAM_INT);
        $query_ppff->execute();
        $ppff_data = $query_ppff->fetch(PDO::FETCH_ASSOC);

        if ($ppff_data) {
            $id_ppff = $ppff_data['id_ppff'];

            // 3. Obtener los hijos asociados a este id_ppff
            $sql_hijos = "SELECT e.id_estudiante, p.nombres, p.apellidos 
                          FROM estudiante_ppff ep
                          INNER JOIN estudiantes e ON ep.estudiante_id = e.id_estudiante
                          INNER JOIN personas p ON e.persona_id = p.id_persona
                          WHERE ep.ppff_id = :id_ppff AND e.estado = '1' AND p.estado = '1' AND ep.estado = '1'
                          ORDER BY p.apellidos, p.nombres";
            $query_hijos = $pdo->prepare($sql_hijos);
            $query_hijos->bindParam(':id_ppff', $id_ppff, PDO::PARAM_INT);
            $query_hijos->execute();
            $hijos_del_padre = $query_hijos->fetchAll(PDO::FETCH_ASSOC);
        }
    }
} catch (PDOException $e) {
    // Manejar errores de base de datos, por ejemplo, loguear el error.
    // Para el usuario, podría ser un mensaje genérico.
    error_log("Error en padres_reportes_controller: " . $e->getMessage());
    $_SESSION['mensaje'] = "Ocurrió un error al cargar la información. Intente más tarde.";
     // Mostrar el error SQL real para depuración (¡NO USAR EN PRODUCCIÓN!)
     //$_SESSION['mensaje'] = "Error al cargar información: " . htmlspecialchars($e->getMessage());
   
    $_SESSION['icono'] = "error";
    // Podrías redirigir o simplemente dejar que la vista muestre el mensaje.
}
?>