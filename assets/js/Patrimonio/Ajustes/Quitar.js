
const PiezaQuitar = "PiezaQuitar";

$(function(){


    /************************************/
    /*      Inicio Buscadores           */
    /************************************/

    /************************************/
    /*      Manejo Pieza                */
    /************************************/
    
    $('#SigmaModalFunciones').on('click','#nomPiezaQuitar',function(){
        BuscarPieza(PiezaQuitar);
    });

    $('#SigmaModalFunciones').on('click','.BuscarPiezaQuitar',function(){
        BuscarPieza(PiezaQuitar);
    });

    $('#SigmaModalFunciones').on('click','.BorrarPiezaQuitar',function(){
        $('#idPiezaQuitar').text('');
        $('#nomPiezaQuitar').val('');
    });

    /************************************/
    /*          Fin Buscadores          */
    /************************************/


    $('#agregarQP').on('click',function(){

        if($('#idBieAjustes').text().trim() == ""){

            Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn btn-primary-sigma">
            <span class="fa fa-times-circle"></span>
            Cerrar
            </button>`;
    
            Cuerpo = "No se puede <strong>Quitar Pieza</strong> debido a que no se ha seleccionado un <strong>Bien</strong>.";
    
    
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
        $('#TablaQuitarPiezas .tr-activa-sigma').remove();
    });

    $('#TablaQuitarPiezas').on('click','.editarQuitado',function(){

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
            "Titulo"            : "Quitar Pieza",
            "EstatusDoc"        : $('#EstatusAjustes').val(),
            "Fila"              : $(this).parent('tr').index(),
            "Botones"           : Botones,
            "idPiezaQuitar"     : fila.find('td:eq(1)').text().trim(),
            "nomPiezaQuitar"    : fila.find('td:eq(2)').text().trim(),
            "InvQP"             : fila.find('td:eq(3)').text().trim(),
            "ObservacionQP"     : fila.find('td:eq(4)').text().trim()
        }
        SetModalFuncionesQuitado(data);
        
    });

    $('#TablaQuitarPiezas tbody').on('click','tr',function(){
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

        //Validar que no se le haga un mantenimiento correctivo a una pieza
        //dañada dos veces en el mismo mantenimiento
        if(Valido){
            
            ValorActual =$('#idPiezaQuitar').text().trim();
            fila = $('#mhOptionR').text().trim();
            $("#TablaQuitarPiezas").find('> tbody > tr').each(function () {

                if(fila != $(this).index() && ValorActual == $(this).find('td:eq(1)').text().trim()){
                    Valido = false;
                    $('#nomPiezaQuitar').focus(); 
                    $('#alertaModal').text('No se puede quitar la misma pieza dos veces');
                    $('.contenedorAlertaModal').show();
                    return false;
                }
            });
        }

        if(Valido){

            var row = parseInt($('#mhOptionR').text());
            var fila = $('#TablaQuitarPiezas tbody tr').eq(row);
            fila.find('td:eq(1)').text($('#idPiezaQuitar').text().trim());
            fila.find('td:eq(2)').text($('#nomPiezaQuitar').val().trim());
            fila.find('td:eq(3)').text($('#InvQP').text().trim());
            fila.find('td:eq(4)').text($('#ObservacionQP').val().trim());
            fila.find('td:eq(5)').text($('#TipoQP').text().trim());
    
            CerrarFunciones();
        }
    });

    window.ObtenerJsonPQuitadas = function(){
        var Quitados = [];
        $("#TablaQuitarPiezas").find('> tbody > tr').each(function () {
            if( $(this).find('td:eq(1)').text().trim() != ''){
                Quitados.push({ 
                    "Id"            : $(this).find('td:eq(0)').text(),
                    "IdPieza"       : $(this).find('td:eq(1)').text(),
                    "Observacion"   : $(this).find('td:eq(4)').text()
                });
            }
        });

        return Quitados;
    }

    window.ExisteQuitado = function(){
        var Existe = false;

        $("#TablaQuitarPiezas").find('> tbody > tr').each(function () {
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
        $("#TablaQuitarPiezas").find('> tbody > tr').each(function () {

            if(pieza == $(this).find('td:eq(1)').text().trim()){
                Existe = true;
                return false;
            }
        });

        return Existe;
    }

    window.QuitarPieza = function(){

        //Agregar registro a la tabla de listas desplegables al final
        $('#TablaQuitarPiezas > tbody:last-child').append(`
            <tr>
                <td style="display:none;"></td>
                <td style="display:none;"></td>
                <td></td>
                <td></td>
                <td style="display:none;"></td>
                <td></td>
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
                <label class="col-lg-3 col-form-label">Pieza :</label>
                <div class="col-lg-9">
                    <div style="width:80%;float:left;">
                        <div style="display:none;" id="idPiezaQuitar">${data['idPiezaQuitar']}</div>
                        <div style="display:none;" id="InvQP">${data['InvQP']}</div>
                        <div style="display:none;" id="TipoQP">${data['TipoQP']}</div>
                        <input type="text" title="Pieza"  readonly
                            class="form-control texto obligatorio buscador Quitado" id="nomPiezaQuitar" value="${data['nomPiezaQuitar']}">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:20%;float:right;padding:10px;">
                        <span title="Buscar Pieza Dañada" class="fa fa-search BuscarPiezaQuitar" 
                            style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Pieza Dañada" class="fa fa-trash-o BorrarPiezaQuitar" 
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