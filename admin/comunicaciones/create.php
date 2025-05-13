<?php
include ('../../app/config.php');
include ('../../admin/layout/parte1.php'); // Usamos el layout de admin

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['mensaje'] = "Debe iniciar sesión para acceder a esta página.";
    $_SESSION['icono'] = "warning";
    header('Location: ' . APP_URL . '/login');
    exit;
}

$usuario_id_sesion = $_SESSION['usuario_id'];
$rol_id_sesion = $_SESSION['rol_id'] ?? null;

// Determinar la URL de retorno basada en el rol
$url_retorno = APP_URL . '/admin'; // URL por defecto para admin u otros roles
if ($rol_id_sesion == 8) { // Asumiendo que 8 es el rol_id de ESTUDIANTE
    $url_retorno = APP_URL . '/admin/deberes/';
} elseif ($rol_id_sesion == 9) { // Asumiendo que 9 es el rol_id de PADRE DE FAMILIA (ajusta este ID)
    $url_retorno = APP_URL . '/admin/reportes/';
}


// Opcional: Cargar lista de hijos si el usuario es un padre (para el campo 'estudiante_afectado_id')
// Esto requeriría una consulta adicional. Por ahora, lo dejaremos como un campo de texto o lo omitiremos
// para simplificar, y el usuario deberá escribir el nombre si es un reporte.
// Si el usuario es un estudiante, podría autocompletarse o permitir seleccionar 'Yo mismo'.

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Enviar Sugerencia o Reporte</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Nueva Comunicación</h3>
                            <div class="card-tools">
                            <a href="<?= htmlspecialchars($url_retorno); ?>" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left-short"></i> Volver</a>
                            </div>
                        </div>
                        <form action="<?= APP_URL; ?>/app/controllers/comunicaciones/store_comunicacion_controller.php" method="post" id="formComunicaciones">
                            <div class="card-body">
                                <?php include ('../../layout/mensajes.php'); ?>
                                <input type="hidden" name="usuario_remitente_id" value="<?= htmlspecialchars($usuario_id_sesion); ?>">

                                <div class="form-group">
                                    <label for="tipo_comunicacion">Tipo de Comunicación <span class="text-danger">*</span></label>
                                    <select name="tipo_comunicacion" id="tipo_comunicacion" class="form-control" required>
                                        <option value="">Seleccione una opción...</option>
                                        <option value="sugerencia">Sugerencia</option>
                                        <option value="reporte_bullying">Reporte de Bullying</option>
                                        <option value="otro_reporte">Otro Tipo de Reporte</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="titulo">Título <span class="text-danger">*</span></label>
                                    <input type="text" name="titulo" id="titulo" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="descripcion">Descripción Detallada <span class="text-danger">*</span></label>
                                    <textarea name="descripcion" id="descripcion" class="form-control" rows="5" required></textarea>
                                </div>

                                <!-- Campos adicionales para reportes -->
                                <div id="campos_reporte" style="display: none;">
                                    <hr>
                                    <h5>Información Adicional del Reporte</h5>
                                    <div class="form-group">
                                        <label for="estudiante_afectado_nombre">Nombre del Estudiante Afectado (si aplica)</label>
                                        <input type="text" name="estudiante_afectado_nombre" id="estudiante_afectado_nombre" class="form-control" placeholder="Nombre completo del estudiante">
                                        <small class="form-text text-muted">Si reporta por usted mismo y es estudiante, puede dejarlo en blanco o indicar "Yo mismo". Si es padre, indique el nombre de su hijo/a.</small>
                                        <!-- En una versión más avanzada, aquí podría ir un select con los hijos del padre o un buscador de estudiantes -->
                                        <input type="hidden" name="estudiante_afectado_id" id="estudiante_afectado_id"> <!-- Se podría llenar con JS si se selecciona de una lista -->
                                    </div>

                                    <div class="form-group">
                                        <label for="fecha_incidente">Fecha del Incidente (si aplica)</label>
                                        <input type="date" name="fecha_incidente" id="fecha_incidente" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label for="lugar_incidente">Lugar del Incidente (si aplica)</label>
                                        <input type="text" name="lugar_incidente" id="lugar_incidente" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label for="testigos_descripcion">Testigos o Evidencia (si aplica)</label>
                                        <textarea name="testigos_descripcion" id="testigos_descripcion" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Enviar Comunicación</button>
                                <a href="<?= htmlspecialchars($url_retorno); ?>" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipoComunicacionSelect = document.getElementById('tipo_comunicacion');
        const camposReporteDiv = document.getElementById('campos_reporte');

        tipoComunicacionSelect.addEventListener('change', function() {
            if (this.value === 'reporte_bullying' || this.value === 'otro_reporte') {
                camposReporteDiv.style.display = 'block';
            } else {
                camposReporteDiv.style.display = 'none';
            }
        });
    });
</script>