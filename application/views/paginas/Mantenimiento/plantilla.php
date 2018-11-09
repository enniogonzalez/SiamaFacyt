<div class="container">
    <div class="row">
        <div class="col-lg-9" style="padding: 0px;">
            <h2><span class="fa fa-sticky-note"></span> Plantillas de Mantenimiento</h2>   
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
        <form class ="formulario-desactivado" id="FormularioActual" method="POST" action = "<?=site_url('/plantilla/guardar')?>">

            <div style="margin: 10px 15px;display:none;" id="alertaFormularioActual" class="alert alert-danger text-center">
            </div>
            <div style="display:none;" id = "IdForm">
                <?=$plm_id?>
            </div>
         
            <div class="form-group row">
                <label for="DocumentoPlantilla" class="col-lg-3 col-form-label">Documento:</label>
                <div class="col-lg-9">
                    <input readonly disabled type="text" maxlength="10"
                        class="form-control texto" id="DocumentoPlantilla" value="<?=$documento?>">
                </div>
            </div>

            <div class="form-group row">
                <label for="EstatusPlantilla" class="col-lg-3 col-form-label">Estatus:</label>
                <div class="col-lg-9">
                    <input readonly disabled type="text" maxlength="100" title = "Estatus de "
                        class="form-control texto estatus" id="EstatusPlantilla" value="<?=$estatus?>">
                </div>
            </div>

            <div class="form-group row">
                <label  class="col-lg-3 col-form-label">Bien:</label>
                <div class="col-lg-9">
                    <div style="width:86%;float:left;">
                        <div style="display:none;" id="idBiePlantilla"><?=$bie_id?></div>
                        <input readonly disabled type="text" title = "Bien al que pertenece la plantilla de mantenimiento"
                            class="form-control obligatorio texto buscador" id="nomBiePlantilla" value="<?=$bie_nom?>">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:14%;float:right;padding:10px;">
                        <span title="Buscar Bien" class="fa fa-search BuscarBienPlantilla" style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Bien" class="fa fa-trash-o BorrarBienPlantilla" style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="FrecuenciaMan" class="col-lg-3 col-form-label">Frecuencia (Meses):</label>
                <div class="col-lg-9">
                    <input readonly disabled type="number" title = "Frecuencia del mantenimiento (meses)" 
                        class="form-control texto obligatorio" step="1" min="1" id="FrecuenciaMan" value="<?=$frecuencia?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="UltimoMantenimiento" class="col-lg-3 col-form-label">Ultimo Mantenimiento:</label>
                <div class="col-lg-9">
                    <input maxlength="100" readonly disabled type="date" 
                    class="form-control obligatorio fecha" id="UltimoMantenimiento" value="<?=$fec_ult?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="ObservacionPlantilla" class="col-lg-3 col-form-label">Observaci&oacute;n:</label>
                <div class="col-lg-9">
                    <textarea  readonly disabled class="form-control texto" rows="3"
                    style = "resize:vertical;" id="ObservacionPlantilla"><?=$observaciones?></textarea>
                </div>
            </div>

            <h3>
                Tareas
            </h3>
            <div class="table-responsive">
                <table id="TablaTareasPlantilla" class="table table-hover tabla-siama tabla-siama-desactivada">
                    <thead class="head-table-siama" style="font-size:11px;">
                        <tr>
                            <th style="width:25%;">Pieza</th>
                            <th style="width:40%;">Titulo</th>
                            <th style="width:25%;">Minutos Estimados</th>
                            <th style="width:5%;">
                                <span id ="agregarTarea" style="color:#28a745;cursor: pointer;" class="fa fa-plus-circle fa-lg"></span>
                            </th>
                            <th style="width:5%;">
                                <span id ="eliminarTarea" style="color:#dc3545;cursor: pointer;" class="fa fa-minus-circle fa-lg"></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody style="font-size:11px;">
                        <?=$Tareas?>
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
        </div>
        <div style="display:none;" id ="ControladorActual"><?=site_url('/plantilla')?></div>
        <div style="background-color: #95a5a6; padding: 10px;">

            <div class="form-group row botoneraFormulario" >

                <?php 
                    $btnAgregar = "
                        <button type=\"button\"  class=\"btn btn-primary-siama\" id=\"AgregarRegistro\">
                            <span class=\"fa fa-plus\"></span>
                            Agregar
                        </button>
                    ";

                    $btnBuscar ="
                        <button  type=\"button\"  class=\"btn btn-primary-siama\" id=\"BuscarRegistro\">
                            <span class=\"fa fa-search\"></span>
                            Buscar
                        </button>
                    ";

                    $btnEditar = "
                        <button type=\"button\"  class=\"btn btn-primary-siama\" id=\"EditarRegistro\">
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

                    if($plm_id == "" ) {
                        echo $btnAgregar;
                    }elseif($estatus == "Solicitado"){
                        echo $btnBuscar;
                        echo $btnEditar;
                        echo $btnAgregar;
                        echo $btnAprobar;
                        echo $btnEliminar;
                    }elseif($estatus == "Aprobado"){
                        echo $btnBuscar;
                        echo $btnAgregar;
                        echo $btnDesaprobar;
                    }
                ?>

            </div>
        </div>
    </div>
</div>