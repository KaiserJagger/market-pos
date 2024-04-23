<?php

class DashboardControlador{

    static public function ctrGetDatosDashboard(){

        $datos = DashboardModelo::mdlGetDatosDashboard();

        return $datos;
    }

    static public function ctrGetVentasMesActual(){

        $ventasMesActual = DashboardModelo::mdlGetVentasMesActual();

        return $ventasMesActual;
    }

    static public function ctrProductosMasVendidos(){
    
        $productosMasVendidos = DashboardModelo::mdlProductosMasVendidos();
    
        return $productosMasVendidos;
    
    }

    static public function ctrProductosPocoStock(){
    
        $productosPocoStock = DashboardModelo::mdlProductosPocoStock();
    
        return $productosPocoStock;
    
    }

    static public function ctrVentasPorCategoria()
    {
        $ventasPorCategorias = DashboardModelo::mdlVentasPorCategoria();
    
        return $ventasPorCategorias;
    }

    

   
}