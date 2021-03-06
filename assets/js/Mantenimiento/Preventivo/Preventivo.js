

var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){

    const Preventivo = "Mantenimiento Preventivo";
    const Bienes = "Bienes";
    const Plantilla = "Platillas de Mantenimiento"

    var idActual ="";
    var plmActual ="";
    var dataInputs= [];
    var idBuscadorActual = "";
    var nombreBuscadorActual = "";
    var idBiePreventivo = "";
    var Tareas = "";

    $('#CancelarModalBuscar').on('click',function(){
        switch(GetSearchType()){
            case Pieza:
                $('#idPiezaTarea').text(idBuscadorActual.trim());
                $('#nomPiezaTarea').val(nombreBuscadorActual.trim());
            break;
            case Proveedor:
                $('#idPro').text(idBuscadorActual.trim());
                $('#nomPro').val(nombreBuscadorActual.trim());
            break;
            case Obrero:
                $('#idObr').text(idBuscadorActual.trim());
                $('#nomObr').val(nombreBuscadorActual.trim());
            break;
            case Bienes:
                $('#idBiePreventivo').text(idBuscadorActual.trim());
                $('#nomBiePreventivo').val(nombreBuscadorActual.trim());
            break;
        }
    });

    $('#InicioPreventivo').on('change',function(){
        if($(this).val() != "" && 
        $('#FinPreventivo').val() != "" 
        && $(this).val() > $('#FinPreventivo').val()){
            $('#FinPreventivo').val($(this).val());
        }

    });

    $('#FinPreventivo').on('change',function(){
        if($(this).val() != "" && 
        $('#InicioPreventivo').val() != "" 
        && $(this).val() < $('#InicioPreventivo').val()){
            $('#InicioPreventivo').val($(this).val());
        }

    });

    $('.botoneraFormulario').on('click','#AgregarRegistro',function(){
        BuscarPlantilla();
        GuardarEstadoActualFormulario();
    })

    $('.botoneraFormulario').on('click','#AprobarRegistro',function(){
        
        var parametros = {
            "id": $('#IdForm').text(),
            "Tipo": "Aprobar",
            "Url":$('#ControladorActual').text().trim() + "/aprobar" 
        }

        //Evitar doble click
        if(!Guardando){
            Guardando = true;
            CambioEstatusMantenimiento(parametros)
        }
    });

    $('.botoneraFormulario').on('click','#BuscarRegistro',function(){
        
        EstablecerBuscador();
        SetSearchType('Formulario');
        SetSearchTitle('Busqueda Mantenimiento Preventivo');
        PrimeraVezBusqueda = true;
        DeshabilitarBotonera();
        SetUrlBusqueda($('#ControladorActual').text().trim()+"/busqueda");
        Busqueda(1,true);
        
    })

    $('.botoneraFormulario').on('click','#CancelarRegistro',function(){
        ClearForm();
        RestablecerEstadoAnteriorFormulario();
        DeshabilitarFormulario();

        AgregarBotoneraPreventivo($('#EstatusPreventivo').val().trim());

        parametros = {
            "Lista": $('#listaBusquedaFormulario').html().trim(),
            "Tipo": Preventivo
        }

        SetSearchModal(parametros,false)
        SetModalEtqContador("")
        SetSearchType("Formulario");
    })

    $('.botoneraFormulario').on('click','#DesaprobarRegistro',function(){
        
        var parametros = {
            "id": $('#IdForm').text(),
            "Tipo": "Desaprobar",
            "Url":$('#ControladorActual').text().trim() + "/reversar" 
        }
        
        //Evitar doble click
        if(!Guardando){
            Guardando = true;
            CambioEstatusMantenimiento(parametros)
        }
    })

    $('.botoneraFormulario').on('click','#EditarRegistro',function(){
        
        var parametros = {
            "id": $('#IdForm').text().trim(),
            "Caso":"Editar",
            "Url": $('#ControladorActual').text().trim()+"/obtener"
        }
        Obtener(parametros);

    });

    $('.botoneraFormulario').on('click','#EliminarRegistro',function(){
        Botones = `
        <button data-dismiss="modal" type="submit" id ="ConfirmarEliminacion" title="Confirmar Eliminar Registro" 
            type="button" style="margin:5px;" class="btn  btn-success">
          <span class="fa fa-check"></span>
          Confirmar
        </button>
        <button data-dismiss="modal" title="Cancelar Eliminacion de Registro" type="button" style="margin:5px;" class="btn  btn-danger">
          <span class="fa fa-ban "></span>
          Cancelar
        </button>`;

        var parametros = {
            "Titulo":"Advertencia",
            "Cuerpo": "<h4>¿Est&aacute; usted seguro de querer eliminar el Mantenimiento Preventivo?</h4>",
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
    })

    $('.botoneraFormulario').on('click','#GuardarRegistro',function(){

        var Tareas = [];
        var Valido = ValidarFormulario();

        if(Valido){
            Tareas = ObtenerJsonTareas();
            Valido = (Tareas.length) > 0;

            if(!Valido && $('#EstatusPreventivo').val().trim() == "Solicitado"){
                SetAlertaFormulario("No se puede guardar Mantenimiento Preventivo debido a que no posee tareas.");
            }else if(!Valido){
                SetAlertaFormulario("No se puede guardar Mantenimiento Preventivo debido a que no posee tareas marcadas como realizadas.");
            }
        }

        if(Valido && !PoseenEjecutor()){
            Valido = false
            SetAlertaFormulario("No se puede guardar Mantenimiento Preventivo debido a que existen tareas que no poseen Obrero o Proveedor que las ejecuten.");
        }

        if(Valido && $('#EstatusPreventivo').val().trim() != "Solicitado" && !ValidarHorasHombre()){
            Valido = false
            SetAlertaFormulario("No se puede guardar Mantenimiento Preventivo debido a que existen tareas que no se le han asignado horas hombre.");
        }

        if(Valido){
            var data = {
                "Lista": $('#listaBusquedaFormulario').html().trim(),
                "Tipo": Preventivo
            }
    
            SetSearchModal(data,false)
            SetModalEtqContador("")
            SetSearchType("Formulario");

            var parametros = {
                "id"            : $('#IdForm').text().trim(),
                "plm_id"        : $('#PlantillaMantenimiento').text().trim(),
                "Estatus"       : $('#EstatusPreventivo').val().trim(),
                "Documento"     : $('#DocumentoPreventivo').val().trim(),
                "Bien"          : $('#idBiePreventivo').text().trim(),
                "Tareas"        : Tareas,
                "Inicio"        : $('#InicioPreventivo').val().trim(),
                "Fin"           : $('#FinPreventivo').val().trim(),
                "Observacion"   : $('#ObservacionPreventivo').val().trim(),
                "Url"           : $('#FormularioActual').attr("action")
            }
            
            console.log(parametros);

            if(!Guardando){
                Guardando = true;
                GuardarFormulario(parametros);
            }
        }
        
    });

    $('#SigmaModalAdvertencias').on('click','#ConfirmarEliminacion',function(){
        var parametros = {
            "id": $('#IdForm').text().trim(),
            "Caso":"Eliminar",
            "Url": $('#ControladorActual').text().trim()+"/obtener"
        }
        Obtener(parametros);
    });

    function AccionEliminar(){

        if($('#EstatusPreventivo').val().trim() != "Solicitado"){
            
            Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn  btn-danger">
            <span class="fa fa-times "></span>
            Cerrar
            </button>`;

            Cuerpo = `No se puede eliminar <strong>Mantenimiento Preventido</strong> debido a que el estatus ha cambiado a 
            <strong>${$('#EstatusPreventivo').val().trim()}</strong>.`;

            var parametros = {
                "Titulo"    : "Advetencia",
                "Cuerpo"    : Cuerpo,
                "Botones"   : Botones
            }

            ModalAdvertencia(parametros,true);
        }else{
            var parametros = {
                "id": $('#IdForm').text().trim(),
                "Url": $('#ControladorActual').text().trim()+"/eliminar"
            }
            Eliminar(parametros)
        }
    }

    function ActivarTareas(){
        $('#eliminarTarea').show();
    }

    function AgregarBotoneraPreventivo(Tipo){
        
        btnAgregar = `
            <button type="button"  class="btn btn-primary-sigma" id="AgregarRegistro">
                <span class="fa fa-plus"></span>
                Agregar
            </button>
        `;

        btnBuscar =`
            <button  type="button"  class="btn btn-primary-sigma" id="BuscarRegistro">
                <span class="fa fa-search"></span>
                Buscar
            </button>
        `;

        btnEditar = `
            <button type="button"  class="btn btn-primary-sigma" id="EditarRegistro">
                <span class="fa fa-pencil-square-o"></span>
                Editar
            </button>
        `;

        btnEliminar = `
            <button title="Eliminar" type="button" class="btn  btn-danger" id="EliminarRegistro">
                <span class="fa fa-trash"></span>
                Eliminar
            </button>
        `;

        btnAprobar = `
           <button  title="Aprobar" type="button"  class="btn btn-success" id="AprobarRegistro">
                <span class="fa fa-check"></span>
                Aprobar
            </button>
        `;

        btnDesaprobar = `
            <button title="Desaprobar" type="button" class="btn  btn-danger" id="DesaprobarRegistro">
                <span class="fa fa-undo"></span>
                Desaprobar
            </button>
        `;

        switch(Tipo){
            case "Solicitado":
                html = btnBuscar + btnEditar + btnAgregar + btnAprobar + btnEliminar;
            break;
            case "Aprobado":
                html = btnBuscar + btnEditar + btnAgregar + btnDesaprobar;
            break;
            case "Afectado":
                html = btnBuscar+ btnEditar+ btnAgregar;
            break;
            case "Realizado":
                html = btnBuscar + btnAgregar;
            break;
            default:
                html = ""

        }
        if(html != ""){
            $('.botoneraFormulario').children().remove();
            $('.botoneraFormulario').append(html);
        }
    }
    
    function BuscarPlantilla(){


        SetSearchThead(thPlantillas);

        parametros = {
            "Lista": $('#listaBusquedaPlantilla').html().trim(),
            "Tipo": Plantilla
        }

        condiciones ={
            "Disponibles": true
        }

        SetSearchModal(parametros,true,condiciones)

    }

    function CambioEstatusMantenimiento(parametros){

        if(parametros['Tipo'] ==  "Aprobar")
            MostrarEstatus(6); 
        else
            MostrarEstatus(8); 

        DeshabilitarBotonera();

        $.ajax({
            url: parametros['Url'] ,
            type: 'POST',
            data: parametros,
            dataType: 'json'
        }).done(function(data){
            Guardando = false;
            HabilitarBotonera();
            if(data['isValid']){
                LlenarFormularioRequest(data['Datos']);

                if(data['Tipo'] ==  "Aprobar")
                    MostrarEstatus(7,true); 
                else
                    MostrarEstatus(9,true); 
                

                setTimeout(function(){
                    CerrarEstatus();
                }, 6000);
            }else{
                
                CerrarEstatus();
                Botones = `
                <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn  btn-danger">
                  <span class="fa fa-times "></span>
                  Cerrar
                </button>`;
        
                var parametros = {
                    "Titulo":"Advertencia",
                    "Cuerpo": data['Mensaje'],
                    "Botones":Botones
                }
        
                ModalAdvertencia(parametros);
            }
        }).fail(function(data){
            Guardando = false;
            HabilitarFormulario(false);
            failAjaxRequest(data);
        });
    }

    function ClearForm(){
        
        $('#IdForm').text(''); 
        $('#idBiePreventivo').text('');
        $('#alertaFormularioActual').hide();
        $('#TablaTareas > tbody').children().remove();
        $('#TablaReparacionesCorrectivas > tbody').children().remove();

        $('.formulario-sigma form .form-control').each(function(){
            $(this).removeClass('is-invalid');
            if($(this).hasClass('texto') || $(this).hasClass('fecha'))
                $(this).val('')
            else if($(this).hasClass('lista'))
                $(this)[0].selectedIndex = 0;
            else if ($(this).hasClass('decimal'))
                $(this).val('0.00')
        })
    }
    
    function DesactivarTareas(){
        $('#eliminarTarea').hide();
    }

    function Editar(){
        
        if($('#EstatusPreventivo').val().trim() == "Realizado"){
            
            Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn  btn-danger">
            <span class="fa fa-times "></span>
            Cerrar
            </button>`;

            Cuerpo = `No se puede editar <strong>Mantenimiento Preventivo</strong> debido a que el estatus ha cambiado a 
            <strong>realizado</strong>.`;

            var parametros = {
                "Titulo":"Advetencia",
                "Cuerpo": Cuerpo,
                "Botones":Botones
            }

            ModalAdvertencia(parametros);
        }else{

            GuardarEstadoActualFormulario();
            HabilitarFormulario()

            $('#nomBiePreventivo').attr("disabled", "disabled");
            $('#nomBiePreventivo').attr("readonly", "readonly");
            if($('#EstatusPreventivo').val() == "Solicitado"){
                $('#EstatusPreventivo').attr("disabled", "disabled");
                $('#EstatusPreventivo').attr("readonly", "readonly");
                ActivarTareas();
            }else{
                $('.formulario-sigma form .form-control').each(function(){
                    $(this).attr("disabled", "disabled");
                    $(this).attr("readonly", "readonly");
                })

                DesactivarTareas();
                $('#ObservacionPreventivo').removeAttr("disabled"); 
                $('#ObservacionPreventivo').removeAttr("readonly");
                setTimeout(function(){$('#ObservacionPreventivo').focus();}, 400);
            }

            
            $(window).scrollTop(0);
        }
    }

    function EstablecerBuscador(){
        SetSearchThead(thPreventivos);
    }

    function GetUrlBusquedaOpcion(opcion){
        switch(opcion){
            case Pieza:
                controlador = "piezas";
            break;
            case Preventivo:
                controlador = "preventivo";
            break;
            case Proveedor:
                controlador = "proveedores";
            break;
            case Obrero:
                controlador = "obreros";
            break;
            case Plantilla:
                controlador = "plantilla";
            break;
            case Bienes:
                controlador = "bienes";
            break;
        }

        return $('#UrlBase').text() + "/" + controlador + "/busqueda"
    }
    
    function GuardarEstadoActualFormulario(){
        dataInputs = [];
        idActual =$('#IdForm').text().trim();
        plmActual =$('#PlantillaMantenimiento').text().trim();
        idBiePreventivo = $('#idBiePreventivo').text().trim();

        Tareas = $('#TablaTareas > tbody').html();
        $('.formulario-sigma form .form-control').each(function(){
            dataInputs.push($(this).val().trim());
        })
    }
    
    function LlenarFormulario(data){
        $('#TablaTareas > tbody').children().remove();

        if(data['Estatus'] != "Solicitado")
            DesactivarTareas();
        else
            ActivarTareas();

        AgregarBotoneraPreventivo(data['Estatus']);


        $('#IdForm').text(data['id']);
        $('#PlantillaMantenimiento').text(data['plm_id']);
        $('#DocumentoPreventivo').val(data['Documento']);
        $('#EstatusPreventivo').val(data['Estatus']);
        $('#idBiePreventivo').text(data['idBien']);
        $('#nomBiePreventivo').val(data['nomBien']);
        $('#InicioPreventivo').val(data['Inicio']);
        $('#FinPreventivo').val(data['Fin']);
        $('#ObservacionPreventivo').val(data['Observaciones']);
        $('#TablaTareas > tbody:last-child').append(data['Tareas']);

    }

    function LlenarFormularioRequest(data){
        
        var parametros = {
            "id"            : data['man_id'],
            "plm_id"        : data['plm_id'],
            "idBien"        : data['bie_id'],     
            "Documento"     : data['documento'],  
            "Estatus"       : data['estatus'],
            "Inicio"        : data['fec_ini'],
            "Fin"           : data['fec_fin'],
            "nomBien"       : data['bie_nom'],
            "Observaciones" : data['observaciones'],
            "Tareas"        : data['Tareas']
        }

        LlenarFormulario(parametros);
        
    }

    function Obtener(parametros){

        MostrarEstatus(5); 

        $.ajax({
            url: parametros['Url'],
            type: "POST",
            data: parametros,
            dataType: 'json'
        }).done(function(data){
            $(window).scrollTop(0);
            if(data['isValid']){
                CerrarEstatus();
                LlenarFormularioRequest(data['Datos']);
                $('#SigmaModalBusqueda').modal('hide');

                if(data['Caso'] == "Editar"){
                    Editar();
                }else if(data['Caso'] == "Eliminar"){
                    AccionEliminar();
                }
            }
        }).fail(function(data){
            failAjaxRequest(data);
        });
    }

    function ObtenerPlantilla(parametros){

        MostrarEstatus(5); 

        $.ajax({
            url: parametros['Url'],
            type: "POST",
            data: parametros,
            dataType: 'json'
        }).done(function(data){
            $(window).scrollTop(0);
            if(data['isValid']){
                CerrarEstatus();
                LlenarFormularioRequest(data['Datos']);
                HabilitarFormulario();
                
                ActivarTareas();
                $('#EstatusPreventivo').attr("disabled", "disabled");
                $('#EstatusPreventivo').attr("readonly", "readonly");
                $('#nomBiePreventivo').attr("disabled", "disabled");
                $('#nomBiePreventivo').attr("readonly", "readonly");
                $(window).scrollTop(0);
                $('#SigmaModalBusqueda').modal('hide');
            }
        }).fail(function(data){
            failAjaxRequest(data);
        });
    }

    function RestablecerEstadoAnteriorFormulario(){
        
        var parametros = {
            "id"            : idActual.trim(),
            "plm_id"        : plmActual.trim(),
            "idBien"        : idBiePreventivo.trim(),     
            "Documento"     : dataInputs[0].trim(),  
            "Estatus"       : dataInputs[1].trim(),
            "nomBien"       : dataInputs[2].trim(),
            "Inicio"        : dataInputs[3].trim(),
            "Fin"           : dataInputs[4].trim(),
            "Observaciones" : dataInputs[5].trim(),
            "Tareas"        : Tareas
        }

        LlenarFormulario(parametros);
    }

    function SetSearchModal(data,buscar =true,condiciones = {}){
        SetSearchType(data['Tipo']);

        SetModalEtqContador(data['Tipo'])
        SetSearchCOB(data['Lista']);


        SetSearchTitle('Busqueda ' + data['Tipo']);
        PrimeraVezBusqueda = true;
        SetUrlBusqueda(GetUrlBusquedaOpcion(data['Tipo']));

        if(buscar)
            Busqueda(1,false,condiciones);
    }

    window.AccionGuardar = function(data){
        LlenarFormularioRequest(data['Datos']);
    }

    window.AccionEliminarFormulario = function(data){
        
        if(data['Datos']['man_id'] == ""){
            ClearForm();
            AgregarBotoneraPrimariaNULL();
        }else{
            LlenarFormularioRequest(data['Datos']);
        }
    }

    window.ActivarCeldaTabla = function(fila){

        //Se busca el indice de la fila que esta activa
        var indexAnt = $('.tr-activa-sigma').index();
        //Se busca el indice de la fila que fue seleccionada
        var indexAct = $(fila).index();
        //Se remueve la clase activa de la fila que esta activa
        $('.tr-activa-sigma').removeClass('tr-activa-sigma');

        //En caso de que los dos indices encontrado anteriormente
        //sean diferentes, de agrega la clase activa a la fila seleccionada
        //esto con la intension de que si se selecciona la misma fila
        //activa, la misma se desactive
        if(indexAnt != indexAct)
            $(fila).addClass('tr-activa-sigma');

    }

    window.InterfazElegirBuscador = function(fila){
        
        switch(GetSearchType()){
            case "Formulario":
                var parametros = {
                    "id": fila.find("td:eq(0)").text().trim(),
                    "Caso":"Buscar",
                    "Url": $('#ControladorActual').text().trim()+"/obtener"
                }
                Obtener(parametros);
            break;
            case Plantilla:
                var parametros = {
                    "id": fila.find("td:eq(0)").text().trim(),
                    "Url": $('#UrlBase').text() + "/plantilla/obtenerMantenimiento"
                }
                ObtenerPlantilla(parametros);
            break;
            case Obrero:
                $('#idObr').text(fila.find("td:eq(0)").text().trim());
                $('#nomObr').val(fila.find("td:eq(5)").text().trim());
                $('#idPro').text('');
                $('#nomPro').val('');
            break;
            case Proveedor:
                $('#idPro').text(fila.find("td:eq(0)").text().trim());
                $('#nomPro').val(fila.find("td:eq(6)").text().trim());
                $('#idObr').text('');
                $('#nomObr').val('');
            break;
            case Pieza:
                $('#idPiezaTarea').text(fila.find("td:eq(0)").text().trim());
                $('#nomPiezaTarea').val(fila.find("td:eq(2)").text().trim());
            break;
            case Bienes:
                $('#idBiePreventivo').text(fila.find("td:eq(0)").text().trim());
                $('#nomBiePreventivo').val(fila.find("td:eq(1)").text().trim());
            break;
        }

        if(GetSearchType() != "Formulario" && GetSearchType() != Plantilla){
            $('#SigmaModalBusqueda').modal('hide');
        }
    }

    window.BuscarProveedor = function(tipo){

        SetOrigenBuscador(origenFuncion);
        SetSearchThead(thProveedores);

        parametros = {
            "Lista": $('#listaBusquedaProveedor').html().trim(),
            "Tipo": tipo
        }

        idBuscadorActual = $('#idPro').text().trim();
        nombreBuscadorActual = $('#nomPro').val().trim();

        SetSearchModal(parametros)

    }

    window.BuscarObrero = function(tipo){
        
        SetOrigenBuscador(origenFuncion);
        SetSearchThead(thObreros);
        parametros = {
            "Lista": $('#listaBusquedaObrero').html().trim(),
            "Tipo": tipo,
        }

        idBuscadorActual = $('#idObr').text().trim();
        nombreBuscadorActual = $('#nomObr').val().trim();

        SetSearchModal(parametros)
    }
});