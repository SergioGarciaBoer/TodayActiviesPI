<?php

class GetUsuario{
    public static $pdo = null;

    public static function init($base){
        self::$pdo = ConexionPdo::conectar($base);
    }

    public static function iniciarSesion($nick, $contraseña){
        $sql = 'SELECT id, nick FROM usuarios WHERE nick=:nick and contraseña=:contrasena';
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([
            ':nick' => $nick,
            ':contrasena'=> $contraseña,
        ]);

        return $stmt->fetch();
    }

    public static function obtenerUsuarioPorId($idUsuario){
        $sql = 'SELECT id, nick, gmail FROM usuarios WHERE id=:idUsuario';
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute(['idUsuario' => $idUsuario]);

        return $stmt->fetch();
    }

    public static function obtenerUsuarioPorGmail($gmailUsuario){
        $sql = 'SELECT id, nick, gmail FROM usuarios WHERE gmail=:gmailUsuario';
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([':gmailUsuario' => $gmailUsuario]);

        return $stmt->fetch();
    }

    public static function obtenerUsuarioPorNick($nickUsuario){
        $sql = 'SELECT id, nick, gmail FROM usuarios WHERE nick=:nickUsuario';
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute(['nickUsuario' => $nickUsuario]);

        return $stmt->fetch();
    }

    public static function insertarUsuario($nick, $contrasena, $gmail){
        $sql= 'INSERT INTO usuarios VALUES(DEFAULT, :nick, :contrasena, :gmail)';
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([
            ':nick'=> $nick,
            ':contrasena'=> $contrasena,
            ':gmail'=> $gmail
        ]);

        return self::$pdo->lastInsertId();
    }

    public static function actualizarDatosUsuario($datosSql, $idUsuario){
        $sql = 'UPDATE usuarios SET';
        $i = 0;
        foreach($datosSql as $key=>$dato){
            if($i > 0){
                $sql .= ' AND';
            }
            $sql .= ' ' . $key . ' = "' . $dato. '"';
            $i++;
        }
        $sql.= ' WHERE id = ' . $idUsuario;

        $stmt = self::$pdo->prepare($sql);
        $stmt->execute();
        return $sql;
    }

    public static function actualizarContrasena($idUsuario, $contrasena){
        $sql = 'UPDATE usuarios SET contraseña = :contrasenaNueva WHERE id = :id';
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([':contrasenaNueva' => $contrasena, ':id' => $idUsuario]);
    }
}

GetUsuario::init('proyecto');