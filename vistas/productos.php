<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h2 class="m-0">Inventario / Productos</h2>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                    <li class="breadcrumb-item active">Inventario / Productos</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">

    <div class="container-fluid">

        <!-- row para criterios de busqueda -->
        <div class="row">

            <div class="col-lg-12">

                <div class="card card-gray shadow">
                    <div class="card-header">
                        <h3 class="card-title">CRITERIOS DE BÚSQUEDA</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool text-warning" id="btnLimpiarBusqueda">
                                <i class="fas fa-times"></i>
                            </button>
                        </div> <!-- ./ end card-tools -->
                    </div> <!-- ./ end card-header -->
                    <div class="card-body">

                        <div class="row">

                            <div class="d-none d-md-flex col-md-12 ">

                                <div style="width: 20%;" class="form-floating mx-1">
                                    <input type="text" id="iptCodigoBarras" class="form-control" data-index="1">
                                    <label for="iptCodigoBarras">Código de Barras</label>
                                </div>

                                <div style="width: 20%;" class="form-floating mx-1">
                                    <input type="text" id="iptCategoria" class="form-control" data-index="3">
                                    <label for="iptCategoria">Categoría</label>
                                </div>

                                <div style="width: 20%;" class="form-floating mx-1">
                                    <input type="text" id="iptProducto" class="form-control" data-index="4">
                                    <label for="iptProducto">Producto</label>
                                </div>

                                <div style="width: 20%;" class="form-floating mx-1">
                                    <input type="text" id="iptPrecioVentaDesde" class="form-control">
                                    <label for="iptPrecioVentaDesde">P. Venta Desde</label>
                                </div>

                                <div style="width: 20%;" class="form-floating mx-1">
                                    <input type="text" id="iptPrecioVentaHasta" class="form-control">
                                    <label for="iptPrecioVentaHasta">P. Venta Hasta</label>
                                </div>
                            </div>

                            <div class="d-block d-sm-none">

                                <div style="width: 100%;" class="form-floating mx-1 my-1">
                                    <input type="text" id="iptCodigoBarras" class="form-control" data-index="1">
                                    <label for="iptCodigoBarras">Código de Barras</label>
                                </div>

                                <div style="width: 100%;" class="form-floating mx-1 my-1">
                                    <input type="text" id="iptCategoria" class="form-control" data-index="3">
                                    <label for="iptCategoria">Categoría</label>
                                </div>

                                <div style="width: 100%;" class="form-floating mx-1 my-1">
                                    <input type="text" id="iptProducto" class="form-control" data-index="4">
                                    <label for="iptProducto">Producto</label>
                                </div>

                                <div style="width: 100%;" class="form-floating mx-1 my-1">
                                    <input type="text" id="iptPrecioVentaDesde" class="form-control">
                                    <label for="iptPrecioVentaDesde">P. Venta Desde</label>
                                </div>

                                <div style="width: 100%;" class="form-floating mx-1 my-1">
                                    <input type="text" id="iptPrecioVentaHasta" class="form-control">
                                    <label for="iptPrecioVentaHasta">P. Venta Hasta</label>
                                </div>
                            </div>

                        </div>
                    </div> <!-- ./ end card-body -->
                </div>

            </div>

        </div>

        <!-- row para listado de productos/inventario -->
        <div class="row">
            <div class="col-lg-12">
                <table id="tbl_productos" class="table table-striped w-100 shadow border border-secondary">
                    <thead class="bg-gray">
                        <tr style="font-size: 15px;">
                            <th></th> <!-- 0 -->
                            <th>Codigo</th> <!-- 1 -->
                            <th>Id Categoria</th> <!-- 2 -->
                            <th>Categoría</th> <!-- 3 -->
                            <th>Producto</th> <!-- 4 -->
                            <th>P. Compra</th> <!-- 5 -->
                            <th>P. Venta</th> <!-- 6 -->
                            <th>P. Venta Mayor</th> <!-- 7 -->
                            <th>P. Venta Oferta</th> <!-- 8 -->
                            <th>Stock</th> <!-- 9 -->
                            <th>Min. Stock</th> <!-- 10 -->
                            <th>Ventas</th> <!-- 11 -->
                            <th>Fecha Creación</th> <!-- 12 -->
                            <th>Fecha Actualización</th> <!-- 13 -->
                            <th class="text-cetner">Opciones</th> <!-- 14 -->
                        </tr>
                    </thead>
                    <tbody class="text-small">
                    </tbody>
                </table>
            </div>
        </div>

    </div><!-- /.container-fluid -->

