<div class="container">
    <div class="row">
        <div class="col-lg-9" style="padding: 0px;">
            <h2><span class="fa fa-file-text"></span> Reportes de Partidas</h2> 
        </div>
    </div>
</div>

<div class="container">
    <div class="formulario-sigma">
        <form class ="" id="FormularioActual" method="POST" action = "<?=site_url('/reportes/partidas/')?>">

            <div style="margin: 10px 15px;display:none;" id="alertaFormularioActual" class="alert alert-danger text-center">
            </div>
            <div style="display:none;" id = "IdForm">
            </div>


            <div class="form-group row">
                <label for="reporte" class="col-lg-2 col-form-label">Reporte:</label>
                <div class="col-lg-10">
                    <select class="form-control obligatorio lista" id="reporte">
                        <option value="listadopartidas">Listado de Partidas</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="nomPar" class="col-lg-2 col-form-label">Partidas:</label>
                <div class="col-lg-10">
                    <div style="width:86%;float:left;">
                        <div style="display:none;" id="idPar"></div>
                        <input readonly  type="text"
                            class="form-control texto buscador" id="nomPar" value="">
                    </div>
                    <div style="width:14%;float:right;padding:10px;">
                        <span title="Buscar Partida" class="fa fa-search BuscarPartida" style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Partida" class="fa fa-trash-o BorrarPartida" style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
            </div>

        </form>
        
        <div style="display:none;">
            <select readonly   id="listaBusquedaPartidas">
                <?=$listaBusquedaPartidas?>
            </select> 
        </div>
        
        <div style="display:none;" id ="ControladorActual"><?=site_url('/reportes/partidas/')?></div>
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