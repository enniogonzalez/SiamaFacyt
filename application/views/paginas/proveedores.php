<div class="container">
    <div class="row">
        <div class="col-lg-9" style="padding: 0px;">
            <h2><span class="fa fa-user"></span> Proveedores</h2>   
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

        <form class ="formulario-desactivado" id="FormularioActual" method="POST" action = "<?=site_url('/proveedores/guardar')?>">

            <div style="margin: 10px 15px;display:none;" id="alertaFormularioActual" class="alert alert-danger text-center">
            </div>
            <div style="display:none;" id = "IdForm">
                <?=$pro_id?> 
            </div>

            <div class="form-group row">
                <label for="rif" class="col-md-3 col-form-label">RIF:</label>
                <div class="col-md-9">
                    <input maxlength="20" readonly disabled type="text" 
                    class="form-control obligatorio texto" id="rif" value="<?=$rif?> ">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="nombrePro" class="col-md-3 col-form-label">Raz&oacute;n Social:</label>
                <div class="col-md-9">
                    <input maxlength="100" readonly disabled type="text" 
                    class="form-control obligatorio texto" id="nombrePro" value="<?=$raz_soc?> ">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="registro" class="col-md-3 col-form-label">RNC:</label>
                <div class="col-md-9">
                    <input maxlength="100" readonly disabled type="text" 
                    class="form-control obligatorio texto" id="registro" value="<?=$reg_nac_con?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="tlf" class="col-md-3 col-form-label">Tel&eacute;fonos:</label>
                <div class="col-md-9">
                    <input maxlength="100" readonly disabled type="text" 
                    class="form-control  texto" id="tlf" value="<?=$telefonos?>">
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
                <label for="direccion" class="col-md-3 col-form-label">Direcci&oacute;n:</label>
                <div class="col-md-9">
                    <textarea  readonly disabled class="form-control obligatorio texto" rows="3"
                    style = "resize:vertical;" id="direccion"><?=$direccion?></textarea>
                    <div class="invalid-feedback">Campo Obligatorio</div>
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
        <div style="display:none;" id ="ControladorActual"><?=site_url('/proveedores')?></div>
        <div style="background-color: #95a5a6; padding: 10px;">

            <div class="form-group row botoneraFormulario" >

                <?php if($pro_id != "" ) {?>
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

                <?php if($pro_id != "" ) {?>
                <button title="Eliminar" type="button" class="btn  btn-danger" id="EliminarRegistro">
                    <span class="fa fa-trash"></span>
                    Eliminar
                </button>
                <?php }?>
            </div>
        </div>
    </div>
</div>