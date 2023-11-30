<?php

require_once("./models/Encuesta.php");

class EncuestaController extends Encuesta
{
    public function CargarEncuesta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $codigoPedido = $parametros["codigoPedido"];
        $puntuacionMesa = $parametros["puntuacionMesa"];
        $puntuacionRestaurante = $parametros["puntuacionRestaurante"];
        $puntuacionMozo = $parametros["puntuacionMozo"];
        $puntuacionCocinero = $parametros["puntuacionCocinero"];
        $comentario = $parametros["comentario"];

        try
        {
            if(!empty($comentario) && $puntuacionMesa >= 1 && $puntuacionMesa <= 10 && $puntuacionRestaurante >= 1 && $puntuacionRestaurante <= 10 && $puntuacionMozo >= 1 && $puntuacionMozo <= 10 && $puntuacionCocinero >= 1 && $puntuacionCocinero <= 10)
            {
                $encuesta = new Encuesta();
                $encuesta->codigoPedido = $codigoPedido;
                $encuesta->puntuacionMesa = $puntuacionMesa;
                $encuesta->puntuacionRestaurante = $puntuacionRestaurante;
                $encuesta->puntuacionMozo = $puntuacionMozo;
                $encuesta->puntuacionCocinero = $puntuacionCocinero;
                $encuesta->comentario = $comentario;
                $encuesta->AltaEncuensta();
                $payload = json_encode(array("Mensaje"=> "Encuesta cargada con exito!"));
            }
            else
            {
                $payload = json_encode(array("Error"=> "Revise los datos! (Puntaciones 1-10)"));
            }
        }
        catch(Throwable $e)
        {
            $payload = json_encode(array("Excepcion"=> $e->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader("Content-type", "application/json");
    }

    public function MostrarMejores($request, $response, $args)
    {
        $lista = Encuesta::GetMejoresEncuestas();
        $payload = json_encode(array("Mejores encuestas" => $lista));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}


?>