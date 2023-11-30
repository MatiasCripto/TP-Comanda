<?php
error_reporting(-1);
ini_set('display_errors', 1);

use Psr7Middlewares\Middleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/db/AccesoDatos.php';
require_once __DIR__ . '/../app/controllers/LoginController.php';
require_once __DIR__ . '/../app/controllers/EmpleadoController.php';
require_once __DIR__ . '/../app/controllers/ProductoController.php';
require_once __DIR__ . '/../app/controllers/MesaController.php';
require_once __DIR__ . '/../app/controllers/PedidoController.php';
require_once __DIR__ . '/../app/controllers/FacturaController.php';
require_once __DIR__ . '/../app/controllers/EncuestaController.php';
require_once __DIR__ . '/../app/middlewares/MWToken.php';
require_once __DIR__ . '/../app/middlewares/MWMozo.php';
require_once __DIR__ . '/../app/middlewares/MWSocio.php';

$app = AppFactory::create();

$app->setBasePath('/slim-php-deployment/app');

$app->addErrorMiddleware(true, true, true);

$app->addBodyParsingMiddleware();

$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->post('/altaEmpleado', \EmpleadoController::class . ':CargarEmpleado')->add(new MWSocio());
    $group->get('/listarEmpleados', \EmpleadoController::class . ':MostrarEmpleados');
    $group->delete('/', \EmpleadoController::class . ':BorrarUno')->add(new MWSocio());
    $group->put('/', \EmpleadoController::class . ':ModificarUno')->add(new MWSocio());
})->add(new MWToken());

$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->post('/altaProducto', \ProductoController::class . ':CargarProducto');
  $group->get('/listarProductos', \ProductoController::class . ':MostrarProductos');
  $group->get('/exportarCSV', \ProductoController::class . ':ExportarProductos')->add(new MWSocio());
  $group->post('/importarCSV', \ProductoController::class . ':ImportarProductos')->add(new MWSocio());
})->add(new MWToken());

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->post('/altaMesa', \MesaController::class . ':CargarMesa');
  $group->get('/listarMesas', \MesaController::class . ':MostrarMesas')->add(new MWSocio());
  $group->put('/cambiarEstado', \MesaController::class . ':CambiarEstadoMesa')->add(new MWMozo());
  $group->delete('/cerrarMesa', \MesaController::class . ':CerrarMesa')->add(new MWSocio());
  $group->put('/abrirMesa', \MesaController::class . ':AbrirMesa')->add(new MWMozo());
})->add(new MWToken());

$app->group('/pedidos', function (RouteCollectorProxy $group) { 
  $group->post('/altaPedido', \PedidoController::class . ':CargarPedido')->add(new MWMozo());
  $group->get('/listarPedidos', \PedidoController::class . ':MostrarPedidos')->add(new MWSocio());
  $group->get('/MostrarPedidosEmpleado', \PedidoController::class . ':MostrarPedidosEmpleado');
  $group->get('/MostrarPedidosPreparados', \PedidoController::class . ':MostrarPedidosPreparados');
  $group->put('/prepararPedido', \PedidoController::class . ':PrepararPedido');
  $group->put('/PedidoListo', \PedidoController::class . ':CambiarEstadoListo');
  $group->get('/ConsultarPedidosListos', \PedidoController::class . ':ConsultarPedidosListos')->add(new MWMozo());
  $group->get('/MesaPopular',  \PedidoController::class . ':ConsultarMesaPopular')->add(new MWSocio());
})->add(new MWToken());

$app->get('/MejoresEncuestas', \EncuestaController::class . ':MostrarMejores')->add(new MWSocio());
$app->post('/Encuesta', \EncuestaController::class . ':CargarEncuesta');
$app->post('/Facturar', \FacturaController::class . ':CargarFactura')->add(new MWMozo());
$app->get('/MostrarFacturas', \FacturaController::class . ':MostrarFacturas');
$app->post('/demoraPedido', \PedidoController::class . ':ConsultarDemoraPedido');
$app->post('/login', \LoginController::class . ':GenerarToken');
$app->get('/login', \LoginController::class . ':Deslogear');

$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("TP Programacion III");
    return $response;
});

$app->run();

?>