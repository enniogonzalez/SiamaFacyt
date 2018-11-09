

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
            case Usuario:
                $('#idUsu').text(idBuscadorActual.trim());
                $('#nomUsu').val(nombreBuscadorActual.trim());
            break;
            case Bienes:
                $('#idBiePreventivo').text(idBuscadorActual.trim());
                $('#nomBiePreventivo').val(nombreBuscadorActual.trim());
            break;
        }
        if(GetSearchType() != "Formulario" && GetSearchType() != Bienes && GetSearchType() != Plantilla){
            
            //Prevenir solapamientos de modales
            setTimeout(function(){ 
                $('#SiamaModalFunciones').modal('show');}, 400);
        }
    })


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
            "Cuerpo": "<h4>¿Est&aacute; usted seguro de querer borrar el Mantenimiento Preventivo?</h4>",
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
    })

    $('.botoneraFormulario').on('click','#BuscarRegistro',function(){
        
        EstablecerBuscador();
        SetSearchType('Formulario');
        SetSearchTitle('Busqueda Mantenimiento Preventivo');
        PrimeraVezBusqueda = true;
        DeshabilitarBotonera();
        SetUrlBusqueda($('#ControladorActual').text().trim()+"/busqueda");
        Busqueda(1,true);
        
    })

    $('#SiamaModalAdvertencias').on('click','#ConfirmarEliminacion',function(){
        var parametros = {
            "id": $('#IdForm').text().trim(),
            "Url": $('#ControladorActual').text().trim()+"/eliminar"
        }
        Eliminar(parametros)
    });

    $('.botoneraFormulario').on('click','#EditarRegistro',function(){
        GuardarEstadoActualFormulario();
        HabilitarFormulario()

        if($('#EstatusPreventivo').val() == "Solicitado"){
            $('#EstatusPreventivo').attr("disabled", "disabled");
            $('#EstatusPreventivo').attr("readonly", "readonly");
            ActivarTareas();
        }else{
            $('.formulario-siama form .form-control').each(function(){
                $(this).attr("disabled", "disabled");
                $(this).attr("readonly", "readonly");
            })

            DesactivarTareas();
            $('#ObservacionPreventivo').removeAttr("disabled"); 
            $('#ObservacionPreventivo').removeAttr("readonly");
            setTimeout(function(){$('#ObservacionPreventivo').focus();}, 400);
        }

        
        $(window).scrollTop(0);
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
            SetAlertaFormulario("No se puede guardar Mantenimiento Preventivo debido a que existen tareas que no poseen Usuario o Proveedor que las ejecuten.");
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
    
    function DesactivarTareas(){
        $('#eliminarTarea').hide();
    }

    function ActivarTareas(){
        $('#eliminarTarea').show();
    }

    function BuscarBien(){

        SetSearchThead(thBienes);

        parametros = {
            "Lista": $('#listaBusquedaBien').html().trim(),
            "Tipo": Bienes
        }

        idBuscadorActual = $('#idBiePreventivo').text().trim();
        nombreBuscadorActual = $('#nomBiePreventivo').val().trim();
        SetSearchModal(parametros)

    }
    
    function BuscarPlantilla(){


        SetSearchThead(thPlantillas);

        parametros = {
            "Lista": $('#listaBusquedaPlantilla').html().trim(),
            "Tipo": Plantilla
        }

        SetSearchModal(parametros)

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
            case Usuario:
                controlador = "usuarios";
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

    function EstablecerBuscador(){
        SetSearchThead(thPreventivos);
    }

    function ClearForm(){
        
        $('#IdForm').text(''); 
        $('#idBiePreventivo').text('');
        $('#alertaFormularioActual').hide();
        $('#TablaTareas > tbody').children().remove();
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
    
    function GuardarEstadoActualFormulario(){
        dataInputs = [];
        idActual =$('#IdForm').text().trim();
        plmActual =$('#PlantillaMantenimiento').text().trim();
        idBiePreventivo = $('#idBiePreventivo').text().trim();

        Tareas = $('#TablaTareas > tbody').html();
        $('.formulario-siama form .form-control').each(function(){
            dataInputs.push($(this).val().trim());
        })
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
                $('#SiamaModalBusqueda').modal('hide');
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
                $('#SiamaModalBusqueda').modal('hide');
            }
        }).fail(function(data){
            failAjaxRequest(data);
        });
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

    function AgregarBotoneraPreventivo(Tipo){
        
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

    window.BuscarProveedor = function(tipo){

        SetSearchThead(thProveedores);

        parametros = {
            "Lista": $('#listaBusquedaProveedor').html().trim(),
            "Tipo": tipo
        }

        idBuscadorActual = $('#idPro').text().trim();
        nombreBuscadorActual = $('#nomPro').val().trim();

        SetSearchModal(parametros)

    }

    window.BuscarUsuario = function(tipo){
        SetSearchThead(thUsuarios);
        parametros = {
            "Lista": $('#listaBusquedaUsuario').html().trim(),
            "Tipo": tipo,
        }

        idBuscadorActual = $('#idUsu').text().trim();
        nombreBuscadorActual = $('#nomUsu').val().trim();

        SetSearchModal(parametros)
    }

    window.ActivarCeldaTabla = function(fila){

        //Se busca el indice de la fila que esta activa
        var indexAnt = $('.tr-activa-siama').index();
        //Se busca el indice de la fila que fue seleccionada
        var indexAct = $(fila).index();
        //Se remueve la clase activa de la fila que esta activa
        $('.tr-activa-siama').removeClass('tr-activa-siama');

        //En caso de que los dos indices encontrado anteriormente
        //sean diferentes, de agrega la clase activa a la fila seleccionada
        //esto con la intension de que si se selecciona la misma fila
        //activa, la misma se desactive
        if(indexAnt != indexAct)
            $(fila).addClass('tr-activa-siama');

    }

    window.AccionGuardar = function(data){
        LlenarFormularioRequest(data['Datos']);
    }

    window.BuscarPieza = function(tipo){


        SetSearchThead(thPiezas);
        parametros = {
            "Lista": $('#listaBusquedaPieza').html().trim(),
            "Tipo": tipo,
        }

        condiciones = {
            "Bien"          : $('#idBiePreventivo').text().trim(),
            "PiezasBien"    : false
        }

        switch(tipo){
            case Pieza:
                idBuscadorActual = $('#idPiezaTarea').text().trim();
                nombreBuscadorActual = $('#nomPiezaTarea').val().trim();
                condiciones['PiezasBien'] = true;
            break;
        }

        SetSearchModal(parametros,true,condiciones);
    }

    window.InterfazElegirBuscador = function(fila){
        
        switch(GetSearchType()){
            case "Formulario":
                var parametros = {
                    "id": fila.find("td:eq(0)").text().trim(),
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
            case Usuario:
                $('#idUsu').text(fila.find("td:eq(0)").text().trim());
                $('#nomUsu').val(fila.find("td:eq(3)").text().trim());
                $('#idPro').text('');
                $('#nomPro').val('');
            break;
            case Proveedor:
                $('#idPro').text(fila.find("td:eq(0)").text().trim());
                $('#nomPro').val(fila.find("td:eq(6)").text().trim());
                $('#idUsu').text('');
                $('#nomUsu').val('');
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
            $('#SiamaModalBusqueda').modal('hide');

            //Prevenir solapamientos de modales
            if(GetSearchType() != Bienes )
                setTimeout(function(){ $('#SiamaModalFunciones').modal('show');}, 400);
        }
    }

    window.AccionEliminarFormulario = function(data){
        
        if(data['Datos']['man_id'] == ""){
            ClearForm();
            AgregarBotoneraPrimariaNULL();
        }else{
            LlenarFormularioRequest(data['Datos']);
        }
    }
});