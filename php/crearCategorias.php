<?php

ini_set("display_errors",1);
ini_set("display_starup_errors",1);
error_reporting(E_ALL );

require_once './Conexion/ConexionPdo.php';

$conexion = new ConexionPdo();
$pdo = $conexion::conectar('proyecto');

$nombreCategorias = ['Restaurante', 'Hoteles', 'Actividades Fisicas', 'Actividades culturales'];

foreach($nombreCategorias as $nombreCategoria){
    $sql = 'INSERT INTO categorias VALUES(DEFAULT, :categoria)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':categoria' => $nombreCategoria
    ]);
}