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

// Obtener niveles y grados para los dropdowns
$sql_niveles_grados = "SELECT n.id_nivel, n.nivel, n.turno, g.id_grado, g.curso, g.paralelo
                 FROM niveles n
                 JOIN grados g ON n.id_nivel = g.nivel_id
                 WHERE n.estado = '1' AND g.estado = '1'
                 ORDER BY n.nivel, n.turno, g.curso, g.paralelo";
$query_niveles_grados_stmt = $pdo->query($sql_niveles_grados);
$niveles_grados_data = $query_niveles_grados_stmt->fetchAll(PDO::FETCH_ASSOC);

$niveles_para_dropdown = []; // Para el select de Nivel: id_nivel => 'Nivel (Turno)'
$grados_data_for_js = [];    // Para el select dinámico de Grado: id_nivel => [{id, nombre}, ...]

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


// Obtener lista de tipos de incidentes para el dropdown
$sql_tipos_incidentes = "SELECT id_tipo_incidente, nombre_tipo, naturaleza 
                         FROM disciplina_tipos_incidentes 
                         WHERE estado = '1'
                         ORDER BY naturaleza, nombre_tipo";
$query_tipos_incidentes = $pdo->prepare($sql_tipos_incidentes);
$query_tipos_incidentes->execute();
$tipos_incidentes = $query_tipos_incidentes->fetchAll(PDO::FETCH_ASSOC);

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Registrar Nuevo Incidente de Conducta</h1>
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
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Complete los datos del incidente</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?=APP_URL?>/app/controllers/conducta/create_controller.php" method="post">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nivel_id">Nivel <span class="text-danger">*</span></label>
                                            <select name="nivel_id" id="nivel_id" class="form-control" required>
                                                <option value="">Seleccione un nivel</option>
                                                <?php foreach($niveles_para_dropdown as $id_nivel_key => $display_text): ?>
                                                    <option value="<?= $id_nivel_key ?>"><?= $display_text ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="grado_id">Grado <span class="text-danger">*</span></label>
                                            <select name="grado_id" id="grado_id" class="form-control" required>
                                                <option value="">Seleccione un nivel primero</option>
                                                <!-- Los grados se cargarán dinámicamente -->
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="estudiante_id">Estudiante <span class="text-danger">*</span></label>
                                            <select name="estudiante_id" id="estudiante_id" class="form-control" required>
                                                <option value="">Seleccione un grado primero</option>
                                                <!-- Los estudiantes se cargarán dinámicamente -->
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="tipo_incidente_id">Tipo de Incidente <span class="text-danger">*</span></label>
                                            <select name="tipo_incidente_id" id="tipo_incidente_id" class="form-control" required>
                                                <option value="">Seleccione un tipo de incidente</option>
                                                <?php foreach ($tipos_incidentes as $tipo): ?>
                                                    <option value="<?= $tipo['id_tipo_incidente']; ?>">
                                                        <?= htmlspecialchars($tipo['nombre_tipo'] . ' (' . ucfirst($tipo['naturaleza']) . ')'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fecha_hora_suceso">Fecha y Hora del Suceso <span class="text-danger">*</span></label>
                                            <input type="datetime-local" name="fecha_hora_suceso" id="fecha_hora_suceso" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lugar_suceso">Lugar del Suceso</label>
                                            <input type="text" name="lugar_suceso" id="lugar_suceso" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="descripcion_detallada">Descripción Detallada del Incidente <span class="text-danger">*</span></label>
                                    <textarea name="descripcion_detallada" id="descripcion_detallada" class="form-control" rows="4" required></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="medidas_tomadas">Medidas Tomadas (si aplica)</label>
                                    <textarea name="medidas_tomadas" id="medidas_tomadas" class="form-control" rows="3"></textarea>
                                </div>

                                <hr>
                                <div class="form-group text-right">
                                    <a href="<?=APP_URL;?>/admin/conducta/" class="btn btn-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">Registrar Incidente</button>
                                </div>
                            </form>
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
    const gradosData = <?= $grados_json_for_js ?>; 

    document.getElementById('nivel_id').addEventListener('change', function() {
        const nivelId = this.value;
        const gradoSelect = document.getElementById('grado_id');
        const estudianteSelect = document.getElementById('estudiante_id');

        gradoSelect.innerHTML = '<option value="">Cargando grados...</option>'; 
        estudianteSelect.innerHTML = '<option value="">Seleccione un grado primero</option>'; // Resetear estudiantes
        
        if (nivelId && gradosData[nivelId]) {
            gradoSelect.innerHTML = '<option value="">Seleccione un grado</option>'; // Opción por defecto
            gradosData[nivelId].forEach(function(grado) {
                const option = document.createElement('option');
                option.value = grado.id;
                option.textContent = grado.nombre;
                gradoSelect.appendChild(option);
            });
        } else {
            gradoSelect.innerHTML = '<option value="">Seleccione un nivel primero</option>';
        }
    });

    // Script para popular estudiantes dinámicamente basado en el grado seleccionado
    document.getElementById('grado_id').addEventListener('change', function() {
        const gradoId = this.value;
        const estudianteSelect = document.getElementById('estudiante_id');
        estudianteSelect.innerHTML = '<option value="">Cargando estudiantes...</option>';

        if (gradoId) {
            fetch('<?= APP_URL ?>/app/controllers/ajax/get_estudiantes_por_grado.php?grado_id=' + gradoId)
                .then(response => response.json())
                .then(data => {
                    estudianteSelect.innerHTML = '<option value="">Seleccione un estudiante</option>'; // Opción por defecto
                    if (data.length > 0) {
                        data.forEach(function(estudiante) {
                            const option = document.createElement('option');
                            option.value = estudiante.id;
                            option.textContent = estudiante.nombre_completo;
                            estudianteSelect.appendChild(option);
                        });
                    } else {
                        estudianteSelect.innerHTML = '<option value="">No hay estudiantes en este grado</option>';
                    }
                })
                .catch(error => {
                    console.error('Error al cargar estudiantes:', error);
                    estudianteSelect.innerHTML = '<option value="">Error al cargar estudiantes</option>';
                });
        } else {
            estudianteSelect.innerHTML = '<option value="">Seleccione un grado primero</option>';
        }
    });
</script>