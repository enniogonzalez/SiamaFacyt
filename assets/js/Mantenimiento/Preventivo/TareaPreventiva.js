
const Pieza = "Pieza";
const Obrero = "Obrero";
const Proveedor = "Proveedor";

$(function(){

    $('#SigmaModalFunciones').on('keypress','.buscador',function(){
        return false;
    })

    /************************************/
    /*      Inicio Buscadores           */
    /************************************/

    /************************************/
    /*      Manejo Proveedores          */
    /************************************/
    
    $('#SigmaModalFunciones').on('click','#nomPro',function(){
        BuscarProveedor(Proveedor);
    });

    $('#SigmaModalFunciones').on('click','.BuscarProveedor',function(){
        BuscarProveedor(Proveedor);
    });

    $('#SigmaModalFunciones').on('click','.BorrarProveedor',function(){
        $('#idPro').text('');
        $('#nomPro').val('');
    });

    /************************************/
    /*      Manejo Obreros             */
    /************************************/
    
    $('#SigmaModalFunciones').on('click','#nomObr',function(){
        BuscarObrero(Obrero);
    });

    $('#SigmaModalFunciones').on('click','.BuscarObrero',function(){
        BuscarObrero(Obrero);
    });

    $('#SigmaModalFunciones').on('click','.BorrarObrero',function(){
        $('#idObr').text('');
        $('#nomObr').val('');
    });
    /************************************/
    /*          Fin Buscadores          */
    /************************************/

    $('#eliminarTarea').on('click',function(){
        $('#TablaTareas .tr-activa-sigma').remove();
    });

    $('#SigmaModalFunciones').on('click','#CancelarEdicionTarea',function(){
        ClearModalFunction();
    });

    $('#SigmaModalFunciones').on('change','#FinTarea',function(){
        if($(this).val() != "" && 
        $('#InicioTarea').val() != "" 
        && $(this).val() < $('#InicioTarea').val()){
            $('#InicioTarea').val($(this).val());
        }
    })

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

        //Validar que exista un obrero o un proveedor asignado
        if(Valido && $('#nomObr').val() == "" && $('#nomPro').val() == ""){
            Valido = false;
            $('#alertaModal').focus(); 
            $('#alertaModal').text('La tarea del Mantenimiento Preventivo debe contar con un Obrero o un Proveedor para realizar el mismo.');
            $('.contenedorAlertaModal').show();
            
            document.getElementsByClassName("contenedorAlertaModal")[0].scrollIntoView();
        }

        if(Valido && $('#EstatusPreventivo').val() != "Solicitado" && $('#HorasRea').val() <= 0){
            Valido = false;
            $('#alertaModal').text('Las horas realizadas tienen que ser un numero mayor a 0.');
            $('.contenedorAlertaModal').show();
            
            document.getElementsByClassName("contenedorAlertaModal")[0].scrollIntoView();
        }

        if(Valido){

            var row = parseInt($('#mhOptionR').text());
            var fila = $('#TablaTareas tbody tr').eq(row);
            fila.find('td:eq(1)').text($('#idPiezaTarea').text().trim());
            fila.find('td:eq(2)').text($('#nomPiezaTarea').val().trim());
            fila.find('td:eq(3)').text($('#TituloCambio').val().trim());
            fila.find('td:eq(4)').text($('#idObr').text().trim());
            fila.find('td:eq(5)').text($('#nomObr').val().trim());
            fila.find('td:eq(6)').text($('#idPro').text().trim());
            fila.find('td:eq(7)').text($('#nomPro').val().trim());
            fila.find('td:eq(9)').text($('#DescripcionT').val().trim());
            fila.find('td:eq(10)').text($('#InicioTarea').val().trim());
            fila.find('td:eq(11)').text($('#FinTarea').val().trim());
            fila.find('td:eq(12)').text($('#HorasRea').val().trim());
            fila.find('td:eq(13)').text($('#ObservacionT').val().trim());
    
            CerrarFunciones();
        }
    });

    $('#SigmaModalFunciones').on('change','#InicioTarea',function(){
        if($(this).val() != "" && 
        $('#FinTarea').val() != "" 
        && $(this).val() > $('#FinTarea').val()){
            $('#FinTarea').val($(this).val());
        }

    });
    
    $('#TablaTareas').on('click','.editarTarea',function(){

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
        if(fila.find('td:eq(8)').text().trim() != ""){
            arrayHerramienta = JSON.parse(fila.find('td:eq(8)').text().trim());

            for(her in arrayHerramienta){
                trHerramienta += `
                    <tr>
                        <td style="display:none;">${arrayHerramienta[her].Id}</td>
                        <td>${arrayHerramienta[her].Herramienta}</td>
                    </tr>
                `;                
            }
        }

        var data = {
            "Titulo"        :   "Editar Tarea",
            "EstatusDoc"    :   $('#EstatusPreventivo').val(),
            "Fila"          :   $(this).parent('tr').index(),
            "Botones"       :   Botones,
            "idPiezaTarea"  :   fila.find('td:eq(1)').text().trim(),
            "nomPiezaTarea" :   fila.find('td:eq(2)').text().trim(),
            "TituloCambio"  :   fila.find('td:eq(3)').text().trim(),
            "idObr"         :   fila.find('td:eq(4)').text().trim(),
            "nomObr"        :   fila.find('td:eq(5)').text().trim(),
            "idPro"         :   fila.find('td:eq(6)').text().trim(),
            "nomPro"        :   fila.find('td:eq(7)').text().trim(),
            "HerramientasT" :   trHerramienta,
            "DescripcionT"  :   fila.find('td:eq(9)').text().trim(),
            "InicioTarea"   :   fila.find('td:eq(10)').text().trim(),
            "FinTarea"      :   fila.find('td:eq(11)').text().trim(),
            "HorasRea"    :   fila.find('td:eq(12)').text().trim(),
            "ObservacionT"  :   fila.find('td:eq(13)').text().trim(),
        }
        
        SetModalFuncionesCambios(data);

        if($('#EstatusPreventivo').val() != "Solicitado"){
            setTimeout(function(){$('#ObservacionT').focus();}, 400);
        }
    });

    $('#TablaTareas').on('click','.realizarTarea',function(){

        $('.tr-activa-sigma').removeClass('tr-activa-sigma');
        var fila = $(this).parent('tr');
        fila.addClass('tr-activa-sigma');

        if($(this).find('span').hasClass('fa-square-o')){
            $(this).find('span').removeClass('fa-square-o');
            $(this).find('span').addClass('fa-check-square-o');
            $(this).parent('tr').find('td:eq(15)').text('Realizado');
        }else{
            $(this).find('span').removeClass('fa-check-square-o');
            $(this).find('span').addClass('fa-square-o');
            $(this).parent('tr').find('td:eq(15)').text('');
        }
    });

    $('#TablaTareas tbody').on('click','tr',function(){
        $(this).removeClass('tr-error-sigma');
        ActivarCeldaTabla(this)
    });
    
    function SetModalFuncionesCambios(data){
        var pointer= "";
        var atributos= "";
        var displayHoras = ""
        if(data['EstatusDoc'] != "Solicitado"){
            pointer= "pointer-events: none;";
            atributos= " readonly disabled ";
        }else{
            displayHoras = "style='display:none;'"
        }

        //Se crea cuerpo html que va a tener la ventana modal de edicion
        var html = `

        <form class="form-horizontal" id="formEditarTarea">
            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Pieza:</label>
                <div class="col-lg-9">
                    <div style="display:none;" id="idPiezaTarea">${data['idPiezaTarea']}</div>
                    <input type="text" title="${data['nomPiezaTarea']}"  readonly disabled
                        class="form-control texto obligatorio Tarea" id="nomPiezaTarea" value="${data['nomPiezaTarea']}">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            
            <div class="form-group row">
                <label for="TituloCambio" class="col-lg-3 col-form-label">T&iacute;tulo:</label>
                <div class="col-lg-9">
                    <input type="text" maxlenght="20" readonly disabled title="${data['TituloCambio']}"
                    class="form-control obligatorio Tarea"  id="TituloCambio" value="${data['TituloCambio']}">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="DescripcionT" class="col-lg-3 col-form-label">Descripci&oacute;n:</label>
                <div class="col-lg-9">
                    <textarea  class="form-control texto obligatorio Tarea" rows="3" readonly
                    style = "resize:vertical;" id="DescripcionT">${data['DescripcionT']}</textarea>
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Obrero:</label>
                <div class="col-lg-9">
                    <div style="width:80%;float:left;">
                        <div style="display:none;" id="idObr">${data['idObr']}</div>
                        <input type="text" title="Obrero que realiza tarea" readonly
                            class="form-control texto  buscador Tarea" id="nomObr" value="${data['nomObr']}">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:20%;float:right;padding:10px;">
                        <span title="Buscar Obrero" class="fa fa-search BuscarObrero" 
                            style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Obrero" class="fa fa-trash-o BorrarObrero" 
                            style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Proveedor:</label>
                <div class="col-lg-9">
                    <div style="width:80%;float:left;">
                        <div style="display:none;" id="idPro">${data['idPro']}</div>
                        <input type="text" title="Proveedor que realiza tarea" readonly
                            class="form-control texto  buscador Tarea" id="nomPro" value="${data['nomPro']}">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:20%;float:right;padding:10px;">
                        <span title="Buscar Proveedor" class="fa fa-search BuscarProveedor" 
                            style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Proveedor" class="fa fa-trash-o BorrarProveedor" 
                            style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>
            
            <div class="form-group row">
                <label for="InicioTarea" class="col-lg-3 col-form-label">Inicio:</label>
                <div class="col-lg-9">
                    <input type="date" 
                    class="form-control obligatorio fecha Tarea" id="InicioTarea" value="${data['InicioTarea']}">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="FinTarea" class="col-lg-3 col-form-label">Fin:</label>
                <div class="col-lg-9">
                    <input type="date" 
                    class="form-control obligatorio fecha Tarea" id="FinTarea" value="${data['FinTarea']}">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row" ${displayHoras} >
                <label for="HorasRea" class="col-lg-3 col-form-label">Horas Estimados:</label>
                <div class="col-lg-9">
                    <input type="number" step = "1" min = "1"
                    class="form-control obligatorio Tarea"  id="HorasRea" value="${data['HorasRea']}">
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
                            <th style="width:100%;">Herramienta</th>
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

    window.ObtenerJsonTareas = function(){
        var Tareas = [];
        var estatuDoc = $('#EstatusPreventivo').val().trim();
        $("#TablaTareas").find('> tbody > tr').each(function () {

            if( $(this).find('td:eq(1)').text().trim() != '' && 
                (   estatuDoc == "Solicitado" || 
                    (   estatuDoc != "Solicitado" && 
                        $(this).find('td:eq(15)').text() == "Realizado"
                    )
                )
            ){
                Tareas.push({ 
                    "Id"            : $(this).find('td:eq(0)').text(),
                    "IdPieza"       : $(this).find('td:eq(1)').text(),
                    "Titulo"        : $(this).find('td:eq(3)').text(),
                    "idObrero"      : $(this).find('td:eq(4)').text(),
                    "idProveedor"   : $(this).find('td:eq(6)').text(),
                    "Herramientas"  : $(this).find('td:eq(8)').text(),
                    "Descripcion"   : $(this).find('td:eq(9)').text(),
                    "Inicio"        : $(this).find('td:eq(10)').text(),
                    "Fin"           : $(this).find('td:eq(11)').text(),
                    "Hor_Eje"       : $(this).find('td:eq(12)').text(),
                    "Observacion"   : $(this).find('td:eq(13)').text(),
                    "Hor_Asi"       : $(this).find('td:eq(14)').text()
                });
            }
        });

        return Tareas;
    }
     
    window.PoseenEjecutor = function(){
        var Poseen = true;

        $("#TablaTareas").find('> tbody > tr').each(function () {
            if($(this).find('td:eq(4)').text().trim() == '' && $(this).find('td:eq(6)').text().trim() == '' ){
                Poseen = false;

                //Salir del ciclo
                return false;
            }
        });

        return Poseen;
    }

    window.ValidarHorasHombre = function(){
        var Valido = true;

        $("#TablaTareas").find('> tbody > tr').each(function () {

            if( $(this).find('td:eq(1)').text().trim() != '' && 
                $(this).find('td:eq(15)').text() == "Realizado" &&
                $(this).find('td:eq(12)').text() <= 0
            ){
                Valido = false;
                $(this).addClass('tr-error-sigma');
            }
        });

        return Valido;
        
    }
});