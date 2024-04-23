<!-- Content Header (Page header) -->
<div class="content-header">

    <div class="container-fluid">

        <div class="row mb-2">

            <div class="col-sm-6">

                <h2 class="m-0">Punto de Venta</h2>

            </div><!-- /.col -->

            <div class="col-sm-6">

                <ol class="breadcrumb float-sm-right">

                    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>

                    <li class="breadcrumb-item active">Ventas</li>

                </ol>

            </div><!-- /.col -->

        </div><!-- /.row -->

    </div><!-- /.container-fluid -->

</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">

    <div class="container-fluid">

        <div class="row mb-3">

            <div class="col-md-9">

                <div class="card card-gray shadow">

                    <div class="card-body p-3">

                        <div class="row">
                            <!-- INPUT PARA INGRESO DEL CODIGO DE BARRAS O DESCRIPCION DEL PRODUCTO -->
                            <div class="col-md-12 mb-3">

                                <div class="form-group mb-2">

                                    <label class="col-form-label" for="iptCodigoVenta">
                                        <i class="fas fa-barcode fs-6"></i>
                                        <span class="small">Productos</span>
                                    </label>

                                    <input type="text" class="form-control form-control-sm" id="iptCodigoVenta" placeholder="Ingrese el código de barras o el nombre del producto">
                                </div>

                            </div>

                            <!-- ETIQUETA QUE MUESTRA LA SUMA TOTAL DE LOS PRODUCTOS AGREGADOS AL LISTADO -->
                            <div class="col-md-7 mb-3 rounded-3" style="background-color: gray;color: white;text-align:center;border:1px solid gray;">
                                <h2 class="fw-bold m-0">$ <span class="fw-bold" id="totalVenta">0.00</span></h2>
                            </div>

                            <!-- BOTONES PARA VACIAR LISTADO Y COMPLETAR LA VENTA -->
                            <div class="col-md-5 text-right">
                                <button class="btn btn-primary" id="btnIniciarVenta">
                                    <i class="fas fa-shopping-cart"></i> Realizar Venta
                                </button>
                                <button class="btn btn-danger" id="btnVaciarListado">
                                    <i class="far fa-trash-alt"></i> Vaciar Listado
                                </button>
                            </div>

                            <!-- LISTADO QUE CONTIENE LOS PRODUCTOS QUE SE VAN AGREGANDO PARA LA COMPRA -->
                            <div class="col-md-12">

                                <table id="lstProductosVenta" class="display nowrap table-striped w-100 shadow ">
                                    <thead class="bg-gray text-left fs-6">
                                        <tr>
                                            <th>Item</th>
                                            <th>Codigo</th>
                                            <th>Id Categoria</th>
                                            <th>Categoria</th>
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                            <th>Precio</th>
                                            <th>Total</th>
                                            <th class="text-center">Opciones</th>
                                            <th>Aplica Peso</th>
                                            <th>Precio Por Mayor</th>
                                            <th>Precio Oferta</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small text-left fs-6">
                                    </tbody>
                                </table>
                                <!-- / table -->
                            </div>
                            <!-- /.col -->
                        </div>
                    </div> <!-- ./ end card-body -->
                </div>

            </div>

            <div class="col-md-3">

                <div class="card card-gray shadow">

                    <!-- <h5 class="card-header py-1 bg-primary text-white text-center">
                        Total Venta: S./ <span id="totalVentaRegistrar">0.00</span>
                    </h5> -->

                    <div class="card-body p-2">

                        <!-- SELECCIONAR TIPO DE DOCUMENTO -->
                        <div class="form-group mb-2">

                            <label class="col-form-label p-0" for="selCategoriaReg">
                                <i class="fas fa-file-alt fs-6"></i>
                                <span class="small">Documento</span><span class="text-danger">*</span>
                            </label>

                            <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="selDocumentoVenta" disabled>
                                <option value="0">Seleccione Documento</option>
                                <option value="1" selected="true">Boleta</option>
                                <option value="2">Factura</option>
                                <option value="3">Ticket</option>
                            </select>

                            <span id="validate_categoria" class="text-danger small fst-italic" style="display:none">
                                Debe Seleccione documento
                            </span>

                        </div>

                        <!-- SELECCIONAR TIPO DE PAGO -->
                        <div class="form-group mb-2">

                            <label class="col-form-label p-0" for="selCategoriaReg">
                                <i class="fas fa-money-bill-alt fs-6"></i>
                                <span class="small">Tipo Pago</span><span class="text-danger">*</span>
                            </label>

                            <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="selTipoPago">
                                <option value="0">Seleccione Tipo Pago</option>
                                <option value="1" selected="true">Efectivo</option>
                                <option value="2">Yape</option>
                                <option value="3">Plin</option>
                                <option value="4">Transferencia</option>
                            </select>

                            <span id="validate_categoria" class="text-danger small fst-italic" style="display:none">
                                Debe Ingresar tipo de pago
                            </span>

                        </div>

                        <!-- SERIE Y NRO DE BOLETA -->
                        <div class="form-group">

                            <div class="row">

                                <div class="col-md-4">

                                    <label for="iptNroSerie" class="p-0 m-0">Serie</label>

                                    <input type="text" min="0" name="iptEfectivo" id="iptNroSerie" class="form-control form-control-sm" placeholder="nro Serie" disabled>
                                </div>

                                <div class="col-md-8">

                                    <label for="iptNroVenta" class="p-0 m-0">Correlativo</label>

                                    <input type="text" min="0" name="iptEfectivo" id="iptNroVenta" class="form-control form-control-sm" placeholder="Nro Venta" disabled>

                                </div>

                            </div>

                        </div>

                        <!-- INPUT DE EFECTIVO ENTREGADO -->
                        <div class="form-group">
                            <label for="iptEfectivoRecibido" class="p-0 m-0">Efectivo recibido</label>
                            <input type="number" min="0" name="iptEfectivo" id="iptEfectivoRecibido" class="form-control form-control-sm" placeholder="Cantidad de efectivo recibida">
                        </div>

                        <!-- INPUT CHECK DE EFECTIVO EXACTO -->
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="chkEfectivoExacto">
                            <label class="form-check-label" for="chkEfectivoExacto">
                                Efectivo Exacto
                            </label>
                        </div>

                        <!-- MOSTRAR MONTO EFECTIVO ENTREGADO Y EL VUELTO -->
                        <div class="row mt-2">

                            <div class="col-12">
                                <h6 class="text-start fw-bold">Monto Efectivo: S./ <span id="EfectivoEntregado">0.00</span></h6>
                            </div>

                            <div class="col-12">
                                <h6 class="text-start text-danger fw-bold">Vuelto: S./ <span id="Vuelto">0.00</span>
                                </h6>
                            </div>

                        </div>

                        <!-- MOSTRAR EL SUBTOTAL, IGV Y TOTAL DE LA VENTA -->
                        <div class="row fw-bold">

                            <div class="col-md-7">
                                <span>OPE. GRAVADAS</span>
                            </div>
                            <div class="col-md-5 text-right">
                                S./ <span class="" id="">0.00</span>
                            </div>

                            <div class="col-md-7">
                                <span>OPE. INAFECTAS</span>
                            </div>
                            <div class="col-md-5 text-right">
                                S./ <span class="" id="">0.00</span>
                            </div>

                            <div class="col-md-7">
                                <span>OPE. EXONERADAS</span>
                            </div>
                            <div class="col-md-5 text-right">
                                S./ <span class="" id="">0.00</span>
                            </div>

                            <div class="col-md-7">
                                <span>IGV (18%)</span>
                            </div>
                            <div class="col-md-5 text-right">
                                S./ <span class="" id="boleta_igv">0.00</span>
                            </div>

                            <div class="col-md-7">
                                <span>SUBTOTAL</span>
                            </div>
                            <div class="col-md-5 text-right">
                                S./ <span class="" id="boleta_subtotal">0.00</span>
                            </div>

                            <div class="col-md-7">
                                <span>TOTAL</span>
                            </div>
                            <div class="col-md-5 text-right">
                                S./ <span class="" id="boleta_total">0.00</span>
                            </div>
                        </div>

                    </div><!-- ./ CARD BODY -->

                </div><!-- ./ CARD -->
            </div>

        </div>
    </div>

