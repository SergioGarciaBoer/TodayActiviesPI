<?php

use PHPUnit\Framework\TestCase;

require_once 'ConexionPdo.php';
require_once 'Usuarios.php';

class usuariosTest  extends TestCase
{


    public function testObtenerUsuarioPorId()
    {
        $usuario = GetUsuario::obtenerUsuarioPorId(1);
        $this->assertEquals(1, $usuario['id']);
        $this->assertEquals("Sergio", $usuario['nick']);
    }

    public function testObtenerUsuarioPorGmail()
    {
        $usuario = GetUsuario::obtenerUsuarioPorGmail("sergiogarciaboer@gmail.com");
        $this->assertEquals(1, $usuario['id']);
        $this->assertEquals("sergiogarciaboer@gmail.com", $usuario['gmail']);
    }

    public function testObtenerUsuarioPorNick()
    {
        $usuario = GetUsuario::obtenerUsuarioPorNick("Sergio");
        $this->assertEquals(1, $usuario['id']);
        $this->assertEquals("Sergio", $usuario['nick']);
    }

    /*public function testInsertarUsuario()
    {
        $usuarioId = GetUsuario::insertarUsuario("UsuarioNuevo", "Contrasena", "gmail@gmail.com");
        $this->assertIsInt($usuarioId);
        $usuario = GetUsuario::obtenerUsuarioPorId($usuarioId);
        $this->assertEquals("UsuarioNuevo", $usuario['nick']);
        $this->assertEquals("gmail@gmail.com", $usuario['gmail']);
    }*/

    /*public function testActualizarDatosUsuario()
    {
        $updateData = ['nick' => 'usuarioEditar', 'gmail' => 'editar@gmail.com'];
        GetUsuario::actualizarDatosUsuario($updateData, 1);

        $result = GetUsuario::obtenerUsuarioPorId(1);
        $this->assertEquals("usuarioEditar", $result['nick']);
        $this->assertEquals("editar@gmail.com", $result['gmail']);
    }*/

    public function testActualizarContrasena()
    {
        GetUsuario::actualizarContrasena(1, "nuevaPass");
        $result = GetUsuario::iniciarSesion("Sergio", "nuevaPass");
        $this->assertEquals(1, $result['id']);
        $this->assertEquals("Sergio", $result['nick']);
    }
}