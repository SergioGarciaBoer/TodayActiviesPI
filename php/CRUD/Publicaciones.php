<?php
class GetPublicacion{
    public static $pdo = null;

    public static function init($base){
        self::$pdo = ConexionPdo::conectar($base);
    }

    public static function obtenerTodasLasPublicaciones(){
        $sql = 'SELECT descripcion, id, titulo FROM publicaciones';
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function obtenerPublicacionesPorId($id){
        $sql = 'SELECT * FROM publicaciones WHERE id = :idPublicacion';
        $stmt =  self::$pdo->prepare($sql);
        $stmt->execute([':idPublicacion' => $id]);
        return $stmt->fetchAll();
    }

    public static function obtenerPublicacionesPorCategoria($idCategoria){
        $sql = 'SELECT p.titulo, p.descripcion, p.id FROM publicaciones p '.
        'JOIN publicacion_categorias pc ON p.id = pc.publicacion_id '.
        'WHERE pc.categoria_id = :idCategoria';
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([':idCategoria' => $idCategoria]);

        $return['publicaciones'] = $stmt->fetchAll();
        $return['sql'] = $sql;
        return $return;
    }

    public static function obtenerPublicacionPorFiltro($categoria, $provincia, $busqueda) {
        $sql = 'SELECT p.titulo, p.descripcion, p.id FROM publicaciones p ';
        $condiciones = [];
        $joins = [];
    
        if ($categoria !== null) {
            $joins[] = 'JOIN publicacion_categorias pc ON p.id = pc.publicacion_id';
            $condiciones[] = 'pc.categoria_id = '.$categoria;
        }
    
        if ($provincia !== null) {
            $joins[] = 'JOIN publicacion_provincias pp ON p.id = pp.publicacion_id';
            $condiciones[] = 'pp.provincia_id = '.$provincia;
        }
    
        if ($busqueda !== null) {
            $condiciones[] = '(p.titulo LIKE "%'.$busqueda.'%" OR p.descripcion LIKE "%'.$busqueda.'%")';
        }
    
        if (!empty($joins)) {
            $sql .= implode(' ', $joins) . ' ';
        }
    
        if (!empty($condiciones)) {
            $sql .= 'WHERE ' . implode(' AND ', $condiciones);
        }

        $stmt = self::$pdo->prepare($sql);
        $stmt->execute();
        $return['publicaciones'] = $stmt->fetchAll();
        $return['sql'] = $sql;
        return $return;
    }

    public static function obtenerPublicacionPorPágina($sql, $numPagina){
        $offset = $numPagina*10 - 10;
        $sql .= ' LIMIT 10 OFFSET '. $offset;
        $stmt =  self::$pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function obtenerPublicacionPorPáginaCategorizado($sql, $numPagina, $idCategoria){
        $offset = $numPagina*10 - 10;
        $sql .= ' LIMIT 10 OFFSET '. $offset;
        $stmt =  self::$pdo->prepare($sql);
        $stmt->execute([':idCategoria' => $idCategoria]);

        return $stmt->fetchAll();
    }

    public static function insertarPublicacion($titulo, $descripcion, $sesion){
        $sql = 'INSERT INTO publicaciones VALUES(DEFAULT, :idSesion, :titulo, :descripcion, DEFAULT)';
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([
            ':idSesion' => 1,
            ':titulo' => $titulo,
            ':descripcion' => $descripcion
        ]);

        return self::$pdo->lastInsertId();
    }

    public static function obtenerImagenPorPublicacionId($idPublicacion){
        $sql = 'SELECT imagen FROM imagenes WHERE publicacion_id = :idPublicacion';
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([':idPublicacion' => $idPublicacion]);

        return $stmt->fetch();
    }

    public static function obtenerImagenesPorPublicacionId($idPublicacion){
        $sql = 'SELECT imagen FROM imagenes WHERE publicacion_id = :idPublicacion';
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([':idPublicacion' => $idPublicacion]);

        return $stmt->fetchAll();
    }

    public static function insertarImagenPorPublicacionId($idPublicacion, $imagen){
        $sql = 'INSERT INTO imagenes VALUES(DEFAULT, :idPublicacion, :imagen)';
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([
            ':idPublicacion' => $idPublicacion,
            ':imagen' => $imagen
        ]);
    }

    public static function obtenerProvinciaPorIdPublicacion($publicacionId){
        $sql = 'SELECT pr.nombre FROM provincias AS pr '.
        'JOIN publicacion_provincias as pp ON pp.provincia_id = pr.id '.
        'WHERE pp.publicacion_id = :publicacionId';
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([':publicacionId' => $publicacionId]);

        return $stmt->fetch();
    }

    public static function comprobarValoracionEnLaPublicidad($usuarioId, $publicacionId){
        $sql= 'SELECT valoracion FROM valoraciones WHERE usuario_id = :usuarioId AND publicacion_id = :publicacionId';
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([
            ':usuarioId' => $usuarioId,
            ':publicacionId' => $publicacionId 
        ]);
        $valoracion = $stmt->fetchAll();
        if(!empty($valoracion)){
            return false;
        }else{
            return true;
        }
    }

    public static function actualizarValoracionEnPublicacion($valoracion, $idUsuario, $idPublicacion){
        $sql = 'UPDATE valoraciones SET valoracion = :valoracion WHERE usuario_id = :usuarioId AND publicacion_id = :publicacionId';
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([
            ':valoracion' => $valoracion,
            ':usuarioId' => $idUsuario,
            ':publicacionId' => $idPublicacion
        ]);
    }

    public static function insertarValoracionEnPublicacion($valoracion, $idUsuario, $idPublicacion){
        $sql = 'INSERT INTO valoraciones VALUES(DEFAULT, :idUsuario, :idPublicacion, :valoracion, DEFAULT)';
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([
            ':valoracion' => $valoracion,
            ':idUsuario' => $idUsuario,
            ':idPublicacion' => $idPublicacion
        ]);
    }

    public static function obtenerMediaDeValoracion($publicacionId){
        $sql = 'SELECT ROUND(AVG(valoracion), 2) AS media FROM valoraciones WHERE publicacion_id = :publicacionId';
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([':publicacionId' => $publicacionId]);

        return $stmt->fetch();
    }

    public static function insertarComentarioEnPublicacion($comentario, $idPublicacion, $idUsuario){
        $sql = 'INSERT INTO comentarios VALUES(DEFAULT, :idPublicacion, :idUsuario, :comentario, DEFAULT)';
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([
            ':idPublicacion' => $idPublicacion,
            ':idUsuario' => $idUsuario,
            ':comentario' => $comentario
        ]);

        return self::$pdo->lastInsertId();
    }

    public static function obtenerComentariosDePublicacion($idPublicacion){
        $sql = 'SELECT c.comentario, u.nick, c.fecha_comentario FROM comentarios c '.
        'JOIN usuarios u ON c.usuario_id = u.id WHERE publicacion_id = :idPublicacion';
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([':idPublicacion' => $idPublicacion]);

        return $stmt->fetchAll();
    }

    public static function obtenerComentariosPorId($idComentario){
        $sql = 'SELECT c.comentario, u.nick, c.fecha_comentario FROM comentarios c '.
        'JOIN usuarios u ON c.usuario_id = u.id WHERE c.id = :idComentario';
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([':idComentario' => $idComentario]);

        return $stmt->fetch();
    }
}

GetPublicacion::init('proyecto');
