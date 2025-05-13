<?php
include ('../../app/config.php');
include ('../../admin/layout/parte1.php');

// Opcional: Verificar permisos específicos para acceder a esta página
if (!isset($_SESSION['usuario_id'])) { // O un rol específico como docente o administrador
    $_SESSION['mensaje'] = "No tiene permisos para acceder a esta sección.";
    $_SESSION['icono'] = "warning";
    header('Location: ' . APP_URL . '/admin/'); // O al login si es más apropiado
    exit;
}

// Obtener niveles y grados para los dropdowns (similar a conducta/create.php)
$sql_niveles_grados = "SELECT n.id_nivel, n.nivel, n.turno, g.id_grado, g.curso, g.paralelo
                 FROM niveles n
                 JOIN grados g ON n.id_nivel = g.nivel_id
                 WHERE n.estado = '1' AND g.estado = '1'
                 ORDER BY n.nivel, n.turno, g.curso, g.paralelo";
$query_niveles_grados_stmt = $pdo->query($sql_niveles_grados);
$niveles_grados_data = $query_niveles_grados_stmt->fetchAll(PDO::FETCH_ASSOC);

$niveles_para_dropdown = [];
$grados_data_for_js = [];

foreach ($niveles_grados_data as $item) {
    if (!isset($niveles_para_dropdown[$item['id_nivel']])) {
        $niveles_para_dropdown[$item['id_nivel']] = htmlspecialchars($item['nivel'] . ' (' . $item['turno'] . ')');
    }
    $grados_data_for_js[$item['id_nivel']][] = [
        'id' => $item['id_grado'],
        'nombre' => htmlspecialchars($item['curso'] . ' ' . $item['paralelo'])
    ];
}
$grados_json_for_js = json_encode($grados_data_for_js);

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Registro de Asistencia de Estudiantes</h1>
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
                            <h3 class="card-title">Seleccione los criterios para tomar asistencia</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_asistencia">Fecha <span class="text-danger">*</span></label>
                                        <input type="date" id="fecha_asistencia" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="nivel_id_asistencia">Nivel <span class="text-danger">*</span></label>
                                        <select id="nivel_id_asistencia" class="form-control" required>
                                            <option value="">Seleccione un nivel</option>
                                            <?php foreach($niveles_para_dropdown as $id_nivel_key => $display_text): ?>
                                                <option value="<?= $id_nivel_key ?>"><?= $display_text ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="grado_id_asistencia">Grado <span class="text-danger">*</span></label>
                                        <select id="grado_id_asistencia" class="form-control" required>
                                            <option value="">Seleccione un nivel primero</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="button" id="btn_cargar_estudiantes" class="btn btn-info btn-block">Cargar Lista de Estudiantes</button>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div id="lista_asistencia_div">
                                <!-- Aquí se cargará la tabla de estudiantes -->
                            </div>
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
    // Script para popular grados dinámicamente basado en el nivel seleccionado
    const gradosDataAsistencia = <?= $grados_json_for_js ?>; 

    document.getElementById('nivel_id_asistencia').addEventListener('change', function() {
        const nivelId = this.value;
        const gradoSelect = document.getElementById('grado_id_asistencia');
        gradoSelect.innerHTML = '<option value="">Cargando grados...</option>'; 
        
        if (nivelId && gradosDataAsistencia[nivelId]) {
            gradoSelect.innerHTML = '<option value="">Seleccione un grado</option>';
            gradosDataAsistencia[nivelId].forEach(function(grado) {
                const option = document.createElement('option');
                option.value = grado.id;
                option.textContent = grado.nombre;
                gradoSelect.appendChild(option);
            });
        } else {
            gradoSelect.innerHTML = '<option value="">Seleccione un nivel primero</option>';
        }
        // Limpiar la lista de asistencia si cambia el nivel/grado
        document.getElementById('lista_asistencia_div').innerHTML = '';
    });

    document.getElementById('grado_id_asistencia').addEventListener('change', function() {
        // Limpiar la lista de asistencia si cambia el grado
        document.getElementById('lista_asistencia_div').innerHTML = '';
    });

    // Script para cargar estudiantes y su estado de asistencia
    document.getElementById('btn_cargar_estudiantes').addEventListener('click', function() {
        const fecha = document.getElementById('fecha_asistencia').value;
        const gradoId = document.getElementById('grado_id_asistencia').value;
        const listaAsistenciaDiv = document.getElementById('lista_asistencia_div');

        if (!fecha || !gradoId) {
            Swal.fire('Error', 'Por favor, seleccione fecha y grado.', 'error');
            return;
        }

        listaAsistenciaDiv.innerHTML = '<p class="text-center">Cargando estudiantes...</p>';

        fetch(`<?= APP_URL ?>/app/controllers/ajax/get_estudiantes_asistencia.php?grado_id=${gradoId}&fecha=${fecha}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    listaAsistenciaDiv.innerHTML = `<p class="text-danger text-center">${data.error}</p>`;
                    return;
                }
                if (data.estudiantes.length > 0) {
                    let formHTML = `<form action="<?=APP_URL?>/app/controllers/asistencia/guardar_asistencia_controller.php" method="post">`;
                    formHTML += `<input type="hidden" name="fecha_asistencia_form" value="${fecha}">`;
                    formHTML += `<input type="hidden" name="grado_id_form" value="${gradoId}">`;
                    formHTML += `<table class="table table-bordered table-hover table-sm"><thead><tr><th>#</th><th>Estudiante</th><th>Estado Asistencia</th><th>Observaciones</th></tr></thead><tbody>`;
                    
                    data.estudiantes.forEach((estudiante, index) => {
                        formHTML += `<tr>
                                        <td>${index + 1}</td>
                                        <td>${estudiante.nombre_completo}</td>
                                        <td>
                                            <input type="hidden" name="asistencias[${estudiante.id_estudiante}][id_asistencia_existente]" value="${estudiante.id_asistencia_existente || ''}">
                                            <select name="asistencias[${estudiante.id_estudiante}][estado]" class="form-control form-control-sm">
                                                <option value="presente" ${estudiante.estado_asistencia_actual === 'presente' ? 'selected' : ''}>Presente</option>
                                                <option value="ausente_justificada" ${estudiante.estado_asistencia_actual === 'ausente_justificada' ? 'selected' : ''}>Ausente Justificada</option>
                                                <option value="ausente_injustificada" ${estudiante.estado_asistencia_actual === 'ausente_injustificada' ? 'selected' : ''}>Ausente Injustificada</option>
                                                <option value="retraso_justificado" ${estudiante.estado_asistencia_actual === 'retraso_justificado' ? 'selected' : ''}>Retraso Justificado</option>
                                                <option value="retraso_injustificado" ${estudiante.estado_asistencia_actual === 'retraso_injustificado' ? 'selected' : ''}>Retraso Injustificado</option>
                                            </select>
                                        </td>
                                        <td><input type="text" name="asistencias[${estudiante.id_estudiante}][observaciones]" class="form-control form-control-sm" value="${estudiante.observaciones_actuales || ''}"></td>
                                     </tr>`;
                    });
                    formHTML += `</tbody></table>`;
                    formHTML += `<div class="form-group text-right mt-3"><button type="submit" class="btn btn-success">Guardar Asistencia</button></div></form>`;
                    listaAsistenciaDiv.innerHTML = formHTML;
                } else {
                    listaAsistenciaDiv.innerHTML = '<p class="text-center">No hay estudiantes en este grado o no se pudieron cargar.</p>';
                }
            })
            .catch(error => {
                console.error('Error al cargar estudiantes para asistencia:', error);
                listaAsistenciaDiv.innerHTML = '<p class="text-danger text-center">Error al cargar la lista de estudiantes.</p>';
            });
    });
</script>