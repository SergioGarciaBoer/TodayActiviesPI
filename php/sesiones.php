<?php

ini_set("display_errors",1);
ini_set("display_starup_errors",1);
error_reporting(E_ALL );

session_start();

require_once './Conexion/ConexionPdo.php';
require_once './CRUD/Usuarios.php';

$conexion = new ConexionPdo();
$pdo = $conexion::conectar('proyecto');

$request = json_decode(trim(file_get_contents("php://input")), true);
$request = $request["data"];

if ($request['accion'] == 'sesion'){
    if($id = GetUsuario::iniciarSesion($request['nick'], $request['contrasena'])){
        $_SESSION['id'] = $id['id'];
        $_SESSION['nick'] = $id['nick'];
        echo json_encode(['status' => 'ok', 'nick' => $id['nick']]);
        die(); 
    }else{
        echo json_encode(['status'=> 'ko']);
        die();
    }
}

if($request['accion'] == 'registro'){
    $sql = 'SELECT nick FROM usuarios WHERE nick=:nick';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nick'=> $request['nick']
    ]);
    if($stmt->fetchAll()){
        echo json_encode(['status'=> 'nick']);
    }else{
        $sql = 'SELECT gmail FROM usuarios WHERE gmail=:gmail';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':gmail'=> $request['gmail']
        ]);
        if($stmt->fetchAll()){
            echo json_encode(['status'=> 'gmail']);
        }else{  
            $idUsuario = GetUsuario::insertarUsuario($request['nick'], $request['contrasena'], $request['gmail']);
            $usuarioDatos = GetUsuario::obtenerUsuarioPorId($idUsuario);
            $_SESSION['id'] = $usuarioDatos['id'];
            $_SESSION['nick'] = $usuarioDatos['nick'];
            echo json_encode(['status'=> 'ok', 'nick' => $usuarioDatos['nick']]);
        }
    }
}

if($request['accion'] == 'cerrar'){
    session_destroy();
    echo json_encode(['status'=> 'ok']);
}

if($request['accion'] == 'sesionAuto'){
    if(!empty($_SESSION['id'])){
        $inicioSesion['status'] = 'ok';
        $inicioSesion['usuario'] = GetUsuario::obtenerUsuarioPorId($_SESSION['id']);
    }else{
        $inicioSesion['status'] = 'ko';
    }
    echo json_encode($inicioSesion);
}
