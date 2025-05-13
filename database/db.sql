
CREATE TABLE roles (

  id_rol        INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nombre_rol    VARCHAR (255) NOT NULL UNIQUE KEY,

  fyh_creacion   DATETIME NULL,
  fyh_actualizacion DATETIME NULL,
  estado        VARCHAR (11)

)ENGINE=InnoDB;
INSERT INTO roles (nombre_rol,fyh_creacion,estado) VALUES  ('ADMINISTRADOR','2024-01-03 16:20:20','1');
INSERT INTO roles (nombre_rol,fyh_creacion,estado) VALUES  ('DIRECTOR ACADÉMICO','2024-01-03 16:20:20','1');
INSERT INTO roles (nombre_rol,fyh_creacion,estado) VALUES  ('DIRECTOR ADMINISTRATIVO','2024-01-03 16:20:20','1');
INSERT INTO roles (nombre_rol,fyh_creacion,estado) VALUES  ('CONTADOR','2024-01-03 16:20:20','1');
INSERT INTO roles (nombre_rol,fyh_creacion,estado) VALUES  ('SECRETARIA','2024-01-03 16:20:20','1');
INSERT INTO roles (nombre_rol,fyh_creacion,estado) VALUES  ('DOCENTE','2024-01-03 16:20:20','1');

CREATE TABLE usuarios (

  id_usuario    INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  rol_id        INT (11) NOT NULL,
  email         VARCHAR (255) NOT NULL UNIQUE KEY,
  password      TEXT NOT NULL,

  fyh_creacion   DATETIME NULL,
  fyh_actualizacion DATETIME NULL,
  estado        VARCHAR (11),

  FOREIGN KEY (rol_id) REFERENCES roles (id_rol) on delete no action on update cascade

)ENGINE=InnoDB;
INSERT INTO usuarios (rol_id,email,password,fyh_creacion,estado)
VALUES ('1','admin@admin.com','$2y$10$0tYmdHU9uGCIxY1f90W1EuIm54NQ8axowkxL1WzLbqO2LdNa8m3l2','2023-12-28 20:29:10','1');


CREATE TABLE personas (

  id_persona      INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  usuario_id             INT (11) NOT NULL,
  nombres            VARCHAR (50) NOT NULL,
  apellidos          VARCHAR (50) NOT NULL,
  ci                 VARCHAR (20) NOT NULL,
  fecha_nacimiento   VARCHAR (20) NOT NULL,
  profesion          VARCHAR (50) NOT NULL,
  direccion          VARCHAR (255) NOT NULL,
  celular            VARCHAR (20) NOT NULL,

  fyh_creacion   DATETIME NULL,
  fyh_actualizacion DATETIME NULL,
  estado        VARCHAR (11),

  FOREIGN KEY (usuario_id) REFERENCES usuarios (id_usuario) on delete no action on update cascade

)ENGINE=InnoDB;
INSERT INTO personas (usuario_id,nombres,apellidos,ci,fecha_nacimiento,profesion,direccion,celular,fyh_creacion,estado)
VALUES ('1','Freddy Eddy','Hilari Michua','12345678','10/10/1990','LICENCIADO EN EDUCACIÓN','Zona Los Pinos Av. Las Rosas Nro 100','75657007','2023-12-28 20:29:10','1');

CREATE TABLE administrativos (

  id_administrativo      INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  persona_id             INT (11) NOT NULL,

  fyh_creacion   DATETIME NULL,
  fyh_actualizacion DATETIME NULL,
  estado        VARCHAR (11),

  FOREIGN KEY (persona_id) REFERENCES personas (id_persona) on delete no action on update cascade

)ENGINE=InnoDB;



CREATE TABLE docentes (

  id_docente             INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  persona_id             INT (11) NOT NULL,
  especialidad           VARCHAR (255) NOT NULL,

  fyh_creacion   DATETIME NULL,
  fyh_actualizacion DATETIME NULL,
  estado        VARCHAR (11),

  FOREIGN KEY (persona_id) REFERENCES personas (id_persona) on delete no action on update cascade

)ENGINE=InnoDB;

CREATE TABLE estudiantes (

  id_estudiante             INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  persona_id             INT (11) NOT NULL,
  nivel_id             INT (11) NOT NULL,
  grado_id             INT (11) NOT NULL,
  rude                  VARCHAR (50) NOT NULL,

  fyh_creacion   DATETIME NULL,
  fyh_actualizacion DATETIME NULL,
  estado        VARCHAR (11),

  FOREIGN KEY (persona_id) REFERENCES personas (id_persona) on delete no action on update cascade,
  FOREIGN KEY (nivel_id) REFERENCES niveles (id_nivel) on delete no action on update cascade,
  FOREIGN KEY (grado_id) REFERENCES grados (id_grado) on delete no action on update cascade


)ENGINE=InnoDB;