</div>

<script>
    let table;
    let items = []; // SE USA PARA EL INPUT DE AUTOCOMPLETE
    let itemProducto = 1;

    let Toast = Swal.mixin({
        toast: true,
        position: 'top',
        showConfirmButton: false,
        timer: 3000
    });

    $(document).ready(function() {

        /* ======================================================================================
        TRAER EL NRO DE BOLETA
        ======================================================================================*/
        CargarNroBoleta();

        /* ======================================================================================
        EVENTO PARA VACIAR EL CARRITO DE COMPRAS
        =========================================================================================*/
        $("#btnVaciarListado").on('click', function() {
            vaciarListado();
        })

        /* ======================================================================================
        INICIALIZAR LA TABLA DE VENTAS
        ======================================================================================*/
        table = $('#lstProductosVenta').DataTable({
            "columns": [{
                    "data": "id"
                },
                {
                    "data": "codigo_producto"
                },
                {
                    "data": "id_categoria"
                },
                {
                    "data": "nombre_categoria"
                },
                {
                    "data": "descripcion_producto"
                },
                {
                    "data": "cantidad"
                },
                {
                    "data": "precio_venta_producto"
                },
                {
                    "data": "total"
                },
                {
                    "data": "acciones"
                },
                {
                    "data": "aplica_peso"
                },
                {
                    "data": "precio_mayor_producto"
                },
                {
                    "data": "precio_oferta_producto"
                }
            ],
            columnDefs: [{
                    targets: 0,
                    visible: false
                },
                {
                    targets: 3,
                    visible: false
                },
                {
                    targets: 2,
                    visible: false
                },
                {
                    targets: 6,
                    orderable: false
                },
                {
                    targets: 9,
                    visible: false
                },
                {
                    targets: 10,
                    visible: false
                },
                {
                    targets: 11,
                    visible: false
                }
            ],
            "order": [
                [0, 'desc']
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            }
        });

        /* ======================================================================================
		TRAER LISTADO DE PRODUCTOS PARA INPUT DE AUTOCOMPLETADO
		======================================================================================*/
        $.ajax({
            async: false,
            url: "ajax/productos.ajax.php",
            method: "POST",
            data: {
                'accion': 6
            },
            dataType: 'json',
            success: function(respuesta) {

                for (let i = 0; i < respuesta.length; i++) {
                    items.push(respuesta[i]['descripcion_producto'])
                }

                $("#iptCodigoVenta").autocomplete({

                    source: items,
                    select: function(event, ui) {

                        CargarProductos(ui.item.value);

                        $("#iptCodigoVenta").val("");

                        $("#iptCodigoVenta").focus();

                        return false;
                    }
                })

            }
        });


        /* ======================================================================================
        EVENTO QUE REGISTRA EL PRODUCTO EN EL LISTADO CUANDO SE INGRESA EL CODIGO DE BARRAS
        ======================================================================================*/
        $("#iptCodigoVenta").change(function() {
            CargarProductos();
        });

        /* ======================================================================================
        EVENTO PARA ELIMINAR UN PRODUCTO DEL LISTADO
        ======================================================================================*/
        $('#lstProductosVenta tbody').on('click', '.btnEliminarproducto', function() {
            table.row($(this).parents('tr')).remove().draw();
            recalcularTotales();
        });

        /* ======================================================================================
        EVENTO PARA AUMENTAR LA CANTIDAD DE UN PRODUCTO DEL LISTADO
        ====================================================================================== */
        $('#lstProductosVenta tbody').on('click', '.btnAumentarCantidad', function() {

            let data = table.row($(this).parents('tr')).data(); //Recuperar los datos de la fila

            let idx = table.row($(this).parents('tr')).index(); // Recuperar el Indice de la Fila

            let codigo_producto = data['codigo_producto'];
            let cantidad = data['cantidad'];

            $.ajax({
                async: false,
                url: "ajax/productos.ajax.php",
                method: "POST",
                data: {
                    'accion': 8,
                    'codigo_producto': codigo_producto,
                    'cantidad_a_comprar': cantidad
                },

                dataType: 'json',
                success: function(respuesta) {

                    if (parseInt(respuesta['existe']) == 0) {

                        Toast.fire({
                            icon: 'error',
                            title: ' El producto ' + data['descripcion_producto'] + ' ya no tiene stock'
                        })

                        $("#iptCodigoVenta").val("");
                        $("#iptCodigoVenta").focus();

                    } else {

                        cantidad = parseInt(data['cantidad']) + 1;

                        table.cell(idx, 5).data(cantidad + ' Und(s)').draw();

                        NuevoPrecio = (parseInt(data['cantidad']) * data['precio_venta_producto'].replace("S./ ", "")).toFixed(2);
                        NuevoPrecio = "S./ " + NuevoPrecio;

                        table.cell(idx, 7).data(NuevoPrecio).draw();

                        recalcularTotales();
                    }
                }
            });

        });

        /* ======================================================================================
        EVENTO PARA DESMINUIR LA CANTIDAD DE UN PRODUCTO DEL LISTADO
        ======================================================================================*/
        $('#lstProductosVenta tbody').on('click', '.btnDisminuirCantidad', function() {

            let data = table.row($(this).parents('tr')).data();

            if (data['cantidad'].replace('Und(s)', '') >= 2) {

                cantidad = parseInt(data['cantidad'].replace('Und(s)', '')) - 1;

                let idx = table.row($(this).parents('tr')).index();

                table.cell(idx, 5).data(cantidad + ' Und(s)').draw();

                NuevoPrecio = (parseInt(data['cantidad']) * data['precio_venta_producto'].replace("S./ ", "")).toFixed(2);
                NuevoPrecio = "S./ " + NuevoPrecio;

                table.cell(idx, 7).data(NuevoPrecio).draw();

            }

            recalcularTotales();
        });

        /* ======================================================================================
        EVENTO PARA INGRESAR EL PESO DEL PRODUCTO
        ====================================================================================== */
        $('#lstProductosVenta tbody').on('click', '.btnIngresarPeso', function() {

            let data = table.row($(this).parents('tr')).data();

            Swal.fire({
                title: "",
                text: "Peso del Producto (Grms):",
                input: 'text',
                width: 300,
                confirmButtonText: 'Aceptar',
                showCancelButton: true,
            }).then((result) => {

                if (result.value) {

                    cantidad = result.value;

                    let idx = table.row($(this).parents('tr')).index();

                    table.cell(idx, 5).data(cantidad + ' Kg(s)').draw();

                    NuevoPrecio = ((parseFloat(data['cantidad']) * data['precio_venta_producto'].replace("S./ ", "")).toFixed(2));
                    NuevoPrecio = "S./ " + NuevoPrecio;

                    table.cell(idx, 7).data(NuevoPrecio).draw();

                    recalcularTotales();

                }

            });


        });

        /* ======================================================================================
        EVENTO PARA MODIFICAR EL PRECIO DE VENTA DEL PRODUCTO
        ======================================================================================*/
        $('#lstProductosVenta tbody').on('click', '.dropdown-item', function() {

            codigo_producto = $(this).attr("codigo");
            console.log("🚀 ~ file: ventas.php:527 ~ $ ~ codigo_producto", codigo_producto)
            precio_venta = parseFloat($(this).attr("precio").replaceAll("S./ ", "")).toFixed(2);

            recalcularMontos(codigo_producto, precio_venta);
        });


        /* ======================================================================================
        EVENTO PARA MODIFICAR LA CANTIDAD DE PRODUCTOS A COMPRAR
        ======================================================================================*/
        $('#lstProductosVenta tbody').on('change', '.iptCantidad', function() {

            let data = table.row($(this).parents('tr')).data();

            cantidad_actual = $(this)[0]['value'];
            cod_producto_actual = $(this)[0]['attributes'][2]['value'];

            console.log("cantidad", $(this)[0]['value'])
            console.log("codigo Producto", $(this)[0]['attributes'][2]['value'])

            if (!$.isNumeric($(this)[0]['value']) || $(this)[0]['value'] <= 0) {

                mensajeToast('error', 'INGRESE UN VALOR NUMERICO Y MAYOR A 0');

                $(this)[0]['value'] = "1";

                $("#iptCodigoVenta").val("");
                $("#iptCodigoVenta").focus();
                return;
            }

            console.log(cantidad_actual)

            table.rows().eq(0).each(function(index) {

                let row = table.row(index);

                let data = row.data();

                if (data['codigo_producto'] == cod_producto_actual) {

                    $.ajax({
                        async: false,
                        url: "ajax/productos.ajax.php",
                        method: "POST",
                        data: {
                            'accion': 8,
                            'codigo_producto': cod_producto_actual,
                            'cantidad_a_comprar': cantidad_actual
                        },
                        dataType: 'json',
                        success: function(respuesta) {

                            if (parseInt(respuesta['existe']) == 0) {

                                mensajeToast('error', ' El producto ' + data['descripcion_producto'] + ' ya no tiene stock');

                                table.cell(index, 5).data('<input type="text" style="width:80px;" codigoProducto = "' + cod_producto_actual + '" class="form-control text-center iptCantidad m-0 p-0" value="1">').draw();

                                $("#iptCodigoVenta").val("");
                                $("#iptCodigoVenta").focus();

                                // ACTUALIZAR EL NUEVO PRECIO DEL ITEM DEL LISTADO DE VENTA
                                NuevoPrecio = (parseFloat(1) * data['precio_venta_producto'].replaceAll("S./ ", "")).toFixed(2);
                                NuevoPrecio = "S./ " + NuevoPrecio;
                                table.cell(index, 7).data(NuevoPrecio).draw();

                                // RECALCULAMOS TOTALES
                                recalcularTotales();

                            } else {

                                // AUMENTAR EN 1 EL VALOR DE LA CANTIDAD
                                table.cell(index, 5).data('<input type="text" style="width:80px;" codigoProducto = "' + cod_producto_actual + '" class="form-control text-center iptCantidad m-0 p-0" value="' + cantidad_actual + '">').draw();


                                // ACTUALIZAR EL NUEVO PRECIO DEL ITEM DEL LISTADO DE VENTA
                                NuevoPrecio = (parseFloat(cantidad_actual) * data['precio_venta_producto'].replaceAll("S./ ", "")).toFixed(2);
                                NuevoPrecio = "S./ " + NuevoPrecio;
                                table.cell(index, 7).data(NuevoPrecio).draw();

                                // RECALCULAMOS TOTALES
                                recalcularTotales();
                            }
                        }
                    });



                }


            });

        });


        /* =======================================================================================
        EVENTO QUE PERMITE CHECKEAR EL EFECTIVO CUANDO ES EXACTO
        =========================================================================================*/
        $("#chkEfectivoExacto").change(function() {

            if ($("#chkEfectivoExacto").is(':checked')) {

                let vuelto = 0;
                let totalVenta = $("#totalVenta").html();

                $("#iptEfectivoRecibido").val(totalVenta);

                $("#EfectivoEntregado").html(totalVenta);

                let EfectivoRecibido = parseFloat($("#EfectivoEntregado").html().replace("S./ ", ""));

                vuelto = parseFloat(totalVenta) - parseFloat(EfectivoRecibido);

                $("#Vuelto").html(vuelto.toFixed(2));

            } else {

                $("#iptEfectivoRecibido").val("")
                $("#EfectivoEntregado").html("0.00");
                $("#Vuelto").html("0.00");

            }
        })

        /* ======================================================================================
        EVENTO QUE SE DISPARA AL DIGITAR EL MONTO EN EFECTIVO ENTREGADO POR EL CLIENTE
        =========================================================================================*/
        $("#iptEfectivoRecibido").keyup(function() {
            actualizarVuelto();
        });

        /* ======================================================================================
        EVENTO PARA INICIAR EL REGISTRO DE LA VENTA
        ====================================================================================== */
        $("#btnIniciarVenta").on('click', function() {
            realizarVenta();
        })


    }) //FIN DOCUMENT READY

    /*===================================================================*/
    //FUNCION PARA CARGAR EL NRO DE BOLETA
    /*===================================================================*/
    function CargarNroBoleta() {

        $.ajax({
            async: false,
            url: "ajax/ventas.ajax.php",
            method: "POST",
            data: {
                'accion': 1
            },
            dataType: 'json',
            success: function(respuesta) {

                serie_boleta = respuesta["serie_boleta"];
                nro_boleta = respuesta["nro_venta"];

                $("#iptNroSerie").val(serie_boleta);
                $("#iptNroVenta").val(nro_boleta);
            }
        });
    }

    /*===================================================================*/
    //FUNCION PARA LIMPIAR TOTALMENTE EL CARRITO DE VENTAS
    /*===================================================================*/
    function vaciarListado() {
        table.clear().draw();
        LimpiarInputs();
    }

    /*===================================================================*/
    //FUNCION PARA LIMPIAR LOS INPUTS DE LA BOLETA Y LABELS QUE TIENEN DATOS
    /*===================================================================*/
    function LimpiarInputs() {
        $("#totalVenta").html("0.00");
        $("#totalVentaRegistrar").html("0.00");
        $("#boleta_total").html("0.00");
        $("#iptEfectivoRecibido").val("");
        $("#EfectivoEntregado").html("0.00");
        $("#Vuelto").html("0.00");
        $("#chkEfectivoExacto").prop('checked', false);
        $("#boleta_subtotal").html("0.00");
        $("#boleta_igv").html("0.00")
    } /* FIN LimpiarInputs */

    /*===================================================================*/
    //FUNCION PARA ACTUALIZAR EL VUELTO
    /*===================================================================*/
    function actualizarVuelto() {

        let totalVenta = $("#totalVenta").html();

        $("#chkEfectivoExacto").prop('checked', false);

        let efectivoRecibido = $("#iptEfectivoRecibido").val();

        if (efectivoRecibido > 0) {

            $("#EfectivoEntregado").html(parseFloat(efectivoRecibido).toFixed(2));

            vuelto = parseFloat(efectivoRecibido) - parseFloat(totalVenta);

            $("#Vuelto").html(vuelto.toFixed(2));

        } else {

            $("#EfectivoEntregado").html("0.00");
            $("#Vuelto").html("0.00");

        }
    }


    function recalcularMontos(codigo_producto, precio_venta) {

        table.rows().eq(0).each(function(index) {

            let row = table.row(index);

            let data = row.data();


            if (data['codigo_producto'] == codigo_producto) {

                // AUMENTAR EN 1 EL VALOR DE LA CANTIDAD
                table.cell(index, 6).data("S./ " + parseFloat(precio_venta).toFixed(2)).draw();

                // cantidad_actual = 
                console.log("🚀 ~ file: ventas.php:744 ~ table.rows ~ data", parseFloat($.parseHTML(data['cantidad'])[0]['value']))
                cantidad_actual = parseFloat($.parseHTML(data['cantidad'])[0]['value']);

                // ACTUALIZAR EL NUEVO PRECIO DEL ITEM DEL LISTADO DE VENTA
                NuevoPrecio = (parseFloat(cantidad_actual) * data['precio_venta_producto'].replaceAll("S./ ", "")).toFixed(2);
                NuevoPrecio = "S./ " + NuevoPrecio;
                table.cell(index, 7).data(NuevoPrecio).draw();

            }


        });

        // RECALCULAMOS TOTALES
        recalcularTotales();

    }

    /*===================================================================*/
    //FUNCION PARA RECALCULAR LOS TOTALES DE VENTA
    /*===================================================================*/
    function recalcularTotales() {

        let TotalVenta = 0.00;

        table.rows().eq(0).each(function(index) {

            let row = table.row(index);
            let data = row.data();

            TotalVenta = parseFloat(TotalVenta) + parseFloat(data['total'].replace("S./ ", ""));

        });

        $("#totalVenta").html("");
        $("#totalVenta").html(TotalVenta.toFixed(2));

        let totalVenta = $("#totalVenta").html();
        let igv = parseFloat(totalVenta) * 0.18
        let subtotal = parseFloat(totalVenta) - parseFloat(igv);

        $("#totalVentaRegistrar").html(totalVenta);

        $("#boleta_subtotal").html(parseFloat(subtotal).toFixed(2));
        $("#boleta_igv").html(parseFloat(igv).toFixed(2));
        $("#boleta_total").html(parseFloat(totalVenta).toFixed(2));

        //limpiamos el input de efectivo exacto; desmarcamos el check de efectivo exacto
        //borramos los datos de efectivo entregado y vuelto
        $("#iptEfectivoRecibido").val("");
        $("#chkEfectivoExacto").prop('checked', false);
        $("#EfectivoEntregado").html("0.00");
        $("#Vuelto").html("0.00");

        $("#iptCodigoVenta").val("");
        $("#iptCodigoVenta").focus();
    }


    /*===================================================================*/
    //FUNCION PARA CARGAR PRODUCTOS EN EL DATATABLE
    /*===================================================================*/
    function CargarProductos(producto = "") {

        if (producto != "") {
             codigo_producto = producto;

        } else {
             codigo_producto = $("#iptCodigoVenta").val();
        }

        codigo_producto = $.trim(codigo_producto.split('/')[0]);
        // console.log("🚀 ~ file: ventas.php:844 ~ CargarProductos ~ codigo_producto", codigo_producto)

        // return;

        let producto_repetido = 0;

        /*===================================================================*/
        // AUMENTAMOS LA CANTIDAD SI EL PRODUCTO YA EXISTE EN EL LISTADO
        /*===================================================================*/
        table.rows().eq(0).each(function(index) {

            let row = table.row(index);
            let data = row.data();
            console.log("🚀 ~ file: ventas.php:829 ~ table.rows ~ data", $.parseHTML(data['cantidad'])[0]['value'])

            if (codigo_producto == data['codigo_producto']) {

                producto_repetido = 1;

                cantidad_a_comprar = parseFloat($.parseHTML(data['cantidad'])[0]['value']) + 1;

                $.ajax({
                    async: false,
                    url: "ajax/productos.ajax.php",
                    method: "POST",
                    data: {
                        'accion': 8,
                        'codigo_producto': codigo_producto,
                        'cantidad_a_comprar': cantidad_a_comprar
                    },
                    dataType: 'json',
                    success: function(respuesta) {

                        if (parseInt(respuesta['existe']) == 0) {

                            mensajeToast('error', ' El producto ' + data['descripcion_producto'] + ' ya no tiene stock');

                            $("#iptCodigoVenta").val("");
                            $("#iptCodigoVenta").focus();

                        } else {

                            // AUMENTAR EN 1 EL VALOR DE LA CANTIDAD
                            table.cell(index, 5).data('<input type="text" style="width:80px;" codigoProducto = "' + codigo_producto + '" class="form-control text-center iptCantidad m-0 p-0" value="' + cantidad_a_comprar + '">').draw();


                            // ACTUALIZAR EL NUEVO PRECIO DEL ITEM DEL LISTADO DE VENTA
                            NuevoPrecio = (parseFloat(cantidad_a_comprar) * data['precio_venta_producto'].replaceAll("S./ ", "")).toFixed(2);
                            NuevoPrecio = "S./ " + NuevoPrecio;
                            table.cell(index, 7).data(NuevoPrecio).draw();

                            // RECALCULAMOS TOTALES
                            recalcularTotales();
                        }
                    }
                });

            }
        });

        // return;

        if (producto_repetido == 1) {
            return;
        }

        console.log(codigo_producto);

        $.ajax({
            url: "ajax/productos.ajax.php",
            method: "POST",
            data: {
                'accion': 7, //BUSCAR PRODUCTOS POR SU CODIGO DE BARRAS
                'codigo_producto': codigo_producto
            },
            dataType: 'json',
            success: function(respuesta) {

                console.log(respuesta);
                /*===================================================================*/
                //SI LA RESPUESTA ES VERDADERO, TRAE ALGUN DATO
                /*===================================================================*/
                if (respuesta) {

                    let TotalVenta = 0.00;

                    table.row.add({
                        'id': itemProducto,
                        'codigo_producto': respuesta['codigo_producto'],
                        'id_categoria': respuesta['id_categoria'],
                        'nombre_categoria': respuesta['nombre_categoria'],
                        'descripcion_producto': respuesta['descripcion_producto'],
                        'cantidad': '<input type="text" style="width:80px;" codigoProducto = "' + respuesta['codigo_producto'] + '" class="form-control text-center iptCantidad p-0 m-0" value="1">',
                        'precio_venta_producto': respuesta['precio_venta_producto'],
                        'total': respuesta['total'],
                        'acciones': "<center>" +
                            // "<span class='btnAumentarCantidad text-success px-1' style='cursor:pointer;' data-bs-toggle='tooltip' data-bs-placement='top' title='Aumentar Stock'> " +
                            // "<i class='fas fa-cart-plus fs-5'></i> " +
                            // "</span> " +
                            // "<span class='btnDisminuirCantidad text-warning px-1' style='cursor:pointer;' data-bs-toggle='tooltip' data-bs-placement='top' title='Disminuir Stock'> " +
                            // "<i class='fas fa-cart-arrow-down fs-5'></i> " +
                            // "</span> " +
                            "<span class='btnEliminarproducto text-danger px-1'style='cursor:pointer;' data-bs-toggle='tooltip' data-bs-placement='top' title='Eliminar producto'> " +
                            "<i class='fas fa-trash fs-5'> </i> " +
                            "</span>" +
                            "<div class='btn-group'>" +
                            "<button type='button' class=' p-0 btn btn-primary transparentbar dropdown-toggle btn-sm' data-bs-toggle='dropdown' aria-expanded='false'>" +
                            "<i class='fas fa-cog text-primary fs-5'></i> <i class='fas fa-chevron-down text-primary'></i>" +
                            "</button>" +

                            "<ul class='dropdown-menu'>" +
                            "<li><a class='dropdown-item' codigo = '" + respuesta['codigo_producto'] + "' precio=' " + respuesta['precio_venta_producto'] + "' style='cursor:pointer; font-size:14px;'>Normal (" + respuesta['precio_venta_producto'] + ")</a></li>" +
                            "<li><a class='dropdown-item' codigo = '" + respuesta['codigo_producto'] + "' precio=' " + respuesta['precio_mayor_producto'] + "' style='cursor:pointer; font-size:14px;'>Por Mayor (S./ " + parseFloat(respuesta['precio_mayor_producto']).toFixed(2) + ")</a></li>" +
                            "<li><a class='dropdown-item' codigo = '" + respuesta['codigo_producto'] + "' precio=' " + respuesta['precio_oferta_producto'] + "' style='cursor:pointer; font-size:14px;'>Oferta (S./ " + parseFloat(respuesta['precio_oferta_producto']).toFixed(2) + ")</a></li>" +
                            "</ul>" +
                            "</div>" +
                            "</center>",
                        'aplica_peso': respuesta['aplica_peso'],
                        'precio_mayor_producto': respuesta['precio_mayor_producto'],
                        'precio_oferta_producto': respuesta['precio_oferta_producto']
                    }).draw();

                    itemProducto = itemProducto + 1;

                    //  Recalculamos el total de la venta
                    recalcularTotales();

                    /*===================================================================*/
                    //SI LA RESPUESTA ES FALSO, NO TRAE ALGUN DATO
                    /*===================================================================*/
                } else {

                    mensajeToast('error', 'EL PRODUCTO NO EXISTE O NO TIENE STOCK');

                    $("#iptCodigoVenta").val("");
                    $("#iptCodigoVenta").focus();
                }

            }
        });

    } /* FIN CargarProductos */

    /*===================================================================*/
    //REALIZAR LA VENTA
    /*===================================================================*/
    function realizarVenta() {

        let count = 0;
        let totalVenta = $("#totalVenta").html();
        let nro_boleta = $("#iptNroVenta").val();

        table.rows().eq(0).each(function(index) {
            count = count + 1;
        });

        if (count > 0) {

            if ($("#iptEfectivoRecibido").val() > 0 && $("#iptEfectivoRecibido").val() != "") {

                if ($("#iptEfectivoRecibido").val() < parseFloat(totalVenta)) {

                    mensajeToast('error', 'EL EFECTIVO ES MENOR EL COSTO TOTAL DE LA VENTA');

                    return false;
                }

                let formData = new FormData();
                let arr = [];

                table.rows().eq(0).each(function(index) {

                    let row = table.row(index);

                    let data = row.data();

                    arr[index] = data['codigo_producto'] + "," + parseFloat($.parseHTML(data['cantidad'])[0]['value']) + "," + data['total'].replace("S./ ", "");

                    formData.append('arr[]', arr[index]);

                });

                formData.append('nro_boleta', nro_boleta);
                formData.append('descripcion_venta', 'Venta realizada con Nro Boleta: ' + nro_boleta);
                formData.append('total_venta', parseFloat(totalVenta));

                $.ajax({
                    url: "ajax/ventas.ajax.php",
                    method: "POST",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(respuesta) {


                        mensajeToast('success', respuesta);

                        table.clear().draw();

                        LimpiarInputs();

                        CargarNroBoleta();

                        window.open('http://localhost/market-pos/vistas/generar_ticket.php?nro_boleta='+nro_boleta);

                    }
                });


            } else {

                mensajeToast('error', 'INGRESE EL MONTO EN EFECTIVO');
            }

        } else {

            mensajeToast('error', 'NO HAY PRODUCTOS EN EL LISTADO');

        }

        $("#iptCodigoVenta").focus();

    } /* FIN realizarVenta */
</script>