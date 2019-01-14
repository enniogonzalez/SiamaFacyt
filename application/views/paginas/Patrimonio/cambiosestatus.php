<div class="container">
    <div class="row">
        <div class="col-lg-9" style="padding: 0px;">
            <h2><span class="fa fa-certificate"></span> Cambio de Estatus</h2>  
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
        
        <form class ="formulario-desactivado" id="FormularioActual" method="POST" action = "<?=site_url('/cambiosestatus/guardar')?>">

            <div style="margin: 10px 15px;display:none;" id="alertaFormularioActual" class="alert alert-danger text-center">
            </div>
            <div style="display:none;" id = "IdForm">
                <?=$cam_id?>
            </div>
         
            <div class="form-group row">
                <label for="DocumentoCambios" class="col-lg-3 col-form-label">Documento:</label>
                <div class="col-lg-9">
                    <input readonly disabled type="text" maxlength="10"
                        class="form-control texto documento" id="DocumentoCambios" value="<?=$documento?>">
                </div>
            </div>

            <div class="form-group row">
                <label for="EstatusCambios" class="col-lg-3 col-form-label">Estatus Documento:</label>
                <div class="col-lg-9">
                    <input readonly disabled type="text" maxlength="100"
                        class="form-control texto estatus" id="EstatusCambios" value="<?=$doc_estatus?>">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Bien:</label>
                <div class="col-lg-9">
                    <div style="width:86%;float:left;">
                        <div style="display:none;" id="idBieCambios"><?=$bie_id?></div>
                        <input readonly disabled type="text"
                            class="form-control obligatorio texto buscador" id="nomBieCambios" value="<?=$bie_nom?>">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:14%;float:right;padding:10px;">
                        <span title="Buscar Bien" class="fa fa-search BuscarBienCambios" style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Bien" class="fa fa-trash-o BorrarBienCambios" style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="estatusBien" class="col-lg-3 col-form-label">Estatus Bien:</label>
                <div class="col-lg-9">
                
                    <input readonly disabled type="text" maxlength="100"
                        class="form-control texto estatus" id="estatusBien" value="<?=$bie_estatus?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="ObservacionCambios" class="col-lg-3 col-form-label">Observaci&oacute;n:</label>
                <div class="col-lg-9">
                    <textarea  readonly disabled class="form-control texto obligatorio" rows="3"
                    style = "resize:vertical;" id="ObservacionCambios"><?=$observaciones?></textarea>
                        <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <h3>
                Pieza
            </h3>
            <div class="table-responsive">
                <table id="TablaPiezasEstatus" class="table table-hover tabla-sigma tabla-sigma-desactivada">
                    <thead class="head-table-sigma" style="font-size:11px;">
                        <tr>
                            <th style="width:55%;">Pieza</th>
                            <th style="width:25%;">Inventario UC</th>
                            <th style="width:10%;">Estatus</th>
                            <th style="width:5%;">
                                <span id ="agregarPieza" style="color:#28a745;cursor: pointer;" class="fa fa-plus-circle fa-lg"></span>
                            </th>
                            <th style="width:5%;">
                                <span id ="eliminarPieza" style="color:#dc3545;cursor: pointer;" class="fa fa-minus-circle fa-lg"></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody style="font-size:11px;">
                        <?=$PiezaCEs?>
                    </tbody>
                </table>
            </div>
        </form>
        <div style="display:none;">
            <select readonly disabled  id="listaBusquedaFormulario">
                <?=$listaBusquedaFormulario?>
            </select> 
            <select readonly disabled  id="listaBusquedaPieza">
                <?=$listaBusquedaPieza?>
            </select> 
            <select readonly disabled  id="listaBusquedaBien">
                <?=$listaBusquedaBien?>
            </select> 
            <select readonly disabled  id="listaBusquedaFalla">
                <?=$listaBusquedaFalla?>
            </select> 
        </div>
        <div style="display:none;" id ="ControladorActual"><?=site_url('/cambiosestatus')?></div>
        <div style="background-color: #95a5a6; padding: 10px;">

            <div class="form-group row botoneraFormulario" >

                <?php 
                    $btnAgregar = "
                        <button type=\"button\"  class=\"btn btn-primary-sigma\" id=\"AgregarRegistro\">
                            <span class=\"fa fa-plus\"></span>
                            Agregar
                        </button>
                    ";

                    $btnBuscar ="
                        <button  type=\"button\"  class=\"btn btn-primary-sigma\" id=\"BuscarRegistro\">
                            <span class=\"fa fa-search\"></span>
                            Buscar
                        </button>
                    ";

                    $btnEditar = "
                        <button type=\"button\"  class=\"btn btn-primary-sigma\" id=\"EditarRegistro\">
                            <span class=\"fa fa-pencil-square-o\"></span>
                            Editar
                        </button>
                    ";

                    $btnEliminar = "
                        <button title=\"Eliminar\" type=\"button\" class=\"btn  btn-danger\" id=\"EliminarRegistro\">
                            <span class=\"fa fa-trash\"></span>
                            Eliminar
                        </button>
                    ";

                    $btnAprobar = "
                        <button title=\"Aprobar\" type=\"button\"  class=\"btn btn-success\" id=\"AprobarRegistro\">
                            <span class=\"fa fa-check\"></span>
                            Aprobar
                        </button>
                    ";

                    $btnDesaprobar = "
                        <button title=\"Desaprobar\" type=\"button\" class=\"btn  btn-danger\" id=\"DesaprobarRegistro\">
                            <span class=\"fa fa-undo\"></span>
                            Desaprobar
                        </button>
                    ";

                    if($cam_id == "" ) {
                        echo $btnAgregar;
                    }elseif($doc_estatus == "Solicitado"){
                        echo $btnBuscar;
                        echo $btnEditar;
                        echo $btnAgregar;
                        echo $btnAprobar;
                        echo $btnEliminar;
                    }elseif($doc_estatus == "Aprobado"){
                        echo $btnBuscar;
                        echo $btnAgregar;
                    }
                ?>

            </div>
        </div>
    </div>
</div>