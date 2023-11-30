<?php
require_once "./models/AuthJWT.php";
require_once "./models/Empleado.php"; 

class LoginController extends AuthJWT
{
    public function GenerarToken($request, $response)
    {
        $datosPost = $request->getParsedBody();
        $datosBD = Empleado::GetEmpleadoPorNombre($datosPost["nombre"]);
        $clave = $datosPost["clave"];       

        if($datosBD != null && md5($clave) == $datosBD->clave)
        {
            if($datosBD->fechaBaja != "0000-00-00")
            {
                $response->getBody()->write(json_encode(array("Error" => "Usuario dado de baja!")));
            }
            else
            {
                $datos = array('id'=> $datosBD->id, 'nombre' => $datosBD->nombre, "rol"=> $datosBD->rol);
                $token = AuthJWT::NuevoToken($datos);   
                echo $token;

                // Validar el token recién generado
                AuthJWT::ValidarToken($token);             
                $payload = json_encode(array('Se ha logeado como:'=> $datosBD->rol, 'Token' => $token));
                $response->getBody()->write($payload);
            }
        }
        else
        {
            $response->getBody()->write(json_encode(array("Error" => "El usuario o la contraseña no coinciden.")));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function Deslogear($request, $response)
    {
        $response->getBody()->write("Cerro su cuenta con exito!");
        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>