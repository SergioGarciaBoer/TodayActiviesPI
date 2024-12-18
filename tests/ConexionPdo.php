<?php

class ConexionPdo
{
    public static function conectar(string $bd = '')
    {
        $usuario = 'root';
        $clave = 'Sergio5a7wXEEmmVhn!';

        $opciones = [
            PDO::ATTR_EMULATE_PREPARES => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            PDO::MYSQL_ATTR_LOCAL_INFILE => true,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
        ];
        try {
            empty($bd)
            ? $pdo = new PDO("mysql:localhost;", $usuario, $clave, $opciones)
            : $pdo = new PDO("mysql:dbname=" . $bd . ";host=localhost;", $usuario, $clave, $opciones);
        } catch (PDOException $e) {
            echo "[x] Conexion fallida: " . $e->getMessage();
        }
        return $pdo;
    }
}