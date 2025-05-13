<?php
include ('../../app/config.php');
include ('../../admin/layout/parte1.php');

// Incluimos el nuevo controlador para obtener los registros de conducta
include ('../../app/controllers/conducta/listado_conducta.php');

// Opcional: Verificar permisos específicos para acceder a esta página
/*
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != ROL_ADMINISTRADOR_ID && $_SESSION['rol_id'] != ROL_DIRECTOR_ID) {
    $_SESSION['mensaje'] = "No tiene permisos para acceder a esta sección.";
    $_SESSION['icono'] = "warning";
    header('Location: ' . APP_URL . '/admin/');
    exit;
}
*/
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Registros de Conducta</h1>
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
                            <h3 class="card-title">Listado de Registros de Conducta</h3>
                            <div class="card-tools">
                                <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-square"></i> Registrar Nuevo Incidente</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="tabla_registros_conducta" class="table table-striped table-bordered table-hover table-sm">
                                <thead>
                                <tr>
                                    <th style="text-align: center">Nro</th>
                                    <th>Estudiante</th>
                                    <th>Tipo de Incidente</th>
                                    <th>Naturaleza</th>
                                    <th style="text-align: center">Fecha y Hora</th>
                                    <th>Reportado Por</th>
                                    <th style="text-align: center">Estado</th>
                                    <th style="text-align: center">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $contador_registros = 0;
                                foreach ($registros_conducta as $registro_data){
                                    $id_registro_actual = $registro_data['id_registro_disciplina'];
                                    $contador_registros++; ?>
                                    <tr>
                                        <td style="text-align: center"><?=$contador_registros;?></td>
                                        <td><?=htmlspecialchars($registro_data['nombre_estudiante']);?></td>
                                        <td><?=htmlspecialchars($registro_data['tipo_incidente']);?></td>
                                        <td>
                                            <?php
                                                if ($registro_data['naturaleza_incidente'] == 'falta') {
                                                    echo '<span class="badge badge-danger">Falta</span>';
                                                } elseif ($registro_data['naturaleza_incidente'] == 'merito') {
                                                    echo '<span class="badge badge-success">Mérito</span>';
                                                } else {
                                                    echo htmlspecialchars($registro_data['naturaleza_incidente']);
                                                }
                                            ?>
                                        </td>
                                        <td style="text-align: center;"><?=htmlspecialchars($registro_data['fecha_suceso_formato']);?></td>
                                        <td><?=htmlspecialchars($registro_data['reportado_por']);?></td>
                                        <td style="text-align: center;">
                                            <?php
                                            if($registro_data['estado_registro_conducta'] == "1") {
                                                echo '<span class="badge badge-success">Activo</span>';
                                            } else {
                                                echo '<span class="badge badge-secondary">Anulado</span>'; // O Inactivo
                                            }
                                            ?>
                                        </td>
                                        <td style="text-align: center">
                                            <div class="btn-group" role="group" aria-label="Acciones">
                                                <a href="show.php?id_registro=<?=$id_registro_actual;?>" type="button" class="btn btn-info btn-sm"><i class="bi bi-eye"></i> Ver</a>
                                                <!-- <a href="edit.php?id_registro=<?=$id_registro_actual;?>" type="button" class="btn btn-success btn-sm"><i class="bi bi-pencil"></i> Editar</a> -->
                                                <!-- Lógica para anular/activar podría ir aquí -->
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
        $("#tabla_registros_conducta").DataTable({
            "responsive": true, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
            "language": {
                "url": "<?=APP_URL;?>/public/dist/Spanish.json" // Ajusta la ruta si es necesario
            }
        }).buttons().container().appendTo('#tabla_registros_conducta_wrapper .col-md-6:eq(0)');
    });
</script>