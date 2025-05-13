# Sistema de Gestión Escolar (SisGestionEscolar)

Bienvenido al Sistema de Gestión Escolar, una aplicación web diseñada para facilitar la administración y seguimiento de información relevante para padres, estudiantes y personal administrativo de una institución educativa.

## Descripción

Este proyecto tiene como objetivo principal centralizar la información académica y conductual de los estudiantes, permitiendo a los padres de familia acceder de forma segura a los reportes de sus hijos. También cuenta con módulos para la gestión administrativa (implícito por la ruta `/admin/`).

## Características Principales

*   **Portal para Padres de Familia:**
    *   Inicio de sesión seguro.
    *   Visualización de la lista de hijos matriculados.
    *   Acceso a reportes detallados por cada hijo:
        *   Información general del estudiante (nivel, grado, turno).
        *   Reporte de Asistencia.
        *   Reporte de Conducta (incidentes disciplinarios).
        *   Reporte de Tareas (asignaciones, fechas de entrega, estado, calificaciones).
*   **Gestión de Usuarios y Roles:**
    *   Diferenciación de roles (ej. Padres de Familia, Administradores).
    *   Control de acceso basado en roles para proteger la información sensible.
*   **Interfaz Intuitiva:** (Asumido, puedes detallar más)
    *   Navegación clara y sencilla para los usuarios.

## Tecnologías Utilizadas

*   **Backend:** PHP
*   **Base de Datos:** MySQL / MariaDB (a través de PDO)
*   **Servidor Web:** Apache (comúnmente usado con WAMP)
*   **Frontend:** HTML, CSS, JavaScript (asumido para la interfaz de usuario)
*   **Entorno de Desarrollo:** WAMP (Windows, Apache, MySQL, PHP)

## Estructura del Proyecto (Simplificada)

```
sisgestionescolar/
├──admin/
│   ├───administrativos/
                    create.php
                    edit.php
                    index.php
                    show.php
        asistencia/
            index.php
        comunicaciones/
            create.php
        conducta/
            create.php
            index.php
        configuraciones/
            gestion/
                create.php
                edit.php
                index.php
                show.php
            institucion/
                create.php
                edit.php
                index.php
                show.php
            index.php
        deberes/
            index.php
        docentes/
            create.php
            edit.php
            index.php
            show.php
        estudiantes/
            edit.php
            index.php
            show.php
        grados/
            create.php
            edit.php
            index.php
            show.php
        inscripciones/
            create.php
            index.php
        layout/
            parte1.php
            parte2.php
        materias/
            create.php
            edit.php
            index.php
            show.php
        niveles/
            create.php
            edit.php
            index.php
            show.php
        reportes/
            detalle_hijo_reporte.php
            index.php
        roles/
            create.php
            edit.php
            index.php
            show.php
        tareas/
            create.php
            edit.php
            index.php
            show.php 
            ver_tarea_estudiante.php
            vista_detalle_tarea.php
        usuarios/
            create.php
            edit.php
            index.php
            show.php
        index.php
        
│        
├── app/
│   ├── controllers/  # Lógica de negocio y control de flujo
│   ├──     administrativos/
                    create.php
                    datos_administrativos.php
                    listado_administrativos.php
                    update.php
            ajax/
                get_estudiantes_asistencia.php
                get_estudiantes_por_grado.php
            asistencia/
                guardar_asistencia_controller.php
            comunicaciones/
                store_comunicacion_controller.php
            conducta/
                create_controller.php
                listado_conducta.php
            configuraciones/
                gestion/
                    create.php
                    datos_gestion.php
                    listado_de_gestiones.php
                    update.php
                institucion/
                    create.php
                    datos_institucion.php
                    delete.php
                    listado_de_instituciones.php
                    update.php
            docentes/
                create.php
                datos_del_docente.php
                listado_de_docentes.php
                update.php
            estudiantes/
                datos_del_estudiante.php
                listado_de_estudiantes.php
                show_controller.php
                update.php
            grados/
                create.php
                delete.php
                datos_grados.php
                listado_de_grados.php
                update.php
            inscripciones/
                create.php
            materias/
                create.php
                delete.php
                datos_materia.php
                listado_de_materias.php
                update.php
            niveles/
                create.php
                delete.php
                datos_nivel.php
                listado_de_niveles.php
                update.php
│   │   ├── reportes/
│   │   │   ├── padres_reportes_controller.php
│   │   │   └── detalle_hijo_controller.php
            roles/
                create.php
                delete.php
                datos_del_rol.php
                listado_de_roles.php
                update.php
            tareas/
                create.php
                delete_tarea_controller.php
                detalle_tarea_controller.php
                edit_data_controller.php
                listado_de_tareas.php
                listado_tareas_estudiante.php
                marcar_entrega_controller.php
                show_controller.php
                show_tarea_controller.php
                update_controller.php
                ver_tarea_estudiante_controller.php
            usuarios/
                create.php
                delete.php
                datos_del_usuario.php
                listado_de_usuarios.php
                update.php
            config.php
    database/
        db.sql
    layout/
        mensajes.php
        parte1.php
        parte2.php
    login/
        controller_login.php
        index.php
        logout.php
        
├── public/           # Archivos accesibles públicamente (CSS, JS, imágenes, index.php)

└── README.md
```
*(Nota: Adapta esta estructura a la real de tu proyecto.)*
@@ -143,12 +149,12 @@

