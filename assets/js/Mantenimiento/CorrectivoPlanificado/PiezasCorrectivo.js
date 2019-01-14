
const PiezaDA = "PiezaDA";
const Falla = "Falla";

$(function(){

    /************************************/
    /*      Inicio Buscadores           */
    /************************************/

    /************************************/
    /*          Manejo Pieza            */
    /************************************/
    
    $('#SigmaModalFunciones').on('click','#nomPiezaDA',function(){
        BuscarPieza(PiezaDA);
    });

    $('#SigmaModalFunciones').on('click','.BuscarPiezaDA',function(){
        BuscarPieza(PiezaDA);
    });

    $('#SigmaModalFunciones').on('click','.BorrarPiezaDA',function(){
        $('#idPieza').text('');
        $('#nomPiezaDA').val('');
    });

    /************************************/
    /*          Manejo Falla            */
    /************************************/
    
    $('#SigmaModalFunciones').on('click','#nomFalla',function(){
        BuscarFalla(Falla);
    });

    $('#SigmaModalFunciones').on('click','.BuscarFalla',function(){
        BuscarFalla(Falla);
    });

    $('#SigmaModalFunciones').on('click','.BorrarFalla',function(){
        $('#idFalla').text('');
        $('#nomFalla').val('');
    });

    /************************************/
    /*          Fin Buscadores          */
    /************************************/


    $('#agregarPieza').on('click',function(){

        valor = $('#OrigenPlanificado').val().trim();

        Botones = `
        <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn btn-primary-sigma">
        <span class="fa fa-times-circle"></span>
        Cerrar
        </button>`;

        valido = true;

        if(valor == ""){
            valido = false;
            Cuerpo = "No se puede <strong>agregar pieza</strong> debido a que no se ha seleccionado un <strong>Origen</strong>.";   
        }else if(valor == "Mantenimiento Preventivo" && $('#idPreventivo').text().trim() == ""){
            valido = false;
            Cuerpo = "No se puede <strong>agregar pieza</strong> debido a que no se ha seleccionado un <strong>Mantenimiento Preventivo</strong>.";   
        }else if(valor == "Mantenimiento Correctivo" && $('#idCorrectivo').text().trim() == ""){
            valido = false;
            Cuerpo = "No se puede <strong>agregar pieza</strong> debido a que no se ha seleccionado un <strong>Mantenimiento Correctivo</strong>.";   
        }else
            AgregarPieza();

        if(!valido){
            var parametros = {
                "Titulo":"Advetencia",
                "Cuerpo": Cuerpo,
                "Botones":Botones
            }

            ModalAdvertencia(parametros);
        }
    });

    $('#eliminarPieza').on('click',function(){
        $('#TablaPiezasDañadas .tr-activa-sigma').remove();
    });

    $('#SigmaModalFunciones').on('click','#CancelarEdicionPiezaDA',function(){
        ClearModalFunction();
    });

    $('#SigmaModalFunciones').on('click','#GuardarEdicionPiezaDA',function(){
        var Valido = true;

        $('#formEditarPiezaDA .PiezaDA').each(function(){
            $(this).removeClass('is-invalid');

            if($(this).hasClass('obligatorio') && $(this).val().trim() == ""){

                if(Valido)
                    $(this).focus();
                
                Valido = false;
                $(this).addClass('is-invalid');
            }
        })


        if(Valido){
            
            ValorActual =$('#idPieza').text().trim();
            fila = $('#mhOptionR').text().trim();
            $("#TablaPiezasDañadas").find('> tbody > tr').each(function () {

                if(fila != $(this).index() && ValorActual == $(this).find('td:eq(1)').text().trim()){
                    Valido = false;
                    $('#nomPiezaDA').focus(); 
                    $('#alertaModal').text('No se puede seleccionar la misma pieza dos veces');
                    $('.contenedorAlertaModal').show();
                    return false;
                }
            });
        }

        if(Valido){

            var row = parseInt($('#mhOptionR').text());
            var fila = $('#TablaPiezasDañadas tbody tr').eq(row);
            fila.find('td:eq(0)').text($('#idPieza').text().trim());
            fila.find('td:eq(1)').text($('#nomPiezaDA').val().trim());
            fila.find('td:eq(2)').text($('#idFalla').text().trim());
            fila.find('td:eq(3)').text($('#nomFalla').val().trim());
            fila.find('td:eq(4)').text($('#ObservacionPieza').val().trim());
    
            CerrarFunciones();
        }
    });

    $('#TablaPiezasDañadas tbody').on('click','tr',function(){
        ActivarCeldaTabla(this)
    });

    $('#TablaPiezasDañadas').on('click','.editarPiezaDA',function(){

        //Se remueve la clase activa de la fila que esta activa
        $('.tr-activa-sigma').removeClass('tr-activa-sigma');

        var fila = $(this).parent('tr');

        //Se agrega la clase activa a la fila actual, esto para evitar
        //que se le quite la clase activa a una fila que esta activa
        //y se quiera editar (o sea, no se quiere quitar la seleccion)
        fila.addClass('tr-activa-sigma');

        //Se crea los botones que va a tener la ventana modal de edicion
        Botones = `
        <button type="submit" id ="GuardarEdicionPiezaDA" title="Guardar Pieza" type="button" style="margin:5px;" class="btn  btn-success">
          <span class="fa fa-floppy-o"></span>
          Guardar
        </button>
        <button data-dismiss="modal" id="CancelarEdicionPiezaDA" title="Cancelar Edici&oacute;n" type="button" style="margin:5px;" class="btn  btn-danger">
          <span class="fa fa-ban "></span>
          Cancelar
        </button>`;

        var data = {
            "Titulo"            : "Pieza",
            "EstatusDoc"        : $('#EstatusPlanificado').val(),
            "Fila"              : $(this).parent('tr').index(),
            "Botones"           : Botones,
            "idPieza"           : fila.find('td:eq(0)').text().trim(),
            "nomPiezaDA"        : fila.find('td:eq(1)').text().trim(),
            "idFalla"           : fila.find('td:eq(2)').text().trim(),
            "nomFalla"          : fila.find('td:eq(3)').text().trim(),
            "ObservacionPieza"  : fila.find('td:eq(4)').text().trim(),
        }
        SetModalFuncionesPiezaDA(data);
        
    });

    function SetModalFuncionesPiezaDA(data){

        //Se crea cuerpo html que va a tener la ventana modal de edicion
        var html = `
        <form class="form-horizontal" id="formEditarPiezaDA">

            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Pieza:</label>
                <div class="col-lg-9">
                    <div style="width:80%;float:left;">
                        <div style="display:none;" id="idPieza">${data['idPieza']}</div>
                        <input type="text" title="Pieza" 
                            class="form-control texto obligatorio buscador PiezaDA" id="nomPiezaDA" value="${data['nomPiezaDA']}">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:20%;float:right;padding:10px;">
                        <span title="Buscar Pieza Dañada" class="fa fa-search BuscarPiezaDA" 
                            style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Pieza Dañada" class="fa fa-trash-o BorrarPiezaDA" 
                            style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Falla:</label>
                <div class="col-lg-9">
                    <div style="width:80%;float:left;">
                        <div style="display:none;" id="idFalla">${data['idFalla']}</div>
                        <input type="text" title="Falla" 
                            class="form-control texto obligatorio buscador PiezaDA" id="nomFalla" value="${data['nomFalla']}">
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
                <label for="ObservacionPieza" class="col-lg-3 col-form-label">Observaci&oacute;n:</label>
                <div class="col-lg-9">
                    <textarea  class="form-control texto obligatorio PiezaDA" rows="3"
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

    window.AgregarPieza = function(){
        $('#TablaPiezasDañadas > tbody:last-child').append(`
            <tr>
                <td style="display:none;"></td>
                <td></td>
                <td style="display:none;"></td>
                <td></td>
                <td style="display:none;"></td>
                <td colspan="2" class ="editarPiezaDA" style="text-align: center;cursor: pointer;">
                    <span class="fa fa-pencil fa-lg"></span>
                </td>
            </tr>
        `);
    }

    window.ExistePiezaDA = function(){
        var Existe = false;

        $("#TablaPiezasDañadas").find('> tbody > tr').each(function () {
            if($(this).find('td:eq(1)').text().trim() != ''){
                Existe = true;

                //Salir del ciclo
                return false;
            }
        });

        return Existe;
    }

    window.ObtenerJsonPiezasDA = function(){
        var PiezaDAs = [];
        $("#TablaPiezasDañadas").find('> tbody > tr').each(function () {
            if( $(this).find('td:eq(1)').text().trim() != ''){
                PiezaDAs.push({ 
                    "pie_id"        : $(this).find('td:eq(0)').text(),
                    "fal_id"        : $(this).find('td:eq(2)').text(),
                    "Observacion"   : $(this).find('td:eq(4)').text(),
                });
            }
        });

        return PiezaDAs;
    }

});