<?php
$id_estudiante = $_GET['id_estudiante']; // Consistent GET parameter
include ('../../app/config.php');
include ('../../admin/layout/parte1.php');
include ('../../app/controllers/estudiantes/datos_del_estudiante.php');
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
                <h1>Modificacion de datos del Estudiante:<?=$apellidos." ".$nombres;?></h1>
            </div>
            <br>
            <form action="<?=APP_URL;?>/app/controllers/estudiantes/update.php" method="post">
            <div class="row">
            <div class="col-md-12">
                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h3 class="card-title"><b>Llene los datos del Estudiante</b></h3>
                        </div>
                        <div class="card-body">
                           
                                <input type="text" value="<?=$id_usuario_estudiante ?? '';?>" name="id_usuario" hidden> <!-- id_usuario del estudiante -->
                                <input type="text" value="<?=$id_persona ?? '';?>" name="id_persona" hidden>
                                <input type="text" value="<?=$id_estudiante ?? '';?>" name="id_estudiante" hidden>
                                <input type="text" value="<?=$id_ppff ?? '';?>" name="id_ppff" hidden>
                                <input type="text" value="<?=$id_persona_ppff ?? '';?>" name="id_persona_ppff" hidden> <!-- Added parent's persona ID -->
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Nombres</label>
                                            <input type="text" name="nombres" value="<?=$nombres;?>" class="form-control" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Apellidos</label>
                                            <input type="text" name="apellidos" value="<?=$apellidos;?>" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Carnet de Identidad</label>
                                            <input type="number" name="ci" value="<?=$ci;?>" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="">Fecha de Nacimiento</label>
                                            <input type="date" name="fecha_nacimiento" value="<?=$fecha_nacimiento;?>" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="">Celular</label>
                                            <input type="number" name="celular" value="<?=$celular;?>" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Correo del Estudiante (Opcional)</label>
                                            <input type="email" name="email" value="<?=$email_estudiante ?? '';?>" class="form-control"> <!-- Removed required, value from $email_estudiante -->
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="">Direccion</label>
                                            <input type="address" name="direccion" value="<?=$direccion;?>" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                               
                                </div>
                                
                            
                        </div> 
                    </div>
                </div>
                <div class="row">

                <div class="col-md-12">
                    <div class="card card-outline card-success">
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
                                                        <option value="<?=$nivele['id_nivel'];?>"<?=$nivele['id_nivel']==$nivel_id ? 'selected' : ''?>><?=$nivele['nivel']." - ".$nivele['turno'];?></option>
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
                                                        <option value="<?=$grado['id_grado'];?>"<?=$grado['id_grado']==$grado_id ? 'selected' : ''?>><?=$grado['curso']." | Paralelo ".$grado['paralelo'];?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Rude</label>
                                            <input type="text" name="rude" value="<?=$rude;?>" class="form-control" required>
                                        </div>
                                    </div>

                                    
                                </div>
                               
                                </div>
                                
                            
                        </div> 
                    </div>
                </div>
                
                <div class="row">

                <div class="col-md-12">
                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h3 class="card-title"><b>Llene los datos del Padre de Familia</b></h3>
                        </div>
                        <div class="card-body">
                           
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Nombres del Padre/Madre</label>
                                            <input type="text" name="nombres_ppff_persona" value="<?=$nombres_persona_ppff ?? '';?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Apellidos del Padre/Madre</label>
                                            <input type="text" name="apellidos_ppff_persona" value="<?=$apellidos_persona_ppff ?? '';?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Carnet de Identidad</label>
                                            <input type="text" name="ci_ppff" value="<?=$ci_ppff ?? '';?>" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Celular</label>
                                            <input type="number" name="celular_ppff" value="<?=$celular_ppff ?? '';?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Email del Padre/Madre (Opcional)</label>
                                            <input type="email" name="email_ppff" value="<?=$email_ppff ?? '';?>" class="form-control">
                                            <input type="hidden" name="id_usuario_ppff" value="<?=$id_usuario_ppff ?? '';?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Ocupacion</label>
                                            <input type="text" name="ocupacion_ppff" value="<?=$ocupacion_ppff ?? '';?>" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Apellidos y Nombres de referencia</label>
                                            <input type="text" name="ref_nombre" value="<?=$ref_nombre ?? '';?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Parentesco referencia</label>
                                            <input type="text" name="ref_parentesco" value="<?=$ref_parentesco ?? '';?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Celular referencia</label>
                                            <input type="number" name="ref_celular" value="<?=$ref_celular ?? '';?>" class="form-control">
                                        </div>
                                    </div>
                                    
                                </div>
                               
                                </div>
                                
                            
                        </div> 
                    </div>
                    
                    <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-success btn-lg">Actualizar</button>
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
