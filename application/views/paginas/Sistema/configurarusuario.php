<div class="container">
    <div class="row">
        <div class="col-lg-9" style="padding: 0px;">
            <h2><span class="fa fa-cog"></span> Configurar Usuario</h2>  
        </div>
    </div>
</div>
<div class="container">
    <div class="formulario-sigma">
        <form  id="FormularioActual" method="POST" action = "<?=site_url('/configurar/guardar')?>">

            <div style="margin: 10px 15px;display:none;" id="alertaFormularioActual" class="alert alert-danger text-center">
            </div>

            <div style="display:none;" id = "IdForm">
                <?=$usu_id?>
            </div>


            <div class="form-group row">
                <label for="nombreUsu" class="col-md-3 col-form-label">Nombre:</label>
                <div class="col-md-9">
                    <input maxlength="100"   type="text" 
                    class="form-control obligatorio texto" id="nombreUsu" value="<?=$this->session->userdata("nombre")?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>


            <div class="form-group row">
                <label for="correo" class="col-md-3 col-form-label">Correo:</label>
                <div class="col-md-9">
                    <input maxlength="100"  type="email" 
                    class="form-control  texto" id="correo" value="<?=$this->session->userdata("correo")?>">
                    <div class="invalid-feedback">Correo Inv&aacute;lido</div>
                </div>
            </div>

        </form>
        <div style="display:none;" id ="ControladorActual"><?=site_url('/usuarios')?></div>
        <div style="background-color: #95a5a6; padding: 10px;">

            <div class="form-group row botoneraFormulario" >

                <button title="Guardar" type="button" class="btn  btn-success" id="GuardarRegistro">
                    <span class="fa fa-floppy-o"></span>
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>