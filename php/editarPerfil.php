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

if($request['accion'] == 'cargar'){
    if(!empty($_SESSION['id'])){
        $status = 'ok';
        $usuario = GetUsuario::obtenerUsuarioPorId($_SESSION['id']);
    }else{
        $status = 'ko';
        $usuario = 'No ha iniciado sesion';
    }
    echo json_encode(['status' => $status, 'usuario' => $usuario]);
    die();
}

if($request['accion'] == 'editarPerfil'){
    if(GetUsuario::iniciarSesion($_SESSION['nick'], $request['contrasena'])){
        $sql = [];
        if(!empty($request['nickNuevo']) && $request['nickNuevo'] != ''){
            $usuario = GetUsuario::obtenerUsuarioPorNick($request['nickNuevo']);
            if($usuario != false){
                echo json_encode(['status' => 'nickRep']);
                die();  
            }
            $sql['nick'] = $request['nickNuevo'];
        }

        if(!empty($request['gmailNuevo']) && $request['gmailNuevo'] != ''){
            $usuario = GetUsuario::obtenerUsuarioPorGmail($request['gmailNuevo']);
            if($usuario != false){
                echo json_encode(['status' => 'gmailRep']);
                die();
            }
            $sql['gmail'] = $request['gmailNuevo'];
        }
        $usuarioEnviar = GetUsuario::actualizarDatosUsuario($sql, $_SESSION['id']);
        echo json_encode($request);
        die();
    }else{
        echo json_encode(['status' => 'errorPass']);
    }
        
}

if($request['accion'] == 'actualizarContrasena'){
    if(GetUsuario::iniciarSesion($_SESSION['nick'], $request['contrasenaAnt'])){
        GetUsuario::actualizarContrasena($_SESSION['id'], $request['contrasenaNuev']);
        echo json_encode(['status' => 'ok']);
    }else{
        echo json_encode(value: ['status' => 'datosInc']);
    }

}