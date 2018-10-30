<div class="container">
    <div class="row">
        <div class="col-lg-9" style="padding: 0px;">
            <h2><span class="fa fa-users"></span> Usuarios</h2>  
        </div>
        <div class="col-lg-3" style="text-align:center;" id="SeccionImprimir">
            <button type="button"  class="btn btn-primary-siama" id="Imprimir">
                <span class="fa fa-print fa-lg"></span>
                Imprimir
            </button>
        </div>
    </div>
</div>
<div class="container">
    <div class="formulario-siama">
        <form class ="formulario-desactivado" id="FormularioActual" method="POST" action = "<?=site_url('/usuarios/guardar')?>">

            <div style="margin: 10px 15px;display:none;" id="alertaFormularioActual" class="alert alert-danger text-center">
            </div>
            <div style="display:none;" id = "IdForm">
                <?=$usu_id?>
            </div>


            <div class="form-group row">
                <label for="Usuario" class="col-md-3 col-form-label">Usuario:</label>
                <div class="col-md-9">
                    <input maxlength="25" readonly disabled type="text" 
                    class="form-control obligatorio texto" id="Usuario" value="<?=$username?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="nombreUsu" class="col-md-3 col-form-label">Nombre:</label>
                <div class="col-md-9">
                    <input maxlength="100" readonly disabled type="text" 
                    class="form-control obligatorio texto" id="nombreUsu" value="<?=$nombre?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="Cargo" class="col-md-3 col-form-label">Cargo:</label>
                <div class="col-md-9">
                    <select readonly disabled class="form-control obligatorio lista" id="Cargo">
                        <?=$cargo?>
                    </select> 
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="correo" class="col-md-3 col-form-label">Correo:</label>
                <div class="col-md-9">
                    <input maxlength="100" readonly disabled type="email" 
                    class="form-control  texto" id="correo" value="<?=$correo?>">
                    <div class="invalid-feedback">Correo Inv&aacute;lido</div>
                </div>
            </div>
            
            <div class="form-group row">
                <label for="Observacion" class="col-md-3 col-form-label">Observaci&oacute;n:</label>
                <div class="col-md-9">
                    <textarea  readonly disabled class="form-control texto" rows="3"
                    style = "resize:vertical;" id="Observacion"><?=$observaciones?></textarea>
                </div>
            </div>

            <h3>
                Permisos
            </h3>
            <div class="table-responsive">
                <table id="TablaPermisos" class="table table-hover tabla-siama tabla-siama-desactivada">
                    <thead class="head-table-siama" style="font-size:13px;">
                        <tr>
                            <th style="width:60%;">Modulo</th>
                            <th style="width:40%;">Acceso</th>
                        </tr>
                    </thead>
                    <tbody >
                        <tr>
                            <td style="font-size:13px;">Localizacion</td>
                            <td class = "seleccionarPermiso" id="perLoc" style="text-align: center;">
                                <span class="fa <?=($Permisos['Localizacion'] ? "fa-check-square-o":"fa-square-o")?>  fa-lg"></span>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size:13px;">Mantenimiento</td>
                            <td class = "seleccionarPermiso" id="perMan" style="text-align: center;">
                                <span class="fa <?=($Permisos['Mantenimiento'] ? "fa-check-square-o":"fa-square-o")?> fa-lg"></span>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size:13px;">Marcas</td>
                            <td class = "seleccionarPermiso" id="perMar" style="text-align: center;">
                                <span class="fa <?=($Permisos['Marcas'] ? "fa-check-square-o":"fa-square-o")?> fa-lg"></span>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size:13px;">Partidas</td>
                            <td class = "seleccionarPermiso" id="perPar" style="text-align: center;">
                                <span class="fa <?=($Permisos['Partidas'] ? "fa-check-square-o":"fa-square-o")?> fa-lg"></span>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size:13px;">Patrimonio</td>
                            <td class = "seleccionarPermiso" id="perPat" style="text-align: center;">
                                <span class="fa <?=($Permisos['Patrimonio'] ? "fa-check-square-o":"fa-square-o")?> fa-lg"></span>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size:13px;">Proveedores</td>
                            <td class = "seleccionarPermiso" id="perPro" style="text-align: center;">
                                <span class="fa <?=($Permisos['Proveedores'] ? "fa-check-square-o":"fa-square-o")?> fa-lg"></span>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size:13px;">Sistema</td>
                            <td class = "seleccionarPermiso" id="perSis" style="text-align: center;">
                                <span class="fa <?=($Permisos['Sistema'] ? "fa-check-square-o":"fa-square-o")?> fa-lg"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
        <div style="display:none;" id ="ControladorActual"><?=site_url('/usuarios')?></div>
        <div style="background-color: #95a5a6; padding: 10px;">

            <div class="form-group row botoneraFormulario" >

                <?php if($usu_id!= "" ) {?>
                <button  type="button"  class="btn btn-primary-siama" id="BuscarRegistro">
                    <span class="fa fa-search"></span>
                    Buscar
                </button>

                <button type="button"  class="btn btn-primary-siama" id="EditarRegistro">
                    <span class="fa fa-pencil-square-o"></span>
                    Editar
                </button>
                <?php }?>

                <button type="button"  class="btn btn-primary-siama" id="AgregarRegistro">
                    <span class="fa fa-plus"></span>
                    Agregar
                </button>

                <?php if($usu_id != "" ) {?>
                <button title="Eliminar" type="button" class="btn  btn-danger" id="EliminarRegistro">
                    <span class="fa fa-trash"></span>
                    Eliminar
                </button>
                <?php }?>
            </div>
        </div>
    </div>
</div>