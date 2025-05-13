<?php
include ('../../app/config.php');
// session_start() debería estar en config.php o al inicio de parte1.php si no está ya en config.php
// Asegúrate que config.php llame a session_start() si aún no lo hace.

// Obtener ID del docente desde la sesión (asumiendo que ya está definido)
$docente_id = $_SESSION['docente_id'] ?? null;
// Verificar si el docente_id está disponible en la sesión
if (is_null($docente_id)) {
    // Si no hay docente_id, es un problema de sesión o el usuario no es un docente logueado correctamente.
    // Guardar un mensaje de error en la sesión para mostrarlo después de la redirección.
    $_SESSION['mensaje'] = "Error de sesión: No se pudo identificar al docente. Por favor, inicie sesión nuevamente.";
    $_SESSION['icono'] = "error";
    // Redirigir a la página de login. Ajusta la URL si es necesario.
    header('Location: '.APP_URL.'/login');
    exit; // Detener la ejecución del script para evitar más errores.
}

// Ahora que hemos verificado la sesión y el docente_id, podemos incluir la parte del layout.
include ('../../admin/layout/parte1.php');
// Los siguientes includes podrían ser redundantes si los datos se obtienen directamente abajo.
// Considera eliminarlos si no son necesarios para otras funcionalidades en esta página.
// include ('../../app/controllers/roles/listado_de_roles.php');
// include ('../../app/controllers/grados/listado_de_grados.php');
// include ('../../app/controllers/niveles/listado_de_niveles.php');
// include ('../../app/controllers/materias/listado_de_materias.php');

// Obtener materias del docente
$sql_materias = "SELECT m.id_materia, m.nombre_materia 
                 FROM docente_materias dm
                 JOIN materias m ON dm.materia_id = m.id_materia
                 WHERE dm.docente_id = :docente_id AND dm.estado = '1'";
$query_materias = $pdo->prepare($sql_materias);
$query_materias->bindParam(':docente_id', $docente_id);
$query_materias->execute();
$materias = $query_materias->fetchAll(PDO::FETCH_ASSOC);

// Obtener niveles y grados para los dropdowns
$sql_niveles_grados = "SELECT n.id_nivel, n.nivel, n.turno, g.id_grado, g.curso, g.paralelo
                FROM niveles n
                JOIN grados g ON n.id_nivel = g.nivel_id
                WHERE n.estado = '1' AND g.estado = '1'
                ORDER BY n.nivel, n.turno, g.curso, g.paralelo";
$query_niveles_grados_stmt = $pdo->query($sql_niveles_grados);
$niveles_grados_data = $query_niveles_grados_stmt->fetchAll(PDO::FETCH_ASSOC);

// Organizar datos para selects
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
?>

<div class="content-wrapper">
    <div class="content">
        <div class="container">
            <div class="row">
                <h1>Crear Nueva Tarea</h1>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Datos de la Tarea</h3>
                            <div class="card-tools">
                                
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <form action="<?=APP_URL?>/app/controllers/tareas/create.php" method="post">
                                <input type="hidden" name="docente_id" value="<?= htmlspecialchars($docente_id) ?>">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Materia</label>
                                            <select name="materia_id" class="form-control" required>
                                                <option value="">Seleccione materia</option>
                                                <?php foreach($materias as $m): ?>
                                                <option value="<?= htmlspecialchars($m['id_materia']) ?>"><?= htmlspecialchars($m['nombre_materia']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nivel</label>
                                            <select name="nivel_id" id="nivel_id" class="form-control" required>
                                                <option value="">Seleccione nivel</option>
                                                <?php foreach($niveles_para_dropdown as $id_nivel_key => $display_text): ?>
                                                <option value="<?= htmlspecialchars($id_nivel_key) ?>"><?= $display_text ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Grado/Paralelo</label>
                                            <select name="grado_id" id="grado_id" class="form-control" required>
                                                <option value="">Seleccione nivel primero</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Título</label>
                                            <input type="text" name="titulo" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha Entrega</label>
                                            <input type="datetime-local" name="fecha_entrega" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Descripción</label>
                                            <textarea name="descripcion" class="form-control" rows="5" required></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">Guardar Tarea</button>
                                        <a href="<?=APP_URL?>/admin/tareas" class="btn btn-secondary">Cancelar</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Script para popular grados dinámicamente basado en el nivel seleccionado
const gradosData = <?= $grados_json_for_js ?>; // Pasa los datos de PHP a JavaScript

document.getElementById('nivel_id').addEventListener('change', function() {
    const nivelId = this.value;
    const gradoSelect = document.getElementById('grado_id');
    gradoSelect.innerHTML = '<option value="">Seleccione grado</option>'; // Limpiar opciones anteriores y añadir la por defecto
    
    if (nivelId && gradosData[nivelId]) {
        gradosData[nivelId].forEach(function(grado) {
            const option = document.createElement('option');
            option.value = grado.id;
            option.textContent = grado.nombre; // El nombre ya viene con htmlspecialchars desde PHP
            gradoSelect.appendChild(option);
        });
    }
});
</script>

<?php
include ('../../admin/layout/parte2.php');
include ('../../layout/mensajes.php');
?>