3.  **Base de Datos:**
    *   Crea una nueva base de datos en phpMyAdmin (o tu gestor de BD preferido).
-    *   Importa el archivo `.sql` con la estructura de la base de datos y/o datos iniciales (debes crear este archivo si no lo tienes).
-    *   Configura los detalles de la conexión a la base de datos en `config/config.php` (o donde tengas tu archivo de configuración principal). Asegúrate de que `$pdo` se conecte correctamente.
+    *   Importa el archivo `app/database/db.sql` que contiene la estructura de la base de datos y/o datos iniciales.
+    *   Configura los detalles de la conexión a la base de datos en `app/config.php`. Asegúrate de que las credenciales y el nombre de la base de datos sean correctos para que `$pdo` se conecte adecuadamente.

4.  **Configuración de la Aplicación:**
-    *   Revisa el archivo `config/config.php` (o similar) y ajusta la constante `APP_URL` para que coincida con tu entorno local (ej. `http://localhost/sisgestionescolar`).
-    *   Asegúrate de que las constantes como `ROL_PADRE_ID` (valor `9` según tus controladores) coincidan con los IDs en tu base de datos.
+    *   Revisa el archivo `app/config.php` y ajusta la constante `APP_URL` para que coincida con tu entorno local (ej. `http://localhost/sisgestionescolar/`).
+    *   Verifica otras constantes de configuración en `app/config.php` como `ROL_PADRE_ID` (valor `9` según tus controladores) y asegúrate de que coincidan con los IDs y configuraciones en tu base de datos.

5.  **Acceder a la Aplicación:**
    *   Abre tu navegador web y ve a `http://localhost/sisgestionescolar` (o la `APP_URL` que hayas configurado).
@@ -162,8 +168,24 @@
    3.  Serán redirigidos a la vista de sus hijos.
    4.  Seleccionar un hijo para ver sus reportes detallados (asistencia, conducta, tareas).
*   **Administradores:**
-    1.  Navegar a la página de inicio de sesión del panel de administración (ej. `http://localhost/sisgestionescolar/admin/login` o similar).
-    2.  (Detallar funcionalidades del administrador).
+    1.  Navegar a la página de inicio de sesión (ej. `http://localhost/sisgestionescolar/app/login/` o la ruta que hayas configurado para el login) y seleccionar el rol de administrador, o directamente a `http://localhost/sisgestionescolar/admin/` si el acceso es diferenciado.
+    2.  Ingresar credenciales de administrador.
+    3.  Acceder al panel de administración (`admin/index.php`) donde podrán:
+        *   Gestionar personal administrativo (`admin/administrativos/`).
+        *   Registrar y consultar asistencia (`admin/asistencia/`).
+        *   Crear y gestionar comunicaciones (`admin/comunicaciones/`).
+        *   Registrar y consultar incidentes de conducta (`admin/conducta/`).
+        *   Configurar parámetros del sistema:
+            *   Gestiones académicas (`admin/configuraciones/gestion/`).
+            *   Datos de la institución (`admin/configuraciones/institucion/`).
+        *   Gestionar deberes y tareas asignadas (`admin/deberes/`, `admin/tareas/`).
+        *   Administrar información de docentes (`admin/docentes/`).
+        *   Administrar información de estudiantes (`admin/estudiantes/`).
+        *   Gestionar grados y niveles (`admin/grados/`, `admin/niveles/`).
+        *   Procesar inscripciones (`admin/inscripciones/`).
+        *   Administrar materias (`admin/materias/`).
+        *   Generar y visualizar reportes (`admin/reportes/`).
+        *   Gestionar roles y usuarios del sistema (`admin/roles/`, `admin/usuarios/`).

## Contribuciones

Si deseas contribuir al proyecto, por favor sigue estos pasos:
1.  Haz un Fork del repositorio.
2.  Crea una nueva rama (`git checkout -b feature/nueva-funcionalidad`).
3.  Realiza tus cambios y haz commit (`git commit -am 'Añade nueva funcionalidad'`).
4.  Sube tus cambios a la rama (`git push origin feature/nueva-funcionalidad`).
5.  Abre un Pull Request.

## Licencia

(Opcional: Especifica la licencia de tu proyecto, ej. MIT, GPL, etc. Si no tienes una, puedes omitir esta sección o investigar cuál se adapta mejor).

---

*Este es un borrador inicial. ¡Siéntete libre de modificarlo y añadir más detalles específicos de tu sistema!*