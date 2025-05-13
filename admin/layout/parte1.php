<?php
// session_start() ya es llamado en config.php, por lo que se elimina de aquí.

if(isset($_SESSION['sesion_email'])){
    $email_sesion = $_SESSION['sesion_email'];
    
    // Obtener el rol_id de la sesión
    $rol_id_sesion = $_SESSION['rol_id'] ?? 0; // 0 si no está definido

    // Definir arrays de roles para facilitar las condiciones   
    $roles_acceso_total = range(1, 5); // Roles del 1 al 5
    define('ROL_DOCENTE', 6);
    define('ROL_ESTUDIANTE', 8);
    define('ROL_PADRE_FAMILIA', 9); // ¡ASEGÚRATE QUE ESTE ID EXISTA Y SEA CORRECTO EN TU BD!
    define('ROL_REGENTE', 10);      // ¡ASEGÚRATE QUE ESTE ID EXISTA Y SEA CORRECTO EN TU BD!
    // Usar sentencias preparadas para seguridad
    $query_sesion = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email AND estado = '1'");
    $query_sesion->bindParam(':email', $email_sesion);
    $query_sesion->execute();
    
    $datos_sesion_usuario = $query_sesion->fetch(PDO::FETCH_ASSOC); // Obtener solo un usuario
    
    if($datos_sesion_usuario){
        // Si se encontró el usuario, puedes obtener su nombre o el email para mostrar
        // Por ejemplo, para mostrar el nombre: $nombre_para_mostrar = $datos_sesion_usuario['nombres'];
        // Para mantener la lógica original de mostrar el email:
        $nombre_sesion_usuario = $datos_sesion_usuario['email']; 
        // Considera si quieres mostrar el nombre real del usuario: $nombre_sesion_usuario = $datos_sesion_usuario['nombres'];
    } else {
        // El usuario de la sesión no se encontró en la BD o no está activo
        // Puedes destruir la sesión y redirigir al login
        session_destroy();
        header('Location:'.APP_URL."/login?mensaje=Usuario no válido o inactivo");
        exit;
    }
}else{
    header('Location:'.APP_URL."/login");
    exit; // Es importante añadir exit después de una redirección
}
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=APP_NAME;?></title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="<?=APP_URL;?>/public/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?=APP_URL;?>/public/dist/css/adminlte.min.css">

    <!-- Sweetaler2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Iconos de bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Datatables -->
    <link rel="stylesheet" href="<?=APP_URL;?>/public/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=APP_URL;?>/public/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=APP_URL;?>/public/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?=APP_URL;?>/admin" class="nav-link"><?=APP_NAME;?></a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">

            <!-- Notifications Dropdown Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-warning navbar-badge">15</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-header">15 Notifications</span>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-envelope mr-2"></i> 4 new messages
                        <span class="float-right text-muted text-sm">3 mins</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-users mr-2"></i> 8 friend requests
                        <span class="float-right text-muted text-sm">12 hours</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-file mr-2"></i> 3 new reports
                        <span class="float-right text-muted text-sm">2 days</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                    <i class="fas fa-th-large"></i>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="<?=APP_URL;?>/admin" class="brand-link">
            <img src="https://cdn-icons-png.flaticon.com/512/5526/5526487.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">GESTIÓN ESCOLAR</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="https://cdn-icons-png.flaticon.com/512/6073/6073873.png" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block"><?=$nombre_sesion_usuario;?></a>
                </div>
            </div>


            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
                         with font-awesome or any other icon font library -->
                    
                    <?php if (in_array($rol_id_sesion, $roles_acceso_total)): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas"><i class="bi bi-gear"></i></i>
                            <p>
                                Configuraciones
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?=APP_URL;?>/admin/configuraciones" class="nav-link"> <!-- Quitado active para que solo se active la página actual -->
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Configurar</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php if (in_array($rol_id_sesion, $roles_acceso_total)): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas"><i class="bi bi-bookshelf"></i></i>
                            <p>
                                Niveles
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?=APP_URL;?>/admin/niveles" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Listado de niveles</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php if (in_array($rol_id_sesion, $roles_acceso_total)): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas"><i class="bi bi-bar-chart-steps"></i></i>
                            <p>
                                Grados
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?=APP_URL;?>/admin/grados" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Listado de grados</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php if (in_array($rol_id_sesion, $roles_acceso_total)): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas"><i class="bi bi-book-half"></i></i>
                            <p>
                                Materias
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?=APP_URL;?>/admin/materias" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Listado de materias</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php if (in_array($rol_id_sesion, $roles_acceso_total)): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas"><i class="bi bi-bookmarks"></i></i>
                            <p>
                                Roles
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?=APP_URL;?>/admin/roles" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Listado de roles</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php if (in_array($rol_id_sesion, $roles_acceso_total)): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas"><i class="bi bi-people-fill"></i></i>
                            <p>
                                Usuarios
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?=APP_URL;?>/admin/usuarios" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Listado de usuarios</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php if (in_array($rol_id_sesion, $roles_acceso_total)): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas"><i class="bi bi-person-lines-fill"></i></i>
                            <p>
                                Administrativos
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?=APP_URL;?>/admin/administrativos" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Listado de administrativos</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php if (in_array($rol_id_sesion, $roles_acceso_total)): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas"><i class="bi bi-person-video3"></i></i>
                            <p>
                                Docentes
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?=APP_URL;?>/admin/docentes" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Listado de Docentes</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php if ($rol_id_sesion == ROL_DOCENTE): // Solo Docentes ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas"><i class="bi bi-book"></i></i>
                            <p>
                                Tareas
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?=APP_URL;?>/admin/tareas" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>crear tarea</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php // Conducta: Roles 1-5, Docente (6), Regente (10)
                    if (in_array($rol_id_sesion, array_merge($roles_acceso_total, [ROL_DOCENTE, ROL_REGENTE]))): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas"><i class="bi bi-person-fill-exclamation"></i></i>
                            <p>
                                Conducta
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?=APP_URL;?>/admin/conducta" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Registrar Conducta</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php // Asistencia: Docente (6), Regente (10)
                    if (in_array($rol_id_sesion, [ROL_DOCENTE, ROL_REGENTE])): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas"><i class="bi bi-person-fill-check"></i></i>
                            <p>
                                Asistencia
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?=APP_URL;?>/admin/asistencia" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Registrar Asistencia</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php // Reportes: Roles 1-5, Padre de Familia (9)
                    if (in_array($rol_id_sesion, array_merge($roles_acceso_total, [ROL_PADRE_FAMILIA]))): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas"><i class="bi bi-person-vcard-fill"></i></i>
                            <p>
                                Reportes
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?=APP_URL;?>/admin/reportes" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Lista de reportes</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php if ($rol_id_sesion == ROL_ESTUDIANTE): // Solo Estudiantes ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas"><i class="bi bi-postcard-fill"></i></i>
                            <i class="nav-icon fas"><i class="bi bi-postcard-fill"></i></i>
                            <p>
                                Tareas pendientes
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?=APP_URL;?>/admin/deberes" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Lista de tareas</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php if (in_array($rol_id_sesion, $roles_acceso_total)): // Solo roles con acceso total ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas"><i class="bi bi-person-plus-fill"></i></i>
                            <p>
                                Estudiantes
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?=APP_URL;?>/admin/inscripciones" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Inscripcion de Estudiantes</p>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?=APP_URL;?>/admin/estudiantes" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Listado de Estudiantes</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a href="<?=APP_URL;?>/login/logout.php" class="nav-link" style="background-color: #eb2d14;color: black">
                            <i class="nav-icon fas"><i class="bi bi-door-open"></i></i>
                            <p>
                                Cerrar sesión
                            </p>
                        </a>
                    </li>


                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>