<div class="container">
    <div class="row">
        <div class="col-lg-9" style="padding: 0px;">
            <h2><span class="fa fa-file-text"></span> Reportes</h2> 
        </div>
    </div>
</div>

<div class="container">
    <div class="formulario-siama">
        <form class ="" id="FormularioActual" method="POST" action = "<?=site_url('/mantenimiento/reportes/')?>">

            <div style="margin: 10px 15px;display:none;" id="alertaFormularioActual" class="alert alert-danger text-center">
            </div>
            <div style="display:none;" id = "IdForm">
            </div>


            <div class="form-group row">
                <label for="reporte" class="col-lg-2 col-form-label">Reporte:</label>
                <div class="col-lg-10">
                    <select class="form-control obligatorio lista" id="reporte">
                        <option value="RepManBie">Mantenimientos por Bien</option>
                        <option value="RepManLoc">Mantenimientos por Localizaci&oacute;n</option>
                        <option value="RepManPro">Mantenimientos por Proveedor</option>
                        <option value="RepManUsu">Mantenimientos por Usuario</option>
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
                <label for="nomUsu" class="col-lg-2 col-form-label">Usuario:</label>
                <div class="col-lg-4">
                    <div style="width:76%;float:left;">
                        <div style="display:none;" id="idUsu"></div>
                        <input readonly  type="text"
                            class="form-control texto  buscador" id="nomUsu" value="">
                    </div>
                    <div style="width:24%;float:right;padding:10px 5px;">
                        <span title="Buscar Usuario" class="fa fa-search BuscarUsuario" style="cursor: pointer;float:left;"></span>
                        <div style="width:6px;float:left;"></div>
                        <span title="Borrar Usuario" class="fa fa-trash-o BorrarUsuario" style="cursor: pointer;float:left;"></span>
                    </div>
                </div>
                <label for="nomPro" class="col-lg-2 col-form-label">Proveedor:</label>
                <div class="col-lg-4">
                    <div style="width:76%;float:left;">
                        <div style="display:none;" id="idPro"></div>
                        <input readonly  type="text"
                            class="form-control texto obligatorio buscador" id="nomPro" value="">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:24%;float:right;padding:10px 5px;">
                        <span title="Buscar Proveedor" class="fa fa-search BuscarProveedor" style="cursor: pointer;float:left;"></span>
                        <div style="width:6px;float:left;"></div>
                        <span title="Borrar Proveedor" class="fa fa-trash-o BorrarProveedor" style="cursor: pointer;float:left;"></span>
                    </div>
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
            <select readonly   id="listaBusquedaUsuario">
                <?=$listaBusquedaUsuario?>
            </select> 
            <select readonly   id="listaBusquedaProveedor">
                <?=$listaBusquedaProveedor?>
            </select> 
        </div>
        <div style="display:none;" id ="ControladorActual"><?=site_url('/marcas')?></div>
        <div style="background-color: #95a5a6; padding: 10px;">

            <div class="form-group row botoneraFormulario" >

                <button type="button"  class="btn btn-primary-siama" id="ImprimirReporte">
                    <span class="fa fa-print fa-lg"></span>
                    Imprimir
                </button>
            </div>
        </div>
    </div>
</div>