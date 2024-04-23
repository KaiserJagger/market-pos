<?php

require_once "conexion.php";

class VentasModelo{
    
    public $resultado;

    
    static public function mdlObtenerNroBoleta(){

        $stmt = Conexion::conectar()->prepare("call prc_obtenerNroBoleta()");

        $stmt -> execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    static public function mdlRegistrarVenta($datos,$nro_boleta,$total_venta,$descripcion_venta){
        
        $date = date('Y-m-d');

        $stmt = Conexion::conectar()->prepare("INSERT INTO venta_cabecera(nro_boleta,descripcion,total_venta)         
                                                VALUES(:nro_boleta,:descripcion,:total_venta)");

        $stmt -> bindParam(":nro_boleta", $nro_boleta , PDO::PARAM_STR);
        $stmt -> bindParam(":descripcion", $descripcion_venta, PDO::PARAM_STR);
        $stmt -> bindParam(":total_venta", $total_venta , PDO::PARAM_STR);


        if($stmt -> execute()){
            
            $stmt = null;

            $stmt = Conexion::conectar()->prepare("UPDATE empresa SET nro_correlativo_venta = LPAD(nro_correlativo_venta + 1,8,'0')");

            if($stmt -> execute()){

                $listaProductos = [];

                for ($i = 0; $i < count($datos); ++$i){
                    
                    $listaProductos = explode(",",$datos[$i]);
        
                    $stmt = Conexion::conectar()->prepare("call prc_registrar_venta_detalle (:p_nro_boleta, 
                                                                                             :p_codigo_producto, 
                                                                                             :p_cantidad, 
                                                                                             :p_total_venta);");
          
                    $stmt -> bindParam(":p_nro_boleta", $nro_boleta , PDO::PARAM_STR);
                    $stmt -> bindParam(":p_codigo_producto", $listaProductos[0] , PDO::PARAM_STR);
                    $stmt -> bindParam(":p_cantidad", $listaProductos[1] , PDO::PARAM_STR);
                    $stmt -> bindParam(":p_total_venta", $listaProductos[2] , PDO::PARAM_STR);


                    if($stmt -> execute()){

                        $concepto = 'VENTA';
                        // //REGISTRAMOS EL KARDEX DE SALIDAS
                        $stmt = Conexion::conectar()->prepare("call prc_registrar_kardex_venta (:p_codigo_producto,
                                                                                                :p_fecha, 
                                                                                                :p_concepto,
                                                                                                :p_comprobante,
                                                                                                :p_unidades);");

                        $stmt -> bindParam(":p_codigo_producto",$listaProductos[0] , PDO::PARAM_STR);
                        $stmt -> bindParam(":p_fecha", $date , PDO::PARAM_STR);
                        $stmt -> bindParam(":p_concepto", $concepto , PDO::PARAM_STR);
                        $stmt -> bindParam(":p_comprobante", $nro_boleta , PDO::PARAM_STR);
                        $stmt -> bindParam(":p_unidades", $listaProductos[1] , PDO::PARAM_STR);
                        
                        if($stmt -> execute()){
                            
                            $resultado = "SE REGISTRO LA VENTA CORRECTAMENTE";

                        }else{
                            
                            $resultado = "Error al actualizar el stock";

                        }

                    }else{
                        
                        $resultado = "Error al registrar la venta";

                    }
                }
            }
        }
    

        return $resultado;
        
       
    }

    static public function mdlListarVentas($fechaDesde,$fechaHasta){

        try {
            
            $stmt = Conexion::conectar()->prepare("SELECT Concat('Boleta Nro: ',v.nro_boleta,' - Total Venta: S./ ',Round(vc.total_venta,2)) as nro_boleta,
                                                            v.codigo_producto,
                                                            c.nombre_categoria,
                                                            p.descripcion_producto,
                                                            case when c.aplica_peso = 1 then concat(v.cantidad,' Kg(s)')
                                                            else concat(v.cantidad,' Und(s)') end as cantidad,                            
                                                            concat('S./ ',round(v.total_venta,2)) as total_venta,
                                                            v.fecha_venta
                                                            FROM venta_detalle v inner join productos p on v.codigo_producto = p.codigo_producto
                                                                                inner join venta_cabecera vc on CONVERT(vc.nro_boleta USING utf8mb3) = CONVERT(v.nro_boleta using utf8mb3)
                                                                                inner join categorias c on c.id_categoria = p.id_categoria_producto
                                                    where DATE(v.fecha_venta) >= date(:fechaDesde) and DATE(v.fecha_venta) <= date(:fechaHasta)
                                                    order by v.nro_boleta asc");

            $stmt -> bindParam(":fechaDesde",$fechaDesde,PDO::PARAM_STR);
            $stmt -> bindParam(":fechaHasta",$fechaHasta,PDO::PARAM_STR);

            $stmt -> execute();

            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            return 'Excepción capturada: '.  $e->getMessage(). "\n";
        }
        

        $stmt = null;
    }

    static public function mdlObtenerDetalleVenta($nro_boleta){

        try {
            
            $stmt = Conexion::conectar()->prepare("select concat('B001-',vc.nro_boleta) as nro_boleta,
                                                        vc.total_venta,
                                                        vc.fecha_venta,
                                                        vd.codigo_producto,
                                                        upper(p.descripcion_producto) as descripcion_producto,
                                                        vd.cantidad,
                                                        vd.precio_unitario_venta,
                                                        vd.total_venta
                                                from venta_cabecera vc inner join venta_detalle vd on vc.nro_boleta = vd.nro_boleta
                                                                        inner join productos p on p.codigo_producto = vd.codigo_producto
                                                where vc.nro_boleta =  :nro_boleta");

            $stmt -> bindParam(":nro_boleta",$nro_boleta,PDO::PARAM_STR);

            $stmt -> execute();

            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            return 'Excepción capturada: '.  $e->getMessage(). "\n";
        }

    }


}