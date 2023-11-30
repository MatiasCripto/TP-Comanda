<?php
require_once __DIR__ . '/../models/Mesa.php';

require_once __DIR__ . '/../models/Pedido.php';


class MesaController extends Mesa
{
    public static $estados = array("con cliente esperando pedido", "con cliente comiendo", "con cliente pagando", "cerrada");
    public function CargarMesa($request, $response, $args)
    {
    $parametros = $request->getParsedBody();
    $estado = $parametros['estado'];
    $codigoUnico = $parametros['codigoUnico']; // Nuevo atributo

    if (in_array($estado, $this::$estados) && strlen($codigoUnico) === 5) {
        $mesa = new Mesa();
        $mesa->estado = $estado;
        $mesa->codigoUnico = $codigoUnico;
        $mesa->AltaMesa();
        $payload = json_encode(array("Mensaje" => "Mesa creada con exito"));
    } else {
        $payload = json_encode(array("Mensaje" => "Estado de mesa no valido o codigo unico debe tener 5 caracteres."));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
    }

    public function MostrarMesas($request, $response, $args)
    {
    $lista = Mesa::GetMesas();
    $mesas = array();

    foreach ($lista as $mesa) {
        $mesas[] = array(
            "id" => $mesa->id,
            "estado" => $mesa->estado,
            "codigoUnico" => $mesa->codigoUnico 
        );
    }

    $payload = json_encode(array("Mesas" => $mesas));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
    }

    public function CambiarEstadoMesa($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $mesa = Mesa::GetMesaPorId($parametros['idMesa']);
        $listaPedidos = Pedido::GetPedidosListos();
        foreach ($listaPedidos as $pedido)
        {
            if($pedido->idMesa == $mesa->id && $pedido->estado == "Listo para servir!")
            {
                Mesa::CambiarEstado($mesa->id, "con cliente comiendo");
                $response->getBody()->write("Se ha modificado el estado de la mesa con exito!\n");
                break;
            }
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CerrarMesa($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $mesa = Mesa::GetMesaPorId($parametros['idMesa']);
        Mesa::CambiarEstado($mesa->id, "cerrada");
        $response->getBody()->write("Mesa cerrada con exito!");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function AbrirMesa($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $mesa = Mesa::GetMesaPorId($parametros['idMesa']);
        Mesa::CambiarEstado($mesa->id, "con cliente esperando pedido");
        $response->getBody()->write("Mesa abierta con exito!");
        return $response->withHeader('Content-Type', 'application/json');
    }    
}

?>