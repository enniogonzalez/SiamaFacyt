<div class="container">
    <div class="row">
        <div class="col-lg-9" style="padding: 0px;">
            <h2><span class="fa fa-file-text"></span> Reportes de Patrimonio</h2> 
        </div>
    </div>
</div>

<div class="container">
    <div class="formulario-sigma">
        <form class ="" id="FormularioActual" method="POST" action = "<?=site_url('/reportes/patrimonio/')?>">

            <div style="margin: 10px 15px;display:none;" id="alertaFormularioActual" class="alert alert-danger text-center">
            </div>
            <div style="display:none;" id = "IdForm">
            </div>

            <div class="form-group row">
                <label for="reporte" class="col-lg-2 col-form-label">Reporte:</label>
                <div class="col-lg-10">
                    <select class="form-control obligatorio lista" id="reporte">
                        <option value="listadoajustes">Listado de Ajustes</option>
                        <option value="listadocambiosestatus">Listado de Cambios de Estatus</option>
                        <option value="listadocompatibilidad">Listado de Compatibilidad</option>
                        <option value="listadobienes">Listado de Bienes</option>
                        <option value="listadopiezas">Listado de Piezas</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="InicioPreventivo" class="col-lg-2 col-form-label">Inicio:</label>
                <div class="col-lg-4">
                    <input maxlength="100"   type="date" 
                    class="form-control obligatorio fecha" id="InicioPreventivo" value="">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
                <label for="FinPreventivo" class="col-lg-2 col-form-label">Fin:</label>
                <div class="col-lg-4">
                    <input maxlength="100"   type="date" 
                    class="form-control obligatorio fecha" id="FinPreventivo" value="">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>
            
            <div class="form-group row">
                <label for="nomBie" class="col-lg-2 col-form-label">Bien:</label>
                <div class="col-lg-4">
                    <div style="width:76%;float:left;">
                        <div style="display:none;" id="idBie"></div>
                        <input readonly  type="text"
                            class="form-control  texto buscador" id="nomBie" value="">
                    </div>
                    <div style="width:24%;float:right;padding:10px 5px;">
                        <span title="Buscar Bien" class="fa fa-search BuscarBien" style="cursor: pointer;float:left;"></span>
                        <div style="width:6px;float:left;"></div>
                        <span title="Borrar Bien" class="fa fa-trash-o BorrarBien" style="cursor: pointer;float:left;"></span>
                    </div>
                </div>

                <label for="nomPie" class="col-lg-2 col-form-label">Pieza:</label>
                <div class="col-lg-4">
                    <div style="width:76%;float:left;">
                        <div style="display:none;" id="idPie"></div>
                        <input readonly  type="text"
                            class="form-control texto obligatorio buscador" id="nomPie" value="">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:24%;float:right;padding:10px 5px;">
                        <span title="Buscar Pieza" class="fa fa-search BuscarPieza" style="cursor: pointer;float:left;"></span>
                        <div style="width:6px;float:left;"></div>
                        <span title="Borrar Pieza" class="fa fa-trash-o BorrarPieza" style="cursor: pointer;float:left;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="nomTPI" class="col-lg-2 col-form-label">Tipo de Pieza:</label>
                <div class="col-lg-4">
                    <div style="width:76%;float:left;">
                        <div style="display:none;" id="idTpi"></div>
                        <input readonly  type="text"
                            class="form-control texto  buscador" id="nomTPI" value="">
                    </div>
                    <div style="width:24%;float:right;padding:10px 5px;">
                        <span title="Buscar Tipo de Pieza" class="fa fa-search BuscarTipoPieza" style="cursor: pointer;float:left;"></span>
                        <div style="width:6px;float:left;"></div>
                        <span title="Borrar Tipo de Pieza" class="fa fa-trash-o BorrarTipoPieza" style="cursor: pointer;float:left;"></span>
                    </div>
                </div>

                <label for="nomLoc" class="col-lg-2 col-form-label">Localizaci&oacute;n:</label>
                <div class="col-lg-4">
                    <div style="width:76%;float:left;">
                        <div style="display:none;" id="idLoc"></div>
                        <input readonly  type="text"
                            class="form-control texto buscador" id="nomLoc" value="">
                    </div>
                    <div style="width:24%;float:right;padding:10px 5px;">
                        <span title="Buscar Localizacion" class="fa fa-search BuscarLocalizacion" style="cursor: pointer;float:left;"></span>
                        <div style="width:6px;float:left;"></div>
                        <span title="Borrar Localizacion" class="fa fa-trash-o BorrarLocalizacion" style="cursor: pointer;float:left;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
            </div>

        </form>
        
        <div style="display:none;">
            <select readonly   id="listaBusquedaBien">
                <?=$listaBusquedaBien?>
            </select> 
            <select readonly   id="listaBusquedaLocalizacion">
                <?=$listaBusquedaLocalizacion?>
            </select> 
            <select readonly   id="listaBusquedaTipoPieza">
                <?=$listaBusquedaTipoPieza?>
            </select> 
            <select readonly   id="listaBusquedaPieza">
                <?=$listaBusquedaPieza?>
            </select> 
        </div>
        <div style="display:none;" id ="ControladorActual"><?=site_url('/reportes/patrimonio/')?></div>
        <div style="background-color: #95a5a6; padding: 10px;">

            <div class="form-group row botoneraFormulario" >

                <button type="button"  class="btn btn-primary-sigma" id="ImprimirReporte">
                    <span class="fa fa-print fa-lg"></span>
                    Imprimir
                </button>
            </div>
        </div>
    </div>
</div>