</div>
<!-- /.content -->

<!-- =============================================================================================================================
VENTA MODAL PARA REGISTRAR O ACTUALIZAR UN PRODUCTO 
===============================================================================================================================-->
<div class="modal fade" id="mdlGestionarProducto" role="dialog">

    <div class="modal-dialog modal-lg">

        <!-- contenido del modal -->
        <div class="modal-content">

            <!-- cabecera del modal -->
            <div class="modal-header bg-gray py-1">

                <h5 class="modal-title">Agregar Producto</h5>

                <button type="button" class="btn btn-outline-primary text-white border-0 fs-5" data-bs-dismiss="modal" id="btnCerrarModal">
                    <i class="far fa-times-circle"></i>
                </button>

            </div>

            <!-- cuerpo del modal -->
            <div class="modal-body">

                <form id="frm-datos-producto" class="needs-validation" novalidate>

                    <!-- Abrimos una fila -->
                    <div class="row">

                        <!-- Columna para registro del codigo de barras -->
                        <div class="col-12 col-lg-6">
                            <div class="form-group mb-2">
                                <label class="" for="iptCodigoReg"><i class="fas fa-barcode fs-6"></i>
                                    <span class="small">Código de Barras</span><span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-sm" id="iptCodigoReg" name="iptCodigoReg" placeholder="Código de Barras" required>
                                <div class="invalid-feedback">Ingrese el codigo de barras</div>
                            </div>
                        </div>

                        <!-- Columna para registro de la categoría del producto -->
                        <div class="col-12  col-lg-6">
                            <div class="form-group mb-2">
                                <label class="" for="selCategoriaReg"><i class="fas fa-dumpster fs-6"></i>
                                    <span class="small">Categoría</span><span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="selCategoriaReg" name="selCategoriaReg" required>
                                </select>
                                <div class="invalid-feedback">Seleccione la categoría</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group mb-2">
                                <label for="iptImagen">
                                    <i class="fas fa-image fs-6"></i>
                                    <span class="small">Seleccione una imagen</span>
                                </label>
                                <input type="file" class="form-control form-control-sm" id="iptImagen" name="iptImagen" accept="image/*" onchange="previewFile(this)">
                            </div>
                        </div>

                        <div class="col-12 col-lg-5 my-1">
                            <div style="width: 100%; height: 280px;">
                                <img id="previewImg" src="vistas/assets/imagenes/no_image.jpg" class="border border-secondary" style="object-fit: cover; width: 100%; height: 100%;" alt="">
                            </div>
                        </div>

                        <div class="col-lg-7">

                            <div class="row">

                                <!-- Columna para registro de la descripción del producto -->
                                <div class="col-12">
                                    <div class="form-group mb-2">
                                        <label class="" for="iptDescripcionReg"><i class="fas fa-file-signature fs-6"></i> <span class="small">Descripción</span><span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm" id="iptDescripcionReg" name="iptDescripcionReg" placeholder="Descripción" required>
                                        <div class="invalid-feedback">Ingrese la descripción</div>
                                    </div>
                                </div>

                                <!-- Columna para registro del Precio de Compra -->
                                <div class="col-12  col-lg-6">
                                    <div class="form-group mb-2">
                                        <label class="" for="iptPrecioCompraReg"><i class="fas fa-dollar-sign fs-6"></i> <span class="small">Precio
                                                Compra</span><span class="text-danger">*</span></label>
                                        <input type="number" min="0" class="form-control form-control-sm" step="0.01" id="iptPrecioCompraReg" placeholder="Precio de Compra" name="iptPrecioCompraReg" required>
                                        <div class="invalid-feedback">Ingrese el Precio de compra</div>
                                    </div>
                                </div>

                                <!-- Columna para registro del Precio de Venta -->
                                <div class="col-12 col-lg-6">
                                    <div class="form-group mb-2">
                                        <label class="" for="iptPrecioVentaReg"><i class="fas fa-dollar-sign fs-6"></i> <span class="small">Precio
                                                Venta</span><span class="text-danger">*</span></label>
                                        <input type="number" min="0" class="form-control form-control-sm" id="iptPrecioVentaReg" name="iptPrecioVentaReg" placeholder="Precio de Venta" step="0.01" required>
                                        <div class="invalid-feedback">Ingrese el precio de venta</div>
                                    </div>
                                </div>

                                <!-- Columna para registro del Precio de Venta Mayor-->
                                <div class="col-12 col-lg-6">
                                    <div class="form-group mb-2">
                                        <label class="" for="iptPrecioVentaMayorReg"><i class="fas fa-dollar-sign fs-6"></i> <span class="small">Precio
                                                Venta x Mayor</span></label>
                                        <input type="number" min="0" class="form-control form-control-sm" id="iptPrecioVentaMayorReg" name="iptPrecioVentaMayorReg" placeholder="Precio de Venta" step="0.01">
                                    </div>
                                </div>

                                <!-- Columna para registro del Precio de Venta Oferta-->
                                <div class="col-12 col-lg-6">
                                    <div class="form-group mb-2">
                                        <label class="" for="iptPrecioVentaOfertaReg"><i class="fas fa-dollar-sign fs-6"></i> <span class="small">Precio
                                                Venta Oferta</span></label>
                                        <input type="number" min="0" class="form-control form-control-sm" id="iptPrecioVentaOfertaReg" name="iptPrecioVentaOfertaReg" placeholder="Precio de Venta" step="0.01">
                                    </div>
                                </div>

                                <!-- Columna para registro del Stock del producto -->
                                <div class="col-12 col-lg-6">
                                    <div class="form-group mb-2">
                                        <label class="" for="iptStockReg"><i class="fas fa-plus-circle fs-6"></i>
                                            <span class="small">Stock</span><span class="text-danger">*</span></label>
                                        <input type="number" min="0" class="form-control form-control-sm" id="iptStockReg" name="iptStockReg" placeholder="Stock" required>
                                        <div class="invalid-feedback">Ingrese el stock</div>
                                    </div>
                                </div>

                                <!-- Columna para registro del Minimo de Stock -->
                                <div class="col-12 col-lg-6">
                                    <div class="form-group mb-2">
                                        <label class="" for="iptMinimoStockReg"><i class="fas fa-minus-circle fs-6"></i> <span class="small">Mínimo
                                                Stock</span><span class="text-danger">*</span></label>
                                        <input type="number" min="0" class="form-control form-control-sm" id="iptMinimoStockReg" name="iptMinimoStockReg" placeholder="Mínimo Stock" required>
                                        <div class="invalid-feedback">Ingrese el minimo stock</div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- creacion de botones para cancelar y guardar el producto -->
                        <button type="button" class="btn btn-danger mt-3 mx-2" style="width:170px;" data-bs-dismiss="modal" id="btnCancelarRegistro">Cancelar</button>
                        <button type="button" style="width:170px;" class="btn btn-primary mt-3 mx-2" id="btnGuardarProducto">Guardar Producto</button>
                        <!-- <button class="btn btn-default btn-success" type="submit" name="submit" value="Submit">Save</button> -->

                    </div>
                </form>

            </div>

        </div>
    </div>


