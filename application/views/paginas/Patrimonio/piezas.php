<div class="container">
    <div class="row">
        <div class="col-lg-9" style="padding: 0px;">
            <h2><span class="fa fa-cubes"></span> Piezas</h2> 
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
        
        <form class ="formulario-desactivado" id="FormularioActual" method="POST" action = "<?=site_url('/piezas/guardar')?>">

            <div style="margin: 10px 15px;display:none;" id="alertaFormularioActual" class="alert alert-danger text-center">
            </div>
            <div style="display:none;" id = "IdForm">
                <?=$pie_id?>
            </div>
         
            <div class="form-group row">
                <label for="estatusPieza" class="col-lg-3 col-form-label">Estatus:</label>
                <div class="col-lg-9">
                    <input maxlength="100" readonly disabled type="text" 
                    class="form-control estatus obligatorio texto" id="estatusPieza" value="<?=$estatus?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="NombrePieza" class="col-lg-3 col-form-label">Nombre:</label>
                <div class="col-lg-9">
                    <input maxlength="100" readonly disabled type="text" 
                    class="form-control obligatorio texto" id="NombrePieza" value="<?=$nombre?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="modeloPieza" class="col-lg-3 col-form-label">Modelo:</label>
                <div class="col-lg-9">
                    <input maxlength="100" readonly disabled type="text" 
                    class="form-control obligatorio texto" id="modeloPieza" value="<?=$modelo?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="serialPieza" class="col-lg-3 col-form-label">Serial:</label>
                <div class="col-lg-9">
                    <input maxlength="100" readonly disabled type="text" 
                    class="form-control obligatorio texto" id="serialPieza" value="<?=$pie_ser?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="invPie" class="col-lg-3 col-form-label">Inventario UC:</label>
                <div class="col-lg-9">
                    <input maxlength="100" readonly disabled type="text" 
                    class="form-control texto" id="invPie" value="<?=$inv_uc?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="nomBie" class="col-lg-3 col-form-label">Bien:</label>
                <div class="col-lg-9">
                    <div style="display:none;" id="idBie"><?=$bie_id?></div>
                    <input readonly disabled type="text"
                        class="form-control texto" id="nomBie" value="<?=$nombie?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="nomMar" class="col-lg-3 col-form-label">Marca:</label>
                <div class="col-lg-9">
                    <div style="width:86%;float:left;">
                        <div style="display:none;" id="idMar"><?=$mar_id?></div>
                        <input readonly disabled type="text"
                            class="form-control obligatorio texto buscador" id="nomMar" value="<?=$nommar?>">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:14%;float:right;padding:10px;">
                        <span title="Buscar Marca" class="fa fa-search BuscarMarca" style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Marca" class="fa fa-trash-o BorrarMarca" style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="nomPro" class="col-lg-3 col-form-label">Proveedor:</label>
                <div class="col-lg-9">
                    <div style="width:86%;float:left;">
                        <div style="display:none;" id="idPro"><?=$pro_id?></div>
                        <input readonly disabled type="text"
                            class="form-control texto obligatorio buscador" id="nomPro" value="<?=$nompro?>">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:14%;float:right;padding:10px;">
                        <span title="Buscar Proveedor" class="fa fa-search BuscarProveedor" style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Proveedor" class="fa fa-trash-o BorrarProveedor" style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>
            
            <div class="form-group row">
                <label for="nomPar" class="col-lg-3 col-form-label">Partidas:</label>
                <div class="col-lg-9">
                    <div style="width:86%;float:left;">
                        <div style="display:none;" id="idPar"><?=$par_id?></div>
                        <input readonly disabled type="text"
                            class="form-control texto obligatorio buscador" id="nomPar" value="<?=$nompar?>">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:14%;float:right;padding:10px;">
                        <span title="Buscar Partida" class="fa fa-search BuscarPartida" style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Partida" class="fa fa-trash-o BorrarPartida" style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="fabPieza" class="col-lg-3 col-form-label">Fecha Fabricaci&oacute;n:</label>
                <div class="col-lg-9">
                    <input maxlength="100" readonly disabled type="date" 
                    class="form-control obligatorio fecha" id="fabPieza" value="<?=$fec_fab?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="fAdqPieza" class="col-lg-3 col-form-label">Fecha Adquisici&oacute;n:</label>
                <div class="col-lg-9">
                    <input maxlength="100" readonly disabled type="date" 
                    class="form-control obligatorio fecha" id="fAdqPieza" value="<?=$fec_adq?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="Instalacion" class="col-lg-3 col-form-label">Fecha Instalaci&oacute;n:</label>
                <div class="col-lg-9">
                    <input maxlength="100" readonly disabled type="date" 
                    class="form-control obligatorio fecha" id="Instalacion" value="<?=$fec_ins?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="tAdqPieza" class="col-lg-3 col-form-label">Tipo Adquisici&oacute;n:</label>
                <div class="col-lg-9">
                    <select readonly disabled class="form-control obligatorio lista" id="tAdqPieza">
                        <?=$ldAdquisicion?>
                    </select> 
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="Observacion" class="col-lg-3 col-form-label">Observaci&oacute;n:</label>
                <div class="col-lg-9">
                    <textarea  readonly disabled class="form-control texto" rows="3"
                    style = "resize:vertical;" id="Observacion"><?=$observaciones?></textarea>
                </div>
            </div>

        </form>
        <div style="display:none;">
            <select readonly disabled  id="listaBusquedaFormulario">
                <?=$listaBusquedaFormulario?>
            </select> 
            <select readonly disabled  id="listaBusquedaMarca">
                <?=$listaBusquedaMarca?>
            </select> 
            <select readonly disabled  id="listaBusquedaProveedor">
                <?=$listaBusquedaProveedor?>
            </select> 
            <select readonly disabled  id="listaBusquedaPartida">
                <?=$listaBusquedaPartida?>
            </select> 
            <select readonly disabled  id="listaBusquedaBien">
                <?=$listaBusquedaBien?>
            </select> 
        </div>
        <div style="display:none;" id ="ControladorActual"><?=site_url('/piezas')?></div>
        <div style="background-color: #95a5a6; padding: 10px;">

            <div class="form-group row botoneraFormulario" >

                <?php if($pie_id != "" ) {?>
                <button  type="button"  class="btn btn-primary-siama" id="BuscarRegistro">
                    <span class="fa fa-search"></span>
                    Buscar
                </button>

                <button type="button"  class="btn btn-primary-siama" id="EditarRegistro">
                    <span class="fa fa-pencil-square-o"></span>
                    Editar
                </button>
                <?php }?>

                <button type="button"  class="btn btn-primary-siama" id="AgregarRegistro">
                    <span class="fa fa-plus"></span>
                    Agregar
                </button>

                <?php if($pie_id != "" ) {?>
                <button title="Eliminar" type="button" class="btn  btn-danger" id="EliminarRegistro">
                    <span class="fa fa-trash"></span>
                    Eliminar
                </button>
                <?php }?>

            </div>
        </div>
    </div>
</div>