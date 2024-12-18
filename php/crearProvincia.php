<?php

ini_set("display_errors",1);
ini_set("display_starup_errors",1);
error_reporting(E_ALL );

require_once './Conexion/ConexionPdo.php';

$conexion = new ConexionPdo();
$pdo = $conexion::conectar('proyecto');

$nombreProvincias = [
    'Almeria', 'Cadiz', 'Cordoba', 
    'Granada', 'Huelva', 'Jaen', 
    'Malaga', 'Sevilla'
];

foreach($nombreProvincias as $nombreProvincia){
    $sql = 'INSERT INTO provincias VALUES(DEFAULT, "'.$nombreProvincia.'")';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}