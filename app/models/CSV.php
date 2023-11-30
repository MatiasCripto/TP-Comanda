<?php

class CSV
{
    public static function ExportarCSV($path)
    {
        $listaProductos = Producto::GetProductos();
        $file = fopen($path, "w");
        if($file)
        {
            foreach($listaProductos as $producto)
            {
                fputcsv($file, (array)$producto);
            }
            fclose($file);
            return $path;
        }
        else
        {
            throw new Exception("No se pudo abrir el archivo para escritura.");
        }
    }

    public static function ImportarCSV($path)
    {
        $array = [];
        $aux = fopen($path, "r");
        if($aux)
        {
            try
            {
                while(!feof($aux))
                {
                    $datos = fgets($aux);
                    if(!empty($datos))
                    {
                        array_push($array, $datos);
                    }
                }
            }
            catch(Exception $e)
            {
                throw new Exception("Error al leer el archivo: " . $e->getMessage());
            }
            finally
            {
                fclose($aux);
                return $array;
            }
        }
        else
        {
            throw new Exception("No se pudo abrir el archivo para lectura.");
        }
    }
}

?>