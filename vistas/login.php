<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MAGA Y TITO | Login</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="vistas/assets/plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="vistas/assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="vistas/assets/dist/css/adminlte.min.css">
</head>

<body class="hold-transition login-page" style="background-image: url('vistas/assets/imagenes/fondo_login_erp_2.jpg'); object-fit: cover; object-position: center;">

    <div class="login-box">

        <div class="card card-outline card-primary">

            <div class="card-header text-center">

                <h1 class="h1"><b>MAGA Y TITO</b></h1>

            </div><!-- /.card-header -->

            <div class="card-body">

                <form method="post" class="needs-validation-login" novalidate>

                    <!-- USUARIO DEL SISTEMA -->
                    <div class="input-group mb-3">
                        
                        <input type="text" class="form-control" placeholder="Usuario del sistema" name="loginUsuario" required>

                        <div class="input-group-append">
                            
                            <div class="input-group-text">

                                <span class="fas fa-user"></span>

                            </div>

                        </div>

                        <div class="invalid-feedback">Debe ingresar su usuario!</div>

                    </div><!-- /.input-group USUARIO -->

                    <!-- PASSWORD DEL USUARIO DEL SISTEMA -->
                    <div class="input-group mb-3">
                        
                        <input type="password" class="form-control" placeholder="ingrese su password" name="loginPassword" required>

                        <div class="input-group-append">
                            
                            <div class="input-group-text">

                                <span class="fas fa-lock"></span>

                            </div>

                        </div>

                        <div class="invalid-feedback">Debe ingresar su contraseña!</div>

                    </div><!-- /.input-group PASSWORD -->

                    <div class="row">

                        <?php

                            $login = new UsuarioControlador();
                            $login->login();

                        ?>

                        <div class="col-md-12 text-center">

                            <button type="submit" class="btn btn-info">Iniciar Sesión</button>

                        </div>

                    </div>

                </form>

            </div><!-- /.card-body -->

        </div>

    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="vistas/assets/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="vistas/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="vistas/assets/dist/js/adminlte.min.js"></script>
</body>

</html>