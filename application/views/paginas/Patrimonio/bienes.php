<div class="container">
    <div class="row">
        <div class="col-lg-12" style="padding: 0px;">
            <h2><span class="fa fa-cube"></span> Bienes</h2> 
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
        
        <form class ="formulario-desactivado" id="FormularioActual" method="POST" action = "<?=site_url('/bienes/guardar')?>">

            <div style="margin: 10px 15px;display:none;" id="alertaFormularioActual" class="alert alert-danger text-center">
            </div>
            <div style="display:none;" id = "IdForm">
                <?=$bie_id?>
            </div>
         
            <div class="form-group row">
                <label for="estatusBien" class="col-lg-3 col-form-label">Estatus:</label>
                <div class="col-lg-9">
                    <input maxlength="100" readonly disabled type="text" 
                    class="form-control estatus obligatorio texto" id="estatusBien" value="<?=$estatus?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="NombreBien" class="col-lg-3 col-form-label">Nombre:</label>
                <div class="col-lg-9">
                    <input maxlength="100" readonly disabled type="text" 
                    class="form-control obligatorio texto" id="NombreBien" value="<?=$nombre?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="modeloBien" class="col-lg-3 col-form-label">Modelo:</label>
                <div class="col-lg-9">
                    <input maxlength="100" readonly disabled type="text" 
                    class="form-control obligatorio texto" id="modeloBien" value="<?=$modelo?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="serialBien" class="col-lg-3 col-form-label">Serial:</label>
                <div class="col-lg-9">
                    <input maxlength="100" readonly disabled type="text" 
                    class="form-control obligatorio texto" id="serialBien" value="<?=$bie_ser?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="invBien" class="col-lg-3 col-form-label">Inventario UC:</label>
                <div class="col-lg-9">
                    <input maxlength="100" readonly disabled type="text" 
                    class="form-control texto" id="invBien" value="<?=$inv_uc?>">
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
                <label for="nomLoc" class="col-lg-3 col-form-label">Localizaci&oacute;n:</label>
                <div class="col-lg-9">
                    <div style="width:86%;float:left;">
                        <div style="display:none;" id="idLoc"><?=$loc_id?></div>
                        <input readonly disabled type="text"
                            class="form-control texto obligatorio buscador" id="nomLoc" value="<?=$nomloc?>">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:14%;float:right;padding:10px;">
                        <span title="Buscar Localizacion" class="fa fa-search BuscarLocalizacion" style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Localizacion" class="fa fa-trash-o BorrarLocalizacion" style="cursor: pointer;float:right;"></span>
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
                <label for="nomCus" class="col-lg-3 col-form-label">Custodio:</label>
                <div class="col-lg-9">
                    <div style="width:86%;float:left;">
                        <div style="display:none;" id="idCus"><?=$custodio?></div>
                        <input readonly disabled type="text"
                            class="form-control texto obligatorio buscador" id="nomCus" value="<?=$nomcus?>">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:14%;float:right;padding:10px;">
                        <span title="Buscar Custodio" class="fa fa-search BuscarCustodio" style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Custodio" class="fa fa-trash-o BorrarCustodio" style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="fabBien" class="col-lg-3 col-form-label">Fecha Fabricaci&oacute;n:</label>
                <div class="col-lg-9">
                    <input maxlength="100" readonly disabled type="date" 
                    class="form-control obligatorio fecha" id="fabBien" value="<?=$fec_fab?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="fAdqBien" class="col-lg-3 col-form-label">Fecha Adquisici&oacute;n:</label>
                <div class="col-lg-9">
                    <input maxlength="100" readonly disabled type="date" 
                    class="form-control obligatorio fecha" id="fAdqBien" value="<?=$fec_adq?>">
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
                <label for="tAdqBien" class="col-lg-3 col-form-label">Tipo Adquisici&oacute;n:</label>
                <div class="col-lg-9">
                    <select readonly disabled class="form-control obligatorio lista" id="tAdqBien">
                        <?=$ldAdquisicion?>
                    </select> 
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="Alimentacion" class="col-lg-3 col-form-label">Fuente Alimentaci&oacute;n:</label>
                <div class="col-lg-9">
                    <select readonly disabled class="form-control obligatorio lista" id="Alimentacion">
                        <?=$ldAlimentacion?>
                    </select> 
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="UsoBien" class="col-lg-3 col-form-label">Uso:</label>
                <div class="col-lg-9">
                    <select readonly disabled class="form-control obligatorio lista" id="UsoBien">
                        <?=$ldUso?>
                    </select> 
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>
            
            <div class="form-group row">
                <label for="tipoBien" class="col-lg-3 col-form-label">Tipo:</label>
                <div class="col-lg-9">
                    <select readonly disabled class="form-control obligatorio lista" id="tipoBien">
                        <?=$ldTipo?>
                    </select> 
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>
            
            <div class="form-group row">
                <label for="tecBien" class="col-lg-3 col-form-label">Tecnolog&iacute;a Predominante:</label>
                <div class="col-lg-9">
                    <select readonly disabled class="form-control obligatorio lista" id="tecBien">
                        <?=$ldTecnologia?>
                    </select> 
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>
            
            <div class="form-group row">
                <label for="riesgoBien" class="col-lg-3 col-form-label">Riesgo:</label>
                <div class="col-lg-9">
                    <select readonly disabled class="form-control obligatorio lista" id="riesgoBien">
                        <?=$ldRiesgo?>
                    </select> 
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="mVol" class="col-lg-3 col-form-label">Voltaje:</label>
                <div class="col-lg-5">
                    <input readonly disabled required type="text"  
                        class="form-control obligatorio medida" id="mVol" value="<?=$med_vol?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
                <div class="col-lg-4">
                    <select readonly disabled class="form-control obligatorio lista" id="uVol">
                        <?=$ldVoltio?>
                    </select> 
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="mAmp" class="col-lg-3 col-form-label">Amperaje:</label>
                <div class="col-lg-5">
                    <input readonly disabled required type="text"  
                        class="form-control obligatorio medida" id="mAmp" value="<?=$med_amp?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
                <div class="col-lg-4">
                    <select readonly disabled class="form-control obligatorio lista" id="uAmp">
                        <?=$ldAmperaje?>
                    </select> 
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="mPot" class="col-lg-3 col-form-label">Potencia:</label>
                <div class="col-lg-5">
                    <input readonly disabled required type="text"  
                        class="form-control obligatorio medida" id="mPot" value="<?=$med_pot?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
                <div class="col-lg-4">
                    <select readonly disabled class="form-control obligatorio lista" id="uPot">
                        <?=$ldPotencia?>
                    </select> 
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="mFre" class="col-lg-3 col-form-label">Frecuencia:</label>
                <div class="col-lg-5">
                    <input readonly disabled required type="text"  
                        class="form-control obligatorio medida" id="mFre" value="<?=$med_fre?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
                <div class="col-lg-4">
                    <select readonly disabled class="form-control obligatorio lista" id="uFre">
                        <?=$ldFrecuencia?>
                    </select> 
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="mCap" class="col-lg-3 col-form-label">Capacidad:</label>
                <div class="col-lg-5">
                    <input readonly disabled required type="text"  
                        class="form-control obligatorio medida" id="mCap" value="<?=$med_cap?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
                <div class="col-lg-4">
                    <select readonly disabled class="form-control obligatorio lista" id="uCap">
                        <?=$ldCapacidad?>
                    </select> 
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="mPre" class="col-lg-3 col-form-label">Presi&oacute;n:</label>
                <div class="col-lg-5">
                    <input readonly disabled required type="text"  
                        class="form-control obligatorio medida" id="mPre" value="<?=$med_pre?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
                <div class="col-lg-4">
                    <select readonly disabled class="form-control obligatorio lista" id="uPre">
                        <?=$ldPresion?>
                    </select> 
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="mFlu" class="col-lg-3 col-form-label">Flujo:</label>
                <div class="col-lg-5">
                    <input readonly disabled required type="text"  
                        class="form-control obligatorio medida" id="mFlu" value="<?=$med_flu?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
                <div class="col-lg-4">
                    <select readonly disabled class="form-control obligatorio lista" id="uFlu">
                        <?=$ldFlujo?>
                    </select> 
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="mTem" class="col-lg-3 col-form-label">Temperatura:</label>
                <div class="col-lg-5">
                    <input readonly disabled required type="text"  
                        class="form-control obligatorio medida" id="mTem" value="<?=$med_tem?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
                <div class="col-lg-4">
                    <select readonly disabled class="form-control obligatorio lista" id="uTem">
                        <?=$ldTemperatura?>
                    </select> 
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="mPes" class="col-lg-3 col-form-label">Peso:</label>
                <div class="col-lg-5">
                    <input readonly disabled required type="text"  
                        class="form-control obligatorio medida" id="mPes" value="<?=$med_pes?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
                <div class="col-lg-4">
                    <select readonly disabled class="form-control obligatorio lista" id="uPes">
                        <?=$ldPeso?>
                    </select> 
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="mVel" class="col-lg-3 col-form-label">Velocidad:</label>
                <div class="col-lg-5">
                    <input readonly disabled required type="text"  
                        class="form-control obligatorio medida" id="mVel" value="<?=$med_vel?>">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
                <div class="col-lg-4">
                    <select readonly disabled class="form-control obligatorio lista" id="uVel">
                        <?=$ldVelocidad?>
                    </select> 
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>


            <div class="form-group row">
                <label for="Recomendacion" class="col-lg-3 col-form-label">Recomendaciones del Fabricante:</label>
                <div class="col-lg-9">
                    <textarea  readonly disabled class="form-control texto" rows="3"
                    style = "resize:vertical;" id="Recomendacion"><?=$rec_fab?></textarea>
                </div>
            </div>

            <div class="form-group row">
                <label for="Observacion" class="col-lg-3 col-form-label">Observaci&oacute;n:</label>
                <div class="col-lg-9">
                    <textarea  readonly disabled class="form-control texto" rows="3"
                    style = "resize:vertical;" id="Observacion"><?=$observaciones?></textarea>
                </div>
            </div>

            <h3>
                Piezas
            </h3>
            <div class="table-responsive">
                <table id="TablaPiezas" class="table table-hover tabla-siama tabla-siama-desactivada">
                    <thead class="head-table-siama" style="font-size:13px;">
                        <tr>
                            <th style="width:55%;">Pieza</th>
                            <th style="width:35%;">Inventario UC</th>
                            <th style="width:10%;">Estatus</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:13px;">
                        <?=$Piezas?>
                    </tbody>
                </table>
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
            <select readonly disabled  id="listaBusquedaLocalizacion">
                <?=$listaBusquedaLocalizacion?>
            </select> 
            <select readonly disabled  id="listaBusquedaPartida">
                <?=$listaBusquedaPartida?>
            </select> 
            <select readonly disabled  id="listaBusquedaCustodio">
                <?=$listaBusquedaCustodio?>
            </select> 
        </div>
        <div style="display:none;" id ="ControladorActual"><?=site_url('/bienes')?></div>
        <div style="background-color: #95a5a6; padding: 10px;">

            <div class="form-group row botoneraFormulario" >

                <?php if($bie_id != "" ) {?>
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

                <?php if($bie_id != "" ) {?>
                <button title="Eliminar" type="button" class="btn  btn-danger" id="EliminarRegistro">
                    <span class="fa fa-trash"></span>
                    Eliminar
                </button>
                <?php }?>

            </div>
        </div>
    </div>
</div>