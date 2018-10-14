
const Pieza = "Pieza";

$(function(){

    $('#SiamaModalFunciones').on('keypress','.buscador',function(){
        return false;
    })

    /************************************/
    /*      Inicio Buscadores           */
    /************************************/

    /************************************/
    /*      Manejo Pieza                */
    /************************************/
    
    $('#SiamaModalFunciones').on('click','#nomPiezaTarea',function(){
        BuscarPieza(Pieza);
    });

    $('#SiamaModalFunciones').on('click','.BuscarPiezaTarea',function(){
        BuscarPieza(Pieza);
    });

    $('#SiamaModalFunciones').on('click','.BorrarPiezaTarea',function(){
        $('#idPiezaTarea').text('');
        $('#nomPiezaTarea').val('');
    });

    /************************************/
    /*          Fin Buscadores          */
    /************************************/


    $('#agregarTarea').on('click',function(){

        if($('#idBiePlantilla').text().trim() == ""){

            Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn btn-primary-siama">
            <span class="fa fa-times-circle"></span>
            Cerrar
            </button>`;
    
            Cuerpo = "No se puede agregar una <strong>Tarea</strong> debido a que no se ha seleccionado un <strong>Bien</strong>.";
    
    
            var parametros = {
                "Titulo":"Advetencia",
                "Cuerpo": Cuerpo,
                "Botones":Botones
            }

            ModalAdvertencia(parametros);
        }else
            agregarTareaPlantilla();
    });

    $('#eliminarTarea').on('click',function(){
        $('#TablaTareasPlantilla .tr-activa-siama').remove();
    });
    
    $('#TablaTareasPlantilla').on('click','.editarTarea',function(){

        //Se remueve la clase activa de la fila que esta activa
        $('.tr-activa-siama').removeClass('tr-activa-siama');

        var fila = $(this).parent('tr');
        //Se agrega la clase activa a la fila actual, esto para evitar
        //que se le quite la clase activa a una fila que esta activa
        //y se quiera editar (o sea, no se quiere quitar la seleccion)
        fila.addClass('tr-activa-siama');

        //Se crea los botones que va a tener la ventana modal de edicion
        Botones = `
        <button type="submit" id ="GuardarEdicionTarea" title="Guardar Tarea" type="button" style="margin:5px;" class="btn  btn-success">
          <span class="fa fa-floppy-o"></span>
          Guardar
        </button>
        <button data-dismiss="modal" id="CancelarEdicionTarea" title="Cancelar Edici&oacute;n" type="button" style="margin:5px;" class="btn  btn-danger">
          <span class="fa fa-ban "></span>
          Cancelar
        </button>`;

        var data = {
            "Titulo"        :   "Editar Tarea",
            "EstatusDoc"    :   $('#EstatusPlantilla').val(),
            "Fila"          :   $(this).parent('tr').index(),
            "Botones"       :   Botones,
            "idPiezaTarea"  :   fila.find('td:eq(1)').text().trim(),
            "nomPiezaTarea" :   fila.find('td:eq(2)').text().trim(),
            "TituloCambio"  :   fila.find('td:eq(3)').text().trim(),
            "MinutosEst"    :   fila.find('td:eq(4)').text().trim(),
            "HerramientasT" :   fila.find('td:eq(5)').text().trim(),
            "DescripcionT"  :   fila.find('td:eq(6)').text().trim(),
            "ObservacionT"  :   fila.find('td:eq(7)').text().trim()
        }

        SetModalFuncionesCambios(data);

        if($('#EstatusPlantilla').val() != "Solicitado"){
            setTimeout(function(){$('#ObservacionT').focus();}, 400);
        }
    });

    $('#TablaTareasPlantilla tbody').on('click','tr',function(){
        ActivarCeldaTabla(this)
    });

    $('#SiamaModalFunciones').on('click','#CancelarEdicionTarea',function(){
        ClearModalFunction();
    });

    $('#SiamaModalFunciones').on('click','#GuardarEdicionTarea',function(){
        var Valido = true;

        $('#formEditarTarea .Tarea').each(function(){
            $(this).removeClass('is-invalid');
            if($(this).hasClass('obligatorio') && $(this).val().trim() == ""){

                if(Valido)
                    $(this).focus();
                
                Valido = false;
                $(this).addClass('is-invalid');
            }
        })

        if(Valido && $('#MinutosEst').val() < 0){
            Valido = false;
            $('#alertaModal').text('Los minutos estimados tienen que se un numero mayor a 0.');
            $('.contenedorAlertaModal').show();
            
            document.getElementsByClassName("contenedorAlertaModal")[0].scrollIntoView();
        }

        if(Valido){

            var row = parseInt($('#mhOptionR').text());
            var fila = $('#TablaTareasPlantilla tbody tr').eq(row);
            fila.find('td:eq(1)').text($('#idPiezaTarea').text().trim());
            fila.find('td:eq(2)').text($('#nomPiezaTarea').val().trim());
            fila.find('td:eq(3)').text($('#TituloCambio').val().trim());
            fila.find('td:eq(4)').text($('#MinutosEst').val().trim());
            fila.find('td:eq(5)').text($('#HerramientasT').val().trim());
            fila.find('td:eq(6)').text($('#DescripcionT').val().trim());
            fila.find('td:eq(7)').text($('#ObservacionT').val().trim());
    
            CerrarFunciones();
        }
    });
    
    window.ObtenerJsonTareas = function(){
        var Tareas = [];
        var estatuDoc = $('#EstatusPlantilla').val().trim();
        $("#TablaTareasPlantilla").find('> tbody > tr').each(function () {
    
            if( $(this).find('td:eq(1)').text().trim() != ''){
                Tareas.push({ 
                    "Id"            : $(this).find('td:eq(0)').text(),
                    "IdPieza"       : $(this).find('td:eq(1)').text(),
                    "Titulo"        : $(this).find('td:eq(3)').text(),
                    "Minutos"       : $(this).find('td:eq(4)').text(),
                    "Herramientas"  : $(this).find('td:eq(5)').text(),
                    "Descripcion"   : $(this).find('td:eq(6)').text(),
                    "Observacion"   : $(this).find('td:eq(7)').text()
                });
            }
        });

        return Tareas;
    }

    window.ExisteTarea = function(){
        var Existe = false;

        $("#TablaTareasPlantilla").find('> tbody > tr').each(function () {
            if($(this).find('td:eq(1)').text().trim() != ''){
                Existe = true;

                //Salir del ciclo
                return false;
            }
        });

        return Existe;
    }
    
    window.agregarTareaPlantilla = function(){
        //Agregar registro a la tabla de listas desplegables al final
        $('#TablaTareasPlantilla > tbody:last-child').append(`
            <tr>
                <td style="display:none;"></td>
                <td style="display:none;"></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="display:none;"></td>
                <td style="display:none;"></td>
                <td style="display:none;"></td>
                <td colspan="2" class ="editarTarea" style="text-align: center;cursor: pointer;">
                    <span class="fa fa-pencil fa-lg"></span>
                </td>
            </tr>
        `);
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
        <form class="form-horizontal" id="formEditarTarea">
            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Pieza:</label>
                <div class="col-lg-9">
                    <div style="width:80%;float:left;">
                        <div style="display:none;" id="idPiezaTarea">${data['idPiezaTarea']}</div>
                        <input type="text" title="Pieza" 
                            class="form-control texto obligatorio buscador Tarea" id="nomPiezaTarea" value="${data['nomPiezaTarea']}">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:20%;float:right;padding:10px;">
                        <span title="Buscar Pieza" class="fa fa-search BuscarPiezaTarea" 
                            style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Pieza" class="fa fa-trash-o BorrarPiezaTarea" 
                            style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            
            <div class="form-group row">
                <label for="TituloCambio" class="col-lg-3 col-form-label">Titulo:</label>
                <div class="col-lg-9">
                    <input type="text" maxlenght="20"
                    class="form-control obligatorio Tarea"  id="TituloCambio" value="${data['TituloCambio']}">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            
            <div class="form-group row">
                <label for="MinutosEst" class="col-lg-3 col-form-label">Minutos Estimados:</label>
                <div class="col-lg-9">
                    <input type="number" step = "1" min = "0"
                    class="form-control obligatorio Tarea"  id="MinutosEst" value="${data['MinutosEst']}">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="HerramientasT" class="col-lg-3 col-form-label">Herramientas:</label>
                <div class="col-lg-9">
                    <textarea  class="form-control texto obligatorio Tarea" rows="3"
                    style = "resize:vertical;" id="HerramientasT">${data['HerramientasT']}</textarea>
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="DescripcionT" class="col-lg-3 col-form-label">Descripci&oacute;n:</label>
                <div class="col-lg-9">
                    <textarea  class="form-control texto obligatorio Tarea" rows="3"
                    style = "resize:vertical;" id="DescripcionT">${data['DescripcionT']}</textarea>
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="ObservacionT" class="col-lg-3 col-form-label">Observaci&oacute;n:</label>
                <div class="col-lg-9">
                    <textarea  class="form-control texto" rows="3"
                    style = "resize:vertical;" id="ObservacionT">${data['ObservacionT']}</textarea>
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