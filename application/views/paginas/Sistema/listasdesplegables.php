<div class="container">
    <h2><span class="fa fa-list"></span> Listas Desplegables</h2>   
</div>
<div class="container">
    <div class="formulario-siama">
        <form id="FormularioActual" method="POST" action = "<?=site_url('/listasdesplegables/guardar')?>">
            <div style="margin: 10px 15px;display:none;" id="alertaFormularioActual" class="alert alert-danger text-center">
            </div>
            <div style="display:none;" id = "IdForm">
                <?=$ld_id?>
            </div>
            <div class="form-group row">
                <label for="CodigoLD" class="col-md-3 col-form-label">C&oacute;digo:</label>
                <div class="col-md-9">
                    <input type="text" require readonly disabled class="form-control" id="CodigoLD"
                    maxlength="10" value="<?=$codigo?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="NombreLD" class="col-md-3 col-form-label">Nombre:</label>
                <div class="col-md-9">
                    <input type="text" require readonly disabled class="form-control" id="NombreLD" 
                    maxlength="100" value="<?=$nombre?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="DescripcionLD" class="col-md-3 col-form-label">Descripci&oacute;n:</label>
                <div class="col-md-9">
                    <textarea  readonly disabled class="form-control" rows="3" style = "resize:vertical;" id="DescripcionLD"><?=$descripcion?></textarea>
                </div>
            </div>

            <div class="table-responsive">
                <table id="TablaListasDesplegables" class="table table-hover tabla-siama tabla-siama-desactivada">
                    <thead class="head-table-siama">
                        <tr>
                            <th style="width:10%;">Valor</th>
                            <th style="width:40%;">Opci&oacute;n</th>
                            <th style="width:40%;">
                                Descripcion
                            </th>
                            <th style="width:5%;">
                                <span id ="agregarOpcionLD" style="color:#28a745;cursor: pointer;" class="fa fa-plus-circle fa-lg"></span>
                            </th>
                            <th style="width:5%;">
                                <span id ="eliminarOpcionLD" style="color:#dc3545;cursor: pointer;" class="fa fa-minus-circle fa-lg"></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody >
                        <?=$opciones?>
                    </tbody>
                </table>
            </div>

        </form>

        <div style="display:none;" id ="ControladorActual"><?=site_url('/listasdesplegables')?></div>
        <div style="background-color: #95a5a6; padding: 10px;">

            <div class="form-group row botoneraFormulario" style="margin:0;">

                <?php if($ld_id != ""){?>
                <button  type="button" style="margin:5px;" class="btn btn-primary-siama" id="BuscarRegistro">
                    <span class="fa fa-search"></span>
                    Buscar
                </button>


                <button type="button" style="margin:5px;" class="btn btn-primary-siama" id="EditarRegistro">
                    <span class="fa fa-pencil-square-o"></span>
                    Editar
                </button>
                <?php }?>
                <button type="button" style="margin:5px;" class="btn btn-primary-siama" id="AgregarRegistro">
                    <span class="fa fa-plus"></span>
                    Agregar
                </button>

                <?php if($ld_id !=""){?>
                <button title="Eliminar Lista Desplegable" type="button" style="margin:5px;" class="btn  btn-danger" id="EliminarRegistro">
                    <span class="fa fa-trash"></span>
                    Eliminar
                </button>
                <?php }?>
            </div>
        </div>
    </div>
</div>