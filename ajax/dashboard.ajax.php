<?php

require_once "../controladores/dashboard.controlador.php";
require_once "../modelos/dashboard.modelo.php";

class AjaxDashboard{

    public function getDatosDashboard(){

        $datos = DashboardControlador::ctrGetDatosDashboard();

        echo json_encode($datos);
    }

    public function getVentasMesActual(){
        echo json_encode(DashboardControlador::ctrGetVentasMesActual(), JSON_NUMERIC_CHECK);
    }
    
    public function getProductosMasVendidos(){
    
        $productosMasVendidos = DashboardControlador::ctrProductosMasVendidos();
    
        echo json_encode($productosMasVendidos);
    
    }

    public function getProductosPocoStock(){
    
        $productosPocoStock = DashboardControlador::ctrProductosPocoStock();
    
        echo json_encode($productosPocoStock);
    
    }

    public function getVentasPorCategorias()
    {
        $ventasPorCategorias = DashboardControlador::ctrVentasPorCategoria();
    
        echo json_encode($ventasPorCategorias, JSON_NUMERIC_CHECK);
    }
    
  
}


if(isset($_POST['accion']) && $_POST['accion'] == 1){ //Ejecutar function ventas del mes (Grafico de Barras)

    $ventasMesActual = new AjaxDashboard();
    $ventasMesActual -> getVentasMesActual();

}else if(isset($_POST['accion']) && $_POST['accion'] == 2){ //Ejecutar function de productos mas vendidos

    $produtosMasVendidos = new AjaxDashboard();
    $produtosMasVendidos -> getProductosMasVendidos();

}else if(isset($_POST['accion']) && $_POST['accion'] == 3){ //Ejecutar function de productos poco stock


    $productosPocoStock = new AjaxDashboard();
    $productosPocoStock -> getProductosPocoStock();

}else if(isset($_POST['accion']) && $_POST['accion'] == 4){ //Ejecutar function de grafico de doughnut


    $ventasPorCategorias = new AjaxDashboard();
    $ventasPorCategorias -> getVentasPorCategorias();

}else{
    $datos = new AjaxDashboard();
    $datos -> getDatosDashboard();
}