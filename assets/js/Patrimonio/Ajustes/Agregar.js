
const PiezaAgregar = "PiezaAgregar";

$(function(){

    /************************************/
    /*      Inicio Buscadores           */
    /************************************/

    /************************************/
    /*      Manejo Pieza                */
    /************************************/
    
    $('#SiamaModalFunciones').on('click','#nomPiezaAgregar',function(){
        BuscarPieza(PiezaAgregar);
    });

    $('#SiamaModalFunciones').on('click','.BuscarPiezaAgregar',function(){
        BuscarPieza(PiezaAgregar);
    });

    $('#SiamaModalFunciones').on('click','.BorrarPiezaAgregar',function(){
        $('#idPiezaAgregar').text('');
        $('#nomPiezaAgregar').val('');
    });
    

    /************************************/
    /*          Fin Buscadores          */
    /************************************/


    $('#agregarAP').on('click',function(){

        if($('#idBieAjustes').text().trim() == ""){

            Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn btn-primary-siama">
            <span class="fa fa-times-circle"></span>
            Cerrar
            </button>`;
    
            Cuerpo = "No se puede <strong>Agregar Pieza</strong> debido a que no se ha seleccionado un <strong>Bien</strong>.";
    
    
            var parametros = {
                "Titulo":"Advetencia",
                "Cuerpo": Cuerpo,
                "Botones":Botones
            }

            ModalAdvertencia(parametros);
        }else
            AgregarPieza();
    });

    $('#eliminarAP').on('click',function(){
        $('#TablaAgregarPiezas .tr-activa-siama').remove();
    });
    
    $('#TablaAgregarPiezas').on('click','.editarAgregado',function(){

        //Se remueve la clase activa de la fila que esta activa
        $('.tr-activa-siama').removeClass('tr-activa-siama');

        var fila = $(this).parent('tr');
        //Se agrega la clase activa a la fila actual, esto para evitar
        //que se le quite la clase activa a una fila que esta activa
        //y se quiera editar (o sea, no se quiere quitar la seleccion)
        fila.addClass('tr-activa-siama');

        //Se crea los botones que va a tener la ventana modal de edicion
        Botones = `
        <button type="submit" id ="GuardarEdicionAgregado" title="Guardar Agregado de Pieza" type="button" style="margin:5px;" class="btn  btn-success">
          <span class="fa fa-floppy-o"></span>
          Guardar
        </button>
        <button data-dismiss="modal" id="CancelarEdicionAgregado" title="Cancelar Edici&oacute;n" type="button" style="margin:5px;" class="btn  btn-danger">
          <span class="fa fa-ban "></span>
          Cancelar
        </button>`;

        var data = {
            "Titulo"            : "Agregar Pieza",
            "EstatusDoc"        : $('#EstatusAjustes').val(),
            "Fila"              : $(this).parent('tr').index(),
            "Botones"           : Botones,
            "idPiezaAgregar"    : fila.find('td:eq(1)').text().trim(),
            "nomPiezaAgregar"   : fila.find('td:eq(2)').text().trim(),
            "InvAP"             : fila.find('td:eq(3)').text().trim(),
            "ObservacionAP"     : fila.find('td:eq(4)').text().trim()
        }

        SetModalFuncionesAgregado(data);

    });
    
    $('#TablaAgregarPiezas tbody').on('click','tr',function(){
        ActivarCeldaTabla(this)
    });

    $('#SiamaModalFunciones').on('click','#CancelarEdicionAgregado',function(){
        ClearModalFunction();
    });

    $('#SiamaModalFunciones').on('click','#GuardarEdicionAgregado',function(){
        var Valido = true;

        $('#formEditarAgregado .Agregado').each(function(){
            $(this).removeClass('is-invalid');
            if($(this).hasClass('obligatorio') && $(this).val().trim() == ""){

                if(Valido)
                    $(this).focus();
                
                Valido = false;
                $(this).addClass('is-invalid');
            }
        })


        //Validar que no se agregue dos veces la misma pieza
        if(Valido){
            
            ValorActual =$('#idPiezaAgregar').text().trim();
            fila = $('#mhOptionR').text().trim();
            $("#TablaAgregarPiezas").find('> tbody > tr').each(function () {

                if(fila != $(this).index() && ValorActual == $(this).find('td:eq(1)').text().trim()){
                    Valido = false;
                    document.getElementsByClassName("contenedorAlertaModal")[0].scrollIntoView();
                    $('#nomPiezaAgregar').focus(); 
                    $('#alertaModal').text('No se puede agregar la misma pieza dos veces');
                    $('.contenedorAlertaModal').show();
                    return false;
                }
            });
        }




        if(Valido){

            var row = parseInt($('#mhOptionR').text());
            var fila = $('#TablaAgregarPiezas tbody tr').eq(row);
            fila.find('td:eq(1)').text($('#idPiezaAgregar').text().trim());
            fila.find('td:eq(2)').text($('#nomPiezaAgregar').val().trim());
            fila.find('td:eq(3)').text($('#InvAP').text().trim());
            fila.find('td:eq(4)').text($('#ObservacionAP').val().trim());
    
            CerrarFunciones();
        }
    });
    

    window.ObtenerJsonPAgregadas = function(){
        var Agregados = [];
        $("#TablaAgregarPiezas").find('> tbody > tr').each(function () {
    
            if( $(this).find('td:eq(1)').text().trim() != ''){
                Agregados.push({ 
                    "Id"            : $(this).find('td:eq(0)').text(),
                    "IdPieza"       : $(this).find('td:eq(1)').text(),
                    "Observacion"   : $(this).find('td:eq(4)').text()
                });
            }
        });

        return Agregados;
    }

    window.ExisteAgregado = function(){
        var Existe = false;

        $("#TablaAgregarPiezas").find('> tbody > tr').each(function () {
            if($(this).find('td:eq(1)').text().trim() != ''){
                Existe = true;

                //Salir del ciclo
                return false;
            }
        });

        return Existe;
    }
    
    window.AgregarPieza = function(){
        //Agregar registro a la tabla de listas desplegables al final
        $('#TablaAgregarPiezas > tbody:last-child').append(`
            <tr>
                <td style="display:none;"></td>
                <td style="display:none;"></td>
                <td></td>
                <td></td>
                <td style="display:none;"></td>
                <td colspan="2" class ="editarAgregado" style="text-align: center;cursor: pointer;">
                    <span class="fa fa-pencil fa-lg"></span>
                </td>
            </tr>
        `);
    }

    window.ExistePiezaAgregada = function(pieza){
        Existe = false;
        $("#TablaAgregarPiezas").find('> tbody > tr').each(function () {
            if(pieza == $(this).find('td:eq(1)').text().trim()){
                Existe = true;
                return false;
            }
        });

        return Existe;
    }

    function SetModalFuncionesAgregado(data){

        //Se crea cuerpo html que va a tener la ventana modal de edicion
        var html = `
        <form class="form-horizontal" id="formEditarAgregado">
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Pieza:</label>
                <div class="col-md-9">
                    <div style="width:80%;float:left;">
                        <div style="display:none;" id="idPiezaAgregar">${data['idPiezaAgregar']}</div>
                        <div style="display:none;" id="InvAP">${data['InvAP']}</div>
                        <input type="text" title="Pieza" readonly
                            class="form-control texto obligatorio buscador Agregado" id="nomPiezaAgregar" value="${data['nomPiezaAgregar']}">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:20%;float:right;padding:10px;">
                        <span title="Buscar Pieza Dañada" class="fa fa-search BuscarPiezaAgregar" 
                            style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Pieza Dañada" class="fa fa-trash-o BorrarPiezaAgregar" 
                            style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="ObservacionAP" class="col-lg-3 col-form-label">Observaci&oacute;n:</label>
                <div class="col-lg-9">
                    <textarea  class="form-control texto" rows="3"
                    style = "resize:vertical;" id="ObservacionAP">${data['ObservacionAP']}</textarea>
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