

var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){

    const Plantilla = "Plantillas de Mantenimiento";
    const Bienes = "Bienes";

    var idActual ="";
    var dataInputs= [];
    var idBuscadorActual = "";
    var nombreBuscadorActual = "";
    var idBiePlantilla = "";
    var Tareas = "";

    EstablecerBuscador();

    if($('#EstatusPlantilla').val() != "Solicitado")
        DesactivarTareas();

    $('#CancelarModalBuscar').on('click',function(){
        switch(GetSearchType()){
            case TipoPieza:
                $('#idTPTarea').text(idBuscadorActual.trim());
                $('#nomTPTarea').val(nombreBuscadorActual.trim());
            break;
            case Bienes:
                $('#idBiePlantilla').text(idBuscadorActual.trim());
                $('#nomBiePlantilla').val(nombreBuscadorActual.trim());
            break;
        }
        if(GetSearchType() != "Formulario" && GetSearchType() != Bienes){
            
            //Prevenir solapamientos de modales
            setTimeout(function(){ 
                $('#SiamaModalFunciones').modal('show');}, 400);
        }
    })

    /************************************/
    /*      Inicio Buscadores           */
    /************************************/
    
    /************************************/
    /*          Manejo Bienes           */
    /************************************/
    
    $('#nomBiePlantilla').on('click',function(){

        if(ExisteTarea() )
            AdvertenciaCambiarBien("cambiar");
        else
            BuscarBien();
    });

    $('.BuscarBienPlantilla').on('click',function(){
        if(ExisteTarea() )
            AdvertenciaCambiarBien("cambiar");
        else
            BuscarBien();
    });

    $('.BorrarBienPlantilla').on('click',function(){
        
        if(ExisteTarea() )
            AdvertenciaCambiarBien("borrar");
        else{
            $('#idBiePlantilla').text("");
            $('#nomBiePlantilla').val("");
        }
    });

    /************************************/
    /*          Fin Buscadores          */
    /************************************/

    $('.botoneraFormulario').on('click','#AgregarRegistro',function(){
        GuardarEstadoActualFormulario();
        ClearForm();
        HabilitarFormulario();
        
        ActivarTareas();
        $('#EstatusPlantilla').val("Solicitado");
        $('#EstatusPlantilla').attr("disabled", "disabled");
        $('#EstatusPlantilla').attr("readonly", "readonly");
        $(window).scrollTop(0);
    });

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

    $('.botoneraFormulario').on('click','#CancelarRegistro',function(){
        ClearForm();
        RestablecerEstadoAnteriorFormulario();
        EstablecerBuscador();
        DeshabilitarFormulario();

        AgregarBotoneraPlantilla($('#EstatusPlantilla').val().trim());

        parametros = {
            "Lista": $('#listaBusquedaFormulario').html().trim(),
            "Tipo": Plantilla
        }

        SetSearchModal(parametros,false)
        SetModalEtqContador("")
        SetSearchType("Formulario");
    });

    $('.botoneraFormulario').on('click','#BuscarRegistro',function(){
        SetSearchType('Formulario');
        SetSearchTitle('Busqueda Plantillas de Mantenimiento');
        PrimeraVezBusqueda = true;
        DeshabilitarBotonera();
        SetUrlBusqueda($('#ControladorActual').text().trim()+"/busqueda");
        Busqueda(1);
        
        setTimeout(function(){
            HabilitarBotonera();
        }, 900);
    });

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
    });

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
            "Cuerpo": "<h4>¿Est&aacute; usted seguro de querer eliminar la Plantilla de Mantenimiento?</h4>",
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
    })

    $('.botoneraFormulario').on('click','#GuardarRegistro',function(){
        var Valido = true;
        var Tareas = [];

        $('.formulario-siama form .form-control').each(function(){
            $(this).removeClass('is-invalid');
            if($(this).hasClass('obligatorio') && $(this).val().trim() == ""){

                if(Valido)
                    $(this).focus();
                
                Valido = false;
                $(this).addClass('is-invalid');
            }
        })

        if(Valido && $('#FrecuenciaMan').val() < 0){
            Valido = false;
            SetAlertaFormulario("La frecuencia tiene que se un numero mayor a 0.");
        }

        if(Valido){
            Tareas = ObtenerJsonTareas();
            Valido = (Tareas.length) > 0;

            if(!Valido){
                SetAlertaFormulario("No se puede guardar Plantilla de Mantenimiento debido a que no posee tareas.");
            }
        }

        if(Valido){
            var data = {
                "Lista": $('#listaBusquedaFormulario').html().trim(),
                "Tipo": Plantilla
            }
    
            SetSearchModal(data,false)
            SetModalEtqContador("")
            SetSearchType("Formulario");

            var parametros = {
                "id"            : $('#IdForm').text().trim(),
                "Estatus"       : $('#EstatusPlantilla').val().trim(),
                "Documento"     : $('#DocumentoPlantilla').val().trim(),
                "Bien"          : $('#idBiePlantilla').text().trim(),
                "Frecuencia"    : $('#FrecuenciaMan').val(),
                "UltMan"        : $('#UltimoMantenimiento').val(),
                "Tareas"        : Tareas,
                "Observacion"   : $('#ObservacionPlantilla').val().trim(),
                "Url"           : $('#FormularioActual').attr("action")
            }
            
            if(!Guardando){
                EstablecerBuscador();
                Guardando = true;
                GuardarFormulario(parametros);
            }
        }
        
    });

    $('#SiamaModalAdvertencias').on('click','#ConfirmarEliminacion',function(){
        var parametros = {
            "id": $('#IdForm').text().trim(),
            "Caso":"Eliminar",
            "Url": $('#ControladorActual').text().trim()+"/obtener"
        }
        Obtener(parametros);
    });

    function AccionEliminar(){

        if($('#EstatusPlantilla').val().trim() == "Aprobado"){
            
            Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn  btn-danger">
            <span class="fa fa-times "></span>
            Cerrar
            </button>`;

            Cuerpo = `No se puede eliminar <strong>Plantilla</strong> debido a que el estatus ha cambiado a 
            <strong>Aprobado</strong>.`;

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
        $('#TablaTareasPlantilla th:nth-child(4)').show();
        $('#TablaTareasPlantilla th:nth-child(5)').show();
    }

    function AdvertenciaCambiarBien(opcion){

        Botones = `
        <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn  btn-danger">
          <span class="fa fa-times "></span>
          Cerrar
        </button>`;

        Cuerpo = `No se puede ${opcion} <strong>Bien</strong> debido a que tiene asociado 
        al menos una <strong>Tarea</strong>.`;


        var parametros = {
            "Titulo":"Advetencia",
            "Cuerpo": Cuerpo,
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
    }

    function AgregarBotoneraPlantilla(Tipo){
        
        btnAgregar = `
            <button type="button"  class="btn btn-primary-siama" id="AgregarRegistro">
                <span class="fa fa-plus"></span>
                Agregar
            </button>
        `;

        btnBuscar =`
            <button  type="button"  class="btn btn-primary-siama" id="BuscarRegistro">
                <span class="fa fa-search"></span>
                Buscar
            </button>
        `;

        btnEditar = `
            <button type="button"  class="btn btn-primary-siama" id="EditarRegistro">
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
            <button title="Aprobar" type="button"  class="btn btn-success" id="AprobarRegistro">
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
                html = btnBuscar + btnAgregar + btnDesaprobar;
            break;
            default:
                html = ""

        }
        if(html != ""){
            $('.botoneraFormulario').children().remove();
            $('.botoneraFormulario').append(html);
        }
    }
    
    function BuscarBien(){

        SetSearchThead(thBienes);

        parametros = {
            "Lista": $('#listaBusquedaBien').html().trim(),
            "Tipo": Bienes
        }

        idBuscadorActual = $('#idBiePlantilla').text().trim();
        nombreBuscadorActual = $('#nomBiePlantilla').val().trim();

        SetSearchModal(parametros)

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
        $('#idBiePlantilla').text('');
        $('#alertaFormularioActual').hide();
        $('#TablaTareasPlantilla > tbody').children().remove();
        $('#TablaReparacionesCorrectivas > tbody').children().remove();

        $('.formulario-siama form .form-control').each(function(){
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
        $('#TablaTareasPlantilla th:nth-child(4)').hide();
        $('#TablaTareasPlantilla th:nth-child(5)').hide();
    }

    function Editar(){

        if($('#EstatusPlantilla').val().trim() == "Aprobado"){
            
            Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn  btn-danger">
            <span class="fa fa-times "></span>
            Cerrar
            </button>`;

            Cuerpo = `No se puede editar <strong>Plantilla de Mantenimiento</strong> debido a que el estatus ha cambiado a 
            <strong>aprobado</strong>.`;

            var parametros = {
                "Titulo":"Advetencia",
                "Cuerpo": Cuerpo,
                "Botones":Botones
            }

            ModalAdvertencia(parametros);
        }else{
            GuardarEstadoActualFormulario();
            HabilitarFormulario()

            if($('#EstatusPlantilla').val() == "Solicitado"){
                $('#EstatusPlantilla').attr("disabled", "disabled");
                $('#EstatusPlantilla').attr("readonly", "readonly");
                ActivarTareas();
            }else{
                $('.formulario-siama form .form-control').each(function(){
                    $(this).attr("disabled", "disabled");
                    $(this).attr("readonly", "readonly");
                })

                DesactivarTareas();
                $('#ObservacionPlantilla').removeAttr("disabled"); 
                $('#ObservacionPlantilla').removeAttr("readonly");
                setTimeout(function(){$('#ObservacionPlantilla').focus();}, 400);
            }

            
            $(window).scrollTop(0);
        }
    }
    
    function EstablecerBuscador(){
        SetSearchThead(thPlantillas);
    }

    function GetUrlBusquedaOpcion(opcion){
        switch(opcion){
            case TipoPieza:
                controlador = "tipopieza";
            break;
            case Herramienta:
                controlador = "herramientas";
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
        idBiePlantilla = $('#idBiePlantilla').text().trim();

        Tareas = $('#TablaTareasPlantilla > tbody').html();
        $('.formulario-siama form .form-control').each(function(){
            dataInputs.push($(this).val().trim());
        })
    }

    function RestablecerEstadoAnteriorFormulario(){
        
        var parametros = {
            "id"            : idActual.trim(),
            "idBien"        : idBiePlantilla.trim(),     
            "Documento"     : dataInputs[0].trim(),  
            "Estatus"       : dataInputs[1].trim(),
            "nomBien"       : dataInputs[2].trim(),
            "Frecuencia"    : dataInputs[3].trim(),
            "UltMan"        : dataInputs[4].trim(),
            "Observaciones" : dataInputs[5].trim(),
            "Tareas"        : Tareas
        }

        LlenarFormulario(parametros);
    }

    function LlenarFormulario(data){
        $('#TablaTareasPlantilla > tbody').children().remove();

        if(data['Estatus'] != "Solicitado")
            DesactivarTareas();
        else
            ActivarTareas();

        AgregarBotoneraPlantilla(data['Estatus']);


        $('#IdForm').text(data['id']);
        $('#DocumentoPlantilla').val(data['Documento']);
        $('#EstatusPlantilla').val(data['Estatus']);
        $('#idBiePlantilla').text(data['idBien']);
        $('#UltimoMantenimiento').val(data['UltMan']);
        $('#nomBiePlantilla').val(data['nomBien']);
        $('#FrecuenciaMan').val(data['Frecuencia']);
        $('#ObservacionPlantilla').val(data['Observaciones']);
        $('#TablaTareasPlantilla > tbody:last-child').append(data['Tareas']);

    }

    function LlenarFormularioRequest(data){
        
        var parametros = {
            "id"            : data['plm_id'],
            "idBien"        : data['bie_id'],     
            "Documento"     : data['documento'],  
            "Estatus"       : data['estatus'],
            "UltMan"        : data['fec_ult'],
            "nomBien"       : data['bie_nom'],
            "Frecuencia"    : data['frecuencia'],
            "Observaciones" : data['observaciones'],
            "Tareas"        : data['Tareas']
        }

        LlenarFormulario(parametros);
        
    }

    function Obtener(parametros){

        if(parametros['Caso'] == 'Eliminar')
            MostrarEstatus(3); 
        else
            MostrarEstatus(5); 

        $.ajax({
            url: parametros['Url'],
            type: "POST",
            data: parametros,
            dataType: 'json'
        }).done(function(data){
            $(window).scrollTop(0);
            if(data['isValid']){
                setTimeout(function(){

                    CerrarEstatus();
                },600)
                LlenarFormularioRequest(data['Datos']);
                $('#SiamaModalBusqueda').modal('hide');
                
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

    function SetSearchModal(data,buscar =true,condiciones = {}){

        SetSearchType(data['Tipo']);
        
        switch(data['Tipo']){
            case TipoPieza:
                controlador = "Tipo de Piezas Disponibles";
            break;
            case Herramienta:
                controlador = "Herramientas";
            break;
            case Plantilla:
                controlador = "Plantillas de Mantenimiento";
            break;
            case Bienes:
            controlador = "Bienes";
            break;
        }

        SetModalEtqContador(controlador)
        SetSearchCOB(data['Lista']);


        SetSearchTitle('Busqueda ' + controlador);
        PrimeraVezBusqueda = true;
        SetUrlBusqueda(GetUrlBusquedaOpcion(data['Tipo']));

        if(buscar)
            Busqueda(1,false,condiciones);
    }

    window.AccionEliminarFormulario = function(data){
        
        if(data['Datos']['plm_id'] == ""){
            ClearForm();
            AgregarBotoneraPrimariaNULL();
        }else{
            LlenarFormularioRequest(data['Datos']);
        }
    }

    window.AccionGuardar = function(data){
        LlenarFormularioRequest(data['Datos']);
    }

    window.BuscarHerramienta = function(tipo){

        SetOrigenBuscador(origenFuncion);
        SetSearchThead(thHerramientas);

        parametros = {
            "Lista": $('#listaBusquedaHerramienta').html().trim(),
            "Tipo": tipo,
        }

        SetSearchModal(parametros,true);
    }

    window.BuscarTipoPieza = function(tipo){

        SetOrigenBuscador(origenFuncion);
        SetSearchThead(thTipoPieza);

        condiciones = {
            "Bien"      : $('#idBiePlantilla').text().trim(),
            "delBien"   : true,
        }

        parametros = {
            "Lista": $('#listaBusquedaTipoPieza').html().trim(),
            "Tipo": tipo,
        }

        idBuscadorActual = $('#idTPTarea').text().trim();
        nombreBuscadorActual = $('#nomTPTarea').val().trim();

        SetSearchModal(parametros,true,condiciones);
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
            case TipoPieza:
                $('#idTPTarea').text(fila.find("td:eq(0)").text().trim());
                $('#nomTPTarea').val(fila.find("td:eq(1)").text().trim());
            break;
            case Herramienta:
                ElegirHerramienta(fila);
            break;
            case Bienes:
                $('#idBiePlantilla').text(fila.find("td:eq(0)").text().trim());
                $('#nomBiePlantilla').val(fila.find("td:eq(1)").text().trim());
            break;
        }

        if(GetSearchType() != "Formulario"  ){
            $('#SiamaModalBusqueda').modal('hide');

            if(GetSearchType() != Bienes){
                $('#SiamaModalFunciones').show();
            }
        }
    }

});