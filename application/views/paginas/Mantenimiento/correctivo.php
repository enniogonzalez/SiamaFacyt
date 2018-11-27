<div class="container">
    <div class="row">
        <div class="col-lg-9" style="padding: 0px;">
            <h2><span class="fa fa-briefcase"></span> Mantenimiento Correctivo</h2>   
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

        <form class ="formulario-desactivado" id="FormularioActual" method="POST" action = "<?=site_url('/correctivo/guardar')?>">

            <div style="margin: 10px 15px;display:none;" id="alertaFormularioActual" class="alert alert-danger text-center">
            </div>
            <div style="display:none;" id = "IdForm">
                <?=$mco_id?>
            </div>
         
            <div class="form-group row">
                <label for="DocumentoCorrectivo" class="col-lg-3 col-form-label">Documento:</label>
                <div class="col-lg-9">
                    <input readonly disabled type="text" maxlength="10"
                        class="form-control texto" id="DocumentoCorrectivo" value="<?=$documento?>">
                </div>
            </div>

            <div class="form-group row">
                <label for="EstatusCorrectivo" class="col-lg-3 col-form-label">Estatus:</label>
                <div class="col-lg-9">
                    <input readonly disabled type="text" maxlength="100"
                        class="form-control texto estatus" id="EstatusCorrectivo" value="<?=$estatus?>">
                </div>
            </div>

            <div class="form-group row">
                <label for="nomBieCorrectivo" class="col-lg-3 col-form-label">Bien:</label>
                <div class="col-lg-9">
                    <div style="width:86%;float:left;">
                        <div style="display:none;" id="idBieCorrectivo"><?=$bie_id?></div>
                        <input readonly disabled type="text"
                            class="form-control obligatorio texto buscador" id="nomBieCorrectivo" value="<?=$bie_nom?>">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:14%;float:right;padding:10px;">
                        <span title="Buscar Bien" class="fa fa-search BuscarBienCorrectivo" style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Bien" class="fa fa-trash-o BorrarBienCorrectivo" style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="InicioCorrectivo" class="col-lg-3 col-form-label">Inicio:</label>
                <div class="col-lg-9">
                    <input maxlength="100" readonly disabled type="date" 
                    class="form-control obligatorio fecha" id="InicioCorrectivo" value="<?=$fec_ini?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="FinCorrectivo" class="col-lg-3 col-form-label">Fin:</label>
                <div class="col-lg-9">
                    <input maxlength="100" readonly disabled type="date" 
                    class="form-control obligatorio fecha" id="FinCorrectivo" value="<?=$fec_fin?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="ObservacionCorrectivo" class="col-lg-3 col-form-label">Observaci&oacute;n:</label>
                <div class="col-lg-9">
                    <textarea  readonly disabled class="form-control texto" rows="3"
                    style = "resize:vertical;" id="ObservacionCorrectivo"><?=$observaciones?></textarea>
                </div>
            </div>

            <h3>
                Cambios Correctivos
            </h3>
            <div class="table-responsive">
                <table id="TablaCambiosCorrectivos" class="table table-hover tabla-siama tabla-siama-desactivada">
                    <thead class="head-table-siama" style="font-size:11px;">
                        <tr>
                            <th style="width:20%;">P. Dañada</th>
                            <th style="width:20%;">P. Cambio</th>
                            <th style="width:15%;">Usuario</th>
                            <th style="width:15%;">Proveedor</th>
                            <th style="width:10%;">Inicio</th>
                            <th style="width:10%;">Fin</th>
                            <th style="width:5%;">
                                <span id ="agregarCambio" style="color:#28a745;cursor: pointer;" class="fa fa-plus-circle fa-lg"></span>
                            </th>
                            <th style="width:5%;">
                                <span id ="eliminarCambio" style="color:#dc3545;cursor: pointer;" class="fa fa-minus-circle fa-lg"></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody style="font-size:11px;">
                        <?=$Cambios?>
                    </tbody>
                </table>
            </div>

            <h3>
                Reparaciones Correctivas
            </h3>
            <div class="table-responsive">
                <table id="TablaReparacionesCorrectivas" class="table table-hover tabla-siama tabla-siama-desactivada">
                    <thead class="head-table-siama" style="font-size:11px;">
                        <tr>
                            <th style="width:22%;">P. Dañada</th>
                            <th style="width:17%;">Usuario</th>
                            <th style="width:17%;">Proveedor</th>
                            <th style="width:17%;">Inicio</th>
                            <th style="width:17%;">Fin</th>
                            <th style="width:5%;">
                                <span id ="agregarReparacion" style="color:#28a745;cursor: pointer;" class="fa fa-plus-circle fa-lg"></span>
                            </th>
                            <th style="width:5%;">
                                <span id ="eliminarReparacion" style="color:#dc3545;cursor: pointer;" class="fa fa-minus-circle fa-lg"></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody style="font-size:11px;">
                        <?=$Reparaciones?>
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
            <select readonly disabled  id="listaBusquedaBien">
                <?=$listaBusquedaBien?>
            </select> 
            <select readonly disabled  id="listaBusquedaFalla">
                <?=$listaBusquedaFalla?>
            </select> 
        </div>
        <div style="display:none;" id ="ControladorActual"><?=site_url('/correctivo')?></div>
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

                    if($mco_id == "" ) {
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