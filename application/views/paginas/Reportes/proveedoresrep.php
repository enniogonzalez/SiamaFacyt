<div class="container">
    <div class="row">
        <div class="col-lg-9" style="padding: 0px;">
            <h2><span class="fa fa-file-text"></span> Reportes de Proveedores</h2> 
        </div>
    </div>
</div>

<div class="container">
    <div class="formulario-sigma">
        <form class ="" id="FormularioActual" method="POST" action = "<?=site_url('/reportes/proveedores/')?>">

            <div style="margin: 10px 15px;display:none;" id="alertaFormularioActual" class="alert alert-danger text-center">
            </div>
            <div style="display:none;" id = "IdForm">
            </div>

            <div class="form-group row">
                <label for="reporte" class="col-lg-2 col-form-label">Reporte:</label>
                <div class="col-lg-10">
                    <select class="form-control obligatorio lista" id="reporte">
                        <option value="listadoproveedores">Listado de Proveedores</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="nomPro" class="col-lg-2 col-form-label">Proveedores:</label>
                <div class="col-lg-10">
                    <div style="width:86%;float:left;">
                        <div style="display:none;" id="idPro"></div>
                        <input readonly  type="text"
                            class="form-control texto buscador" id="nomPro" value="">
                    </div>
                    <div style="width:14%;float:right;padding:10px;">
                        <span title="Buscar Marca" class="fa fa-search BuscarMarca" style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Marca" class="fa fa-trash-o BorrarMarca" style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
            </div>

        </form>
        
        <div style="display:none;">
            <select readonly   id="listaBusquedaProveedores">
                <?=$listaBusquedaProveedores?>
            </select> 
        </div>
        <div style="display:none;" id ="ControladorActual"><?=site_url('/reportes/proveedores/')?></div>
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