CREATE TABLE ppffs (

  id_ppff             INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  persona_id             INT (11) NOT NULL,
  nombres_apellidos_ppff    VARCHAR (50) NOT NULL,
  ci_ppff                   VARCHAR (50) NOT NULL,
  celular_ppff              VARCHAR (50) NOT NULL,
  ocupacion_ppff            VARCHAR (50) NOT NULL,
  ref_nombre                VARCHAR (50) NOT NULL,
  ref_parentesco            VARCHAR (50) NOT NULL,
  ref_celular               VARCHAR (50) NOT NULL,
  
  fyh_creacion   DATETIME NULL,
  fyh_actualizacion DATETIME NULL,
  estado        VARCHAR (11),

  FOREIGN KEY (persona_id) REFERENCES personas (id_persona) on delete no action on update cascade

)ENGINE=InnoDB;

CREATE TABLE estudiante_ppff (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  estudiante_id INT(11) NOT NULL,
  ppff_id INT(11) NOT NULL,
  parentesco VARCHAR(50) NOT NULL,
  
  fyh_creacion DATETIME NULL,
  fyh_actualizacion DATETIME NULL,
  estado VARCHAR(11),
  
  FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id_estudiante),
  FOREIGN KEY (ppff_id) REFERENCES ppffs(id_ppff),
  UNIQUE KEY (estudiante_id, ppff_id)
) ENGINE=InnoDB;



CREATE TABLE configuracion_instituciones (

  id_config_institucion    INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nombre_institucion       VARCHAR (255) NOT NULL,
  logo                     VARCHAR (255) NULL,
  direccion                VARCHAR (255) NOT NULL,
  telefono                 VARCHAR (100) NULL,
  celular                  VARCHAR (100) NULL,
  correo                   VARCHAR (100) NULL,

  fyh_creacion   DATETIME NULL,
  fyh_actualizacion DATETIME NULL,
  estado        VARCHAR (11)

)ENGINE=InnoDB;
INSERT INTO configuracion_instituciones (nombre_institucion,logo,direccion,telefono,celular,correo,fyh_creacion,estado)
VALUES ('Hilari Web School','logo.jpg','Zona Los Olivos Calle Max Toledo Av. 6 nro 100','2228837','59175657007','info@hilariweb.com','2023-12-28 20:29:10','1');


CREATE TABLE gestiones (

  id_gestion      INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  gestion         VARCHAR (255) NOT NULL,

  fyh_creacion   DATETIME NULL,
  fyh_actualizacion DATETIME NULL,
  estado        VARCHAR (11)

)ENGINE=InnoDB;
INSERT INTO gestiones (gestion,fyh_creacion,estado)
VALUES ('GESTIÓN 2024','2023-12-28 20:29:10','1');

CREATE TABLE niveles (

  id_nivel       INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  gestion_id     INT (11) NOT NULL,
  nivel          VARCHAR (255) NOT NULL,
  turno          VARCHAR (255) NOT NULL,

  fyh_creacion   DATETIME NULL,
  fyh_actualizacion DATETIME NULL,
  estado        VARCHAR (11),

  FOREIGN KEY (gestion_id) REFERENCES gestiones (id_gestion) on delete no action on update cascade

)ENGINE=InnoDB;
INSERT INTO niveles (gestion_id,nivel,turno,fyh_creacion,estado)
VALUES ('1','INICIAL','MAÑANA','2023-12-28 20:29:10','1');


CREATE TABLE grados (

  id_grado       INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nivel_id       INT (11) NOT NULL,
  curso          VARCHAR (255) NOT NULL,
  paralelo       VARCHAR (255) NOT NULL,

  fyh_creacion   DATETIME NULL,
  fyh_actualizacion DATETIME NULL,
  estado        VARCHAR (11),

  FOREIGN KEY (nivel_id) REFERENCES niveles (id_nivel) on delete no action on update cascade

)ENGINE=InnoDB;
INSERT INTO grados (nivel_id,curso,paralelo,fyh_creacion,estado)
VALUES ('1','INICIAL - 1','A','2023-12-28 20:29:10','1');


CREATE TABLE materias (

  id_materia      INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nombre_materia         VARCHAR (255) NOT NULL,

  fyh_creacion   DATETIME NULL,
  fyh_actualizacion DATETIME NULL,
  estado        VARCHAR (11)

)ENGINE=InnoDB;
INSERT INTO materias (nombre_materia,fyh_creacion,estado)
VALUES ('MATEMÁTICA','2023-12-28 20:29:10','1');

