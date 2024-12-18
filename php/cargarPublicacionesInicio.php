<?php

ini_set("display_errors",1);
ini_set("display_starup_errors",1);
error_reporting(E_ALL );

session_start();

require_once './Conexion/ConexionPdo.php';
require_once './CRUD/Publicaciones.php';
require_once './CRUD/Usuarios.php';

$conexion = new ConexionPdo();
$pdo = $conexion::conectar('proyecto');

$request = json_decode(trim(file_get_contents("php://input")), true);
$request = $request["data"];

if($request['accion'] == 'cargarPublicaciones'){

    $publicacionesBD = GetPublicacion::obtenerTodasLasPublicaciones();
    $paginacion = (int)(count($publicacionesBD) / 10);
    $resto = count($publicacionesBD) % 10;
    if($resto != 0){
        $paginacion++;
    }

    $sql = 'SELECT * FROM publicaciones';
    $publicaciones = GetPublicacion::obtenerPublicacionPorPágina($sql, 1);
    $publicacionCliente = [];
    foreach($publicaciones as $publicacion){
        $publicacion['imagen'] = GetPublicacion::obtenerImagenPorPublicacionId($publicacion['id']);
        $publicacion['provincia'] = GetPublicacion::obtenerProvinciaPorIdPublicacion($publicacion['id']);
        $publicacionCliente[] = $publicacion;
    }

    if(!empty($_SESSION['id'])){
        $inicioSesion['status'] = 'ok';
        $inicioSesion['usuario'] = GetUsuario::obtenerUsuarioPorId($_SESSION['id']);
    }else{
        $inicioSesion['status'] = 'ko';
    }

    echo json_encode([
        'publicaciones' => $publicacionCliente,
        'paginas' => $paginacion,
        'usuario' => $inicioSesion
    ]);
    die();
}

if($request['accion'] == 'paginacion'){
    $sql = 'SELECT descripcion, id, titulo FROM PUBLICACIONES';
    $publicaciones = GetPublicacion::obtenerPublicacionPorPágina($sql, (int)$request['paginaDeseada']);
    $publicacionCliente = [];
    foreach($publicaciones as $publicacion){
        $publicacion['imagen'] = GetPublicacion::obtenerImagenPorPublicacionId($publicacion['id']);
        $publicacion['provincia'] = GetPublicacion::obtenerProvinciaPorIdPublicacion($publicacion['id']);
        $publicacionCliente[] = $publicacion;
    }
    echo json_encode(['publicaciones'=> $publicacionCliente]);

    die();
}

if($request['accion'] == 'mostrarDetalles'){
    $sql = 'SELECT titulo, descripcion, id FROM publicaciones WHERE id = :idPublicacion';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':idPublicacion' => $request['idPublicacion']]);

    $publicacion = $stmt->fetch();
    
    $sql = 'SELECT imagen FROM imagenes WHERE publicacion_id = :idPublicacion';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':idPublicacion' => $request['idPublicacion']]);

    $imagenes = $stmt->fetchAll();
    $publicacion['imagenes'] = $imagenes;

    $mediaValoracion = GetPublicacion::obtenerMediaDeValoracion($request['idPublicacion']);
    $publicacion['media'] = $mediaValoracion['media'];

    echo json_encode($publicacion);
}

if($request['accion'] == 'valoracion'){
    if(!empty($_SESSION['id'])){
        if(GetPublicacion::comprobarValoracionEnLaPublicidad($_SESSION['id'], $request['idPublicacion'])){
            GetPublicacion::insertarValoracionEnPublicacion($request['valoracion'], $_SESSION['id'], $request['idPublicacion']);
            $accion = 'inserccion';
        }else{
            GetPublicacion::actualizarValoracionEnPublicacion($request['valoracion'], $_SESSION['id'], $request['idPublicacion']);
            $accion  = 'actualizacion';
        }
        
        $media = GetPublicacion::obtenerMediaDeValoracion($request['idPublicacion']);
        echo json_encode(['media' => $media['media'], 'accion' => $accion, 'status' => 'ok']);
        die();
    }else{
        echo json_encode(['status' => 'noSesion']);
        die();
    }
    

}


if($request['accion'] == 'comentario'){
    $idComentario = GetPublicacion::insertarComentarioEnPublicacion($request['comentario'], $request['idPubli'], $_SESSION['id']);
    $comentario = GetPublicacion::obtenerComentariosPorId($idComentario);
    echo json_encode($comentario);
    die();
}

if($request['accion'] == 'verComentarios'){
    if(!empty($_SESSION['id'])){
        $comentarios = GetPublicacion::obtenerComentariosDePublicacion($request['idPubli']);
        echo json_encode(['status' => 'ok', 'comentarios' => $comentarios]);
        die();
    }else{
        echo json_encode(['status' => 'ko']);
    }
}