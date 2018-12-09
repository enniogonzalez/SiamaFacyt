
const TipoPiezaAgregar = "TipoPiezaAgregar";

$(function(){

    /************************************/
    /*      Inicio Buscadores           */
    /************************************/

    /************************************/
    /*      Manejo Tipo de Pieza        */
    /************************************/
    
    $('#SiamaModalFunciones').on('click','#nomTPAgregar',function(){
        BuscarTipoPieza(TipoPiezaAgregar);
    });

    $('#SiamaModalFunciones').on('click','.BuscarTPAgregar',function(){
        BuscarTipoPieza(TipoPiezaAgregar);
    });

    $('#SiamaModalFunciones').on('click','.BorrarTPAgregar',function(){
        $('#idTPAgrega').text('');
        $('#nomTPAgregar').val('');
    });
    

    /************************************/
    /*          Fin Buscadores          */
    /************************************/


    $('#agregarAP').on('click',function(){

        if($('#idBieCompatibilidad').text().trim() == ""){

            Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn btn-primary-siama">
            <span class="fa fa-times-circle"></span>
            Cerrar
            </button>`;
    
            Cuerpo = "No se puede <strong>agregar Tipo de Pieza</strong> debido a que no se ha seleccionado un <strong>Bien</strong>.";
    
    
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
        $('#TablaAgregarTipos .tr-activa-siama').remove();
    });
    
    $('#TablaAgregarTipos').on('click','.editarAgregado',function(){

        //Se remueve la clase activa de la fila que esta activa
        $('.tr-activa-siama').removeClass('tr-activa-siama');

        var fila = $(this).parent('tr');
        //Se agrega la clase activa a la fila actual, esto para evitar
        //que se le quite la clase activa a una fila que esta activa
        //y se quiera editar (o sea, no se quiere quitar la seleccion)
        fila.addClass('tr-activa-siama');

        //Se crea los botones que va a tener la ventana modal de edicion
        Botones = `
        <button type="submit" id ="GuardarEdicionAgregado" title="Guardar Agregado" type="button" style="margin:5px;" class="btn  btn-success">
          <span class="fa fa-floppy-o"></span>
          Guardar
        </button>
        <button data-dismiss="modal" id="CancelarEdicionAgregado" title="Cancelar Edici&oacute;n" type="button" style="margin:5px;" class="btn  btn-danger">
          <span class="fa fa-ban "></span>
          Cancelar
        </button>`;

        var data = {
            "Titulo"        : "Agregar Tipo de Pieza",
            "EstatusDoc"    : $('#EstatusCompatibilidad').val(),
            "Fila"          : $(this).parent('tr').index(),
            "Botones"       : Botones,
            "idTPAgrega"    : fila.find('td:eq(0)').text().trim(),
            "nomTPAgregar"  : fila.find('td:eq(1)').text().trim(),
            "ObservacionTP" : fila.find('td:eq(2)').text().trim()
        }

        SetModalFuncionesAgregado(data);
    });
    
    $('#TablaAgregarTipos tbody').on('click','tr',function(){
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


        if(Valido){
            
            ValorActual =$('#idTPAgrega').text().trim();
            fila = $('#mhOptionR').text().trim();
            $("#TablaAgregarTipos").find('> tbody > tr').each(function () {

                if(fila != $(this).index() && ValorActual == $(this).find('td:eq(0)').text().trim()){
                    Valido = false;
                    document.getElementsByClassName("contenedorAlertaModal")[0].scrollIntoView();
                    $('#nomTPAgregar').focus(); 
                    $('#alertaModal').text('No se puede agregar el mismo tipo de pieza dos veces');
                    $('.contenedorAlertaModal').show();
                    return false;
                }
            });
        }

        if(Valido){

            var row = parseInt($('#mhOptionR').text());
            var fila = $('#TablaAgregarTipos tbody tr').eq(row);
            fila.find('td:eq(0)').text($('#idTPAgrega').text().trim());
            fila.find('td:eq(1)').text($('#nomTPAgregar').val().trim());
            fila.find('td:eq(2)').text($('#ObservacionTP').val().trim());
    
            CerrarFunciones();
        }
    });
    
    window.ObtenerJsonPAgregadas = function(){
        var Agregados = [];
        $("#TablaAgregarTipos").find('> tbody > tr').each(function () {
    
            if( $(this).find('td:eq(1)').text().trim() != ''){
                Agregados.push({ 
                    "IdTipoPieza"   : $(this).find('td:eq(0)').text(),
                    "Observacion"   : $(this).find('td:eq(2)').text()
                });
            }
        });

        return Agregados;
    }

    window.ExisteAgregado = function(){
        var Existe = false;

        $("#TablaAgregarTipos").find('> tbody > tr').each(function () {
            if($(this).find('td:eq(0)').text().trim() != ''){
                Existe = true;

                //Salir del ciclo
                return false;
            }
        });

        return Existe;
    }
    
    window.AgregarPieza = function(){
        
        $('#TablaAgregarTipos > tbody:last-child').append(`
            <tr>
                <td style="display:none;"></td>
                <td></td>
                <td style="display:none;"></td>
                <td colspan="2" class ="editarAgregado" style="text-align: center;cursor: pointer;">
                    <span class="fa fa-pencil fa-lg"></span>
                </td>
            </tr>
        `);
    }

    function SetModalFuncionesAgregado(data){

        //Se crea cuerpo html que va a tener la ventana modal de edicion
        var html = `
        <form class="form-horizontal" id="formEditarAgregado">
            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Tipo de Pieza:</label>
                <div class="col-lg-9">
                    <div style="width:80%;float:left;">
                        <div style="display:none;" id="idTPAgrega">${data['idTPAgrega']}</div>
                        <input type="text" title="Tipo de Pieza" readonly
                            class="form-control texto obligatorio buscador Agregado" id="nomTPAgregar" value="${data['nomTPAgregar']}">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:20%;float:right;padding:10px;">
                        <span title="Buscar Tipo de Pieza" class="fa fa-search BuscarTPAgregar" 
                            style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Tipo de Pieza" class="fa fa-trash-o BorrarTPAgregar" 
                            style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="ObservacionTP" class="col-lg-3 col-form-label">Observaci&oacute;n:</label>
                <div class="col-lg-9">
                    <textarea  class="form-control texto" rows="3"
                    style = "resize:vertical;" id="ObservacionTP">${data['ObservacionTP']}</textarea>
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