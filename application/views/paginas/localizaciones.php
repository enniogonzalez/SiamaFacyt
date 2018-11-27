<div class="container">
    <div class="row">
        <div class="col-lg-9" style="padding: 0px;">
            <h2><span class="fa fa-map-marker"></span> Localizaci&oacute;n</h2>
        </div>
    </div>
</div>


<div class="container">
    <div class="formulario-siama">
        
        <div style="text-align:center;" id="SeccionImprimir">
            <button type="button"  class="btn btn-primary-siama" id="Imprimir">
                <span class="fa fa-print fa-lg"></span>
                Imprimir
            </button>
        </div>

        <form class ="formulario-desactivado" id="FormularioActual" method="POST" action = "<?=site_url('/localizaciones/guardar')?>">

            <div style="margin: 10px 15px;display:none;" id="alertaFormularioActual" class="alert alert-danger text-center">
            </div>
            <div style="display:none;" id = "IdForm">
                <?=$loc_id?>
            </div>

            <div class="form-group row">
                <label for="NombreLoc" class="col-md-3 col-form-label">Nombre:</label>
                <div class="col-md-9">
                    <input readonly disabled type="text" class="form-control obligatorio texto" id="NombreLoc" value="<?=$nombre?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="Ubicacion" class="col-md-3 col-form-label">Ubicaci&oacute;n:</label>
                <div class="col-md-9">
                    <textarea  readonly disabled class="form-control obligatorio texto" 
                    rows="3" style = "resize:vertical;" id="Ubicacion"><?=$ubicacion?></textarea>
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="Tipo" class="col-md-3 col-form-label">Tipo:</label>
                <div class="col-md-9">
                    <select readonly disabled class="form-control obligatorio lista" id="Tipo">
                        <?= $TipoLocalizacion?>
                    </select> 
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>


            <div class="form-group row">
                <label for="Amperaje" class="col-md-3 col-form-label">Capacidad Amperaje:</label>
                <div class="col-md-9">
                    <input readonly disabled required type="number" step=".01" 
                        class="form-control obligatorio decimal" id="Amperaje" value="<?=$cap_amp?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="LocPad" class="col-md-3 col-form-label">Localizaci&oacute;n Padre:</label>
                <div class="col-md-9">
                    <div style="width:86%;float:left;">
                        <div style="display:none;" id="idPad"><?=$idpad?></div>
                        <input readonly disabled type="text"
                            class="form-control texto buscador" id="LocPad" value="<?=$nombrepadre?>">
                    </div>
                    <div style="width:14%;float:right;padding:5px;">
                        <span title="Buscar Padre" class="fa fa-search BuscarPadre" style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Padre" class="fa fa-trash-o BorrarPadre" style="cursor: pointer;float:right;"></span>
                    </div>
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
        <div style="display:none;" id ="ControladorActual"><?=site_url('/localizaciones')?></div>
        <div style="background-color: #95a5a6; padding: 10px;">

            <div class="form-group row botoneraFormulario" >
                <?php if ($loc_id != ""){?>
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

                <?php if ($loc_id != ""){?>
                <button title="Eliminar" type="button" class="btn  btn-danger" id="EliminarRegistro">
                    <span class="fa fa-trash"></span>
                    Eliminar
                </button>
                <?php }?>

            </div>
        </div>
    </div>
</div>