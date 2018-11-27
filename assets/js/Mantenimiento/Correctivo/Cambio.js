
const ProveedorC = "ProveedorC";
const FallaC = "FallaC";
const UsuarioC = "UsuariosC";
const PiezaDC = "PiezaDC";
const PiezaCC = "PiezaCC";

$(function(){

    /************************************/
    /*      Inicio Buscadores           */
    /************************************/

    /************************************/
    /*      Manejo Proveedores          */
    /************************************/
    
    $('#SiamaModalFunciones').on('click','#nomProC',function(){
        BuscarProveedor(ProveedorC);
    });

    $('#SiamaModalFunciones').on('click','.BuscarProveedorC',function(){
        BuscarProveedor(ProveedorC);
    });

    $('#SiamaModalFunciones').on('click','.BorrarProveedorC',function(){
        $('#idProC').text('');
        $('#nomProC').val('');
    });
    

    /************************************/
    /*      Manejo Fallas             */
    /************************************/
    
    $('#SiamaModalFunciones').on('click','#nomFallaCambio',function(){
        BuscarFalla(FallaC);
    });

    $('#SiamaModalFunciones').on('click','.BuscarFallaCambio',function(){
        BuscarFalla(FallaC);
    });

    $('#SiamaModalFunciones').on('click','.BorrarFallaCambio',function(){
        $('#idFallaCambio').text('');
        $('#nomFallaCambio').val('');
    });

    /************************************/
    /*      Manejo Usuarios             */
    /************************************/
    
    $('#SiamaModalFunciones').on('click','#nomUsuCambio',function(){
        BuscarUsuario(UsuarioC);
    });

    $('#SiamaModalFunciones').on('click','.BuscarUsuarioC',function(){
        BuscarUsuario(UsuarioC);
    });

    $('#SiamaModalFunciones').on('click','.BorrarUsuarioC',function(){
        $('#idUsuCambio').text('');
        $('#nomUsuCambio').val('');
    });

    /************************************/
    /*      Manejo Pieza                */
    /************************************/
    
    $('#SiamaModalFunciones').on('click','#nomPiezaDC',function(){
        BuscarPieza(PiezaDC);
    });

    $('#SiamaModalFunciones').on('click','.BuscarPiezaDC',function(){
        BuscarPieza(PiezaDC);
    });

    $('#SiamaModalFunciones').on('click','.BorrarPiezaDC',function(){
        $('#idPiezaDC').text('');
        $('#nomPiezaDC').val('');
    });
    
    $('#SiamaModalFunciones').on('click','#nomPiezaCC',function(){
        BuscarPieza(PiezaCC);
    });

    $('#SiamaModalFunciones').on('click','.BuscarPiezaCC',function(){
        BuscarPieza(PiezaCC);
    });

    $('#SiamaModalFunciones').on('click','.BorrarPiezaCC',function(){
        $('#idPiezaCC').text('');
        $('#idBienPiezaCC').text('');
        $('#nomPiezaCC').val('');
        
    });

    /************************************/
    /*          Fin Buscadores          */
    /************************************/


    $('#agregarCambio').on('click',function(){

        if($('#idBieCorrectivo').text().trim() == ""){

            Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn btn-primary-siama">
            <span class="fa fa-times-circle"></span>
            Cerrar
            </button>`;
    
            Cuerpo = "No se puede agregar un <strong>Cambio Correctivo</strong> debido a que no se ha seleccionado un <strong>Bien</strong>.";
    
    
            var parametros = {
                "Titulo":"Advetencia",
                "Cuerpo": Cuerpo,
                "Botones":Botones
            }

            ModalAdvertencia(parametros);
        }else
            AgregarCambioCorrectivo();
    });

    $('#eliminarCambio').on('click',function(){
        $('#TablaCambiosCorrectivos .tr-activa-siama').remove();
    });
    
    $('#TablaCambiosCorrectivos').on('click','.editarCambio',function(){

        //Se remueve la clase activa de la fila que esta activa
        $('.tr-activa-siama').removeClass('tr-activa-siama');

        var fila = $(this).parent('tr');
        //Se agrega la clase activa a la fila actual, esto para evitar
        //que se le quite la clase activa a una fila que esta activa
        //y se quiera editar (o sea, no se quiere quitar la seleccion)
        fila.addClass('tr-activa-siama');

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
            "nomPiezaDC"        :   fila.find('td:eq(2)').text().trim(),
            "idBienPiezaCC"     :   fila.find('td:eq(3)').text().trim(),
            "idPiezaCC"         :   fila.find('td:eq(4)').text().trim(),
            "nomPiezaCC"        :   fila.find('td:eq(5)').text().trim(),
            "idUsuCambio"       :   fila.find('td:eq(6)').text().trim(),
            "nomUsuCambio"      :   fila.find('td:eq(7)').text().trim(),
            "idProC"            :   fila.find('td:eq(8)').text().trim(),
            "nomProC"           :   fila.find('td:eq(9)').text().trim(),
            "InicioCambio"      :   fila.find('td:eq(10)').text().trim(),
            "FinCambio"         :   fila.find('td:eq(11)').text().trim(),
            "ObservacionC"      :   fila.find('td:eq(12)').text().trim(),
            "idFallaCambio"     :   fila.find('td:eq(14)').text().trim(),
            "nomFallaCambio"    :   fila.find('td:eq(15)').text().trim(),
        }

        SetModalFuncionesCambios(data);

        if($('#EstatusCorrectivo').val() != "Solicitado"){
            setTimeout(function(){$('#ObservacionC').focus();}, 400);
        }
    });
    
    $('#TablaCambiosCorrectivos').on('click','.realizarCambio',function(){
        $('.tr-activa-siama').removeClass('tr-activa-siama');
        var fila = $(this).parent('tr');
        fila.addClass('tr-activa-siama');

        if($(this).find('span').hasClass('fa-square-o')){
            $(this).find('span').removeClass('fa-square-o');
            $(this).find('span').addClass('fa-check-square-o');
            $(this).parent('tr').find('td:eq(13)').text('Realizado');
        }else{
            $(this).find('span').removeClass('fa-check-square-o');
            $(this).find('span').addClass('fa-square-o');
            $(this).parent('tr').find('td:eq(13)').text('');
        }
    });

    $('#TablaCambiosCorrectivos tbody').on('click','tr',function(){
        ActivarCeldaTabla(this)
    });

    $('#SiamaModalFunciones').on('click','#CancelarEdicionCambio',function(){
        ClearModalFunction();
    });

    $('#SiamaModalFunciones').on('click','#GuardarEdicionCambio',function(){
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

        //Validar que exista un usuario o un proveedor asignado
        if(Valido && $('#nomUsuCambio').val() == "" && $('#nomProC').val() == ""){
            Valido = false;
            $('#alertaModal').focus(); 
            $('#alertaModal').text('El Cambio Correctivo debe contar con un Usuario o un Proveedor para realizar el mismo.');
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
            fila.find('td:eq(2)').text($('#nomPiezaDC').val().trim());
            fila.find('td:eq(3)').text($('#idBienPiezaCC').text().trim());
            fila.find('td:eq(4)').text($('#idPiezaCC').text().trim());
            fila.find('td:eq(5)').text($('#nomPiezaCC').val().trim());
            fila.find('td:eq(6)').text($('#idUsuCambio').text().trim());
            fila.find('td:eq(7)').text($('#nomUsuCambio').val().trim());
            fila.find('td:eq(8)').text($('#idProC').text().trim());
            fila.find('td:eq(9)').text($('#nomProC').val().trim());
            fila.find('td:eq(10)').text($('#InicioCambio').val().trim());
            fila.find('td:eq(11)').text($('#FinCambio').val().trim());
            fila.find('td:eq(12)').text($('#ObservacionC').val().trim());
            fila.find('td:eq(14)').text($('#idFallaCambio').text().trim());
            fila.find('td:eq(15)').text($('#nomFallaCambio').val().trim());
    
            CerrarFunciones();
        }
    });
    
    $('#SiamaModalFunciones').on('change','#InicioCambio',function(){
        if($(this).val() != "" && 
        $('#FinCambio').val() != "" 
        && $(this).val() > $('#FinCambio').val()){
            $('#FinCambio').val($(this).val());
        }

    });

    $('#SiamaModalFunciones').on('change','#FinCambio',function(){
        if($(this).val() != "" && 
        $('#InicioCambio').val() != "" 
        && $(this).val() < $('#InicioCambio').val()){
            $('#InicioCambio').val($(this).val());
        }
    })

     window.ObtenerJsonCambios = function(){
        var Cambios = [];
        var estatuDoc = $('#EstatusCorrectivo').val().trim();
        $("#TablaCambiosCorrectivos").find('> tbody > tr').each(function () {
    
            if( $(this).find('td:eq(1)').text().trim() != '' && 
                (   estatuDoc == "Solicitado" || 
                    (   estatuDoc != "Solicitado" && 
                        $(this).find('td:eq(13)').text() == "Realizado"
                    )
                )
            ){
                Cambios.push({ 
                    "Id"            : $(this).find('td:eq(0)').text(),
                    "IdPiezaD"      : $(this).find('td:eq(1)').text(),
                    "idBienPiezaC"  : $(this).find('td:eq(3)').text(),
                    "IdPiezaC"      : $(this).find('td:eq(4)').text(),
                    "IdUsu"         : $(this).find('td:eq(6)').text(),
                    "IdPro"         : $(this).find('td:eq(8)').text(),
                    "Inicio"        : $(this).find('td:eq(10)').text(),
                    "Fin"           : $(this).find('td:eq(11)').text(),
                    "Observacion"   : $(this).find('td:eq(12)').text(),
                    "Estatus"       : $(this).find('td:eq(13)').text(),
                    "FallaCambio"   : $(this).find('td:eq(14)').text()
                });
            }
        });

        return Cambios;
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
    
    window.AgregarCambioCorrectivo = function(){
        //Agregar registro a la tabla de listas desplegables al final
        $('#TablaCambiosCorrectivos > tbody:last-child').append(`
            <tr>
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
                <label class="col-lg-3 col-form-label">Usuario:</label>
                <div class="col-lg-9">
                    <div style="width:80%;float:left;">
                        <div style="display:none;" id="idUsuCambio">${data['idUsuCambio']}</div>
                        <input type="text" title="Usuario que realiza cambio" ${atributos} readonly
                            class="form-control texto  buscador Cambio" id="nomUsuCambio" value="${data['nomUsuCambio']}">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:20%;float:right;padding:10px;">
                        <span title="Buscar Usuario" class="fa fa-search BuscarUsuarioC" 
                            style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Usuario" class="fa fa-trash-o BorrarUsuarioC" 
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
                    <div style="width:80%;float:left;">
                        <div style="display:none;" id="idFallaCambio">${data['idFallaCambio']}</div>
                        <input type="text" title="Falla" ${atributos} readonly
                            class="form-control texto obligatorio buscador Cambio" id="nomFallaCambio" value="${data['nomFallaCambio']}">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:20%;float:right;padding:10px;">
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
});