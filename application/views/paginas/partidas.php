<div class="container">
    <h2><span class="fa fa-bookmark"></span> Partidas</h2>   
</div>

<div class="container">
    <div class="formulario-siama">
        <form class ="formulario-desactivado" id="FormularioActual" method="POST" action = "<?=site_url('/partidas/guardar')?>">

            <div style="margin: 10px 15px;display:none;" id="alertaFormularioActual" class="alert alert-danger text-center">
            </div>
            <div style="display:none;" id = "IdForm">
                <?=$par_id?> 
            </div>
         
            <div class="form-group row">
                <label for="codigoPartida" class="col-md-3 col-form-label">C&oacute;digo:</label>
                <div class="col-md-9">
                    <input maxlength="10" readonly disabled type="text" 
                    class="form-control obligatorio texto" id="codigoPartida" value="<?=$codigo?> ">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="NombrePartida" class="col-md-3 col-form-label">Nombre:</label>
                <div class="col-md-9">
                    <input maxlength="100" readonly disabled type="text" class="form-control obligatorio texto" id="NombrePartida" value="<?=$nombre?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="LocPad" class="col-md-3 col-form-label">Partida Padre:</label>
                <div class="col-md-9">
                    <div style="width:86%;float:left;">
                        <div style="display:none;" id="idPad"><?=$idpad?></div>
                        <input readonly disabled type="text"
                            class="form-control texto" id="ParPad" value="<?=$nombrepadre?> ">
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
        <div style="display:none;" id ="ControladorActual"><?=site_url('/partidas')?></div>
        <div style="background-color: #95a5a6; padding: 10px;">

            <div class="form-group row botoneraFormulario" >

                <?php if($par_id != "" ) {?>
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

                <?php if($par_id != "" ) {?>
                <button title="Eliminar" type="button" class="btn  btn-danger" id="EliminarRegistro">
                    <span class="fa fa-trash"></span>
                    Eliminar
                </button>
                <?php }?>

            </div>
        </div>
    </div>
</div>