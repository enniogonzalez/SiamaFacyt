
const PiezaCE = "PiezaCE";
const Falla = "Falla";

$(function(){


    /************************************/
    /*      Inicio Buscadores           */
    /************************************/

    /************************************/
    /*      Manejo Pieza                */
    /************************************/
    
    $('#SiamaModalFunciones').on('click','#nomPiezaCE',function(){
        BuscarPieza(PiezaCE);
    });

    $('#SiamaModalFunciones').on('click','.BuscarPiezaCE',function(){
        BuscarPieza(PiezaCE);
    });

    $('#SiamaModalFunciones').on('click','.BorrarPiezaCE',function(){
        $('#idPieza').text('');
        $('#InvPieza').text('');
        $('#nomPiezaCE').val('');
        $('#idFalla').text('');
        $('#nomFalla').val('');
        $('#estatusPieza').val('');
        $('.divFallaPieza').hide();
    });
    /************************************/
    /*      Manejo Fallas             */
    /************************************/
    
    $('#SiamaModalFunciones').on('click','#nomFalla',function(){
        BuscarFalla(Falla);
    });

    $('#SiamaModalFunciones').on('click','.BuscarFalla',function(){
        BuscarFalla(Falla);
    });

    $('#SiamaModalFunciones').on('click','.BorrarFalla',function(){
        $('#idFalla').text('');
        $('#nomFalla').val('');
    });


    /************************************/
    /*          Fin Buscadores          */
    /************************************/


    $('#agregarPieza').on('click',function(){

        if($('#idBieCambios').text().trim() == ""){

            Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn btn-primary-siama">
            <span class="fa fa-times-circle"></span>
            Cerrar
            </button>`;
    
            Cuerpo = "No se puede <strong>agregar pieza</strong> debido a que no se ha seleccionado un <strong>Bien</strong>.";
    
    
            var parametros = {
                "Titulo":"Advetencia",
                "Cuerpo": Cuerpo,
                "Botones":Botones
            }

            ModalAdvertencia(parametros);
        }else
            AgregarPieza();
    });

    $('#eliminarPieza').on('click',function(){
        $('#TablaPiezasEstatus .tr-activa-siama').remove();
    });

    $('#TablaPiezasEstatus').on('click','.editarPiezaCE',function(){

        //Se remueve la clase activa de la fila que esta activa
        $('.tr-activa-siama').removeClass('tr-activa-siama');

        var fila = $(this).parent('tr');

        //Se agrega la clase activa a la fila actual, esto para evitar
        //que se le quite la clase activa a una fila que esta activa
        //y se quiera editar (o sea, no se quiere quitar la seleccion)
        fila.addClass('tr-activa-siama');

        //Se crea los botones que va a tener la ventana modal de edicion
        Botones = `
        <button type="submit" id ="GuardarEdicionPiezaCE" title="Guardar Pieza" type="button" style="margin:5px;" class="btn  btn-success">
          <span class="fa fa-floppy-o"></span>
          Guardar
        </button>
        <button data-dismiss="modal" id="CancelarEdicionPiezaCE" title="Cancelar Edici&oacute;n" type="button" style="margin:5px;" class="btn  btn-danger">
          <span class="fa fa-ban "></span>
          Cancelar
        </button>`;

        var data = {
            "Titulo"            : "Pieza",
            "EstatusDoc"        : $('#EstatusCambios').val(),
            "Fila"              : $(this).parent('tr').index(),
            "Botones"           : Botones,
            "idPieza"           : fila.find('td:eq(1)').text().trim(),
            "nomPiezaCE"        : fila.find('td:eq(2)').text().trim(),
            "InvPieza"          : fila.find('td:eq(3)').text().trim(),
            "ObservacionPieza"  : fila.find('td:eq(4)').text().trim(),
            "estatusPieza"      : fila.find('td:eq(5)').text().trim(),
            "idFalla"           : fila.find('td:eq(6)').text().trim(),
            "nomFalla"          : fila.find('td:eq(7)').text().trim(),
        }
        SetModalFuncionesPiezaCE(data);
        
    });

    $('#TablaPiezasEstatus tbody').on('click','tr',function(){
        ActivarCeldaTabla(this)
    });

    $('#SiamaModalFunciones').on('click','#CancelarEdicionPiezaCE',function(){
        ClearModalFunction();
    });

    $('#SiamaModalFunciones').on('click','#GuardarEdicionPiezaCE',function(){
        var Valido = true;

        $('#formEditarPiezaCE .PiezaCE').each(function(){
            $(this).removeClass('is-invalid');

            if($(this).hasClass('obligatorio') && $(this).val().trim() == ""){

                if(Valido)
                    $(this).focus();
                
                Valido = false;
                $(this).addClass('is-invalid');
            }
        })


        if($('#estatusPieza').val().trim() == "Inactivo" && $('#idFalla').text().trim()==""){
            Valido = false;
            $('#nomFalla').addClass('is-invalid');
        }

        if(Valido){
            
            ValorActual =$('#idPieza').text().trim();
            fila = $('#mhOptionR').text().trim();
            $("#TablaPiezasEstatus").find('> tbody > tr').each(function () {

                if(fila != $(this).index() && ValorActual == $(this).find('td:eq(1)').text().trim()){
                    Valido = false;
                    $('#nomPiezaCE').focus(); 
                    $('#alertaModal').text('No se puede seleccionar la misma pieza dos veces');
                    $('.contenedorAlertaModal').show();
                    return false;
                }
            });
        }


        if(Valido){

            var row = parseInt($('#mhOptionR').text());
            var fila = $('#TablaPiezasEstatus tbody tr').eq(row);
            fila.find('td:eq(1)').text($('#idPieza').text().trim());
            fila.find('td:eq(2)').text($('#nomPiezaCE').val().trim());
            fila.find('td:eq(3)').text($('#InvPieza').text().trim());
            fila.find('td:eq(4)').text($('#ObservacionPieza').val().trim());
            fila.find('td:eq(5)').text($('#estatusPieza').val().trim());
    
            CerrarFunciones();
        }
    });

    window.ObtenerJsonPiezasCE = function(){
        var PiezaCEs = [];
        $("#TablaPiezasEstatus").find('> tbody > tr').each(function () {
            if( $(this).find('td:eq(1)').text().trim() != ''){
                PiezaCEs.push({ 
                    "Id"            : $(this).find('td:eq(0)').text(),
                    "IdPieza"       : $(this).find('td:eq(1)').text(),
                    "Observacion"   : $(this).find('td:eq(4)').text(),
                    "Estatus"       : $(this).find('td:eq(5)').text(),
                });
            }
        });

        return PiezaCEs;
    }

    window.ExistePiezaCE = function(){
        var Existe = false;

        $("#TablaPiezasEstatus").find('> tbody > tr').each(function () {
            if($(this).find('td:eq(1)').text().trim() != ''){
                Existe = true;

                //Salir del ciclo
                return false;
            }
        });

        return Existe;
    }

    window.ExistePiezaQuitada = function(pieza){
        Existe = false;
        $("#TablaPiezasEstatus").find('> tbody > tr').each(function () {

            if(pieza == $(this).find('td:eq(1)').text().trim()){
                Existe = true;
                return false;
            }
        });

        return Existe;
    }

    window.AgregarPieza = function(){

        //Agregar registro a la tabla de listas desplegables al final
        $('#TablaPiezasEstatus > tbody:last-child').append(`
            <tr>
                <td style="display:none;"></td>
                <td style="display:none;"></td>
                <td></td>
                <td></td>
                <td style="display:none;"></td>
                <td ></td>
                <td style="display:none;"></td>
                <td style="display:none;"></td>
                <td colspan="2" class ="editarPiezaCE" style="text-align: center;cursor: pointer;">
                    <span class="fa fa-pencil fa-lg"></span>
                </td>
            </tr>
        `);
    }

    function SetModalFuncionesPiezaCE(data){

        if(data['estatusPieza'] != "Activo"){
            estiloFalla = "style = \"display:none;\"";
        }else{
            estiloFalla = "";
        }

        //Se crea cuerpo html que va a tener la ventana modal de edicion
        var html = `
        <form class="form-horizontal" id="formEditarPiezaCE">

            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Pieza:</label>
                <div class="col-lg-9">
                    <div style="width:80%;float:left;">
                        <div style="display:none;" id="idPieza">${data['idPieza']}</div>
                        <div style="display:none;" id="InvPieza">${data['InvPieza']}</div>
                        <input type="text" title="Pieza" readonly
                            class="form-control texto obligatorio buscador PiezaCE" id="nomPiezaCE" value="${data['nomPiezaCE']}">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:20%;float:right;padding:10px;">
                        <span title="Buscar Pieza Dañada" class="fa fa-search BuscarPiezaCE" 
                            style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Pieza Dañada" class="fa fa-trash-o BorrarPiezaCE" 
                            style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row divFallaPieza" ${estiloFalla}>
                <label class="col-lg-3 col-form-label">Falla:</label>
                <div class="col-lg-9">
                    <div style="width:80%;float:left;">
                        <div style="display:none;" id="idFalla">${data['idFalla']}</div>
                        <input type="text" title="Falla" readonly
                            class="form-control texto buscador PiezaCE" id="nomFalla" value="${data['nomFalla']}">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:20%;float:right;padding:10px;">
                        <span title="Buscar Falla" class="fa fa-search BuscarFalla" 
                            style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Falla" class="fa fa-trash-o BorrarFalla" 
                            style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="estatusPieza" class="col-lg-3 col-form-label">Estatus:</label>
                <div class="col-lg-9">
                
                    <input readonly disabled type="text" maxlength="100"
                    class="form-control texto estatus" id="estatusPieza" value="${data['estatusPieza']}">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="ObservacionPieza" class="col-lg-3 col-form-label">Observaci&oacute;n:</label>
                <div class="col-lg-9">
                    <textarea  class="form-control texto obligatorio PiezaCE" rows="3"
                    style = "resize:vertical;" id="ObservacionPieza">${data['ObservacionPieza']}</textarea>
                    <div class="invalid-feedback">Campo Obligatorio</div>
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