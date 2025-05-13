<?php
include ('../../app/config.php');
// Incluimos el nuevo controlador para obtener los datos del estudiante
include ('../../app/controllers/estudiantes/show_controller.php');

include ('../../admin/layout/parte1.php');


?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detalle del Estudiante</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                Estudiante: <?= htmlspecialchars($estudiante_data['apellidos_estudiante'] ?? 'N/A'); ?>, <?= htmlspecialchars($estudiante_data['nombres_estudiante'] ?? 'N/A'); ?>
                            </h3>
                            <div class="card-tools">
                                <a href="<?= APP_URL; ?>/admin/estudiantes/" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left-short"></i> Volver al Listado</a>
                                <a href="edit.php?id_estudiante=<?= $estudiante_data['id_estudiante'] ?? ''; ?>" class="btn btn-success btn-sm"><i class="bi bi-pencil"></i> Editar Estudiante</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if ($estudiante_data): ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4>Datos Personales del Estudiante</h4>
                                        <p><strong>Apellidos y Nombres:</strong> <?= htmlspecialchars($estudiante_data['apellidos_estudiante'] . ', ' . $estudiante_data['nombres_estudiante']); ?></p>
                                        <p><strong>Cédula de Identidad:</strong> <?= htmlspecialchars($estudiante_data['ci_estudiante']); ?></p>
                                        <p><strong>Fecha de Nacimiento:</strong> <?= htmlspecialchars(date("d/m/Y", strtotime($estudiante_data['fecha_nac_estudiante']))); ?></p>
                                        <p><strong>Celular:</strong> <?= htmlspecialchars($estudiante_data['celular_estudiante'] ?? 'N/A'); ?></p>
                                        <p><strong>Dirección:</strong> <?= htmlspecialchars($estudiante_data['direccion_estudiante'] ?? 'N/A'); ?></p>
                                        <?php if (!empty($estudiante_data['email_estudiante'])): ?>
                                            <p><strong>Email (Usuario):</strong> <?= htmlspecialchars($estudiante_data['email_estudiante']); ?></p>
                                        <?php endif; ?>
                                        <p><strong>Estado:</strong> 
                                            <?php
                                            if($estudiante_data['estado_estudiante'] == "1") {
                                                echo '<span class="badge badge-success">Activo</span>';
                                            } else {
                                                echo '<span class="badge badge-danger">Inactivo</span>';
                                            }
                                            ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h4>Datos Académicos</h4>
                                        <p><strong>RUDE:</strong> <?= htmlspecialchars($estudiante_data['rude']); ?></p>
                                        <p><strong>Nivel:</strong> <?= htmlspecialchars($estudiante_data['nombre_nivel'] . ' - ' . $estudiante_data['turno_nivel']); ?></p>
                                        <p><strong>Grado:</strong> <?= htmlspecialchars($estudiante_data['nombre_grado'] . ' ' . $estudiante_data['paralelo_grado']); ?></p>
                                    </div>
                                </div>
                                <hr>
                                <h4>Padres/Madres de Familia Asociados</h4>
                                <?php if (!empty($padres_del_estudiante)): ?>
                                    <table class="table table-bordered table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>Apellidos y Nombres del Padre/Madre</th>
                                                <th>CI</th>
                                                <th>Celular</th>
                                                <th>Ocupación</th>
                                                <th>Email (Usuario)</th>
                                                <th>Parentesco</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($padres_del_estudiante as $padre): ?>
                                                <tr>
                                                    <td>
                                                        <?= htmlspecialchars(($padre['apellidos_ppff'] ?? '') . ', ' . ($padre['nombres_ppff'] ?? '')); ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($padre['ci_ppff']); ?></td>
                                                    <td><?= htmlspecialchars($padre['celular_ppff']); ?></td>
                                                    <td><?= htmlspecialchars($padre['ocupacion_ppff']); ?></td>
                                                    <td><?= htmlspecialchars($padre['email_ppff'] ?? 'N/A'); ?></td>
                                                    <td><?= htmlspecialchars($padre['parentesco']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <p>No hay padres/madres de familia asociados a este estudiante.</p>
                                <?php endif; ?>

                            <?php else: ?>
                                <div class="alert alert-danger" role="alert">
                                    No se pudo cargar la información del estudiante.
                                </div>
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
include ('../../layout/mensajes.php'); // Para mostrar mensajes de sesión
?>