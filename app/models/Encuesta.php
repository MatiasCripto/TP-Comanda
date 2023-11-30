<?php

class Encuesta
{
    public $codigoPedido;
    public $puntuacionMesa;
    public $puntuacionRestaurante;
    public $puntuacionMozo;
    public $puntuacionCocinero;
    public $comentario;
    public $fecha;

    public function AltaEncuensta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuestas (codigoPedido, puntuacionMesa, puntuacionRestaurante, puntuacionMozo, puntuacionCocinero, comentario, fecha) VALUES (:codigoPedido, :puntuacionMesa, :puntuacionRestaurante, :puntuacionMozo, :puntuacionCocinero, :comentario, :fecha)");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(":fecha", date_format($fecha, "Y-m-d"));
        $consulta->bindValue(":codigoPedido", $this->codigoPedido);
        $consulta->bindValue(":puntuacionMesa", $this->puntuacionMesa);
        $consulta->bindValue(":puntuacionRestaurante", $this->puntuacionRestaurante);
        $consulta->bindValue(":puntuacionMozo", $this->puntuacionMozo);
        $consulta->bindValue(":puntuacionCocinero", $this->puntuacionCocinero);
        $consulta->bindValue(":comentario", $this->comentario);
        $consulta->execute();
    }

    public static function GetEncuestas()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuestas");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

    public static function GetMejoresEncuestas()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuestas WHERE puntuacionMesa > 5 AND puntuacionRestaurante > 5 AND puntuacionMozo > 5 AND puntuacionCocinero > 5");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }
}

?>