CREATE TABLE grados_materias (
  id_grado_materia INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  grado_id INT(11) NOT NULL,
  materia_id INT(11) NOT NULL,

  fyh_creacion   DATETIME NULL,
  fyh_actualizacion DATETIME NULL,
  estado        VARCHAR (11),

  FOREIGN KEY (grado_id) REFERENCES grados (id_grado) on delete cascade on update cascade,
  FOREIGN KEY (materia_id) REFERENCES materias (id_materia) on delete cascade on update cascade,
  UNIQUE KEY (grado_id, materia_id)

)ENGINE=InnoDB;
-- Ejemplo de inserción (opcional, ajusta según tus datos)
-- INSERT INTO grados_materias (grado_id, materia_id, fyh_creacion, estado) VALUES (1, 1, NOW(), '1');


CREATE TABLE docente_materias (
  id_docente_materia INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  docente_id INT(11) NOT NULL,
  materia_id INT(11) NOT NULL,
  
  fyh_creacion DATETIME NULL,
  fyh_actualizacion DATETIME NULL,
  estado VARCHAR(11),
  
  FOREIGN KEY (docente_id) REFERENCES docentes(id_docente),
  FOREIGN KEY (materia_id) REFERENCES materias(id_materia),
  UNIQUE KEY (docente_id, materia_id)
) ENGINE=InnoDB;

CREATE TABLE tareas (
  id_tarea INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  docente_id INT(11) NOT NULL,
  materia_id INT(11) NOT NULL,
  nivel_id INT(11) NOT NULL,
  grado_id INT(11) NOT NULL,
  titulo VARCHAR(255) NOT NULL,
  descripcion TEXT NOT NULL,
  fecha_asignacion DATETIME NOT NULL,
  fecha_entrega DATETIME NOT NULL,
  
  fyh_creacion DATETIME NULL,
  fyh_actualizacion DATETIME NULL,
  estado VARCHAR(11),
  
  FOREIGN KEY (docente_id) REFERENCES docentes(id_docente) ON DELETE NO ACTION ON UPDATE CASCADE,
  FOREIGN KEY (materia_id) REFERENCES materias(id_materia) ON DELETE NO ACTION ON UPDATE CASCADE,
  FOREIGN KEY (nivel_id) REFERENCES niveles(id_nivel) ON DELETE NO ACTION ON UPDATE CASCADE,
  FOREIGN KEY (grado_id) REFERENCES grados(id_grado) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE registro_tareas (
  id_registro INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  tarea_id INT(11) NOT NULL,
  estudiante_id INT(11) NOT NULL,
  estado ENUM('pendiente', 'entregado', 'evaluado', 'no_entregado') DEFAULT 'pendiente',
  fecha_entrega DATETIME NULL,
  calificacion DECIMAL(5,2) NULL,
  observaciones TEXT NULL,
  
  fyh_creacion DATETIME NULL,
  fyh_actualizacion DATETIME NULL,
  estado_registro VARCHAR(11),
  
  FOREIGN KEY (tarea_id) REFERENCES tareas(id_tarea) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id_estudiante) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE KEY (tarea_id, estudiante_id)
) ENGINE=InnoDB;
-- -----------------------------------------------------
-- Tabla para el registro de Asistencia de Estudiantes
-- -----------------------------------------------------
CREATE TABLE asistencia_estudiantes (
  id_asistencia         INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  estudiante_id         INT(11) NOT NULL,
  fecha                 DATE NOT NULL,
  -- Opcional: si la asistencia es por materia/clase específica
  -- materia_id            INT(11) NULL, 
  -- docente_id            INT(11) NULL, -- Quién tomó la asistencia
  estado_asistencia     ENUM('presente', 'ausente_justificada', 'ausente_injustificada', 'retraso_justificado', 'retraso_injustificado') NOT NULL,
  observaciones         TEXT NULL,
  
  fyh_creacion          DATETIME NULL,
  fyh_actualizacion     DATETIME NULL,
  estado                VARCHAR(11), -- '1' para activo, '0' para inactivo/anulado

  FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id_estudiante) ON DELETE CASCADE ON UPDATE CASCADE
  -- FOREIGN KEY (materia_id) REFERENCES materias(id_materia) ON DELETE SET NULL ON UPDATE CASCADE,
  -- FOREIGN KEY (docente_id) REFERENCES docentes(id_docente) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabla para Tipos de Incidentes Disciplinarios (Faltas o Méritos)
