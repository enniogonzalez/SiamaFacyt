
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="author" content="Ennio Gonzalez">
        <meta name="description" content="Sistema automatizado de mantenimiento">
        <title>SiAMa FACYT</title>

        <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/Siama.css">
        <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/font-awesome-4.7.0/css/font-awesome.css">
        <script src="<?=base_url()?>assets/js/Siama/jquery-3.3.1.min.js"></script>
        <script src="<?=base_url()?>assets/js/Siama/bootstrap.js"></script>
        <script src="<?=base_url()?>assets/js/Siama/Restablecer.js"></script>
    </head>
    <body class="body-siama">

        <header class="header-siama">
            <div class="container text-center">
                <img src="<?=base_url()?>assets/images/logoPagina.png" style="height: 50px;margin-top: -15px;">  
                <h1 style="display: inline-block;">Bienvenido a SiAMa FACYT</h1>
            </div>
        </header>
        <div class="container container-login-siama">

            <form id="resetform" class="form-signin login-siama" method="POST" action = "<?=site_url('/restablecer/guardar/')?>">
                <h2 class="font-weight-normal text-center">Restablecer Contraseña</h2>
                <hr>
                <div style="display:none;" id="alertaLogin" class="alert alert-danger text-center">
                    Usuario inv&aacute;lido
                </div>

                
                <div class="form-group row">
                    <label for="inputUsuario" class="col-lg-2 col-form-label">Usuario:</label>
                    <div class="col-lg-10">
                        <input type="text" name ="inputUsuario" id="inputUsuario" class="form-control" required placeholder="Ingresar Usuario"  autofocus>
                        <div id="invalidUser" class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                </div>

                <button class="btn btn-lg btn-primary-siama btn-block" id="loginbutton" type="submit"><span class="fa fa-arrow-circle-right  " style="margin-right:5px;"></span>Restablecer</button>
            </form>
        </div>
    </body>
</html>