<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="author" content="Ennio Gonzalez">
        <meta name="description" content="Sistema automatizado de mantenimiento">
        <title>SiamaUC</title>

        <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/Siama.css">
        <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/font-awesome-4.7.0/css/font-awesome.css">
        <script src="<?=base_url()?>assets/js/Siama/jquery-3.3.1.min.js"></script>
        <script src="<?=base_url()?>assets/js/Siama/bootstrap.js"></script>
        <script src="<?=base_url()?>assets/js/Siama/login.js"></script>
    </head>
    <body class="body-siama">

        <header class="header-siama">
            <div class="container text-center">
                <h1>SiamaUC</h1>
            </div>
        </header>
        <div class="container container-login-siama">

            <form id="loginform" class="form-signin login-siama" method="POST" action = "<?=site_url('/login/validar')?>">
                <h2 class="font-weight-normal text-center">Inicio de Sesion</h2>

                <div style="display:none;" id="alertaLogin" class="alert alert-danger text-center">
                    Usuario o Contraseña inv&aacute;lida
                </div>
                <div class="form-group">
                    <input type="text" name ="inputUsuario" id="inputUsuario" class="form-control" required placeholder="Ingresar Usuario"  autofocus>
                    <div id="invalidUser" class="invalid-feedback">Campo Obligatorio</div>
                </div>
                <div class="form-group">
                    <input type="password" name="inputPassword" id="inputPassword" class="form-control" required placeholder="Ingresar Contraseña" >
                    <div id="invalidPassword" class="invalid-feedback">Campo Obligatorio</div>
                </div>
                <button class="btn btn-lg btn-primary-siama btn-block" id="loginbutton" type="submit"><span class="fa fa-sign-in " style="margin-right:5px;"></span>Ingresar</button>
            </form>
        </div>
    </body>
</html>