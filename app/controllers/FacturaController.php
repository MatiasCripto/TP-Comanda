<?php
require_once "./models/Factura.php";
require_once "./models/Pedido.php";

class FacturaController extends Factura
{
    public function CargarFactura($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $codigoPedido = $parametros['codigoPedido'];

        $factura = new Factura();
        $factura->codigoPedido = $codigoPedido;
        $precios = Pedido::TraerPrecios($codigoPedido);
        $precioFinal = $this->CalcularPrecioFinal($precios);
        $factura->montoTotal = $precioFinal;
        $factura->pagada = False;
        $factura->AltaFactura();
        $payload = json_encode(array("Mensaje" => "Factura creada con exito"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function CalcularPrecioFinal($lista)
    {
        $precioFinal = 0;
        foreach ($lista as $item)
        {
            $precioFinal = $precioFinal + ($item[0] * $item[1]);
        }
        return $precioFinal;
    }

    public function MostrarFacturas($request, $response, $args)
    {
        $lista = Factura::GetFacturas();
        $payload = json_encode(array("Facturas" => $lista));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>