-- -----------------------------------------------------
CREATE TABLE disciplina_tipos_incidentes (
  id_tipo_incidente     INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nombre_tipo           VARCHAR(255) NOT NULL UNIQUE,
  descripcion_tipo      TEXT NULL,
  -- 'falta' para comportamiento negativo, 'merito' para positivo
  naturaleza            ENUM('falta', 'merito') NOT NULL DEFAULT 'falta', 
  -- Leve, Moderada, Grave para faltas; o niveles para méritos
  gravedad_nivel        VARCHAR(50) NULL, 
  
  fyh_creacion          DATETIME NULL,
  fyh_actualizacion     DATETIME NULL,
  estado                VARCHAR(11)
) ENGINE=InnoDB;

-- Insertar algunos tipos de incidentes de ejemplo
INSERT INTO disciplina_tipos_incidentes (nombre_tipo, descripcion_tipo, naturaleza, gravedad_nivel, fyh_creacion, estado) VALUES
('Incumplimiento de uniforme', 'No portar el uniforme completo o adecuado.', 'falta', 'Leve', NOW(), '1'),
('Falta de respeto al docente', 'Actitud irrespetuosa hacia un miembro del personal docente.', 'falta', 'Moderada', NOW(), '1'),
('Agresión verbal a compañero', 'Uso de lenguaje ofensivo o amenazas hacia otro estudiante.', 'falta', 'Grave', NOW(), '1'),
('Uso de celular en clase', 'Utilización de dispositivos móviles sin autorización durante la clase.', 'falta', 'Leve', NOW(), '1'),
('Participación destacada', 'Contribución significativa y positiva en actividades académicas.', 'merito', 'Destacado', NOW(), '1'),
('Colaboración y compañerismo', 'Ayuda y apoyo constante a sus compañeros.', 'merito', 'Notable', NOW(), '1');

-- -----------------------------------------------------
-- Tabla para el Registro de Incidentes Disciplinarios o Méritos
-- -----------------------------------------------------
CREATE TABLE disciplina_registros (
  id_registro_disciplina INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  estudiante_id          INT(11) NOT NULL,
  tipo_incidente_id      INT(11) NOT NULL,
  fecha_hora_suceso      DATETIME NOT NULL,
  lugar_suceso           VARCHAR(255) NULL,
  descripcion_detallada  TEXT NOT NULL,
  -- Quién reporta/registra (puede ser un docente o administrativo)
  reportado_por_usuario_id INT(11) NULL, 
  medidas_tomadas        TEXT NULL,
  -- Para saber si el padre ya fue informado de este registro específico
  notificado_ppff        BOOLEAN DEFAULT FALSE, 
  
  fyh_creacion           DATETIME NULL,
  fyh_actualizacion      DATETIME NULL,
  estado                 VARCHAR(11),

  FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id_estudiante) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (tipo_incidente_id) REFERENCES disciplina_tipos_incidentes(id_tipo_incidente) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (reportado_por_usuario_id) REFERENCES usuarios(id_usuario) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;
-- -----------------------------------------------------
-- Tabla para Sugerencias y Reportes (Bullying, etc.)
-- -----------------------------------------------------
CREATE TABLE comunicaciones_internas (
  id_comunicacion         INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  -- Quién envía la comunicación (debe ser un usuario registrado)
  usuario_remitente_id    INT(11) NOT NULL,
  -- Tipo de comunicación
  tipo_comunicacion       ENUM('sugerencia', 'reporte_bullying', 'otro_reporte') NOT NULL,
  titulo                  VARCHAR(255) NOT NULL,
  descripcion             TEXT NOT NULL,
  -- Para reportes de bullying, quién es el estudiante afectado/víctima.
  -- Si un estudiante reporta por sí mismo, sería su propio ID.
  -- Si un padre reporta por su hijo, sería el ID del hijo.
  -- Puede ser NULL para sugerencias generales.
  estudiante_afectado_id  INT(11) NULL,
  -- Campos específicos para reportes de incidentes
  fecha_incidente         DATE NULL, -- Fecha en que ocurrió el incidente
  lugar_incidente         VARCHAR(255) NULL,
  testigos_descripcion    TEXT NULL, -- Descripción de posibles testigos
  -- Estado del seguimiento por parte de la institución
  estado_seguimiento      ENUM('nuevo', 'leido', 'en_investigacion', 'acciones_tomadas', 'resuelto', 'cerrado') NOT NULL DEFAULT 'nuevo',
  -- Respuesta o feedback de la institución hacia el remitente (opcional)
  respuesta_institucion   TEXT NULL,
  
  fyh_creacion            DATETIME NULL,
  fyh_actualizacion       DATETIME NULL,
  -- Para borrado lógico del registro en sí
  estado                  VARCHAR(11) DEFAULT '1', 

  FOREIGN KEY (usuario_remitente_id) REFERENCES usuarios(id_usuario) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (estudiante_afectado_id) REFERENCES estudiantes(id_estudiante) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;
