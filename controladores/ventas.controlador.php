<?php

class VentasControlador{

    static public function ctrObtenerNroBoleta(){
        
        $nroBoleta = VentasModelo::mdlObtenerNroBoleta();

        return $nroBoleta;

    }

    static public function ctrRegistrarVenta($datos,$nro_boleta,$total_venta,$descripcion_venta){
        
        $productos = VentasModelo::mdlRegistrarVenta($datos,$nro_boleta,$total_venta,$descripcion_venta);

        return $productos;

    }


    static public function ctrListarVentas($fechaDesde, $fechaHasta){

        $ventas = VentasModelo::mdlListarVentas($fechaDesde,$fechaHasta);

        return $ventas;
    }

    static public function ctrObtenerDetalleVenta($nro_boleta)
    {
        $detalle_venta = VentasModelo::mdlObtenerDetalleVenta($nro_boleta);

        return $detalle_venta;
    }
    
}