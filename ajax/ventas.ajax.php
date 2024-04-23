<?php

require_once "../controladores/ventas.controlador.php";
require_once "../modelos/ventas.modelo.php";

class AjaxVentas
{

    public function ajaxObtenerNroBoleta()
    {

        $nroBoleta = VentasControlador::ctrObtenerNroBoleta();

        echo json_encode($nroBoleta, JSON_UNESCAPED_UNICODE);
    }

    public function ajaxRegistrarVenta($datos, $nro_boleta, $total_venta, $descripcion_venta)
    {

        $registroVenta = VentasControlador::ctrRegistrarVenta($datos, $nro_boleta, $total_venta, $descripcion_venta);
        echo json_encode($registroVenta, JSON_UNESCAPED_UNICODE);
    }

    public function ajaxListarVentas($fechaDesde, $fechaHasta)
    {

        $ventas = VentasControlador::ctrListarVentas($fechaDesde, $fechaHasta);

        echo json_encode($ventas, JSON_UNESCAPED_UNICODE);
    }

    public function AjaxGenerarTicketVenta($nro_boleta)
    {

        require('../vistas/assets/plugins/fpdf/fpdf.php');

        $pdf = new FPDF($orientation = 'P', $unit = 'mm', array(80, 200));        
        $pdf->AddPage();        
        $pdf->setMargins(10,10,10);

        //nombre del negocio
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(60, 6, 'Maga & Tito Minimarket',0,0,'C');

        $pdf->Ln();
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(60, 6, "RUC 10467291241 Av. Brasil 1347",0,0,'C');  

        $pdf->Ln();
        $pdf->Cell(60, 6, utf8_decode("Jesus María - Lima"),0,0,'C');     
        
        $pdf->Ln();

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(60, 6, utf8_decode("TICKET DE VENTA"),0,0,'C');

        $pdf->Ln();

        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(60, 4, utf8_decode("B001 - " .$nro_boleta),0,0,'C');
        
        $pdf->Ln();
        $pdf->Cell(60, 6, utf8_decode("________________________________"),0,0,'C');
        $pdf->Ln();

        
        $total = 0;

        $productos = VentasControlador::ctrObtenerDetalleVenta($nro_boleta);

        $pdf->SetFont('Arial', '', 8);

        foreach ($productos as $pro) {
            
            $pdf->Cell(22, 4, $pro["codigo_producto"]);
            
            $pdf->Cell(35, 4,  utf8_decode(strtoupper(substr($pro["descripcion_producto"], 0, 20))));
            
            $pdf->Ln();

            $pdf->Cell(15, 4,  $pro["cantidad"], 0, 0, 'R');

            $pdf->Cell(5, 4, "X", 0, 0, 'R');

            $pdf->Cell(15, 4, "S. " . number_format($pro["precio_unitario_venta"], 2, ".", ","), 0, 0, 'R');

            $pdf->Cell(25, 5,  "S./ " . number_format($pro["cantidad"] * $pro["precio_unitario_venta"], 2, ".", ","), 0, 0, "R");            

            $pdf->Ln();

            $total += $pro["cantidad"] * $pro["precio_unitario_venta"];
                        
        }

        $pdf->Ln();
        $pdf->Cell(20, 4, "OP. GRAVADA:" );
        $pdf->Cell(40, 4, "S./ ". ROUND($total,2) - ROUND($total*0.18,2),0, 0, 'R');
        $pdf->Ln();

        $pdf->Cell(20, 4, "OP. INAFECTA:" );
        $pdf->Cell(40, 4, "S./ ". ROUND(0.00,2),0, 0, 'R');
        $pdf->Ln();
        
        
        $pdf->Cell(20, 4, "I.G.V.:" );
        $pdf->Cell(40, 4, "S./ ". ROUND($total*0.18,2),0, 0, 'R');
        $pdf->Ln();

        $pdf->Cell(20, 4, "TOTAL A PAGAR: ");
        $pdf->Cell(40, 4, "S./ ". ROUND($total,2),0, 0, 'R');
        $pdf->Ln();

        $pdf->Output();
    }
}

if (isset($_POST["accion"]) && $_POST["accion"] == 1) {

    $nroBoleta = new AjaxVentas();
    $nroBoleta->ajaxObtenerNroBoleta();
} else if (isset($_POST["accion"]) && $_POST["accion"] == 2) { // LISTADO DE VENTAS POR RANGO DE FECHAS

    $ventas = new AjaxVentas();
    $ventas->ajaxListarVentas($_POST["fechaDesde"], $_POST["fechaHasta"]);
} else {

    if ((isset($_POST["arr"]))) {

        $registrar = new AjaxVentas();
        $registrar->ajaxRegistrarVenta($_POST["arr"], $_POST['nro_boleta'], $_POST['total_venta'], $_POST['descripcion_venta']);
    }
}


if (isset($_GET["nro_boleta"])) {

    $ventas = new AjaxVentas;
    $ventas->AjaxGenerarTicketVenta($_GET["nro_boleta"]);
}