</div>
<!-- /. End Ventana Modal para ingreso de Productos -->

<!-- Ventana Modal para aumentar o diminuir el stock del Productos -->
<div class="modal fade" id="mdlGestionarStock" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-gray py-2">
                <h6 class="modal-title" id="titulo_modal_stock">Adicionar Stock</h6>
                <button type="button" class="btn-close text-white fs-6" data-bs-dismiss="modal" aria-label="Close" id="btnCerrarModalStock">
                </button>
            </div>

            <div class="modal-body">

                <div class="row">

                    <div class="col-12 mb-3">
                        <label for="" class="form-label text-primary d-block">Codigo: <span id="stock_codigoProducto" class="text-secondary"></span></label>
                        <label for="" class="form-label text-primary d-block">Producto: <span id="stock_Producto" class="text-secondary"></span></label>
                        <label for="" class="form-label text-primary d-block">Stock: <span id="stock_Stock" class="text-secondary"></span></label>
                    </div>

                    <div class="col-12">
                        <div class="form-group mb-2">
                            <label class="" for="iptStockSumar">
                                <i class="fas fa-plus-circle fs-6"></i> <span class="small" id="titulo_modal_label">Agregar al Stock</span>
                            </label>
                            <input type="number" min="0" class="form-control form-control-sm" id="iptStockSumar" placeholder="Ingrese cantidad a agregar al Stock">
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="" class="form-label text-danger">Nuevo Stock: <span id="stock_NuevoStock" class="text-secondary"></span></label><br>
                    </div>

                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" id="btnCancelarRegistroStock">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm" id="btnGuardarNuevorStock">Guardar</button>
            </div>

        </div>
    </div>
