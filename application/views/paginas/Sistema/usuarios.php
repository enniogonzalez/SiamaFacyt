<div class="container">
    <div class="row">
        <div class="col-md-9" style="padding: 0px;">
            <h2><span class="fa fa-users"></span> Usuarios</h2>  
        </div>
    </div>
</div>
<div class="container">
    <div class="formulario-sigma">
        
        <div style="text-align:center;" id="SeccionImprimir">
            <button type="button"  class="btn btn-primary-sigma" id="Imprimir">
                <span class="fa fa-print fa-lg"></span>
                Imprimir
            </button>
        </div>

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
                <label for="rol" class="col-md-3 col-form-label">Rol:</label>
                <div class="col-md-9">
                    <select readonly disabled class="form-control obligatorio lista" id="rol">
                        <option value =''></option>
                        <?php
                            $RolActual="";
                            foreach($rolesApp as $rol){
                                if($rol['rol_id'] == $rol_id ){
                                    $RolActual = $rol['nombre']; 
                                }

                                $opcion = "<option value ='" . $rol['rol_id'] . "' "
                                    . ($rol['rol_id'] == $rol_id ? "selected":"")
                                    . ">" .$rol['nombre'] . "</option>";
                                echo($opcion);
                            }
                        ?>
                    </select> 
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>
            
            <div class="form-group row" id="divLocalizaciones" 
                <?=$RolActual=="Director de Dependencia"?"":"style='display:none;'"?>
            >
                <label for="nomLoc" class="col-md-3 col-form-label">Localizaci&oacute;n:</label>
                <div class="col-md-9">
                    <div style="width:86%;float:left;">
                        <div style="display:none;" id="idLoc"><?=$loc_id?></div>
                        <input readonly disabled type="text"
                            class="form-control texto buscador" id="nomLoc" value="<?=$loc_nom?>">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:14%;float:right;padding:10px;">
                        <span title="Buscar Localizacion" class="fa fa-search BuscarLocalizacion" style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Localizacion" class="fa fa-trash-o BorrarLocalizacion" style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="correo" class="col-md-3 col-form-label">Correo:</label>
                <div class="col-md-9">
                    <input maxlength="100" readonly disabled type="email" 
                    class="form-control  obligatorio texto" id="correo" value="<?=$correo?>">
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

        </form>
        <div style="display:none;">
            <select readonly disabled  id="listaBusquedaFormulario">
                <?=$listaBusquedaFormulario?>
            </select> 
            <select readonly disabled  id="listaBusquedaLocalizacion">
                <?=$listaBusquedaLocalizacion?>
            </select> 
        </div>
        <div style="display:none;" id ="ControladorActual"><?=site_url('/usuarios')?></div>
        <div style="background-color: #95a5a6; padding: 10px;">

            <div class="form-group row botoneraFormulario" >

                <?php if($usu_id!= "" ) {?>
                <button  type="button"  class="btn btn-primary-sigma" id="BuscarRegistro">
                    <span class="fa fa-search"></span>
                    Buscar
                </button>

                <button type="button"  class="btn btn-primary-sigma" id="EditarRegistro">
                    <span class="fa fa-pencil-square-o"></span>
                    Editar
                </button>
                <?php }?>

                <button type="button"  class="btn btn-primary-sigma" id="AgregarRegistro">
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