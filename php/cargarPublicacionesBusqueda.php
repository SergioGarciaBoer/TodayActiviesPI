<?php

ini_set("display_errors",1);
ini_set("display_starup_errors",1);
error_reporting(E_ALL );

session_start();

require_once './Conexion/ConexionPdo.php';
require_once './CRUD/Publicaciones.php';

$request = json_decode(trim(file_get_contents("php://input")), true);
$request = $request["data"];

if($request['accion'] == 'publicacionPorCategoria'){
    $publicacionesCliente = [];
    $publicacionesContar = GetPublicacion::obtenerPublicacionesPorCategoria($request['idCategoria']);
    $paginas = (int)(count($publicacionesContar['publicaciones'])/10);
    $resto = count($publicacionesContar['publicaciones'])%10;
    if($resto != 0){
        $paginas++;
    }
    $sql = $publicacionesContar['sql'];
    $publicaciones = GetPublicacion::obtenerPublicacionPorPáginaCategorizado($sql, 1, $request['idCategoria']);

    foreach ($publicaciones as $publicacion){
        $publicacion['imagenes'] = GetPublicacion::obtenerImagenPorPublicacionId($publicacion['id']);
        $publicacion['provincia'] = GetPublicacion::obtenerProvinciaPorIdPublicacion($publicacion['id'])['nombre'];
        $publicacionesCliente[] = $publicacion;
    }
    echo json_encode(['publicaciones' => $publicacionesCliente, 'paginas' => $paginas]);
    die();
}

if($request['accion'] == 'buscarPorFiltros'){
    $publicacionesCliente = [];
    ($request['buscar'] == '') ? $buscar = null : $buscar =  $request['buscar'];
    ($request['idCategoria'] == 'todos') ? $categoria = null : (int)$categoria =  $request['idCategoria'];
    ($request['idProvincia'] == 'default') ? $provincia = null : (int)$provincia =  $request['idProvincia'];
    $publicacionesContar = GetPublicacion::obtenerPublicacionPorFiltro($categoria, $provincia, $buscar);

    $paginas = (int)(count($publicacionesContar['publicaciones'])/10);
    $resto = count($publicacionesContar['publicaciones'])%10;
    if($resto != 0){
        $paginas++;
    }

    $publicacionesFiltradas = GetPublicacion::obtenerPublicacionPorPágina($publicacionesContar['sql'],1);

    foreach($publicacionesFiltradas as $publicacion){
        $publicacion['imagenes'] = GetPublicacion::obtenerImagenPorPublicacionId($publicacion['id']);
        $publicacion['provincia'] = GetPublicacion::obtenerProvinciaPorIdPublicacion($publicacion['id'])['nombre'];
        $publicacionesCliente[] = $publicacion;
    }
    echo json_encode(['publicaciones' => $publicacionesCliente, 'paginas' => $paginas, 'sql' => $publicacionesContar['sql']]);
    die();
}

if($request['accion'] == 'paginacionCat'){
    $sql = 'SELECT p.titulo, p.descripcion, p.id FROM publicaciones p '.
        'JOIN publicacion_categorias pc ON p.id = pc.publicacion_id '.
        'WHERE pc.categoria_id = :idCategoria';
    $publicaciones = GetPublicacion::obtenerPublicacionPorPáginaCategorizado($sql, $request['paginaDeseada'], $request['idCategoria']);

    foreach ($publicaciones as $publicacion){
        $publicacion['imagenes'] = GetPublicacion::obtenerImagenPorPublicacionId($publicacion['id']);
        $publicacion['provincia'] = GetPublicacion::obtenerProvinciaPorIdPublicacion($publicacion['id'])['nombre'];
        $publicacionesCliente[] = $publicacion;
    }
    echo json_encode(['publicaciones' => $publicacionesCliente]);
    die();
}

if($request['accion'] == 'paginacionFil'){
    $publicaciones = GetPublicacion::obtenerPublicacionPorPágina($request['sql'], $request['paginaDeseada']);
    foreach ($publicaciones as $publicacion){
        $publicacion['imagenes'] = GetPublicacion::obtenerImagenPorPublicacionId($publicacion['id']);
        $publicacion['provincia'] = GetPublicacion::obtenerProvinciaPorIdPublicacion($publicacion['id'])['nombre'];
        $publicacionesCliente[] = $publicacion;
    }
    echo json_encode(['publicaciones' => $publicacionesCliente]);
    die();
}

echo json_encode('hola');
die();