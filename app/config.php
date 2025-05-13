<?php
/**
 * Created by PhpStorm.
 * User: HILARIWEB
 * Date: 28/12/2023
 * Time: 19:18
 */

// Iniciar la sesión si no está iniciada ya.
// Esto asegura que session_start() se llama una sola vez y al principio.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
define('SERVIDOR','localhost');
define('USUARIO','root');
define('PASSWORD','');
define('BD','sisgestionescolar');

define('APP_NAME','SISTEMA DE GESTIÓN ESCOLAR');
define('APP_URL','http://localhost/sisgestionescolar');
define('KEY_API_MAPS','');

$servidor = "mysql:dbname=".BD.";host=".SERVIDOR;

try{
    $pdo = new PDO($servidor,USUARIO,PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8"));
    //echo "conexión existosa a la base de datos";
}catch (PDOException $e){
    print_r($e);
    echo "error no se pudo conectar a la base de datos";
}

date_default_timezone_set("America/Caracas");
$fechaHora = date('Y-m-d H:i:s');

$fecha_actual = date('Y-m-d');
$dia_actual = date('d');
$mes_actual = date('m');
$ano_actual = date('Y');

$estado_de_registro = '1';
