<div class="container">
    <div class="row">
        <div class="col-lg-9" style="padding: 0px;">
            <h2><span class="fa fa-bullseye"></span> Mantenimiento Preventivo</h2>    
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
        <form class ="formulario-desactivado" id="FormularioActual" method="POST" action = "<?=site_url('/preventivo/guardar')?>">

            <div style="margin: 10px 15px;display:none;" id="alertaFormularioActual" class="alert alert-danger text-center">
            </div>
            <div style="display:none;" id = "IdForm">
                <?=$man_id?>
            </div>
         
            <div class="form-group row">
                <label for="DocumentoPreventivo" class="col-lg-3 col-form-label">Documento:</label>
                <div class="col-lg-9">
                    <input readonly disabled type="text" maxlength="10"
                        class="form-control texto" id="DocumentoPreventivo" value="<?=$documento?>">
                </div>
            </div>

            <div class="form-group row">
                <label for="EstatusPreventivo" class="col-lg-3 col-form-label">Estatus:</label>
                <div class="col-lg-9">
                    <input readonly disabled type="text" maxlength="100" title = "Estatus de "
                        class="form-control texto estatus" id="EstatusPreventivo" value="<?=$estatus?>">
                </div>
            </div>

            <div class="form-group row">
                <label  class="col-lg-3 col-form-label">Bien:</label>
                <div class="col-lg-9">
                    <div style="display:none;" id="idBiePreventivo"><?=$bie_id?></div>
                    <input readonly disabled type="text" title = "Bien al que pertenece el mantenimiento preventivo"
                        class="form-control obligatorio texto " id="nomBiePreventivo" value="<?=$bie_nom?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="InicioPreventivo" class="col-lg-3 col-form-label">Inicio:</label>
                <div class="col-lg-9">
                    <input maxlength="100" readonly disabled type="date" 
                    class="form-control obligatorio fecha" id="InicioPreventivo" value="<?=$fec_ini?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="FinPreventivo" class="col-lg-3 col-form-label">Fin:</label>
                <div class="col-lg-9">
                    <input maxlength="100" readonly disabled type="date" 
                    class="form-control obligatorio fecha" id="FinPreventivo" value="<?=$fec_fin?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="ObservacionPreventivo" class="col-lg-3 col-form-label">Observaci&oacute;n:</label>
                <div class="col-lg-9">
                    <textarea  readonly disabled class="form-control texto" rows="3"
                    style = "resize:vertical;" id="ObservacionPreventivo"><?=$observaciones?></textarea>
                </div>
            </div>

            <h3>
                Tareas
            </h3>
            <div class="table-responsive">
                <table id="TablaTareas" class="table table-hover tabla-siama tabla-siama-desactivada">
                    <thead class="head-table-siama" style="font-size:11px;">
                        <tr>
                            <th style="width:20%;">Pieza</th>
                            <th style="width:30%;">Titulo</th>
                            <th style="width:20%;">Usuario</th>
                            <th style="width:20%;">Proveedor</th>
                            <th style="width:10%;text-align: center;" colspan="2">
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
            <select readonly disabled  id="listaBusquedaProveedor">
                <?=$listaBusquedaProveedor?>
            </select> 
            <select readonly disabled  id="listaBusquedaUsuario">
                <?=$listaBusquedaUsuario?>
            </select> 
            <select readonly disabled  id="listaBusquedaPlantilla">
                <?=$listaBusquedaPlantilla?>
            </select> 
            <select readonly disabled  id="listaBusquedaBien">
                <?=$listaBusquedaBien?>
            </select> 
        </div>
        <div style="display:none;" id ="ControladorActual"><?=site_url('/preventivo')?></div>
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

                    if($man_id == "" ) {
                        echo $btnAgregar;
                    }elseif($estatus == "Solicitado"){
                        echo $btnBuscar;
                        echo $btnEditar;
                        echo $btnAgregar;
                        echo $btnAprobar;
                        echo $btnEliminar;
                    }elseif($estatus == "Aprobado"){
                        echo $btnBuscar;
                        echo $btnEditar;
                        echo $btnAgregar;
                        echo $btnDesaprobar;
                    }elseif($estatus == "Afectado"){
                        echo $btnBuscar;
                        echo $btnEditar;
                        echo $btnAgregar;
                    }elseif($estatus == "Realizado"){
                        echo $btnBuscar;
                        echo $btnAgregar;
                    }
                ?>

            </div>
        </div>
    </div>
</div>