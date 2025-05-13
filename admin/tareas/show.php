<?php
include ('../../app/config.php');
include ('../../app/controllers/tareas/show_tarea_controller.php'); // Incluye el nuevo controlador
include ('../../admin/layout/parte1.php');

if (!isset($_SESSION['docente_id'])) { // Doble verificación por si acaso
    $_SESSION['mensaje'] = "Debe iniciar sesión como docente para ver esta página.";
    $_SESSION['icono'] = "warning";
    header('Location: ' . APP_URL . '/login');
    exit;
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detalle de la Tarea</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Tarea: <?= htmlspecialchars($tarea_data['titulo'] ?? 'N/A'); ?></h3>
                            <div class="card-tools">
                                <a href="<?= APP_URL; ?>/admin/tareas/" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left-short"></i> Volver al Listado</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if ($tarea_data): ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Título:</strong> <?= htmlspecialchars($tarea_data['titulo']); ?></p>
                                        <p><strong>Materia:</strong> <?= htmlspecialchars($tarea_data['nombre_materia']); ?></p>
                                        <p><strong>Nivel:</strong> <?= htmlspecialchars($tarea_data['nombre_nivel'] . ' - ' . $tarea_data['turno']); ?></p>
                                        <p><strong>Grado:</strong> <?= htmlspecialchars($tarea_data['nombre_grado'] . ' ' . $tarea_data['paralelo']); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Fecha de Asignación:</strong> <?= htmlspecialchars($tarea_data['fecha_asignacion_formato']); ?></p>
                                        <p><strong>Fecha Límite de Entrega:</strong> <?= htmlspecialchars($tarea_data['fecha_entrega_formato']); ?></p>
                                        <p><strong>Estado de la Tarea:</strong>
                                            <?php
                                            if ($tarea_data['estado_tarea'] == "1") {
                                                echo '<span class="badge badge-success">Activa</span>';
                                            } else {
                                                echo '<span class="badge badge-danger">Inactiva</span>';
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                                <hr>
                                <p><strong>Descripción:</strong></p>
                                <p><?= nl2br(htmlspecialchars($tarea_data['descripcion'])); ?></p>
                                <hr>
                                <!-- ... (código existente de la descripción de la tarea) ... -->
<hr>

<h4>Estadísticas de Entrega</h4>
<div class="row">
    <!-- Small box para Total Asignados -->
    <div class="col-md-4 col-sm-6 col-12">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3><?= $estadisticas_tarea['total_asignados']; ?></h3>
                <p>Total Asignados</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    <!-- Small box para Total Entregados -->
    <div class="col-md-4 col-sm-6 col-12">
        <div class="small-box bg-info"> 
            <div class="inner">
                <h3><?= htmlspecialchars($estadisticas_tarea['total_entregados'] ?? 0); ?> / <?= htmlspecialchars($estadisticas_tarea['total_evaluados'] ?? 0); ?></h3>
                <p>Entregados / Evaluados</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>
    <!-- Small box para Porcentaje de Entregas -->
    <div class="col-md-4 col-sm-6 col-12"> 
        <div class="small-box bg-success">
              <div class="inner">
                <h3><?= number_format($estadisticas_tarea['porcentaje_entregas'] ?? 0.0, 1); ?><sup style="font-size: 0.5em; top: -0.7em;">%</sup></h3>
                <p>Porcentaje de Entregas</p>
              </div>
              <div class="icon"><i class="fas fa-chart-pie"></i> 
              </div>
            </div>
    </div>
</div>
<hr>
<!-- ... (mostrar detalles de la tarea usando $tarea_data) ... -->
<h3>Calificar Entregas de Estudiantes</h3>
<?php if (!empty($estudiantes_asignados)): ?>
    <form action="<?= APP_URL; ?>/app/controllers/tareas/marcar_entrega_controller.php" method="post">
        <input type="hidden" name="id_tarea" value="<?= htmlspecialchars($tarea_data['id_tarea'] ?? ''); ?>">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Estado Entrega</th>
                    <th>Fecha Entrega</th>
                    <th>Archivo Entregado</th>
                    <th>Calificación</th>
                    <th>Observaciones Docente</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estudiantes_asignados as $estudiante): ?>
                    <tr>
                        <td><?= htmlspecialchars($estudiante['apellidos_estudiante'] . ', ' . $estudiante['nombres_estudiante']); ?></td>
                        <td>
                        
<select name="registros[<?= htmlspecialchars($estudiante['id_registro_tarea']); ?>][estado_entrega]" class="form-control form-control-sm">
    <?php 
        // Los valores del ENUM son: 'pendiente', 'entregado', 'evaluado', 'no_entregado'
        // Los valores que se muestran al usuario pueden ser con mayúscula inicial
        $estados_para_select = [
            'pendiente' => 'Pendiente', 
            'entregado' => 'Entregada', 
            'evaluado' => 'Evaluada', // o 'Calificada' si así lo llamas en la UI
            'no_entregado' => 'No Entregada'
        ];
        // Obtener el estado actual en minúsculas para la comparación
        $estado_actual_db = strtolower($estudiante['estado_tarea_estudiante']);

        foreach ($estados_para_select as $valor_db => $texto_mostrar) {
            $selected = ($valor_db == $estado_actual_db) ? 'selected' : '';
            echo "<option value=\"".htmlspecialchars($valor_db)."\" $selected>".htmlspecialchars($texto_mostrar)."</option>";
        }
        // Si el estado actual no está en la lista (poco probable si el ENUM está bien definido),
        // podrías añadir una opción por defecto o manejarlo.
        if (!array_key_exists($estado_actual_db, $estados_para_select) && !empty($estudiante['estado_tarea_estudiante'])) {
             echo "<option value=\"".htmlspecialchars($estado_actual_db)."\" selected>".htmlspecialchars(ucfirst($estado_actual_db))." (Actual)</option>";
        }
    ?>
</select>

                        </td>
                        <td><?= htmlspecialchars($estudiante['fecha_entrega_estudiante_formato'] ?? 'N/A'); ?></td>
                        <td>
                            <?php if (!empty($estudiante['archivo_entrega_estudiante'])): ?>
                                <a href="<?= APP_URL . '/public/archivos_tareas_entregadas/' . htmlspecialchars($estudiante['archivo_entrega_estudiante']); ?>" target="_blank">Ver Archivo</a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td>
                            <input type="hidden" name="registros[<?= htmlspecialchars($estudiante['id_registro_tarea']); ?>][id_registro_tarea]" value="<?= htmlspecialchars($estudiante['id_registro_tarea']); ?>">
                            <input type="number" name="registros[<?= htmlspecialchars($estudiante['id_registro_tarea']); ?>][calificacion]" value="<?= htmlspecialchars($estudiante['calificacion'] ?? ''); ?>" class="form-control form-control-sm" min="0" max="100">
                        </td>
                        <td>
                            <textarea name="registros[<?= htmlspecialchars($estudiante['id_registro_tarea']); ?>][observaciones_docente]" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($estudiante['observaciones_docente'] ?? ''); ?></textarea>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Guardar Calificaciones</button>
    </form>
<?php else: ?>
    <p>No hay estudiantes asignados a esta tarea o aún no se han procesado las inscripciones.</p>
<?php endif; ?>
                            <?php else: ?>
                                <p>No se pudo cargar la información de la tarea.</p>
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
include ('../../layout/mensajes.php'); // Para mostrar mensajes de sesión
?>