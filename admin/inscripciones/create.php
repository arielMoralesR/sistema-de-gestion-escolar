<?php
include ('../../app/config.php');
include ('../../admin/layout/parte1.php');
include ('../../app/controllers/roles/listado_de_roles.php');
include ('../../app/controllers/niveles/listado_de_niveles.php');
include ('../../app/controllers/grados/listado_de_grados.php');

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <br>
    <div class="content">
        <div class="container">
            <div class="row">
                <h1>Creación de un nuevo Estudiante</h1>
            </div>
            <br>
            <form action="<?=APP_URL;?>/app/controllers/inscripciones/create.php" method="post">
            <div class="row">
            <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><b>Llene los datos del Estudiante</b></h3>
                        </div>
                        <div class="card-body">
                           
                                <div class="row">
                                    <!-- Campo de rol eliminado, se manejará en backend -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Nombres</label>
                                            <input type="text" name="nombres" class="form-control" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Apellidos</label>
                                            <input type="text" name="apellidos" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Carnet de Identidad</label>
                                            <input type="number" name="ci" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="">Fecha de Nacimiento</label>
                                            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento_estudiante" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="">Celular</label>
                                            <input type="number" name="celular" class="form-control" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="">Direccion</label>
                                            <input type="address" name="direccion" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                               
                                </div>
                                
                            
                        </div> 
                    </div>
                </div>
                <div class="row">

                <div class="col-md-12">
                    <div class="card card-outline card-warning">
                        <div class="card-header">
                            <h3 class="card-title"><b>Llene los datos academicos</b></h3>
                        </div>
                        <div class="card-body">
                           
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Nivel</label>
                                            
                                                <select name="nivel_id" id="" class="form-control">
                                                    <?php
                                                    foreach ($niveles as $nivele){ ?>
                                                        <option value="<?=$nivele['id_nivel'];?>"><?=$nivele['nivel']." - ".$nivele['turno'];?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                                
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Grado</label>
                                            <select name="grado_id" id="" class="form-control">
                                                    <?php
                                                    foreach ($grados as $grado){ ?>
                                                        <option value="<?=$grado['id_grado'];?>"><?=$grado['curso']." | Paralelo ".$grado['paralelo'];?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Rude</label>
                                            <input type="text" name="rude" class="form-control" required>
                                        </div>
                                    </div>
                                </div> 
                                <div class="row">
                                    <!-- En la sección de datos académicos, agregar: -->
<div class="col-md-3">
    <div class="form-group">
        <label for="">¿Crear usuario para el estudiante? (Secundaria)</label>
        <select name="crear_usuario_estudiante" class="form-control" id="crear-usuario-est">
            <option value="0">No</option>
            <option value="1">Sí (Solo secundaria)</option>
        </select>
    </div>
</div>

<!-- Campos condicionales para usuario del estudiante -->
<div id="campos-usuario-est" class="col-md-5" style="display:none;">
    
        
            <div class="form-group">
                <label for="">Correo electrónico del estudiante</label>
                <input type="email" name="email_estudiante" class="form-control">
            </div>
        
                                
                    </div>
                </div>
                
                <div class="row">

                <div class="col-md-12">
                    <!-- Cambiar esta sección en el formulario -->
<div class="card card-outline card-danger">
    <div class="card-header">
        <h3 class="card-title"><b>Llene los datos del Padre de Familia (Usuario del sistema)</b></h3>
    </div>
    <div class="card-body">
        <div class="row">
        <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Nombres</label>
                                            <input type="text" name="nombres_ppff" class="form-control" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Apellidos</label>
                                            <input type="text" name="apellidos_ppff" class="form-control" required>
                                        </div>
                                    </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Correo electrónico</label>
                    <input type="email" name="email_ppff" class="form-control" required>
                </div>
            </div>
            <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="">Fecha de Nacimiento</label>
                                            <input type="date" name="fecha_nacimiento_ppff" id="fecha_nacimiento_padre" class="form-control" required>
                                        </div>
                                    </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Carnet de Identidad</label>
                    <input type="number" name="ci_ppff" class="form-control" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Celular</label>
                    <input type="number" name="celular_ppff" class="form-control" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Ocupacion</label>
                    <input type="text" name="ocupacion_ppff" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">Dirección del Padre/Madre</label>
                    <input type="address" name="direccion_ppff" class="form-control" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Apellidos y Nombres de referencia</label>
                    <input type="text" name="ref_nombre" class="form-control" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Parentesco referencia</label>
                    <input type="text" name="ref_parentesco" class="form-control" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Celular referencia</label>
                    <input type="number" name="ref_celular" class="form-control" required>
                </div>
            </div>
        </div>
    </div>
</div> 
                    </div>
                    
                    <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-lg">Registrar</button>
                                            <a href="<?=APP_URL;?>/admin/estudiantes" class="btn btn-secondary btn-lg">Cancelar</a>
                                        </div>
                                    </div>
                                </div>
            </form>
                
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
// Mostrar/ocultar campos de usuario según selección
document.getElementById('crear-usuario-est').addEventListener('change', function() {
    document.getElementById('campos-usuario-est').style.display = 
        this.value === '1' ? 'block' : 'none';
});

// Validar fechas de nacimiento
document.addEventListener('DOMContentLoaded', function() {
    const hoy = new Date();
    
    // Para el estudiante (mínimo 5 años atrás)
    const fechaMaxEstudiante = new Date();
    fechaMaxEstudiante.setFullYear(hoy.getFullYear() - 5);
    // Formatear a YYYY-MM-DD para el input date
    const mesEstudiante = (fechaMaxEstudiante.getMonth() + 1).toString().padStart(2, '0');
    const diaEstudiante = fechaMaxEstudiante.getDate().toString().padStart(2, '0');
    const maxEstudianteStr = `${fechaMaxEstudiante.getFullYear()}-${mesEstudiante}-${diaEstudiante}`;
    document.getElementById('fecha_nacimiento_estudiante').setAttribute('max', maxEstudianteStr);

    // Para el padre (mínimo 18 años atrás)
    const fechaMaxPadre = new Date();
    fechaMaxPadre.setFullYear(hoy.getFullYear() - 18);
    // Formatear a YYYY-MM-DD para el input date
    const mesPadre = (fechaMaxPadre.getMonth() + 1).toString().padStart(2, '0');
    const diaPadre = fechaMaxPadre.getDate().toString().padStart(2, '0');
    const maxPadreStr = `${fechaMaxPadre.getFullYear()}-${mesPadre}-${diaPadre}`;
    document.getElementById('fecha_nacimiento_padre').setAttribute('max', maxPadreStr);
});
</script>
