<?php
include ('../../app/config.php');
include ('../../admin/layout/parte1.php'); // Usamos el layout de admin por ahora

// Incluimos el nuevo controlador
include ('../../app/controllers/reportes/detalle_hijo_controller.php');

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-8">
                    <h1 class="m-0">Reportes de: <?= htmlspecialchars($estudiante_seleccionado_data['apellidos'] ?? ''); ?>, <?= htmlspecialchars($estudiante_seleccionado_data['nombres'] ?? 'Estudiante'); ?></h1>
                    <p class="text-muted">
                        Nivel: <?= htmlspecialchars($estudiante_seleccionado_data['nombre_nivel'] ?? ''); ?> (<?= htmlspecialchars($estudiante_seleccionado_data['turno'] ?? ''); ?>) - 
                        Grado: <?= htmlspecialchars($estudiante_seleccionado_data['nombre_grado'] ?? ''); ?> <?= htmlspecialchars($estudiante_seleccionado_data['paralelo'] ?? ''); ?>
                    </p>
                </div><!-- /.col -->
                <div class="col-sm-4">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= APP_URL; ?>/admin/reportes/">Mis Hijos</a></li>
                        <li class="breadcrumb-item active">Detalle Reporte</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <?php if (isset($_SESSION['mensaje'])): // Mostrar mensajes de sesión ?>
                <div class="row">
                    <div class="col-md-12">
                        <script>
                            Swal.fire({
                                icon: '<?= $_SESSION['icono']; ?>',
                                title: '<?= $_SESSION['mensaje']; ?>',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        </script>
                    </div>
                </div>
                <?php 
                    unset($_SESSION['mensaje']);
                    unset($_SESSION['icono']);
                ?>
            <?php endif; ?>

            <!-- Reporte de Asistencia -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-calendar-check mr-1"></i> Reporte de Asistencia</h3>
                        </div>
                        <div class="card-body table-responsive p-0" style="max-height: 300px;">
                            <?php if (!empty($reporte_asistencia)): ?>
                                <table class="table table-sm table-hover table-head-fixed">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                            <th>Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($reporte_asistencia as $asistencia): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($asistencia['fecha_formato']); ?></td>
                                                <td>
                                                    <?php 
                                                        $estado_as = htmlspecialchars($asistencia['estado_asistencia']);
                                                        $badge_class = 'secondary';
                                                        if (str_contains($estado_as, 'presente')) $badge_class = 'success';
                                                        if (str_contains($estado_as, 'ausente')) $badge_class = 'danger';
                                                        if (str_contains($estado_as, 'retraso')) $badge_class = 'warning';
                                                        echo "<span class='badge badge-$badge_class'>" . ucfirst(str_replace('_', ' ', $estado_as)) . "</span>";
                                                    ?>
                                                </td>
                                                <td><?= htmlspecialchars($asistencia['observaciones'] ?? 'N/A'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="p-3">No hay registros de asistencia para mostrar.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reporte de Conducta -->
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-gavel mr-1"></i> Reporte de Conducta</h3>
                        </div>
                        <div class="card-body table-responsive p-0" style="max-height: 300px;">
                            <?php if (!empty($reporte_conducta)): ?>
                                <table class="table table-sm table-hover table-head-fixed">
                                    <thead>
                                        <tr>
                                            <th>Fecha Suceso</th>
                                            <th>Tipo Incidente</th>
                                            <th>Naturaleza</th>
                                            <th>Descripción</th>
                                            <th>Medidas Tomadas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($reporte_conducta as $conducta): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($conducta['fecha_suceso_formato']); ?></td>
                                                <td><?= htmlspecialchars($conducta['nombre_tipo']); ?> (<?= htmlspecialchars($conducta['gravedad_nivel'] ?? 'N/A'); ?>)</td>
                                                <td>
                                                    <?php
                                                        if ($conducta['naturaleza'] == 'falta') echo '<span class="badge badge-danger">Falta</span>';
                                                        elseif ($conducta['naturaleza'] == 'merito') echo '<span class="badge badge-success">Mérito</span>';
                                                        else echo htmlspecialchars($conducta['naturaleza']);
                                                    ?>
                                                </td>
                                                <td><?= nl2br(htmlspecialchars($conducta['descripcion_detallada'])); ?></td>
                                                <td><?= nl2br(htmlspecialchars($conducta['medidas_tomadas'] ?? 'N/A')); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="p-3">No hay registros de conducta para mostrar.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reporte de Tareas -->
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card card-danger card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-tasks mr-1"></i> Reporte de Tareas</h3>
                        </div>
                        <div class="card-body table-responsive p-0" style="max-height: 400px;">
                            <?php if (!empty($reporte_tareas)): ?>
                                <table class="table table-sm table-hover table-head-fixed">
                                    <thead>
                                        <tr>
                                            <th>Tarea</th>
                                            <th>Materia</th>
                                            <th>F. Asignación</th>
                                            <th>F. Límite</th>
                                            <th>Estado Entrega</th>
                                            <th>F. Entrega Est.</th>
                                            <th>Calificación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($reporte_tareas as $tarea): ?>
                                            <tr>
                                                <td>
                                                    <a href="<?= APP_URL; ?>/admin/tareas/vista_detalle_tarea.php?id_tarea=<?= htmlspecialchars($tarea['id_tarea'] ?? 0); ?>">
                                                        <?= htmlspecialchars($tarea['titulo_tarea']); ?>
                                                    </a>
                                                </td>
                                                <td><?= htmlspecialchars($tarea['nombre_materia']); ?></td>
                                                <td><?= htmlspecialchars($tarea['fecha_asignacion_tarea']); ?></td>
                                                <td><?= htmlspecialchars($tarea['fecha_limite_tarea']); ?></td>
                                                <td>
                                                    <?php 
                                                        $estado_tarea_est = htmlspecialchars($tarea['estado_entrega_estudiante']);
                                                        $badge_class_tarea = 'secondary';
                                                        if ($estado_tarea_est == 'entregado') $badge_class_tarea = 'info';
                                                        if ($estado_tarea_est == 'evaluado') $badge_class_tarea = 'success';
                                                        if ($estado_tarea_est == 'pendiente') $badge_class_tarea = 'warning';
                                                        if ($estado_tarea_est == 'no_entregado') $badge_class_tarea = 'danger';
                                                        echo "<span class='badge badge-$badge_class_tarea'>" . ucfirst(str_replace('_', ' ', $estado_tarea_est)) . "</span>";
                                                    ?>
                                                </td>
                                                <td><?= htmlspecialchars($tarea['fecha_entrega_estudiante'] ?? 'N/A'); ?></td>
                                                <td><?= htmlspecialchars($tarea['calificacion'] ?? 'N/A'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="p-3">No hay tareas registradas para este estudiante.</p>
                            <?php endif; ?>
                        </div>
                    </div>
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