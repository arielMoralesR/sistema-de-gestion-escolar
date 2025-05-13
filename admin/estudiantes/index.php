<?php
include ('../../app/config.php');
include ('../../admin/layout/parte1.php');

// Incluimos el controlador corregido para obtener los estudiantes
include ('../../app/controllers/estudiantes/listado_de_estudiantes.php');

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Listado de Estudiantes</h1>
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
                            <h3 class="card-title">Estudiantes Registrados y Activos</h3>
                            <div class="card-tools">
                                <a href="<?=APP_URL;?>/admin/inscripciones/create.php" class="btn btn-primary"><i class="bi bi-plus-square"></i> Nueva Inscripción</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="tabla_estudiantes" class="table table-striped table-bordered table-hover table-sm">
                                <thead>
                                <tr>
                                    <th style="text-align: center">Nro</th>
                                    <th>Apellidos y Nombres</th>
                                    <th>CI</th>
                                    <th>RUDE</th>
                                    <th>Nivel</th>
                                    <th>Grado</th>
                                    <th style="text-align: center">Estado</th>
                                    <th style="text-align: center">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $contador_estudiantes = 0;
                                foreach ($estudiantes as $estudiante_data){
                                    $id_estudiante_actual = $estudiante_data['id_estudiante'];
                                    $contador_estudiantes++; ?>
                                    <tr>
                                        <td style="text-align: center"><?=$contador_estudiantes;?></td>
                                        <td><?=htmlspecialchars($estudiante_data['apellidos_estudiante'] . ', ' . $estudiante_data['nombres_estudiante']);?></td>
                                        <td><?=htmlspecialchars($estudiante_data['ci_estudiante']);?></td>
                                        <td><?=htmlspecialchars($estudiante_data['rude']);?></td>
                                        <td><?=htmlspecialchars($estudiante_data['nombre_nivel'] . ' - ' . $estudiante_data['turno_nivel']);?></td>
                                        <td><?=htmlspecialchars($estudiante_data['nombre_grado'] . ' ' . $estudiante_data['paralelo_grado']);?></td>
                                        <td style="text-align: center;">
                                            <?php
                                            if($estudiante_data['estado_estudiante'] == "1") {
                                                echo '<span class="badge badge-success">Activo</span>';
                                            } else {
                                                echo '<span class="badge badge-danger">Inactivo</span>';
                                            }
                                            ?>
                                        </td>
                                        <td style="text-align: center">
                                            <div class="btn-group" role="group" aria-label="Acciones">
                                                <a href="show.php?id_estudiante=<?=$id_estudiante_actual;?>" type="button" class="btn btn-info btn-sm"><i class="bi bi-eye"></i> Ver</a>
                                                <a href="edit.php?id_estudiante=<?=$id_estudiante_actual;?>" type="button" class="btn btn-success btn-sm"><i class="bi bi-pencil"></i> Editar</a>
                                                <!-- Lógica para eliminar/desactivar podría ir aquí -->
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
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
include ('../../layout/mensajes.php');
?>

<script>
    $(function () {
        $("#tabla_estudiantes").DataTable({
            "responsive": true, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
            "language": {
                "url": "<?=APP_URL;?>/public/dist/Spanish.json" // Ajusta la ruta si es necesario
            }
        }).buttons().container().appendTo('#tabla_estudiantes_wrapper .col-md-6:eq(0)');
    });
</script>