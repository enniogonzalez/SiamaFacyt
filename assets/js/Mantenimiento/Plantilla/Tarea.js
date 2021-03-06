
const TipoPieza = "TipoPieza";
const Herramienta = "Herramienta";

$(function(){

    $('#SigmaModalFunciones').on('keypress','.buscador',function(){
        return false;
    })

    /************************************/
    /*      Inicio Buscadores           */
    /************************************/

    /************************************/
    /*      Manejo TipoPieza                */
    /************************************/
    
    $('#SigmaModalFunciones').on('click','#nomTPTarea',function(){
        BuscarTipoPieza(TipoPieza);
    });

    $('#SigmaModalFunciones').on('click','.BuscarTPTarea',function(){
        BuscarTipoPieza(TipoPieza);
    });

    $('#SigmaModalFunciones').on('click','.BorrarTPTarea',function(){
        $('#idTPTarea').text('');
        $('#nomTPTarea').val('');
    });

    /************************************/
    /*          Fin Buscadores          */
    /************************************/


    $('#agregarTarea').on('click',function(){

        if($('#idBiePlantilla').text().trim() == ""){

            Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn btn-primary-sigma">
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
        $('#TablaTareasPlantilla .tr-activa-sigma').remove();
    });
    
    $('#TablaTareasPlantilla').on('click','.editarTarea',function(){

        //Se remueve la clase activa de la fila que esta activa
        $('.tr-activa-sigma').removeClass('tr-activa-sigma');

        var fila = $(this).parent('tr');
        //Se agrega la clase activa a la fila actual, esto para evitar
        //que se le quite la clase activa a una fila que esta activa
        //y se quiera editar (o sea, no se quiere quitar la seleccion)
        fila.addClass('tr-activa-sigma');

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

        trHerramienta = '';
        if(fila.find('td:eq(5)').text().trim() != ""){
            arrayHerramienta = JSON.parse(fila.find('td:eq(5)').text().trim());

            for(her in arrayHerramienta){
                trHerramienta += `
                    <tr>
                        <td style="display:none;">${arrayHerramienta[her].Id}</td>
                        <td>${arrayHerramienta[her].Herramienta}</td>
                        <td colspan="2" class ="editarHerramienta" style="text-align: center;cursor: pointer;">
                            <span class="fa fa-pencil fa-lg"></span>
                        </td>
                    </tr>
                `;                
            }
        }

        var data = {
            "Titulo"        :   "Editar Tarea",
            "EstatusDoc"    :   $('#EstatusPlantilla').val(),
            "Fila"          :   $(this).parent('tr').index(),
            "Botones"       :   Botones,
            "idTPTarea"     :   fila.find('td:eq(1)').text().trim(),
            "nomTPTarea"    :   fila.find('td:eq(2)').text().trim(),
            "TituloPlantilla"  :   fila.find('td:eq(3)').text().trim(),
            "HorasEst"    :   fila.find('td:eq(4)').text().trim(),
            "HerramientasT" :   trHerramienta,
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

    $('#SigmaModalFunciones').on('click','#agregarHerramienta',function(){

        $('#TablaTareasHerramientas > tbody:last-child').append(`
            <tr>
                <td style="display:none;"></td>
                <td></td>
                <td colspan="2" class ="editarHerramienta" style="text-align: center;cursor: pointer;">
                    <span class="fa fa-pencil fa-lg"></span>
                </td>
            </tr>
        `);

    });

    $('#SigmaModalFunciones').on('click','#CancelarEdicionTarea',function(){
        ClearModalFunction();
    });

    $('#SigmaModalFunciones').on('click','#eliminarHerramienta',function(){
        $('#TablaTareasHerramientas .tr-activa-sigma').remove();
    });

    $('#SigmaModalFunciones').on('click','#GuardarEdicionTarea',function(){
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

        if(Valido && $('#HorasEst').val() <= 0){
            Valido = false;
            $('#alertaModal').text('Los minutos estimados tienen que se un numero mayor a 0.');
            $('.contenedorAlertaModal').show();
            
            document.getElementsByClassName("contenedorAlertaModal")[0].scrollIntoView();
        }

        if(Valido && !ExisteHerramienta()){
            Valido = false;
            $('#alertaModal').text('No se puede guardar una tarea sin herramientas asignadas.');
            $('.contenedorAlertaModal').show();
            
            document.getElementsByClassName("contenedorAlertaModal")[0].scrollIntoView();
        }

        if(Valido && !ValidoTipoTitulo()){
            Valido = false;
            $('#alertaModal').text('No puede haber mas de una tarea con el mismo tipo de pieza y mismo título.');
            $('.contenedorAlertaModal').show();
            
            document.getElementsByClassName("contenedorAlertaModal")[0].scrollIntoView();
        }

        if(Valido){

            var row = parseInt($('#mhOptionR').text());
            var fila = $('#TablaTareasPlantilla tbody tr').eq(row);
            fila.find('td:eq(1)').text($('#idTPTarea').text().trim());
            fila.find('td:eq(2)').text($('#nomTPTarea').val().trim());
            fila.find('td:eq(3)').text($('#TituloPlantilla').val().trim());
            fila.find('td:eq(4)').text($('#HorasEst').val().trim());
            fila.find('td:eq(5)').text(JSON.stringify(ObtenerJsonHerramienta()));
            fila.find('td:eq(6)').text($('#DescripcionT').val().trim());
            fila.find('td:eq(7)').text($('#ObservacionT').val().trim());
    
            CerrarFunciones();
        }
    });

    $('#SigmaModalFunciones').on('click','#TablaTareasHerramientas tbody td',function(){
       
        var indexAnt = $('#TablaTareasHerramientas .tr-activa-sigma').index();
        var fila = $(this).parent('tr');
        var indexAct = fila.index();

        $('#TablaTareasHerramientas .tr-activa-sigma').removeClass('tr-activa-sigma');
        $('#actHer').text(indexAct);

        if(indexAnt != indexAct)
            fila.addClass('tr-activa-sigma');

        var cell = $(this).index();
        
        if(cell == 2){
            if(!fila.hasClass('tr-activa-sigma'))
                $(this).parent('tr').addClass('tr-activa-sigma');

            $('#TablaTareasHerramientas .tr-activa-sigma').removeClass('tr-activa-sigma');
            $(this).parent('tr').addClass('tr-activa-sigma');
            BuscarHerramienta(Herramienta);
        }

    });
    
    function ExisteHerramienta(){
        existe = false;
        $("#TablaTareasHerramientas").find('> tbody > tr').each(function () {
            if( $(this).find('td:eq(0)').text().trim() != ''){
                existe = true;
                return;
            }
        });

        return existe;
    }

    function ObtenerJsonHerramienta(){
        
        var jsonHer = [];
        $("#TablaTareasHerramientas").find('> tbody > tr').each(function () {
            if( $(this).find('td:eq(0)').text().trim() != ''){
                jsonHer.push({ 
                    "Id"            : $(this).find('td:eq(0)').text(),
                    "Herramienta"   : $(this).find('td:eq(1)').text()
                });
            }
        });

        return jsonHer;
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
            <div style="display:none;" id="actHer"></div>
            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Tipo de Pieza:</label>
                <div class="col-lg-9">
                    <div style="width:80%;float:left;">
                        <div style="display:none;" id="idTPTarea">${data['idTPTarea']}</div>
                        <input type="text" title="Tipo de Pieza" readonly
                            class="form-control texto obligatorio buscador Tarea" id="nomTPTarea" value="${data['nomTPTarea']}">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:20%;float:right;padding:10px;">
                        <span title="Buscar Tipo de Pieza" class="fa fa-search BuscarTPTarea" 
                            style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Tipo de Pieza" class="fa fa-trash-o BorrarTPTarea" 
                            style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="TituloPlantilla" class="col-lg-3 col-form-label">T&iacute;tulo:</label>
                <div class="col-lg-9">
                    <input type="text" maxlenght="20"
                    class="form-control obligatorio Tarea"  id="TituloPlantilla" value="${data['TituloPlantilla']}">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="HorasEst" class="col-lg-3 col-form-label">Horas Estimadas:</label>
                <div class="col-lg-9">
                    <input type="number" step = "0.1" min = "0"
                    class="form-control obligatorio Tarea"  id="HorasEst" value="${data['HorasEst']}">
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

            <h3>
                Herramientas
            </h3>
            <div class="table-responsive">
                <table id="TablaTareasHerramientas" class="table table-hover tabla-sigma">
                    <thead class="head-table-sigma">
                        <tr>
                            <th style="width:90%;">Herramienta</th>
                            <th style="width:5%;">
                                <span id ="agregarHerramienta" style="color:#28a745;cursor: pointer;" class="fa fa-plus-circle fa-lg"></span>
                            </th>
                            <th style="width:5%;">
                                <span id ="eliminarHerramienta" style="color:#dc3545;cursor: pointer;" class="fa fa-minus-circle fa-lg"></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data['HerramientasT']}
                    </tbody>
                </table>
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

    function ValidoTipoTitulo(){
    
        TipoPiezaAct = $('#idTPTarea').text().trim();
        TituloAct = $('#TituloPlantilla').val().trim().toLowerCase();
        fila = $('#mhOptionR').text().trim();

        Valido = true;

        $("#TablaTareasPlantilla").find('> tbody > tr').each(function () {

            if( fila != $(this).index() && 
                $(this).find('td:eq(1)').text().trim() == TipoPiezaAct &&
                $(this).find('td:eq(3)').text().trim().toLowerCase() == TituloAct
            ){
                Valido = false;
                return false;
            }
        });

        return Valido;
    }

    window.agregarTareaPlantilla = function(){
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

    window.ElegirHerramienta = function(fila){

        var row = parseInt($('#actHer').text());
        var filaHerramienta = $('#TablaTareasHerramientas tbody tr').eq(row);

        var existe = false;
        
        $("#TablaTareasHerramientas").find('> tbody > tr').each(function () {
            if( row != $(this).index() &&
                $(this).find('td:eq(0)').text().trim() == fila.find("td:eq(0)").text().trim()
            ){
                existe = true;
                return;
            }
        });

        if(!existe){
            filaHerramienta.find('td:eq(0)').text(fila.find("td:eq(0)").text().trim());
            filaHerramienta.find('td:eq(1)').text(fila.find("td:eq(1)").text().trim());
        }else{
            
            Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn btn-primary-sigma">
            <span class="fa fa-times-circle"></span>
            Cerrar
            </button>`;
    
            Cuerpo = "No se puede agregar <strong>Herramienta</strong> debido ya se ha agregado anteriormente a la plantilla.";
    
    
            var parametros = {
                "Titulo":"Advetencia",
                "Cuerpo": Cuerpo,
                "Botones":Botones
            }

            ModalAdvertencia(parametros,true);
        }
        
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

    window.ObtenerJsonTareas = function(){
        var Tareas = [];
        $("#TablaTareasPlantilla").find('> tbody > tr').each(function () {
    
            if( $(this).find('td:eq(1)').text().trim() != ''){
                Tareas.push({ 
                    "Id"            : $(this).find('td:eq(0)').text(),
                    "IdPieza"       : $(this).find('td:eq(1)').text(),
                    "Titulo"        : $(this).find('td:eq(3)').text(),
                    "Horas"         : $(this).find('td:eq(4)').text(),
                    "Herramientas"  : JSON.parse($(this).find('td:eq(5)').text()),
                    "Descripcion"   : $(this).find('td:eq(6)').text(),
                    "Observacion"   : $(this).find('td:eq(7)').text()
                });
            }
        });

        return Tareas;
    }
    
});