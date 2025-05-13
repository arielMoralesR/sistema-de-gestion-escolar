<?php
include ('../../app/config.php');
include ('../../app/controllers/tareas/ver_tarea_estudiante_controller.php'); // Incluye el nuevo controlador
include ('../../admin/layout/parte1.php'); // Asumiendo que los estudiantes usan el mismo layout

// Doble verificación de sesión de estudiante por si acaso
if (!isset($_SESSION['estudiante_id'])) {
    $_SESSION['mensaje'] = "Debe iniciar sesión como estudiante para ver esta página.";
    $_SESSION['icono'] = "warning";
    header('Location: ' . APP_URL . '/login');
    exit;
}
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detalle de Tarea</h1>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Tarea: <?= htmlspecialchars($tarea_detalle_estudiante['titulo'] ?? 'N/A'); ?></h3>
                            <div class="card-tools">
                                <a href="<?= APP_URL; ?>/admin/deberes/" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left-short"></i> Volver a Mis Deberes</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if ($tarea_detalle_estudiante && $registro_tarea_estudiante): ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Materia:</strong> <?= htmlspecialchars($tarea_detalle_estudiante['nombre_materia']); ?></p>
                                        <p><strong>Docente:</strong> <?= htmlspecialchars($tarea_detalle_estudiante['nombre_docente']); ?></p>
                                        <p><strong>Grado:</strong> <?= htmlspecialchars($tarea_detalle_estudiante['grado_completo']); ?></p>
                                        <p><strong>Nivel:</strong> <?= htmlspecialchars($tarea_detalle_estudiante['nivel_completo']); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Fecha de Asignación:</strong> <?= htmlspecialchars($tarea_detalle_estudiante['fecha_asignacion_formato']); ?></p>
                                        <p><strong>Fecha Límite de Entrega:</strong> <span class="text-danger font-weight-bold"><?= htmlspecialchars($tarea_detalle_estudiante['fecha_entrega_formato']); ?></span></p>
                                    </div>
                                </div>
                                <hr>
                                <h4>Descripción de la Tarea:</h4>
                                <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; border: 1px solid #dee2e6;">
                                    <?= nl2br(htmlspecialchars($tarea_detalle_estudiante['descripcion'])); ?>
                                </div>
                                
                                <!-- Aquí podrías añadir un enlace al archivo adjunto de la tarea si existe -->
                                <!-- Ejemplo:
                                <?php if (!empty($tarea_detalle_estudiante['archivo_tarea'])): // Asumiendo que tienes 'archivo_tarea' en la consulta ?>
                                    <hr>
                                    <p><strong>Archivo Adjunto por el Docente:</strong> 
                                        <a href="<?= APP_URL . '/public/archivos_tareas_docente/' . htmlspecialchars($tarea_detalle_estudiante['archivo_tarea']); ?>" target="_blank">
                                            <i class="bi bi-paperclip"></i> Descargar Archivo
                                        </a>
                                    </p>
                                <?php endif; ?>
                                -->

                                <hr>
                                <h4>Mi Entrega:</h4>
                                <div class="row">
                                    <div class="col-md-4">
                                        <p><strong>Estado de mi entrega:</strong> 
                                            <?php
                                                $estado_est = strtolower($registro_tarea_estudiante['estado_entrega']);
                                                if ($estado_est == 'entregado') echo '<span class="badge badge-info">Entregada</span>';
                                                elseif ($estado_est == 'evaluado') echo '<span class="badge badge-success">Evaluada</span>';
                                                elseif ($estado_est == 'no_entregado') echo '<span class="badge badge-danger">No Entregada</span>';
                                                else echo '<span class="badge badge-warning">Pendiente</span>';
                                            ?>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Fecha de mi entrega:</strong> <?= htmlspecialchars($registro_tarea_estudiante['fecha_entrega_realizada_formato'] ?? 'Aún no entregada'); ?></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Calificación:</strong> <span class="font-weight-bold"><?= htmlspecialchars($registro_tarea_estudiante['calificacion'] ?? 'Sin calificar'); ?></span></p>
                                    </div>
                                </div>
                                <?php if (!empty($registro_tarea_estudiante['observaciones_docente'])): ?>
                                    <p><strong>Observaciones del Docente:</strong></p>
                                    <div style="background-color: #e9ecef; padding: 10px; border-radius: 5px;">
                                        <?= nl2br(htmlspecialchars($registro_tarea_estudiante['observaciones_docente'])); ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Aquí iría el formulario para que el estudiante suba su tarea -->
                                <hr>
                                <h4>Realizar Entrega / Ver mi Archivo Entregado:</h4>
                                <?php if (strtolower($registro_tarea_estudiante['estado_entrega']) == 'pendiente' || strtolower($registro_tarea_estudiante['estado_entrega']) == 'no_entregado'): ?>
                                    <p>Puedes adjuntar tu archivo aquí (si es necesario) y marcar la tarea como entregada.</p>
                                    <!-- Formulario de entrega -->
                                    <form action="<?= APP_URL; ?>/app/controllers/tareas/entregar_tarea_estudiante.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="id_registro_tarea" value="<?= htmlspecialchars($registro_tarea_estudiante['id_registro']); ?>">
                                        <input type="hidden" name="id_tarea" value="<?= htmlspecialchars($tarea_detalle_estudiante['id_tarea']); ?>">
                                        <div class="form-group">
                                            <label for="archivo_estudiante">Adjuntar archivo (opcional):</label>
                                            <input type="file" name="archivo_estudiante" id="archivo_estudiante" class="form-control-file">
                                        </div>
                                        <button type="submit" name="marcar_como_entregada" class="btn btn-success"><i class="bi bi-check-circle"></i> Marcar como Entregada</button>
                                    </form>
                                <?php else: ?>
                                    <p>Ya has realizado la entrega de esta tarea o ya ha sido evaluada.</p>
                                    <!-- Aquí podrías mostrar el archivo que el estudiante subió, si lo tienes guardado -->
                                <?php endif; ?>

                            <?php else: ?>
                                <div class="alert alert-warning">No se pudo cargar la información completa de la tarea. Por favor, inténtelo de nuevo o contacte al soporte.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include ('../../admin/layout/parte2.php');
include ('../../layout/mensajes.php');
?>