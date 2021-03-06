
const ProveedorC = "ProveedorC";
const FallaC = "FallaC";
const ObreroC = "ObrerosC";
const PiezaDC = "PiezaDC";
const PiezaCC = "PiezaCC";

$(function(){

    /************************************/
    /*      Inicio Buscadores           */
    /************************************/

    /************************************/
    /*      Manejo Proveedores          */
    /************************************/
    
    $('#SigmaModalFunciones').on('click','#nomProC',function(){
        BuscarProveedor(ProveedorC);
    });

    $('#SigmaModalFunciones').on('click','.BuscarProveedorC',function(){
        BuscarProveedor(ProveedorC);
    });

    $('#SigmaModalFunciones').on('click','.BorrarProveedorC',function(){
        $('#idProC').text('');
        $('#nomProC').val('');
    });
    
    /************************************/
    /*      Manejo Fallas             */
    /************************************/
    
    $('#SigmaModalFunciones').on('click','#nomFallaCambio',function(){
        if($('#OrigenCorrectivo').val().trim() == "Bien"){
            BuscarFalla(FallaC);
        }
    });

    $('#SigmaModalFunciones').on('click','.BuscarFallaCambio',function(){
        if($('#OrigenCorrectivo').val().trim() == "Bien"){
            BuscarFalla(FallaC);
        }
    });

    $('#SigmaModalFunciones').on('click','.BorrarFallaCambio',function(){
        $('#idFallaCambio').text('');
        $('#nomFallaCambio').val('');
    });

    /************************************/
    /*      Manejo Obreros             */
    /************************************/
    
    $('#SigmaModalFunciones').on('click','#nomObrCambio',function(){
        BuscarObrero(ObreroC);
    });

    $('#SigmaModalFunciones').on('click','.BuscarObreroC',function(){
        BuscarObrero(ObreroC);
    });

    $('#SigmaModalFunciones').on('click','.BorrarObreroC',function(){
        $('#idObrCambio').text('');
        $('#nomObrCambio').val('');
    });

    /************************************/
    /*      Manejo Pieza                */
    /************************************/
    
    $('#SigmaModalFunciones').on('click','#nomPiezaDC',function(){
        BuscarPieza(PiezaDC);
    });

    $('#SigmaModalFunciones').on('click','.BuscarPiezaDC',function(){
        BuscarPieza(PiezaDC);
    });

    $('#SigmaModalFunciones').on('click','.BorrarPiezaDC',function(){
        $('#idPiezaDC').text('');
        $('#nomPiezaDC').val('');
    });
    
    $('#SigmaModalFunciones').on('click','#nomPiezaCC',function(){
        BuscarPieza(PiezaCC);
    });

    $('#SigmaModalFunciones').on('click','.BuscarPiezaCC',function(){
        BuscarPieza(PiezaCC);
    });

    $('#SigmaModalFunciones').on('click','.BorrarPiezaCC',function(){
        $('#idPiezaCC').text('');
        $('#idBienPiezaCC').text('');
        $('#nomPiezaCC').val('');
        
    });

    /************************************/
    /*          Fin Buscadores          */
    /************************************/

    $('#agregarCambio').on('click',function(){

        var Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn btn-primary-sigma">
            <span class="fa fa-times-circle"></span>
            Cerrar
            </button>`;
        var alerta = false;
        
        if($('#OrigenCorrectivo').val().trim() == ""){
            alerta = true;
            Cuerpo = "No se puede agregar un <strong>Cambio Correctivo</strong> debido a que no se ha seleccionado un <strong>Origen</strong>.";
        }else if($('#OrigenCorrectivo').val().trim() == "Mantenimiento Correctivo Planificado" && $('#idManCorPla').text().trim() == ""){
            alerta = true;
            Cuerpo = "No se puede agregar un <strong>Cambio Correctivo</strong> debido a que no se ha seleccionado un <strong>Mantenimiento Correctivo Planificado</strong>.";
        }else if($('#OrigenCorrectivo').val().trim() == "Bien" && $('#idBieCorrectivo').text().trim() == ""){
            alerta = true;
            Cuerpo = "No se puede agregar un <strong>Cambio Correctivo</strong> debido a que no se ha seleccionado un <strong>Bien</strong>.";
        }else
            AgregarCambioCorrectivo();

        if(alerta){
            var parametros = {
                "Titulo":"Advetencia",
                "Cuerpo": Cuerpo,
                "Botones":Botones
            }

            ModalAdvertencia(parametros);
        }
    });

    $('#eliminarCambio').on('click',function(){
        $('#TablaCambiosCorrectivos .tr-activa-sigma').remove();
    });

    $('#SigmaModalFunciones').on('click','#CancelarEdicionCambio',function(){
        ClearModalFunction();
    });

    $('#SigmaModalFunciones').on('change','#FinCambio',function(){
        if($(this).val() != "" && 
        $('#InicioCambio').val() != "" 
        && $(this).val() < $('#InicioCambio').val()){
            $('#InicioCambio').val($(this).val());
        }
    })
    
    $('#SigmaModalFunciones').on('change','#InicioCambio',function(){
        if($(this).val() != "" && 
        $('#FinCambio').val() != "" 
        && $(this).val() > $('#FinCambio').val()){
            $('#FinCambio').val($(this).val());
        }

    });

    $('#SigmaModalFunciones').on('click','#GuardarEdicionCambio',function(){
        var Valido = true;

        $('#formEditarCambio .Cambio').each(function(){
            $(this).removeClass('is-invalid');
            if($(this).hasClass('obligatorio') && $(this).val().trim() == ""){

                if(Valido)
                    $(this).focus();
                
                Valido = false;
                $(this).addClass('is-invalid');
            }
        })

        //Validar que exista un Obrero o un proveedor asignado
        if(Valido && $('#nomObrCambio').val() == "" && $('#nomProC').val() == ""){
            Valido = false;
            $('#alertaModal').focus(); 
            $('#alertaModal').text('El Cambio Correctivo debe contar con un Obrero o un Proveedor para realizar el mismo.');
            $('.contenedorAlertaModal').show();
            
            document.getElementsByClassName("contenedorAlertaModal")[0].scrollIntoView();
        }

        //Validar que no se le haga un mantenimiento correctivo a una pieza
        //dañada dos veces en el mismo mantenimiento
        if(Valido){
            
            ValorActual =$('#idPiezaDC').text().trim();
            fila = $('#mhOptionR').text().trim();
            $("#TablaCambiosCorrectivos").find('> tbody > tr').each(function () {

                if(fila != $(this).index() && ValorActual == $(this).find('td:eq(1)').text().trim()){
                    Valido = false;
                    document.getElementsByClassName("contenedorAlertaModal")[0].scrollIntoView();
                    $('#nomPiezaDC').focus(); 
                    $('#alertaModal').text('No puede haber mas de un cambio correctivo con la misma Pieza Dañada');
                    $('.contenedorAlertaModal').show();
                    return false;
                }
            });

            if(Valido && ExistePiezaReparacion(ValorActual)){
                Valido = false;
                document.getElementsByClassName("contenedorAlertaModal")[0].scrollIntoView();
                $('#nomPiezaDC').focus(); 
                $('#alertaModal').text('La pieza dañada ya fue seleccionada para realizarle una Reparacion Correctiva');
                $('.contenedorAlertaModal').show();
            }
        }


        //Validar que no se asigne misma pieza a ser 
        //cambiada a dos piezas dañadas distintas
        if(Valido){
            
            ValorActual =$('#idPiezaCC').text().trim();
            fila = $('#mhOptionR').text().trim();
            $("#TablaCambiosCorrectivos").find('> tbody > tr').each(function () {

                if(fila != $(this).index() && ValorActual == $(this).find('td:eq(4)').text().trim()){
                    Valido = false;
                    document.getElementsByClassName("contenedorAlertaModal")[0].scrollIntoView();
                    $('#nomPiezaCC').focus(); 
                    $('#alertaModal').text('No puede haber mas de un cambio correctivo con la misma Pieza de Cambio');
                    $('.contenedorAlertaModal').show();
                    return false;
                }
            });

        }


        if(Valido){

            var row = parseInt($('#mhOptionR').text());
            var fila = $('#TablaCambiosCorrectivos tbody tr').eq(row);
            fila.find('td:eq(1)').text($('#idPiezaDC').text().trim());
            fila.find('td:eq(2)').text($('#idTipoPieza').text().trim());
            fila.find('td:eq(3)').text($('#nomPiezaDC').val().trim());
            fila.find('td:eq(4)').text($('#idBienPiezaCC').text().trim());
            fila.find('td:eq(5)').text($('#idPiezaCC').text().trim());
            fila.find('td:eq(6)').text($('#nomPiezaCC').val().trim());
            fila.find('td:eq(7)').text($('#idObrCambio').text().trim());
            fila.find('td:eq(8)').text($('#nomObrCambio').val().trim());
            fila.find('td:eq(9)').text($('#idProC').text().trim());
            fila.find('td:eq(10)').text($('#nomProC').val().trim());
            fila.find('td:eq(11)').text($('#InicioCambio').val().trim());
            fila.find('td:eq(12)').text($('#FinCambio').val().trim());
            fila.find('td:eq(13)').text($('#ObservacionC').val().trim());
            fila.find('td:eq(14)').text($('#idFallaCambio').text().trim());
            fila.find('td:eq(15)').text($('#nomFallaCambio').val().trim());
    
            CerrarFunciones();
        }
    });
    
    $('#TablaCambiosCorrectivos').on('click','.editarCambio',function(){

        //Se remueve la clase activa de la fila que esta activa
        $('.tr-activa-sigma').removeClass('tr-activa-sigma');

        var fila = $(this).parent('tr');
        //Se agrega la clase activa a la fila actual, esto para evitar
        //que se le quite la clase activa a una fila que esta activa
        //y se quiera editar (o sea, no se quiere quitar la seleccion)
        fila.addClass('tr-activa-sigma');

        //Se crea los botones que va a tener la ventana modal de edicion
        Botones = `
        <button type="submit" id ="GuardarEdicionCambio" title="Guardar Cambios" type="button" style="margin:5px;" class="btn  btn-success">
          <span class="fa fa-floppy-o"></span>
          Guardar
        </button>
        <button data-dismiss="modal" id="CancelarEdicionCambio" title="Cancelar Edici&oacute;n" type="button" style="margin:5px;" class="btn  btn-danger">
          <span class="fa fa-ban "></span>
          Cancelar
        </button>`;

        var data = {
            "Titulo"            :   "Editar Cambio Correctivo",
            "EstatusDoc"        :   $('#EstatusCorrectivo').val(),
            "Fila"              :   $(this).parent('tr').index(),
            "Botones"           :   Botones,
            "idPiezaDC"         :   fila.find('td:eq(1)').text().trim(),
            "idTipoPieza"       :   fila.find('td:eq(2)').text().trim(),
            "nomPiezaDC"        :   fila.find('td:eq(3)').text().trim(),
            "idBienPiezaCC"     :   fila.find('td:eq(4)').text().trim(),
            "idPiezaCC"         :   fila.find('td:eq(5)').text().trim(),
            "nomPiezaCC"        :   fila.find('td:eq(6)').text().trim(),
            "idObrCambio"       :   fila.find('td:eq(7)').text().trim(),
            "nomObrCambio"      :   fila.find('td:eq(8)').text().trim(),
            "idProC"            :   fila.find('td:eq(9)').text().trim(),
            "nomProC"           :   fila.find('td:eq(10)').text().trim(),
            "InicioCambio"      :   fila.find('td:eq(11)').text().trim(),
            "FinCambio"         :   fila.find('td:eq(12)').text().trim(),
            "ObservacionC"      :   fila.find('td:eq(13)').text().trim(),
            "idFallaCambio"     :   fila.find('td:eq(14)').text().trim(),
            "nomFallaCambio"    :   fila.find('td:eq(15)').text().trim(),
        }

        SetModalFuncionesCambios(data);

        if($('#EstatusCorrectivo').val() != "Solicitado"){
            setTimeout(function(){$('#ObservacionC').focus();}, 400);
        }
    });
    
    $('#TablaCambiosCorrectivos').on('click','.realizarCambio',function(){
        $('.tr-activa-sigma').removeClass('tr-activa-sigma');
        var fila = $(this).parent('tr');
        fila.addClass('tr-activa-sigma');

        if($(this).find('span').hasClass('fa-square-o')){
            $(this).find('span').removeClass('fa-square-o');
            $(this).find('span').addClass('fa-check-square-o');
            $(this).parent('tr').find('td:eq(16)').text('Realizado');
        }else{
            $(this).find('span').removeClass('fa-check-square-o');
            $(this).find('span').addClass('fa-square-o');
            $(this).parent('tr').find('td:eq(16)').text('');
        }
    });

    $('#TablaCambiosCorrectivos tbody').on('click','tr',function(){
        ActivarCeldaTabla(this)
    });

    function SetModalFuncionesCambios(data){
        var pointer= "";
        var atributos= "";
        
        if(data['EstatusDoc'] != "Solicitado"){
            pointer= "pointer-events: none;";
            atributos= " readonly disabled ";
        }

        //Se crea cuerpo html que va a tener la ventana modal de edicion
        var html = `
        <form class="form-horizontal" id="formEditarCambio">
            <div style="${pointer}">
            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Pieza Dañada:</label>
                <div class="col-lg-9">
                    <div style="width:80%;float:left;">
                        <div style="display:none;" id="idPiezaDC">${data['idPiezaDC']}</div>
                        <div style="display:none;" id="idTipoPieza">${data['idTipoPieza']}</div>
                        <input type="text" title="Pieza Dañada" ${atributos} readonly
                            class="form-control texto obligatorio buscador Cambio" id="nomPiezaDC" value="${data['nomPiezaDC']}">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:20%;float:right;padding:10px;">
                        <span title="Buscar Pieza Dañada" class="fa fa-search BuscarPiezaDC" 
                            style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Pieza Dañada" class="fa fa-trash-o BorrarPiezaDC" 
                            style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Pieza Cambio:</label>
                <div class="col-lg-9">
                    <div style="width:80%;float:left;">
                        <div style="display:none;" id="idBienPiezaCC">${data['idBienPiezaCC']}</div>
                        <div style="display:none;" id="idPiezaCC">${data['idPiezaCC']}</div>
                        <input type="text" title="Pieza Cambio" ${atributos} readonly
                            class="form-control texto obligatorio buscador Cambio" id="nomPiezaCC" value="${data['nomPiezaCC']}">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:20%;float:right;padding:10px;">
                        <span title="Buscar Pieza Cambio" class="fa fa-search BuscarPiezaCC" 
                            style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Pieza Cambio" class="fa fa-trash-o BorrarPiezaCC" 
                            style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Obrero:</label>
                <div class="col-lg-9">
                    <div style="width:80%;float:left;">
                        <div style="display:none;" id="idObrCambio">${data['idObrCambio']}</div>
                        <input type="text" title="Obrero que realiza cambio" ${atributos} readonly
                            class="form-control texto  buscador Cambio" id="nomObrCambio" value="${data['nomObrCambio']}">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:20%;float:right;padding:10px;">
                        <span title="Buscar Obrero" class="fa fa-search BuscarObreroC" 
                            style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Obrero" class="fa fa-trash-o BorrarObreroC" 
                            style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Proveedor:</label>
                <div class="col-lg-9">
                    <div style="width:80%;float:left;">
                        <div style="display:none;" id="idProC">${data['idProC']}</div>
                        <input type="text" title="Proveedor que realiza cambio" ${atributos} readonly
                            class="form-control texto  buscador Cambio" id="nomProC" value="${data['nomProC']}">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:20%;float:right;padding:10px;">
                        <span title="Buscar Proveedor" class="fa fa-search BuscarProveedorC" 
                            style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Proveedor" class="fa fa-trash-o BorrarProveedorC" 
                            style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>
            
            <div class="form-group row">
                <label for="InicioCambio" class="col-lg-3 col-form-label">Inicio:</label>
                <div class="col-lg-9">
                    <input type="date" 
                    class="form-control obligatorio fecha Cambio" ${atributos} id="InicioCambio" value="${data['InicioCambio']}">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="FinCambio" class="col-lg-3 col-form-label">Fin:</label>
                <div class="col-lg-9">
                    <input type="date" 
                    class="form-control obligatorio fecha Cambio" id="FinCambio" ${atributos} value="${data['FinCambio']}">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Falla:</label>
                <div class="col-lg-9">
                    <div style="width:${$('#OrigenCorrectivo').val().trim() == "Mantenimiento Correctivo Planificado" ? "100":"80"}%;float:left;">
                        <div style="display:none;" id="idFallaCambio">${data['idFallaCambio']}</div>
                        <input type="text" title="Falla" ${atributos} readonly 
                            class="form-control texto obligatorio buscador Cambio" id="nomFallaCambio" value="${data['nomFallaCambio']}">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:20%;float:right;padding:10px;${$('#OrigenCorrectivo').val().trim() == "Mantenimiento Correctivo Planificado" ? "display:none;":""}">
                        <span title="Buscar Falla" class="fa fa-search BuscarFallaCambio" 
                            style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Falla" class="fa fa-trash-o BorrarFallaCambio" 
                            style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            </div>
            <div class="form-group row">
                <label for="ObservacionC" class="col-lg-3 col-form-label">Observaci&oacute;n:</label>
                <div class="col-lg-9">
                    <textarea  class="form-control texto" rows="3"
                    style = "resize:vertical;" id="ObservacionC">${data['ObservacionC']}</textarea>
                </div>
            </div>
        </form>`;
        

        //Se crea los parametros necesarios para llenar la ventana modal
        var parametros = {
            "Titulo":data['Titulo'],
            "Cuerpo": html,
            "Columna": 0,
            "Fila":data['Fila'],
            "Botones":data['Botones']
        }

        //Llamar funcion que establece ventana modal segun parametros
        ModalEditarFuncion(parametros);



    }
    
    window.AgregarCambioCorrectivo = function(){
        //Agregar registro a la tabla de listas desplegables al final
        $('#TablaCambiosCorrectivos > tbody:last-child').append(`
            <tr>
                <td style="display:none;"></td>
                <td style="display:none;"></td>
                <td style="display:none;"></td>
                <td></td>
                <td style="display:none;"></td>
                <td style="display:none;"></td>
                <td></td>
                <td style="display:none;"></td>
                <td></td>
                <td style="display:none;"></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="display:none;"></td>
                <td style="display:none;"></td>
                <td style="display:none;"></td>
                <td style="display:none;"></td>
                <td colspan="2" class ="editarCambio" style="text-align: center;cursor: pointer;">
                    <span class="fa fa-pencil fa-lg"></span>
                </td>
            </tr>
        `);
    }

    window.ExisteCambio = function(){
        var Existe = false;

        $("#TablaCambiosCorrectivos").find('> tbody > tr').each(function () {
            if($(this).find('td:eq(1)').text().trim() != ''){
                Existe = true;

                //Salir del ciclo
                return false;
            }
        });

        return Existe;
    }

    window.ExistePiezaCambio = function(pieza){
        Existe = false;
        $("#TablaCambiosCorrectivos").find('> tbody > tr').each(function () {

            if(pieza == $(this).find('td:eq(1)').text().trim()){
                Existe = true;
                return false;
            }
        });

        return Existe;
    }

    window.ObtenerJsonCambios = function(){
        var Cambios = [];
        var estatuDoc = $('#EstatusCorrectivo').val().trim();
        $("#TablaCambiosCorrectivos").find('> tbody > tr').each(function () {
    
            if( $(this).find('td:eq(1)').text().trim() != '' && 
                (   estatuDoc == "Solicitado" || 
                    (   estatuDoc != "Solicitado" && 
                        $(this).find('td:eq(16)').text() == "Realizado"
                    )
                )
            ){
                Cambios.push({ 
                    "Id"            : $(this).find('td:eq(0)').text(),
                    "IdPiezaD"      : $(this).find('td:eq(1)').text(),
                    "idBienPiezaC"  : $(this).find('td:eq(4)').text(),
                    "IdPiezaC"      : $(this).find('td:eq(5)').text(),
                    "IdObr"         : $(this).find('td:eq(7)').text(),
                    "IdPro"         : $(this).find('td:eq(9)').text(),
                    "Inicio"        : $(this).find('td:eq(11)').text(),
                    "Fin"           : $(this).find('td:eq(12)').text(),
                    "Observacion"   : $(this).find('td:eq(13)').text(),
                    "FallaCambio"   : $(this).find('td:eq(14)').text(),
                    "Estatus"       : $(this).find('td:eq(16)').text(),
                });
            }
        });

        return Cambios;
    }
});