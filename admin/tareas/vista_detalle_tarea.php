<?php
include ('../../app/config.php');
include ('../../admin/layout/parte1.php'); // Usamos el layout de admin

// Incluimos el controlador para obtener los datos de la tarea
include ('../../app/controllers/tareas/detalle_tarea_controller.php');

// Para el botón "Volver", intentamos obtener la URL del referente.
$url_volver = APP_URL . "/admin/reportes/"; // URL por defecto si no hay referente
if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
    // Validar que el referente sea del mismo sitio para evitar redirecciones abiertas
    if (strpos($_SERVER['HTTP_REFERER'], APP_URL) === 0) {
        $url_volver = $_SERVER['HTTP_REFERER'];
    }
}

// Si se pasó id_estudiante en la URL, podríamos usarlo para un "Volver" más específico
// Ejemplo: if(isset($_GET['id_estudiante'])) { $url_volver = APP_URL . "/admin/reportes/detalle_hijo_reporte.php?id_estudiante=" . $_GET['id_estudiante']; }
// Por ahora, usamos HTTP_REFERER o el general de reportes.

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-9">
                    <h1 class="m-0">
                        Detalle de la Tarea: 
                        <?php if ($tarea_data && isset($tarea_data['titulo_tarea'])): ?>
                            <?= htmlspecialchars($tarea_data['titulo_tarea']); ?>
                        <?php else: ?>
                            Tarea no encontrada
                        <?php endif; ?>
                    </h1>
                </div><!-- /.col -->
                <div class="col-sm-3">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= APP_URL; ?>/admin/">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="<?= $url_volver; ?>">Reportes</a></li>
                        <li class="breadcrumb-item active">Detalle Tarea</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-ban"></i> Error</h5>
                    <?= htmlspecialchars($error_message); ?>
                </div>
            <?php elseif (!$tarea_data): ?>
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Atención</h5>
                    No se pudo cargar la información de la tarea o la tarea no existe.
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Información de la Tarea
                                </h3>
                                <div class="card-tools">
                                    <a href="<?= $url_volver; ?>" class="btn btn-tool"><i class="fas fa-arrow-left"></i> Volver</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Materia:</strong> <?= htmlspecialchars($tarea_data['nombre_materia'] ?? 'N/A'); ?></p>
                                        <p><strong>Docente:</strong> <?= htmlspecialchars($tarea_data['nombre_docente'] ?? 'N/A'); ?></p>
                                        <?php if (isset($tarea_data['nombre_nivel']) && isset($tarea_data['nombre_grado'])): ?>
                                        <p><strong>Curso:</strong> <?= htmlspecialchars($tarea_data['nombre_nivel'] . ' - ' . $tarea_data['nombre_grado'] . ' ' . ($tarea_data['paralelo_descripcion'] ?? '')); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Fecha de Asignación:</strong> <?= htmlspecialchars($tarea_data['fecha_asignacion_formato'] ?? 'N/A'); ?></p>
                                        <p><strong>Fecha Límite de Entrega:</strong> <?= htmlspecialchars($tarea_data['fecha_limite_formato'] ?? 'N/A'); ?></p>
                                        <p><strong>Estado de la Tarea:</strong> <span class="badge badge-info"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $tarea_data['estado_tarea'] ?? 'Desconocido'))); ?></span></p>
                                    </div>
                                </div>
                                <hr>
                                <h5><strong>Descripción / Instrucciones:</strong></h5>
                                <div style="background-color: #f9f9f9; border: 1px solid #eee; padding: 15px; border-radius: 5px; min-height: 100px;">
                                    <?= nl2br(htmlspecialchars($tarea_data['descripcion_tarea'] ?? 'No hay descripción disponible.')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (isset($tarea_data['archivos_adjuntos']) && !empty($tarea_data['archivos_adjuntos'])): ?>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-paperclip mr-1"></i> Archivos Adjuntos</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($tarea_data['archivos_adjuntos'] as $archivo): ?>
                                        <li class="list-group-item">
                                            <i class="fas fa-file-alt mr-2"></i>
                                            <a href="<?= APP_URL . '/' . htmlspecialchars($archivo['ruta_archivo']); ?>" target="_blank" download="<?= htmlspecialchars($archivo['nombre_archivo']); ?>">
                                                <?= htmlspecialchars($archivo['nombre_archivo']); ?>
                                            </a>
                                            <span class="text-muted ml-2">(<?= htmlspecialchars($archivo['tipo_archivo'] ?? 'N/A'); ?>, <?= round(($archivo['tamanio_archivo'] ?? 0) / 1024, 2); ?> KB)</span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            <?php endif; ?>

            <div class="row mb-3">
                <div class="col-md-12">
                    <a href="<?= $url_volver; ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver al Reporte</a>
                </div>
            </div>

        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
include ('../../admin/layout/parte2.php');
?>