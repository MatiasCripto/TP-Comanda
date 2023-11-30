<?php
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/CSV.php';



class ProductoController extends Producto
{
    public static $sectres = array("Vinoteca", "Cerveceria", "Cocina", "CandyBar");
    public function CargarProducto($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $descripcion = $parametros['descripcion'];
        $precio = $parametros['precio'];
        $sector = $parametros['sector'];

        if(in_array($sector, $this::$sectres))
        {
            $producto = new Producto();
            $producto->descripcion = $descripcion;
            $producto->precio = $precio;
            $producto->sector = $sector;
            $producto->AltaProducto();
            $payload = json_encode(array("Mensaje" => "Producto creado con exito"));
        }
        else
        {
            $payload = json_encode(array("Mensaje" => "Sector de producto no valido. (Vinoteca / Cerveceria / Cocina / CandyBar)"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function MostrarProductos($request, $response, $args)
    {
        $lista = Producto::GetProductos();
        $payload = json_encode(array("Productos" => $lista));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ExportarProductos($request, $response, $args)
    {
        try
        {
            $archivo = CSV::ExportarCSV("productos.csv");
            if(file_exists($archivo) && filesize($archivo) > 0)
            {
                $payload = json_encode(array("Archivo creado:" => $archivo));
            }
            else
            {
                $payload = json_encode(array("Error" => "Datos ingresados invalidos."));
            }
            $response->getBody()->write($payload);
        }
        catch(Exception $e)
        {
            echo $e;
        }
        finally
        {
            return $response->withHeader('Content-Type', 'text/csv');
        }    
    }

    public function ImportarProductos($request, $response, $args)
    {
        try
        {
            if(isset($_FILES["archivo"]) && $_FILES["archivo"]["error"] == UPLOAD_ERR_OK)
            {
                $archivo = $_FILES["archivo"]["tmp_name"];
                Producto::LoadCSV($archivo);
                $payload = json_encode(array("Mensaje" => "Productos cargados!"));
            }
        else
        {
            throw new Exception("No se pudo cargar el archivo.");
        }
        }
        catch(Throwable $mensaje)
        {
            $payload = json_encode(array("Error" => $mensaje->getMessage()));
        }
        finally
        {
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }    
    }

}

?>