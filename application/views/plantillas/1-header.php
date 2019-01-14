<?php header('Access-Control-Allow-Origin: *');?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="author" content="Ennio Gonzalez">
        <meta name="description" content="Sistema automatizado de mantenimiento">
        
		<link rel="icon" type="image/x-icon" href="<?=base_url()?>assets/images/logoPagina.png" />
        <title>SiGMa FACYT</title>
        
        <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/menu.css">
        <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/Sigma.css">
        <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/font-awesome-4.7.0/css/font-awesome.css">
        <script src="<?=base_url()?>assets/js/Sigma/jquery-3.3.1.min.js"></script>
        <script src="<?=base_url()?>assets/js/Sigma/bootstrap.js"></script>
        <script src="<?=base_url()?>assets/js/html2canvas.min.js"></script>
        <script src="<?=base_url()?>assets/js/jsPDF.min.js"></script>
        <script src="<?=base_url()?>assets/js/Sigma/menu.js"></script>
        <script src="<?=base_url()?>assets/js/Sigma/Modales.js"></script>
        <script src="<?=base_url()?>assets/js/Sigma/Global.js"></script>
        <script src="<?=base_url()?>assets/js/Sigma/sha1.js"></script>
        <script src="<?=base_url()?>assets/js/Sigma/md5.js"></script>
        <script src="<?=base_url()?>assets/js/Sigma/thBuscadores.js"></script>
        <?=$JsFile?>
    </head>
    <body class="body-sigma">
    <!-- <div style='width:100px;background-color: black;'>hola</div> -->
        


                
                    

