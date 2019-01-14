
        <!-- Modal Funciones -->
        <div class="modal fade" id="SigmaModalFunciones" tabindex="-1" role="dialog" aria-labelledby="SigmaModalFuncionesEtiqueta" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" style="z-index:1 !important;margin-top:10vh;">
                <div class="modal-content containerModalMensajes">
                <div class="modal-header" style="background-color:#2C3E50;color:#fff; ">
                    <h4 class="modal-title" id="SigmaModalFuncionesEtiqueta" style="text-align:center;width:100%;"></h4>
                    <label style="display:none;" id = "mhOptionR"></label>
                    <label style="display:none;" id = "mhOptionC"></label>
                </div>
                <div style="width: 100%;padding:5px 20px;display:none;" class="body-sigma contenedorAlertaModal">
                    <div class="bordeAlertaModal">
                        <div style="margin:0;" id="alertaModal" class="alert alert-danger text-center">
                        </div>
                    </div>
                </div>
                <div class="modal-body body-sigma" >
                </div>
                <div class="modal-footer" style="background-color: #95a5a6; padding: 10px;">
                </div>
                </div>
            </div>
        </div>
        
        <!-- Modal Errores -->
        <div class="modal fade" id="SigmaModalAdvertencias" tabindex="-1" role="dialog" aria-labelledby="SigmaModalAdvertenciasEtiqueta" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" style="z-index:3 !important;margin-top:7vh;">
                <div class="modal-content containerModalMensajes" style="width: 94%;">
                <div class="modal-header" style="background-color:#dc3545;color:#fff; ">
                    <h4 class="modal-title" id="SigmaModalAdvertenciasEtiqueta" style="text-align:center;width:100%;"></h4>
                </div>
                <div class="modal-body body-sigma" style="text-align:center;">
                </div>
                <div class="modal-footer" style="background-color: #95a5a6; padding: 10px;">
                </div>
                </div>
            </div>
        </div>

        
        <!-- Modal Busqueda -->
        <div class="modal fade" id="SigmaModalBusqueda" tabindex="-1" role="dialog" aria-labelledby="SigmaModalBusquedaEtiqueta" data-backdrop="static" data-keyboard="false" >
            <div class="modal-busqueda modal-dialog" style="z-index:2 !important;margin-top:10vh; width:85%;">
                <div class="modal-content containerModalMensajes">
                <div class="modal-header" style="background-color:#2C3E50;color:#fff; ">
                    <h4 class="modal-title" id="SigmaModalFuncionesEtiqueta" style="text-align:center;width:100%;"></h4>
                    <label style="display:none;" id = "etqContador"></label>
                    <label style="display:none;" id = "OpcionBusqueda"></label>
                    <div style="display:none;" id = "ParametrosBuscador"></div>
                    <div style="display:none;" id = "OrigenBuscador"></div>
                </div>
                <div style="width: 100%;padding:5px 20px;display:none;" class="body-sigma contenedorAlertaModalBusqueda">
                    <div style="margin:0;" id="alertaModal" class="alert alert-danger text-center">
                    </div>
                </div>
                
                <div class="modal-body body-sigma" >
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label">Busqueda:</label>
                        <div class="col-md-4">
                            <input type="text" id="TextoBusqueda" class="form-control" maxlength="100" placeholder="Ingresar Texto a Buscar">
                        </div>
                    </div>

                    <div class="form-group row fechasBusqueda">
                        <label class="col-md-2 col-form-label">Rango Fecha:</label>
                        <div class="col-md-4" >
                            <input type="date" class="form-control  " id="fInicioBusqueda" value="">
                        </div>
                        <div class="col-md-4" >
                            <input type="date" class="form-control  " id="fFinBusqueda" value="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label">Ordenar Por:</label>
                        <div class="col-md-4" >
                            <select class="form-control" id="CampoOrden">
                                <?=$OrdenarBusqueda?>
                            </select> 
                        </div>
                        <div class="col-md-4" >
                            <select class="form-control" id="TipoOrden">
                            <option value="ASC">Ascedente</option>
                            <option value="DESC">Descendente</option>
                            </select> 
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table  class="tabla-sigma table table-hover" id="TablaBusquedaFormulario">
                            <thead class="head-table-sigma">
                            </thead>
                            <tbody >
                            </tbody>
                        </table>
                    </div>

                    <div class="row" id ="RegistrosEncontrados">
                        
                    </div>
                    <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-end paginacion-sigma">
                    </ul>
                    </nav>
                </div>

                <div class="modal-footer" style="background-color: #95a5a6; padding: 10px;text-align:right;">
                    <div class="table-responsive">
                        <button  type="button" style="margin:5px;" class="btn btn-primary-sigma" id="FiltrarModalBuscar">
                            <span class="fa fa-search"></span>
                            Filtrar
                        </button>
                        <button type="submit" type="button" style="margin:5px;" class="btn  btn-success" id="ElegirModalBuscar">
                            <span class="fa fa-check"></span>
                            Elegir
                        </button>
                        <button data-dismiss="modal"  type="button" style="margin:5px;" class="btn  btn-danger" id="CancelarModalBuscar">
                            <span class="fa fa-ban "></span>
                            Cancelar
                        </button>
                    </div>
                </div>
                </div>
            </div>
        </div>

    
        <!-- Modal Estatus -->
        <div class = "estatusTransaccion">
            <div style="width:100%;">
                <div style="width:100%;text-align:right;padding: 0px 5px;">
                    <span id="CerrarEstatusInformacion" class="fa fa-times-circle" ></span>
                </div>
                <div id="InformacionEstatus">
                </div>
            </div>
        </div>