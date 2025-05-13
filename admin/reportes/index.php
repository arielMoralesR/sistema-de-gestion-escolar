<?php
include ('../../app/config.php');
include ('../../app/controllers/reportes/padres_reportes_controller.php');
include ('../../admin/layout/parte1.php'); // Usamos el layout de admin por ahora

// Incluimos el nuevo controlador


?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Reportes Académicos y de Conducta</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">Bienvenido/a, <?= $nombre_padre_logueado; ?></li>
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

            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">Comunicación con la Institución</h3>
                        </div>
                        <div class="card-body">
                            <p>Si desea enviar una sugerencia, reportar un incidente o cualquier otra comunicación importante, por favor utilice el siguiente enlace:</p>
                            <a href="<?= APP_URL; ?>/admin/comunicaciones/create.php" class="btn btn-primary"><i class="bi bi-envelope-plus"></i> Enviar Sugerencia o Reporte</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Mis Hijos/as</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($hijos_del_padre)): ?>
                                <p>Seleccione un estudiante para ver sus reportes detallados:</p>
                                <div class="list-group">
                                    <?php foreach ($hijos_del_padre as $hijo): ?>
                                        <a href="detalle_hijo_reporte.php?estudiante_id=<?= $hijo['id_estudiante']; ?>" class="list-group-item list-group-item-action">
                                            <i class="fas fa-user-graduate mr-2"></i>
                                            <?= htmlspecialchars($hijo['apellidos'] . ', ' . $hijo['nombres']); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php elseif (isset($persona_padre_data) && $persona_padre_data): // El padre está identificado pero no tiene hijos vinculados ?>
                                <div class="alert alert-warning" role="alert">
                                    No se encontraron hijos/as asociados a su cuenta. Si cree que esto es un error, por favor contacte a la administración de la institución.
                                </div>
                            <?php elseif (!isset($persona_padre_data) || !$persona_padre_data): // No se pudo identificar al padre (error en controlador) ?>
                                 <div class="alert alert-danger" role="alert">
                                    No se pudo verificar su información como padre/madre de familia. Por favor, intente iniciar sesión nuevamente o contacte a la administración.
                                 </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Aquí se podrían cargar los reportes del hijo seleccionado más adelante -->
            <div id="reportes_hijo_seleccionado">
            </div>
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
include ('../../admin/layout/parte2.php');
// No incluimos mensajes.php aquí ya que los manejamos arriba para evitar doble SweetAlert
?>