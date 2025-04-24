<?php
/**
 * Created by PhpStorm.
 * User: HILARIWEB
 * Date: 5/1/2024
 * Time: 08:40
 */


$sql_usuarios = "SELECT 
    usu.id_usuario,
    usu.email,
    usu.estado,
    usu.password,
    usu.fyh_creacion,
    rol.nombre_rol,
    p.nombres,
    p.apellidos,
    p.ci,
    p.fecha_nacimiento,
    p.celular,
    p.direccion
FROM 
    usuarios AS usu
INNER JOIN 
    roles AS rol ON rol.id_rol = usu.rol_id
INNER JOIN 
    personas AS p ON p.usuario_id = usu.id_usuario
WHERE 
    usu.estado = '1' AND p.estado = '1';
 ";
$query_usuarios = $pdo->prepare($sql_usuarios);
$query_usuarios->execute();
$usuarios = $query_usuarios->fetchAll(PDO::FETCH_ASSOC);

foreach ($usuarios as $usuario){
    $nombres = $usuario['nombres'];
    $apellidos = $usuario['apellidos'];
    $nombre_rol = $usuario['nombre_rol'];
    $email = $usuario['email'];
    $password = $usuario['password'];
    $fyh_creacion = $usuario['fyh_creacion'];
    $estado = $usuario['estado'];
}