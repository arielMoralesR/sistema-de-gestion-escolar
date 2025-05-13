<?php
include ('../../app/config.php');

$id_estudiante_sesion = $_SESSION['estudiante_id'] ?? null;
$rol_id_sesion = $_SESSION['rol_id'] ?? null; // Usamos rol_id en lugar de rol_nombre
// var_dump($_SESSION); // Comenta o elimina estas líneas de depuración
// die(); 
// La verificación de rol y la redirección DEBEN ir ANTES de cualquier salida HTML
if ($rol_id_sesion != 8 || !$id_estudiante_sesion) { // Comparamos con el ID de rol para ESTUDIANTE (asumiendo que es 8)
    $_SESSION['mensaje'] = "Acceso no autorizado. Debe iniciar sesión como estudiante.";
    $_SESSION['icono'] = "warning";
    header('Location: ' . APP_URL . '/login');
    exit;
}
 
include ('../../admin/layout/parte1.php'); // Asumiendo que los estudiantes también usan este layout
include ('../../app/controllers/tareas/listado_tareas_estudiante.php'); // Controlador para obtener tareas del estudiante
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Mis Deberes y Tareas</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <?php include ('../../layout/mensajes.php'); // Para mostrar mensajes de sesión ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">Tu Voz Cuenta</h3>
                        </div>
                        <div class="card-body">
                            <p>Si tienes alguna sugerencia para mejorar nuestra institución, o necesitas reportar un incidente (como bullying u otra situación), por favor utiliza el siguiente enlace. Tu comunicación es importante para nosotros.</p>
                            <a href="<?= APP_URL; ?>/admin/comunicaciones/create.php" class="btn btn-primary"><i class="bi bi-envelope-plus"></i> Enviar Sugerencia o Reporte</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">Listado de Tareas Asignadas</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($tareas_del_estudiante)): ?>
                                <table id="tabla_mis_deberes" class="table table-striped table-bordered table-hover table-sm">
                                    <thead>
                                    <tr>
                                        <th style="text-align: center">Nro</th>
                                        <th>Título Tarea</th>
                                        <th>Materia</th>
                                        <th style="text-align: center">Fecha Límite Entrega</th>
                                        <th style="text-align: center">Mi Estado</th>
                                        <th style="text-align: center">Calificación</th>
                                        <th style="text-align: center">Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $contador_tareas_estudiante = 0;
                                    foreach ($tareas_del_estudiante as $tarea_est_data){
                                        $contador_tareas_estudiante++; ?>
                                        <tr>
                                            <td style="text-align: center"><?=$contador_tareas_estudiante;?></td>
                                            <td><?=htmlspecialchars($tarea_est_data['titulo']);?></td>
                                            <td><?=htmlspecialchars($tarea_est_data['nombre_materia']);?></td>
                                            <td style="text-align: center;"><?=htmlspecialchars($tarea_est_data['fecha_entrega_formato']);?></td>
                                            <td style="text-align: center;">
                                                <?php
                                                    $estado_est = strtolower($tarea_est_data['estado_entrega_estudiante']);
                                                    if ($estado_est == 'entregado') echo '<span class="badge badge-info">Entregada</span>';
                                                    elseif ($estado_est == 'evaluado') echo '<span class="badge badge-success">Evaluada</span>';
                                                    elseif ($estado_est == 'no_entregado') echo '<span class="badge badge-danger">No Entregada</span>';
                                                    else echo '<span class="badge badge-warning">Pendiente</span>';
                                                ?>
                                            </td>
                                            <td style="text-align: center;"><?=htmlspecialchars($tarea_est_data['calificacion'] ?? 'N/A');?></td>
                                            <td style="text-align: center">
                                                <div class="btn-group" role="group" aria-label="Acciones Tarea Estudiante">
                                                    <a href="<?= APP_URL; ?>/admin/tareas/ver_tarea_estudiante.php?id_tarea=<?= $tarea_est_data['id_tarea']; ?>&id_registro=<?= $tarea_est_data['id_registro_tarea']; ?>" type="button" class="btn btn-info btn-sm"><i class="bi bi-eye"></i> Ver/Entregar</a>
                                                </div>
                                            </td>
                                        </tr> 
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p>No tienes deberes o tareas asignadas por el momento.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
include ('../../admin/layout/parte2.php');
// include ('../../layout/mensajes.php'); // Movido arriba para que se muestre antes de la tabla
?>

<script>
    $(function () {
        $("#tabla_mis_deberes").DataTable({
            "pageLength": 10, // Mostrar más tareas por defecto si lo deseas
            "language": { /* ... (tu configuración de idioma para DataTables) ... */ },
            "responsive": true, "lengthChange": true, "autoWidth": false
        });
    });
</script>