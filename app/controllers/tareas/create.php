<?php
include ('../../../app/config.php');

// 1. Verificar sesión y obtener docente_id
session_start();
if(!isset($_SESSION['usuario_id'])) {
    $_SESSION['mensaje'] = "Debe iniciar sesión primero";
    $_SESSION['icono'] = "error";
    header('Location: '.APP_URL.'/login');
    exit;
}

$docente_id = $_SESSION['docente_id'] ?? null;
if(!$docente_id || $docente_id != $_POST['docente_id']) {
    $_SESSION['mensaje'] = "Error de identificación del docente";
    $_SESSION['icono'] = "error";
    header('Location: '.APP_URL.'/login');
    exit;
}

// 2. Validar que el docente imparte la materia asignada
$materia_id = $_POST['materia_id'];
$stmt = $pdo->prepare("SELECT COUNT(*) FROM docente_materias 
                      WHERE docente_id = :docente_id AND materia_id = :materia_id AND estado = '1'");
$stmt->execute([':docente_id' => $docente_id, ':materia_id' => $materia_id]);

if($stmt->fetchColumn() == 0) {
    $_SESSION['mensaje'] = "No tiene permiso para asignar tareas en esta materia";
    $_SESSION['icono'] = "error";
    header('Location: '.APP_URL.'/admin/tareas/create.php');
    exit;
}

// 3. Recibir otros datos del formulario
$nivel_id = $_POST['nivel_id'];
$grado_id = $_POST['grado_id'];
$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$fecha_entrega = $_POST['fecha_entrega'];
$fecha_asignacion = date('Y-m-d H:i:s'); // La fecha de asignación es la actual
$fyh_creacion = date('Y-m-d H:i:s');     // Fecha y hora para los campos de auditoría
$estado_tarea_default = '1';             // Estado por defecto para una nueva tarea (ej: '1' = activa)

// 4. Registrar la tarea
$pdo->beginTransaction();
try {
    // Insertar en tareas
    $stmt_tarea = $pdo->prepare('INSERT INTO tareas 
                                (docente_id, materia_id, nivel_id, grado_id, titulo, descripcion, 
                                 fecha_asignacion, fecha_entrega, fyh_creacion, estado) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    
    $stmt_tarea->execute([
        $docente_id, $materia_id, $nivel_id, $grado_id, $titulo, $descripcion,
        $fecha_asignacion, $fecha_entrega, $fyh_creacion, $estado_tarea_default
    ]);
    
    $tarea_id = $pdo->lastInsertId();
    
    // Obtener estudiantes del grado
    $stmt_estudiantes = $pdo->prepare('SELECT id_estudiante FROM estudiantes 
                                      WHERE grado_id = ? AND estado = "1"');
    $stmt_estudiantes->execute([$grado_id]);
    $estudiantes = $stmt_estudiantes->fetchAll(PDO::FETCH_ASSOC);
    
    // Registrar tareas para cada estudiante
    $stmt_registro = $pdo->prepare('INSERT INTO registro_tareas 
                                  (tarea_id, estudiante_id, estado, fyh_creacion, estado_registro) 
                                  VALUES (?, ?, "pendiente", ?, "1")');
    
    foreach($estudiantes as $estudiante) {
        $stmt_registro->execute([
            $tarea_id, $estudiante['id_estudiante'], $fyh_creacion
        ]);
    }
    
    $pdo->commit();
    
    $_SESSION['mensaje'] = "Tarea asignada correctamente a ".count($estudiantes)." estudiantes";
    $_SESSION['icono'] = "success";
    header('Location: '.APP_URL.'/admin/tareas');
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    $_SESSION['mensaje'] = "Error al asignar tarea: ".$e->getMessage();
    $_SESSION['icono'] = "error";
    header('Location: '.APP_URL.'/admin/tareas/create.php');
    exit;
}