<?php


class ProductosControlador{

    static public function ctrCargaMasivaProductos($fileProductos){
        
        $respuesta = ProductosModelo::mdlCargaMasivaProductos($fileProductos);
        
        return $respuesta;
    }

    static public function ctrListarProductos(){
    
        $productos = ProductosModelo::mdlListarProductos();
    
        return $productos;
    
    }

    static public function ctrRegistrarProducto($array_datos_producto, $imagen){

        $registroProducto = ProductosModelo::mdlRegistrarProducto($array_datos_producto, $imagen);

        return $registroProducto;
    }

    static public function ctrAumentarStock($codigo_producto, $nuevo_stock){
        
        $respuesta = ProductosModelo::mdlAumentarStock($codigo_producto, $nuevo_stock);
        
        return $respuesta;
    }

    static public function ctrDisminuirStock($codigo_producto, $nuevo_stock){
        
        $respuesta = ProductosModelo::mdlDisminuirStock($codigo_producto, $nuevo_stock);
        
        return $respuesta;
    }

    static public function ctrActualizarProducto($table, $data, $id, $nameId){
        
        $respuesta = ProductosModelo::mdlActualizarInformacion($table, $data, $id, $nameId);
        
        return $respuesta;
    }

    static public function ctrEliminarProducto($table, $id, $nameId)
    {
        $respuesta = ProductosModelo::mdlEliminarInformacion($table, $id, $nameId);
        
        return $respuesta;
    }

    /*===================================================================
    LISTAR NOMBRE DE PRODUCTOS PARA INPUT DE AUTO COMPLETADO
    ====================================================================*/
    static public function ctrListarNombreProductos(){

        $producto = ProductosModelo::mdlListarNombreProductos();

        return $producto;
    }

    /*===================================================================
    BUSCAR PRODUCTO POR SU CODIGO DE BARRAS
    ====================================================================*/
    static public function ctrGetDatosProducto($codigo_producto){
            
        $producto = ProductosModelo::mdlGetDatosProducto($codigo_producto);

        return $producto;

    }

    static public function ctrVerificaStockProducto($codigo_producto,$cantidad_a_comprar){

        $respuesta = ProductosModelo::mdlVerificaStockProducto($codigo_producto, $cantidad_a_comprar);
    
        return $respuesta;
    }


  
}