</div>
<!-- /. End Ventana Modal para ingreso de Productos -->

<script>
    let accion;
    let table;
    let operacion_stock = 0; // permitar definir si vamos a sumar o restar al stock (1: sumar, 2:restar)

    /*===================================================================*/
    //INICIALIZAMOS EL MENSAJE DE TIPO TOAST (EMERGENTE EN LA PARTE SUPERIOR)
    /*===================================================================*/
    let Toast = Swal.mixin({
        toast: true,
        position: 'top',
        showConfirmButton: false,
        timer: 3000
    });

    $(document).ready(function() {


        /*===================================================================*/
        // CARGA DEL LISTADO CON EL PLUGIN DATATABLE JS
        /*===================================================================*/
        table = $("#tbl_productos").DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    text: 'Agregar Producto',
                    className: 'addNewRecord',
                    action: function(e, dt, node, config) {
                        $("#mdlGestionarProducto").modal('show');
                        accion = 2; //registrar
                        $(".needs-validation").removeClass("was-validated");

                        /*===================================================================*/
                        //SOLICITUD AJAX PARA CARGAR SELECT DE CATEGORIAS
                        /*===================================================================*/
                        $.ajax({
                            url: "ajax/categorias.ajax.php",
                            cache: false,
                            contentType: false,
                            processData: false,
                            dataType: 'json',
                            success: function(respuesta) {

                                let options = '<option selected value="">Seleccione una categoría</option>';

                                for (let index = 0; index < respuesta.length; index++) {
                                    options = options + '<option value=' + respuesta[index][0] + '>' + respuesta[index][
                                        1
                                    ] + '</option>';
                                }

                                $("#selCategoriaReg").append(options);
                            }
                        });
                    }
                },
                'excel', 'print', 'pageLength'
            ],
            pageLength: [5, 10, 15, 30, 50, 100],
            pageLength: 10,
            ajax: {
                url: "ajax/productos.ajax.php",
                dataSrc: '',
                type: "POST",
                data: {
                    'accion': 1 //1: LISTAR PRODUCTOS
                },
            },
            responsive: {
                details: {
                    type: 'column'
                }
            },
            columnDefs: [{
                    targets: 0,
                    orderable: false,
                    className: 'control'
                },
                {
                    targets: 2,
                    visible: false
                },
                {
                    targets: 7,
                    visible: false
                },
                {
                    targets: 8,
                    visible: false
                },
                {
                    targets: 9,
                    createdCell: function(td, cellData, rowData, row, col) {
                        if (parseFloat(rowData[9]) <= parseFloat(rowData[10])) {
                            $(td).parent().css('background', '#FF5733')
                            $(td).parent().css('color', '#ffffff')
                        }
                    }
                },
                {
                    targets: 11,
                    visible: false
                },
                {
                    targets: 12,
                    visible: false
                },
                {
                    targets: 13,
                    visible: false
                },
                {
                    targets: 14,
                    orderable: false,
                    render: function(data, type, full, meta) {
                        return "<center>" +
                            "<span class='btnEditarProducto text-primary px-1' style='cursor:pointer;'>" +
                            "<i class='fas fa-pencil-alt fs-5'></i>" +
                            "</span>" +
                            "<span class='btnAumentarStock text-success px-1' style='cursor:pointer;'>" +
                            "<i class='fas fa-plus-circle fs-5'></i>" +
                            "</span>" +
                            "<span class='btnDisminuirStock text-warning px-1' style='cursor:pointer;'>" +
                            "<i class='fas fa-minus-circle fs-5'></i>" +
                            "</span>" +
                            "<span class='btnEliminarProducto text-danger px-1' style='cursor:pointer;'>" +
                            "<i class='fas fa-trash fs-5'></i>" +
                            "</span>" +
                            "</center>"
                    }
                }

            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            }
        });

        /*===================================================================*/
        // EVENTOS PARA CRITERIOS DE BUSQUEDA (CODIGO, CATEGORIA Y PRODUCTO)
        /*===================================================================*/
        $("#iptCodigoBarras").keyup(function() {
            table.column($(this).data('index')).search(this.value).draw();
        })

        $("#iptCategoria").keyup(function() {
            table.column($(this).data('index')).search(this.value).draw();
        })

        $("#iptProducto").keyup(function() {
            table.column($(this).data('index')).search(this.value).draw();
        })

        /*===================================================================*/
        // EVENTOS PARA CRITERIOS DE BUSQUEDA POR RANGO (PREVIO VENTA)
        /*===================================================================*/
        $("#iptPrecioVentaDesde, #iptPrecioVentaHasta").keyup(function() {
            table.draw();
        })

        $.fn.dataTable.ext.search.push(

            function(settings, data, dataIndex) {

                let precioDesde = parseFloat($("#iptPrecioVentaDesde").val());
                let precioHasta = parseFloat($("#iptPrecioVentaHasta").val());

                let col_venta = parseFloat(data[6]);

                if ((isNaN(precioDesde) && isNaN(precioHasta)) ||
                    (isNaN(precioDesde) && col_venta <= precioHasta) ||
                    (precioDesde <= col_venta && isNaN(precioHasta)) ||
                    (precioDesde <= col_venta && col_venta <= precioHasta)) {
                    return true;
                }

                return false;
            }
        )

        /*===================================================================*/
        // EVENTO PARA LIMPIAR INPUTS DE CRITERIOS DE BUSQUEDA
        /*===================================================================*/
        $("#btnLimpiarBusqueda").on('click', function() {

            $("#iptCodigoBarras").val('')
            $("#iptCategoria").val('')
            $("#iptProducto").val('')
            $("#iptPrecioVentaDesde").val('')
            $("#iptPrecioVentaHasta").val('')

            table.search('').columns().search('').draw();
        })

        $("#btnCancelarRegistro, #btnCerrarModal").on('click', function() {

            $("#iptCodigoReg").val("");
            $("#selCategoriaReg").val(0);
            $("#iptDescripcionReg").val("");
            $("#iptPrecioCompraReg").val("");
            $("#iptPrecioVentaReg").val("");
            $("#iptPrecioVentaMayorReg").val("");
            $("#iptPrecioVentaOfertaReg").val("");
            $("#iptStockReg").val("");
            $("#iptMinimoStockReg").val("");

        })


        /* ======================================================================================
        EVENTO AL DAR CLICK EN EL BOTON AUMENTAR STOCK
        =========================================================================================*/
        $('#tbl_productos tbody').on('click', '.btnAumentarStock', function() {

            operacion_stock = 1; //sumar stock
            accion = 3;

            $("#mdlGestionarStock").modal('show'); //MOSTRAR VENTANA MODAL

            $("#titulo_modal_stock").html('Aumentar Stock'); // CAMBIAR EL TITULO DE LA VENTANA MODAL
            $("#titulo_modal_label").html('Agregar al Stock'); // CAMBIAR EL TEXTO DEL LABEL DEL INPUT PARA INGRESO DE STOCK
            $("#iptStockSumar").attr("placeholder", "Ingrese cantidad a agregar al Stock"); //CAMBIAR EL PLACEHOLDER 

            let data = table.row($(this).parents('tr')).data(); //OBTENER EL ARRAY CON LOS DATOS DE CADA COLUMNA DEL DATATABLE

            $("#stock_codigoProducto").html(data[1]) //CODIGO DEL PRODUCTO DEL DATATABLE
            $("#stock_Producto").html(data[4]) //NOMBRE DEL PRODUCTO DEL DATATABLE
            $("#stock_Stock").html(data[9]) //STOCK ACTUAL DEL PRODUCTO DEL DATATABLE

            $("#stock_NuevoStock").html(parseFloat($("#stock_Stock").html()));

        })

        /* ======================================================================================
        EVENTO AL DAR CLICK EN EL BOTON DISMINUIR STOCK
        =========================================================================================*/
        $('#tbl_productos tbody').on('click', '.btnDisminuirStock', function() {

            operacion_stock = 2; //restar stock
            accion = 3;
            $("#mdlGestionarStock").modal('show'); //MOSTRAR VENTANA MODAL

            $("#titulo_modal_stock").html('Disminuir Stock'); // CAMBIAR EL TITULO DE LA VENTANA MODAL
            $("#titulo_modal_label").html('Disminuir al Stock'); // CAMBIAR EL TEXTO DEL LABEL DEL INPUT PARA INGRESO DE STOCK
            $("#iptStockSumar").attr("placeholder", "Ingrese cantidad a disminuir al Stock"); //CAMBIAR EL PLACEHOLDER 


            let data = table.row($(this).parents('tr')).data(); //OBTENER EL ARRAY CON LOS DATOS DE CADA COLUMNA DEL DATATABLE

            $("#stock_codigoProducto").html(data[1]) //CODIGO DEL PRODUCTO DEL DATATABLE
            $("#stock_Producto").html(data[4]) //NOMBRE DEL PRODUCTO DEL DATATABLE
            $("#stock_Stock").html(data[9]) //STOCK ACTUAL DEL PRODUCTO DEL DATATABLE

            $("#stock_NuevoStock").html(parseFloat($("#stock_Stock").html()));

        })

        /* ======================================================================================
        EVENTO QUE LIMPIA EL INPUT DE INGRESO DE STOCK AL CERRAR LA VENTANA MODAL
        =========================================================================================*/
        $("#btnCancelarRegistroStock, #btnCerrarModalStock").on('click', function() {
            $("#iptStockSumar").val("")
        })

        /* ======================================================================================
        EVENTO AL DIGITAR LA CANTIDAD A AUMENTAR O DISMINUIR DEL STOCK
        =========================================================================================*/
        $("#iptStockSumar").keyup(function() {

            // console.log($("#iptStockSumar").val());

            if (operacion_stock == 1) {

                if ($("#iptStockSumar").val() != "" && $("#iptStockSumar").val() > 0) {

                    let stockActual = parseFloat($("#stock_Stock").html());
                    let cantidadAgregar = parseFloat($("#iptStockSumar").val());

                    $("#stock_NuevoStock").html(stockActual + cantidadAgregar);

                } else {

                    // Toast.fire({
                    //     icon: 'warning',
                    //     title: 'Ingrese un valor mayor a 0'
                    // });

                    mensajeToast('error', 'Ingrese un valor mayor a 0');

                    $("#iptStockSumar").val("")
                    $("#stock_NuevoStock").html(parseFloat($("#stock_Stock").html()));

                }

            } else {

                if ($("#iptStockSumar").val() != "" && $("#iptStockSumar").val() > 0) {

                    let stockActual = parseFloat($("#stock_Stock").html());
                    let cantidadAgregar = parseFloat($("#iptStockSumar").val());

                    $("#stock_NuevoStock").html(stockActual - cantidadAgregar);

                    if (parseInt($("#stock_NuevoStock").html()) < 0) {

                        // Toast.fire({
                        //     icon: 'warning',
                        //     title: 'La cantidad a disminuir no puede ser mayor al stock actual (Nuevo stock < 0)'
                        // });

                        mensajeToast('error', 'La cantidad a disminuir no puede ser mayor al stock actual (Nuevo stock < 0)');

                        $("#iptStockSumar").val("");
                        $("#iptStockSumar").focus();
                        $("#stock_NuevoStock").html(parseFloat($("#stock_Stock").html()));
                    }
                } else {

                    // Toast.fire({
                    //     icon: 'warning',
                    //     title: 'Ingrese un valor mayor a 0'
                    // });

                    mensajeToast('error', 'Ingrese un valor mayor a 0');

                    $("#iptStockSumar").val("")
                    $("#stock_NuevoStock").html(parseFloat($("#stock_Stock").html()));
                }
            }

        })

        /* ======================================================================================
        EVENTO QUE REGISTRA EN BD EL AUMENTO O DISMINUCION DE STOCK
        =========================================================================================*/
        $("#btnGuardarNuevorStock").on('click', function() {

            if ($("#iptStockSumar").val() != "" && $("#iptStockSumar").val() > 0) {

                let nuevoStock = parseFloat($("#stock_NuevoStock").html()),
                    codigo_producto = $("#stock_codigoProducto").html();

                let datos = new FormData();

                datos.append('accion', accion);
                datos.append('nuevoStock', nuevoStock);
                datos.append('codigo_producto', codigo_producto);
                datos.append('tipo_movimiento', operacion_stock);

                $.ajax({
                    url: "ajax/productos.ajax.php",
                    method: "POST",
                    data: datos,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(respuesta) {

                        $("#stock_NuevoStock").html("");
                        $("#iptStockSumar").val("");

                        $("#mdlGestionarStock").modal('hide');

                        table.ajax.reload();

                        // Swal.fire({
                        //     position: 'center',
                        //     icon: 'success',
                        //     title: 'Se actualizó el stock correctamente.!',
                        //     showConfirmButton: false,
                        //     timer: 1500
                        // })

                        mensajeToast('success', 'Se actualizó el stock correctamente.!')

                    }
                });

            } else {
                // Toast.fire({
                //     icon: 'warning',
                //     title: 'Debe ingresar la cantidad a aumentar'
                // });

                mensajeToast('error', 'Debe ingresar la cantidad a aumentar');

                return false;
            }

        })

        /* ======================================================================================
        EVENTO AL DAR CLICK EN EL BOTON EDITAR PRODUCTO
        =========================================================================================*/
        $('#tbl_productos tbody').on('click', '.btnEditarProducto', function() {

            accion = 4; //seteamos la accion para editar

            $("#mdlGestionarProducto").modal('show');

            let data = ($(this).parents('tr').hasClass('child')) ?
                table.row($(this).parents().prev('tr')).data() :
                table.row($(this).parents('tr')).data();


            $("#iptCodigoReg").val(data["codigo_producto"]);
            $("#selCategoriaReg").val(data[2]);
            $("#iptDescripcionReg").val(data[4]);
            $("#iptPrecioCompraReg").val(data[5]);
            $("#iptPrecioVentaReg").val(data[6]);
            $("#iptPrecioVentaMayorReg").val(data[7]);
            $("#iptPrecioVentaOfertaReg").val(data[8]);
            $("#iptStockReg").val(data[9].replace(' Und(s)', '').replace(' Kg(s)', ''));
            $("#iptMinimoStockReg").val(data[10].replace(' Und(s)', '').replace(' Kg(s)', ''));

        })

        /* ======================================================================================
        EVENTO AL DAR CLICK EN EL BOTON ELIMINAR PRODUCTO
        =========================================================================================*/
        $('#tbl_productos tbody').on('click', '.btnEliminarProducto', function() {

            accion = 5; //seteamos la accion para editar

            let data = table.row($(this).parents('tr')).data();

            let codigo_producto = data["codigo_producto"];

            Swal.fire({
                title: 'Está seguro de eliminar el producto?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, deseo eliminarlo!',
                cancelButtonText: 'Cancelar',
            }).then((result) => {

                if (result.isConfirmed) {

                    let datos = new FormData();

                    datos.append("accion", accion);
                    datos.append("codigo_producto", codigo_producto); //codigo_producto               

                    $.ajax({
                        url: "ajax/productos.ajax.php",
                        method: "POST",
                        data: datos,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function(respuesta) {

                            if (respuesta == "ok") {

                                Toast.fire({
                                    icon: 'success',
                                    title: 'El producto se eliminó correctamente'
                                });

                                table.ajax.reload();

                            } else {
                                Toast.fire({
                                    icon: 'error',
                                    title: 'El producto no se pudo eliminar'
                                });
                            }

                        }
                    });

                }
            })
        })


    });

    // CALCULA LA UTILIDAD
    function calcularUtilidad() {

        let iptPrecioCompraReg = $("#iptPrecioCompraReg").val();
        let iptPrecioVentaReg = $("#iptPrecioVentaReg").val();
        let Utilidad = iptPrecioVentaReg - iptPrecioCompraReg;

        // $("#iptUtilidadReg").val(Utilidad.toFixed(2));

    }


    /*===================================================================*/
    //EVENTO QUE LIMPIA LOS MENSAJES DE ALERTA DE INGRESO DE DATOS DE CADA INPUT AL CANCELAR LA VENTANA MODAL
    /*===================================================================*/
    document.getElementById("btnCancelarRegistro").addEventListener("click", function() {
        $(".needs-validation").removeClass("was-validated");
    })


    /*===================================================================*/
    //FUNCION QUE PERMITE PREVISUALIZAR LA IMAGEN
    /*===================================================================*/
    function previewFile(input) {

        let file = $("input[type=file]").get(0).files[0];

        if (file) {
            let reader = new FileReader();

            reader.onload = function() {
                $("#previewImg").attr("src", reader.result);
            }

            reader.readAsDataURL(file);
        }
    }

    /*===================================================================*/
    //EVENTO QUE GUARDA LOS DATOS DEL PRODUCTO, PREVIA VALIDACION DEL INGRESO DE LOS DATOS OBLIGATORIOS
    /*===================================================================*/
    document.getElementById("btnGuardarProducto").addEventListener("click", function() {

        let imagen_valida = true;

        // Get the forms we want to add validation styles to
        let forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        let validation = Array.prototype.filter.call(forms, function(form) {

            if (form.checkValidity() === true) {

                
                let file = $("#iptImagen").val();
                
                let ext = file.substring(file.lastIndexOf("."));

                if (ext != ".jpg" && ext != ".png" && ext != ".gif" && ext != ".jpeg" && ext != ".webp") {
                    mensajeToast('error', "La extensión " + ext + " no es una imagen válida");
                    imagen_valida = false;
                }
                

                if (!imagen_valida) {
                    return;
                }

                const inputImage = document.querySelector('#iptImagen');

                let formData = new FormData();

                formData.append('detalle_producto', $("#frm-datos-producto").serialize());
                formData.append('accion', 2)
                formData.append('archivo[]', inputImage.files[0])

                Swal.fire({
                    title: 'Está seguro de registrar el producto?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, deseo registrarlo!',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {

                    if (result.isConfirmed) {

                        if (accion == 2) {
                            let titulo_msj = "El producto se registró correctamente"
                        }

                        if (accion == 4) {
                            let titulo_msj = "El producto se actualizó correctamente"
                        }

                        $.ajax({
                            url: "ajax/productos.ajax.php",
                            method: "POST",
                            data: formData,
                            cache: false,
                            contentType: false,
                            processData: false,
                            dataType: 'json',
                            success: function(respuesta) {

                                if (respuesta == "ok") {

                                    // Toast.fire({
                                    //     icon: 'success',
                                    //     title: titulo_msj
                                    // });

                                    mensajeToast('success', titulo_msj)
                                    table.ajax.reload();

                                    $("#mdlGestionarProducto").modal('hide');

                                    $("#iptCodigoReg").val("");
                                    $("#selCategoriaReg").val(0);
                                    $("#iptDescripcionReg").val("");
                                    $("#iptPrecioCompraReg").val("");
                                    $("#iptPrecioVentaReg").val("");
                                    $("#iptPrecioVentaMayorReg").val("");
                                    $("#iptPrecioVentaOfertaReg").val("");
                                    $("#iptStockReg").val("");
                                    $("#iptMinimoStockReg").val("");

                                    $("#iptImagen").val('');                                    
                                    $("#previewImg").attr("src", "vistas/assets/imagenes/no_image.jpg");

                                } else {
                                    Toast.fire({
                                        icon: 'error',
                                        title: 'El producto no se pudo registrar'
                                    });
                                }

                            }
                        });

                    }
                })
            } else {
                console.log("No paso la validacion")
            }

            form.classList.add('was-validated');

            return false;

        });
    });
</script>