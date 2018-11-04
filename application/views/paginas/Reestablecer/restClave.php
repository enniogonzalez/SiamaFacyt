<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="author" content="Ennio Gonzalez">
        <meta name="description" content="Sistema automatizado de mantenimiento">
        <title>SiamaFACYT</title>

        <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/Siama.css">
        <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/font-awesome-4.7.0/css/font-awesome.css">
        <script src="<?=base_url()?>assets/js/Siama/jquery-3.3.1.min.js"></script>
        <script src="<?=base_url()?>assets/js/Siama/bootstrap.js"></script>
        <script src="<?=base_url()?>assets/js/Siama/Reestablecer.js"></script>
        <script src="<?=base_url()?>assets/js/Siama/md5.js"></script>
    </head>
    <body class="body-siama">

        <header class="header-siama">
            <div class="container text-center">
                <h1>Bienvenido a SiamaFACYT</h1>
            </div>
        </header>
        <div class="container container-login-siama">

            <form id="resetpassform" class="form-signin login-siama" method="POST" action = "<?=site_url('/reestablecer/reset/')?>">
                
                <input type="hidden" name="token" id="token" value="<?=$token?>">
                <h2 class="font-weight-normal text-center">Reestablecer Contraseña</h2>
                <label style="margin-bottom:0px;font-size: small;text-align:center;width: 100%;">Usuario: <?=$username?></label>
                <hr style="margin-top:0px;">
                <div style="display:none;" id="alertaReset" class="alert alert-danger text-center">
                    Las contraseñas no coinciden
                </div>

                <div class="form-group row">
                    <label for="inputPassword" class="col-lg-4 col-form-label">Contraseña:</label>
                    <div class="col-lg-8">
                        <input type="password" maxlength="10" name="inputPassword" id="inputPassword" class="form-control" required placeholder="Ingresar Contraseña" >
                        <div id="invalidPassword" class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="inputPassword2" class="col-lg-4 col-form-label">Confirmar Contraseña:</label>
                    <div class="col-lg-8">
                        <input type="password" maxlength="10" name="inputPassword2" id="inputPassword2" class="form-control" required placeholder="Ingresar Contraseña" >
                        <div id="invalidPassword2" class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                </div>

                <button class="btn btn-lg btn-primary-siama btn-block" id="loginbutton" type="submit"><span class="fa fa-arrow-circle-right  " style="margin-right:5px;"></span>Reestablecer</button>
            </form>
        </div>
    </body>
</html>