<?php
include ('../../app/config.php');
include ('../../admin/layout/parte1.php');

// Verificar si el usuario es un docente y tiene docente_id en sesión
// parte1.php ya maneja la sesión general, pero aquí necesitamos específicamente docente_id
if (!isset($_SESSION['docente_id'])) {
    $_SESSION['mensaje'] = "Debe iniciar sesión como docente para ver esta página.";
    $_SESSION['icono'] = "warning";
    header('Location: ' . APP_URL . '/login'); // O a una página de error/dashboard principal
    exit;
}

include ('../../app/controllers/tareas/listado_de_tareas.php'); // Incluimos el nuevo controlador
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <br>
    <div class="content">
        <div class="container">
            <div class="row">
                <h1>Mis Tareas Asignadas</h1>
            </div>
            <br>
            <div class="row">

                <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">tareas asignadas</h3>
                            <div class="card-tools">
                                <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-square"></i> Crear Nueva Tarea</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-striped table-bordered table-hover table-sm">
                                <thead>
                                <tr>
                                    <th style="text-align: center">Nro</th>
                                    <th>Título</th>
                                    <th>Materia</th>
                                    <th>Nivel</th>
                                    <th>Grado</th>
                                    <th style="text-align: center">F. Asignación</th>
                                    <th style="text-align: center">F. Entrega</th>
                                    <th style="text-align: center">Estado</th>
                                    <th style="text-align: center">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $contador_tareas = 0;
                                // Usamos la variable $tareas_del_docente del controlador
                                foreach ($tareas_del_docente as $tarea_data){ 
                                    $id_tarea_actual = $tarea_data['id_tarea'];
                                    $contador_tareas = $contador_tareas +1; ?>
                                    <tr>
                                        <td style="text-align: center"><?=$contador_tareas;?></td>
                                        <td><?=htmlspecialchars($tarea_data['titulo']);?></td>
                                        <td><?=htmlspecialchars($tarea_data['nombre_materia']);?></td>
                                        <td><?=htmlspecialchars($tarea_data['nombre_nivel'] . ' - ' . $tarea_data['turno']);?></td>
                                        <td><?=htmlspecialchars($tarea_data['nombre_grado'] . ' ' . $tarea_data['paralelo']);?></td>
                                        <td style="text-align: center;"><?=htmlspecialchars($tarea_data['fecha_asignacion_formato']);?></td>
                                        <td style="text-align: center;"><?=htmlspecialchars($tarea_data['fecha_entrega_formato']);?></td>
                                        <td style="text-align: center;">
                                            <?php
                                            if($tarea_data['estado_tarea'] == "1") {
                                                echo '<span class="badge badge-success">Activa</span>';
                                            } else {
                                                echo '<span class="badge badge-danger">Inactiva</span>';
                                            }
                                            ?>
                                        </td>
                                        <td style="text-align: center">
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                <a href="show.php?id_tarea=<?=$id_tarea_actual;?>" type="button" class="btn btn-info btn-sm"><i class="bi bi-eye"></i></a>
                                                <a href="edit.php?id_tarea=<?=$id_tarea_actual;?>" type="button" class="btn btn-success btn-sm"><i class="bi bi-pencil"></i></a>
                                                
                                                <form action="<?=APP_URL;?>/app/controllers/tareas/delete_tarea_controller.php" onclick="preguntar<?=$id_tarea_actual;?>(event)" method="post" id="miFormulario<?=$id_tarea_actual;?>" style="display:inline;">
                                                    <input type="text" name="id_tarea" value="<?=$id_tarea_actual;?>" hidden>
                                                    <button type="submit" class="btn btn-danger btn-sm" style="border-radius: 0px 5px 5px 0px"><i class="bi bi-trash"></i></button>
                                                </form>
                                                <script>
                                                    function preguntar<?=$id_tarea_actual;?>(event) {
                                                        event.preventDefault();
                                                        Swal.fire({
                                                            title: 'Eliminar registro',
                                                            text: '¿Desea eliminar este registro?',
                                                            icon: 'question',
                                                            showDenyButton: true,
                                                            confirmButtonText: 'Eliminar',
                                                            confirmButtonColor: '#a5161d',
                                                            denyButtonColor: '#270a0a',
                                                            denyButtonText: 'Cancelar',
                                                        }).then((result) => {
                                                            if (result.isConfirmed) {
                                                                var form = $('#miFormulario<?=$id_tarea_actual;?>');
                                                                form.submit();
                                                            }
                                                        });
                                                    }
                                                </script>
                                            </div>
                                        </td>
                                    </tr> 
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
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
include ('../../layout/mensajes.php');

?>

<script>
    $(function () {
        $("#example1").DataTable({
            "pageLength": 5,
            "language": {
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Tareas",
                "infoEmpty": "Mostrando 0 a 0 de 0 Tareas",
                "infoFiltered": "(Filtrado de _MAX_ total Tareas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Tareas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscador:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "responsive": true, "lengthChange": true, "autoWidth": false,
            buttons: [{
                extend: 'collection',
                text: 'Reportes',
                orientation: 'landscape',
                buttons: [{
                    text: 'Copiar',
                    extend: 'copy',
                }, {
                    extend: 'pdf'
                },{
                    extend: 'csv'
                },{
                    extend: 'excel'
                },{
                    text: 'Imprimir',
                    extend: 'print'
                }
                ]
            },
                {
                    extend: 'colvis',
                    text: 'Visor de columnas',
                    collectionLayout: 'fixed three-column'
                }
            ],
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>