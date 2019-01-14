
const TipoPiezaQuitar = "TipoPiezaQuitar";

$(function(){


    /************************************/
    /*      Inicio Buscadores           */
    /************************************/

    /************************************/
    /*      Manejo Tipo de Pieza        */
    /************************************/
    
    $('#SigmaModalFunciones').on('click','#nomTPQuitar',function(){
        BuscarTipoPieza(TipoPiezaQuitar);
    });

    $('#SigmaModalFunciones').on('click','.BuscarTPQuitar',function(){
        BuscarTipoPieza(TipoPiezaQuitar);
    });

    $('#SigmaModalFunciones').on('click','.BorrarTPQuitar',function(){
        $('#idTPQuitar').text('');
        $('#nomTPQuitar').val('');
    });

    /************************************/
    /*          Fin Buscadores          */
    /************************************/


    $('#agregarQP').on('click',function(){

        if($('#idBieCompatibilidad').text().trim() == ""){

            Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn btn-primary-sigma">
            <span class="fa fa-times-circle"></span>
            Cerrar
            </button>`;
    
            Cuerpo = "No se puede <strong>quitar Tipo de Pieza</strong> debido a que no se ha seleccionado un <strong>Bien</strong>.";
    
    
            var parametros = {
                "Titulo":"Advetencia",
                "Cuerpo": Cuerpo,
                "Botones":Botones
            }

            ModalAdvertencia(parametros);
        }else
            QuitarPieza();
    });

    $('#eliminarQP').on('click',function(){
        $('#TablaQuitarTipos .tr-activa-sigma').remove();
    });

    $('#TablaQuitarTipos').on('click','.editarQuitado',function(){

        //Se remueve la clase activa de la fila que esta activa
        $('.tr-activa-sigma').removeClass('tr-activa-sigma');

        var fila = $(this).parent('tr');
        //Se agrega la clase activa a la fila actual, esto para evitar
        //que se le quite la clase activa a una fila que esta activa
        //y se quiera editar (o sea, no se quiere quitar la seleccion)
        fila.addClass('tr-activa-sigma');

        //Se crea los botones que va a tener la ventana modal de edicion
        Botones = `
        <button type="submit" id ="GuardarEdicionQuitado" title="Guardar Quitado" type="button" style="margin:5px;" class="btn  btn-success">
          <span class="fa fa-floppy-o"></span>
          Guardar
        </button>
        <button data-dismiss="modal" id="CancelarEdicionQuitado" title="Cancelar Edici&oacute;n" type="button" style="margin:5px;" class="btn  btn-danger">
          <span class="fa fa-ban "></span>
          Cancelar
        </button>`;

        var data = {
            "Titulo"            : "Quitar Tipo de Pieza",
            "EstatusDoc"        : $('#EstatusCompatibilidad').val(),
            "Fila"              : $(this).parent('tr').index(),
            "Botones"           : Botones,
            "idTPQuitar"     : fila.find('td:eq(1)').text().trim(),
            "nomTPQuitar"    : fila.find('td:eq(2)').text().trim(),
            "InvQP"             : fila.find('td:eq(3)').text().trim(),
            "ObservacionQP"     : fila.find('td:eq(4)').text().trim()
        }
        SetModalFuncionesQuitado(data);
        
    });

    $('#TablaQuitarTipos tbody').on('click','tr',function(){
        ActivarCeldaTabla(this)
    });

    $('#SigmaModalFunciones').on('click','#CancelarEdicionQuitado',function(){
        ClearModalFunction();
    });

    $('#SigmaModalFunciones').on('click','#GuardarEdicionQuitado',function(){
        var Valido = true;

        $('#formEditarQuitado .Quitado').each(function(){
            $(this).removeClass('is-invalid');
            if($(this).hasClass('obligatorio') && $(this).val().trim() == ""){

                if(Valido)
                    $(this).focus();
                
                Valido = false;
                $(this).addClass('is-invalid');
            }
        })


        if(Valido){
            
            ValorActual =$('#idTPQuitar').text().trim();
            fila = $('#mhOptionR').text().trim();
            $("#TablaQuitarTipos").find('> tbody > tr').each(function () {

                if(fila != $(this).index() && ValorActual == $(this).find('td:eq(0)').text().trim()){
                    Valido = false;
                    $('#nomTPQuitar').focus(); 
                    $('#alertaModal').text('No se puede quitar el mismo tipo de pieza dos veces');
                    $('.contenedorAlertaModal').show();
                    return false;
                }
            });
        }

        if(Valido){

            var row = parseInt($('#mhOptionR').text());
            var fila = $('#TablaQuitarTipos tbody tr').eq(row);
            fila.find('td:eq(0)').text($('#idTPQuitar').text().trim());
            fila.find('td:eq(1)').text($('#nomTPQuitar').val().trim());
            fila.find('td:eq(2)').text($('#ObservacionQP').val().trim());
    
            CerrarFunciones();
        }
    });

    window.ObtenerJsonPQuitadas = function(){
        var Quitados = [];
        $("#TablaQuitarTipos").find('> tbody > tr').each(function () {
            if( $(this).find('td:eq(1)').text().trim() != ''){
                Quitados.push({ 
                    "IdPieza"       : $(this).find('td:eq(0)').text(),
                    "Observacion"   : $(this).find('td:eq(2)').text()
                });
            }
        });

        return Quitados;
    }

    window.ExisteQuitado = function(){
        var Existe = false;

        $("#TablaQuitarTipos").find('> tbody > tr').each(function () {
            if($(this).find('td:eq(1)').text().trim() != ''){
                Existe = true;

                //Salir del ciclo
                return false;
            }
        });

        return Existe;
    }

    window.ExistePiezaQuitada = function(Tpieza){
        Existe = false;
        $("#TablaQuitarTipos").find('> tbody > tr').each(function () {

            if(Tpieza == $(this).find('td:eq(1)').text().trim()){
                Existe = true;
                return false;
            }
        });

        return Existe;
    }

    window.QuitarPieza = function(){

        //Agregar registro a la tabla de listas desplegables al final
        $('#TablaQuitarTipos > tbody:last-child').append(`
            <tr>
                <td style="display:none;"></td>
                <td></td>
                <td style="display:none;"></td>
                <td colspan="2" class ="editarQuitado" style="text-align: center;cursor: pointer;">
                    <span class="fa fa-pencil fa-lg"></span>
                </td>
            </tr>
        `);
    }

    function SetModalFuncionesQuitado(data){

        //Se crea cuerpo html que va a tener la ventana modal de edicion
        var html = `
        <form class="form-horizontal" id="formEditarQuitado">

            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Tipo de Pieza:</label>
                <div class="col-lg-9">
                    <div style="width:80%;float:left;">
                        <div style="display:none;" id="idTPQuitar">${data['idTPQuitar']}</div>
                        <input type="text" title="Tipo de Pieza"  readonly
                            class="form-control texto obligatorio buscador Quitado" id="nomTPQuitar" value="${data['nomTPQuitar']}">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:20%;float:right;padding:10px;">
                        <span title="Buscar Tipo de Pieza" class="fa fa-search BuscarTPQuitar" 
                            style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Tipo de Pieza" class="fa fa-trash-o BorrarTPQuitar" 
                            style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="ObservacionQP" class="col-lg-3 col-form-label">Observaci&oacute;n:</label>
                <div class="col-lg-9">
                    <textarea  class="form-control texto" rows="3"
                    style = "resize:vertical;" id="ObservacionQP">${data['ObservacionQP']}</textarea>
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