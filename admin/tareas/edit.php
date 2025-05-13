<?php
include ('../../app/config.php');
include ('../../admin/layout/parte1.php');

// Controlador para obtener los datos de la tarea y listas para el formulario
include ('../../app/controllers/tareas/edit_data_controller.php');

if (!isset($_SESSION['docente_id'])) {
    $_SESSION['mensaje'] = "Debe iniciar sesión como docente para ver esta página.";
    $_SESSION['icono'] = "warning";
    header('Location: ' . APP_URL . '/login');
    exit;
}

if (!$tarea_a_editar) { // Si el controlador no encontró la tarea
    // El controlador ya debería haber redirigido con un mensaje, pero por si acaso.
    if (!isset($_SESSION['mensaje'])) {
        $_SESSION['mensaje'] = "No se pudo cargar la tarea para editar.";
        $_SESSION['icono'] = "error";
    }
    header('Location: ' . APP_URL . '/admin/tareas/');
    exit;
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Editar Tarea</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Modificar datos de la tarea</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?=APP_URL?>/app/controllers/tareas/update_controller.php" method="post">
                                <input type="hidden" name="id_tarea" value="<?= htmlspecialchars($tarea_a_editar['id_tarea']); ?>">
                                <input type="hidden" name="docente_id" value="<?= htmlspecialchars($_SESSION['docente_id']); ?>">

                                <div class="form-group">
                                    <label for="titulo">Título de la Tarea</label>
                                    <input type="text" name="titulo" id="titulo" class="form-control" value="<?= htmlspecialchars($tarea_a_editar['titulo']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="materia_id">Materia</label>
                                    <select name="materia_id" id="materia_id" class="form-control" required>
                                        <option value="">Seleccione materia</option>
                                        <?php foreach($materias_docente as $materia): ?>
                                            <option value="<?= $materia['id_materia']; ?>" <?= ($materia['id_materia'] == $tarea_a_editar['materia_id']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($materia['nombre_materia']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Nivel (No editable)</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($tarea_a_editar['nombre_nivel'] . ' - ' . $tarea_a_editar['turno']); ?>" readonly>
                                    <input type="hidden" name="nivel_id" value="<?= htmlspecialchars($tarea_a_editar['nivel_id']); ?>">
                                </div>

                                <div class="form-group">
                                    <label>Grado (No editable)</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($tarea_a_editar['nombre_grado'] . ' ' . $tarea_a_editar['paralelo']); ?>" readonly>
                                    <input type="hidden" name="grado_id" value="<?= htmlspecialchars($tarea_a_editar['grado_id']); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="descripcion">Descripción</label>
                                    <textarea name="descripcion" id="descripcion" class="form-control" rows="5" required><?= htmlspecialchars($tarea_a_editar['descripcion']); ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="fecha_entrega">Fecha Límite de Entrega</label>
                                    <input type="datetime-local" name="fecha_entrega" id="fecha_entrega" class="form-control" value="<?= htmlspecialchars($tarea_a_editar['fecha_entrega_formato_input']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="estado_tarea">Estado de la Tarea</label>
                                    <select name="estado_tarea" id="estado_tarea" class="form-control" required>
                                        <option value="1" <?= ($tarea_a_editar['estado_tarea'] == '1') ? 'selected' : ''; ?>>Activa</option>
                                        <option value="0" <?= ($tarea_a_editar['estado_tarea'] == '0' || $tarea_a_editar['estado_tarea'] == '') ? 'selected' : ''; ?>>Inactiva</option>
                                        <!-- Asumimos que el estado en la BD es '1' para activa y otro valor (ej. '0' o NULL) para inactiva -->
                                    </select>
                                </div>

                                <hr>
                                <div class="form-group">
                                    <a href="<?=APP_URL;?>/admin/tareas/" class="btn btn-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-success">Actualizar Tarea</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include ('../../admin/layout/parte2.php');
include ('../../layout/mensajes.php');
?>