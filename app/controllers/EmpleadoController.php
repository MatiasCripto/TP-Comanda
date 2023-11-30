<?php

require_once __DIR__ . '/../models/Empleado.php';



class EmpleadoController extends Empleado
{
    public static $roles = array("Bartender", "Cervecero", "Cocinero", "Mozo", "Socio");
    public function CargarEmpleado($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $nombre = $parametros['nombre'];
        $clave = $parametros['clave'];
        $claveHasheada = md5($clave);
        $rol = $parametros['rol'];
    
        if (in_array($rol, $this::$roles))
        {
            $empleado = new Empleado();
            $empleado->nombre = $nombre;
            $empleado->clave = $claveHasheada;
            $empleado->rol = $rol;
            $empleado->AltaEmpleado();
            $payload = json_encode(array("Mensaje" => "Usuario creado con exito"));
        }
        else
        {
            $payload = json_encode(array("Mensaje" => "Rol de empleado no valido. (Bartender / Cervecero / Cocinero / Mozo / Socio)"));
        }
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    

    public function MostrarEmpleados($request, $response, $args)
    {
        $lista = Empleado::GetEmpleados();
        $payload = json_encode(array("Empleados" => $lista));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];
        Empleado::UpdateEmpleado($id);
        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $usuarioId = $parametros['id'];
        Empleado::DeleteEmpleado($usuarioId);
        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}    

?>