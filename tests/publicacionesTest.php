<?php
use PHPUnit\Framework\TestCase;

require_once 'ConexionPdo.php';
require_once 'Publicaciones.php';

class publicacionesTest extends TestCase {
    public static $pdo;

    public function testObtenerTodasLasPublicaciones() {
        $publicacion = GetPublicacion::obtenerTodasLasPublicaciones();
        $this->assertEquals($publicacion[0]['titulo'], 'Bolera Málaga');
    }

    public function testObtenerPublicacionesPorId() {
        $publicacion = GetPublicacion::obtenerPublicacionesPorId(3);
        $this->assertEquals($publicacion[0]['id'], 3);
    }

    public function testObtenerPublicacionesPorCategoria() {
        $publicacion = GetPublicacion::obtenerPublicacionesPorCategoria(3);
        $this->assertEquals($publicacion['publicaciones'][0]['id'], 3);
    }

    public function testObtenerPublicacionPorFiltro() {
        $provincia = GetPublicacion::obtenerProvinciaPorIdPublicacion(3);
        print_r($provincia);
        $this->assertEquals($provincia['nombre'], 'Malaga');
    }

    public function testObtenerComentariosPorId () {
        $comentario = GetPublicacion::obtenerComentariosPorId(1);
        $this->assertEquals($comentario['comentario'], 'Bonito lugar!');
    }
}
