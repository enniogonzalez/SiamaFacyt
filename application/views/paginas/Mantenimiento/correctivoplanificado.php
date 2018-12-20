<div class="container">
    <div class="row">
        <div class="col-lg-12" style="padding: 0px;">
            <h2><span class="fa fa-calendar"></span> Mantenimiento Correctivo Planificado</h2>  
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
        
        <form class ="formulario-desactivado" id="FormularioActual" method="POST" action = "<?=site_url('/correctivoplanificado/guardar')?>">

            <div style="margin: 10px 15px;display:none;" id="alertaFormularioActual" class="alert alert-danger text-center">
            </div>
            <div style="display:none;" id = "IdForm">
                <?=$cpl_id?>
            </div>
         
            <div class="form-group row">
                <label for="DocumentoPlanificado" class="col-lg-3 col-form-label">Documento:</label>
                <div class="col-lg-9">
                    <input readonly disabled type="text" maxlength="10"
                        class="form-control texto" id="DocumentoPlanificado" value="<?=$documento?>">
                </div>
            </div>

            <div class="form-group row">
                <label for="EstatusPlanificado" class="col-lg-3 col-form-label">Estatus:</label>
                <div class="col-lg-9">
                    <input readonly disabled type="text" maxlength="100"
                        class="form-control texto estatus" id="EstatusPlanificado" value="<?=$estatus?>">
                </div>
            </div>

            <div class="form-group row">
                <label for="OrigenPlanificado" class="col-lg-3 col-form-label">Origen:</label>
                <div class="col-lg-9">
                    <select disabled readonly class="form-control obligatorio texto" id="OrigenPlanificado">
                        <option value=""></option>
                        <option value="Mantenimiento Correctivo" <?=$origen == "Mantenimiento Correctivo" ? "selected": ""?>>Mantenimiento Correctivo</option>
                        <option value="Mantenimiento Preventivo" <?=$origen == "Mantenimiento Preventivo" ? "selected": ""?>>Mantenimiento Preventivo</option>
                    </select>
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row" id="divCorrectivo" <?=$origen == "Mantenimiento Correctivo" ? "": "style='display:none;'"?>>
                <label for="ManCorrectivo" class="col-lg-3 col-form-label">Mantenimiento Correctivo:</label>
                <div class="col-lg-9">
                    <div style="width:86%;float:left;">
                        <div style="display:none;" id="idCorrectivo"><?=$mco_doc?></div>
                        <input readonly disabled type="text"
                            class="form-control texto buscador" id="ManCorrectivo" value="<?=$mco_doc?>">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:14%;float:right;padding:10px;">
                        <span title="Buscar Mantenimiento Correctivo" class="fa fa-search BuscarCorrectivo" style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Mantenimiento Correctivo" class="fa fa-trash-o BorrarCorrectivo" style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>


            <div class="form-group row" id="divPreventivo" <?=$origen == "Mantenimiento Preventivo" ? "": "style='display:none;'"?>>
                <label for="ManPreventivo" class="col-lg-3 col-form-label">Mantenimiento Preventivo:</label>
                <div class="col-lg-9">
                    <div style="width:86%;float:left;">
                        <div style="display:none;" id="idPreventivo"><?=$man_id?></div>
                        <input readonly disabled type="text"
                            class="form-control texto buscador" id="ManPreventivo" value="<?=$man_doc?>">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:14%;float:right;padding:10px;">
                        <span title="Buscar Mantenimiento Preventivo" class="fa fa-search BuscarPreventivo" style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Mantenimiento Preventivo" class="fa fa-trash-o BorrarPreventivo" style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row" id="divBien"  <?=$origen != "" ? "": "style='display:none;'"?>>
                <label class="col-lg-3 col-form-label">Bien:</label>
                <div class="col-lg-9">
                    <div style="display:none;" id="idBien"><?=$bie_id?></div>
                    <input readonly disabled type="text" maxlength="100"
                        class="form-control texto" id="nomBien" value="<?=$bie_nom?>">
                </div>
            </div>

            <div class="form-group row">
                <label for="FechaEjecucion" class="col-lg-3 col-form-label">Fecha Ejecuci&oacute;n:</label>
                <div class="col-lg-9">
                    <input maxlength="100" readonly disabled type="date" 
                    class="form-control obligatorio fecha" id="FechaEjecucion" value="<?=$fec_eje?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="ObservacionCP" class="col-lg-3 col-form-label">Observaci&oacute;n:</label>
                <div class="col-lg-9">
                    <textarea  readonly disabled class="form-control texto" rows="3"
                    style = "resize:vertical;" id="ObservacionCP"><?=$observaciones?></textarea>
                        <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <h3>
                Piezas Dañadas
            </h3>
            <div class="table-responsive">
                <table id="TablaPiezasDañadas" class="table table-hover tabla-siama tabla-siama-desactivada">
                    <thead class="head-table-siama" style="font-size:11px;">
                        <tr>
                            <th style="width:55%;">Pieza</th>
                            <th style="width:35%;">Falla</th>
                            <th style="width:5%;">
                                <span id ="agregarPieza" style="color:#28a745;cursor: pointer;" class="fa fa-plus-circle fa-lg"></span>
                            </th>
                            <th style="width:5%;">
                                <span id ="eliminarPieza" style="color:#dc3545;cursor: pointer;" class="fa fa-minus-circle fa-lg"></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody style="font-size:11px;">
                        <?=$Piezas?>
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
            <select readonly disabled  id="listaBusquedaCorrectivo">
                <?=$listaBusquedaCorrectivo?>
            </select>
            <select readonly disabled  id="listaBusquedaPreventivo">
                <?=$listaBusquedaPreventivo?>
            </select> 
            <select readonly disabled  id="listaBusquedaFalla">
                <?=$listaBusquedaFalla?>
            </select> 
        </div>
        <div style="display:none;" id ="ControladorActual"><?=site_url('/correctivoplanificado')?></div>
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

                    if($cpl_id == "" ) {
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
                    }
                ?>

            </div>
        </div>
    </div>
</div>