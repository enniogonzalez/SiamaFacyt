

var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){
    const CorrectivoPlanificado = "Correctivo Planificado";
    const Correctivo = "Correctivo";
    const Preventivo = "Preventivo";

    var idActual ="";
    var dataInputs= [];
    var idBuscadorActual = "";
    var nombreBuscadorActual = "";
    var idCorrectivo = "";
    var PiezasDanadas = "";

    EstablecerBuscador();



    $('#CancelarModalBuscar').on('click',function(){
        switch(GetSearchType()){
            case PiezaDA:
                $('#idPieza').text(idBuscadorActual.trim());
                $('#nomPiezaDA').val(nombreBuscadorActual.trim());
            break;
            case Correctivo:
                $('#idCorrectivo').text(idBuscadorActual.trim());
                $('#ManCorrectivo').val(nombreBuscadorActual.trim());
            break;
            case Preventivo:
                $('#idPreventivo').text(idBuscadorActual.trim());
                $('#ManPreventivo').val(nombreBuscadorActual.trim());
            break;
        }

    })

    /************************************/
    /*      Inicio Buscadores           */
    /************************************/
    

    /************************************/
    /*          Manejo Correctivo       */
    /************************************/
    
    $('#ManCorrectivo').on('click',function(){

        if(ExistePiezaDA())
            AdvertenciaCambiarBien("cambiar");
        else
            BuscarCorrectivo();
    });

    $('.BuscarCorrectivo').on('click',function(){
        if(ExistePiezaDA())
            AdvertenciaCambiarBien("cambiar");
        else
            BuscarCorrectivo();
    });

    $('.BorrarCorrectivo').on('click',function(){
        
        if(ExistePiezaDA())
            AdvertenciaCambiarBien("eliminar");
        else{
            BorrarMantenimientos()
        }
    });

    /************************************/
    /*          Manejo Preventivo       */
    /************************************/
    
    $('#ManPreventivo').on('click',function(){

        if(ExistePiezaDA())
            AdvertenciaCambiarBien("cambiar");
        else
            BuscarPreventivo();
    });

    $('.BuscarPreventivo').on('click',function(){
        if(ExistePiezaDA())
            AdvertenciaCambiarBien("cambiar");
        else
            BuscarPreventivo();
    });

    $('.BorrarPreventivo').on('click',function(){
        
        if(ExistePiezaDA())
            AdvertenciaCambiarBien("eliminar");
        else{
            BorrarMantenimientos()
        }
    });

    /************************************/
    /*          Fin Buscadores          */
    /************************************/

    $('.botoneraFormulario').on('click','#AgregarRegistro',function(){
        GuardarEstadoActualFormulario();
        ClearForm();
        HabilitarFormulario();
        EsconderMantenimientos();
        $('#EstatusPlanificado').val("Solicitado");
        $('#nomBien').attr("disabled", "disabled");
        $('#nomBien').attr("readonly", "readonly");
        $(window).scrollTop(0);
    })

    $('.botoneraFormulario').on('click','#AprobarRegistro',function(){
        
        var parametros = {
            "id": $('#IdForm').text().trim(),
            "Tipo": "Aprobar",
            "Url":$('#ControladorActual').text().trim() + "/aprobar" 
        }

        //Evitar doble click
        if(!Guardando){
            Guardando = true;
            AprobarAjuste(parametros)
        }
    });

    $('.botoneraFormulario').on('click','#BuscarRegistro',function(){
        SetSearchType('Formulario');
        SetSearchTitle('Busqueda Mantenimiento Correctivo Planificado');
        PrimeraVezBusqueda = true;
        DeshabilitarBotonera();
        SetUrlBusqueda($('#ControladorActual').text().trim()+"/busqueda");
        Busqueda(1,true);
        
        setTimeout(function(){
            HabilitarBotonera();
        }, 900);
    })

    $('.botoneraFormulario').on('click','#CancelarRegistro',function(){
        ClearForm();
        RestablecerEstadoAnteriorFormulario();
        EstablecerBuscador();
        DeshabilitarFormulario();

        AgregarBotoneraPlanificado($('#EstatusPlanificado').val().trim());

        parametros = {
            "Lista": $('#listaBusquedaFormulario').html().trim(),
            "Tipo": CorrectivoPlanificado
        }

        SetSearchModal(parametros,false)
        SetModalEtqContador("")
        SetSearchType("Formulario");
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
            "Cuerpo": "<h4>¿Est&aacute; usted seguro de querer eliminar el Mantenimiento Correctivo Planificado?</h4>",
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
    })

    $('.botoneraFormulario').on('click','#GuardarRegistro',function(){
        var Valido = true;
        var Agregados = [];
        var PiezaDAs = [];

        $('.formulario-siama form .form-control').removeClass('is-invalid');
        $('.formulario-siama form .form-control').each(function(){
            if($(this).hasClass('obligatorio') && $(this).val().trim() == ""){

                if(Valido)
                    $(this).focus();
                
                Valido = false;
                $(this).addClass('is-invalid');
            }
        })

        valor = $('#OrigenPlanificado').val().trim();
        if(Valido && valor == "Mantenimiento Preventivo" && $('#idPreventivo').text().trim() == ""){
            Valido = false;
            $('#ManPreventivo').addClass('is-invalid');
        }
        
        if(Valido && valor == "Mantenimiento Correctivo" && $('#idCorrectivo').text().trim() == ""){
            Valido = false;
            $('#ManCorrectivo').addClass('is-invalid'); 
        }


        if(Valido && !ExistePiezaDA()){
            Valido = false;
            SetAlertaFormulario("No se puede guardar Mantenimiento Correctivo Planificado, debido a que debe existir al menos una pieza dañada.");
        }

        if(Valido){
            var data = {
                "Lista": $('#listaBusquedaFormulario').html().trim(),
                "Tipo": CorrectivoPlanificado
            }
    
            SetSearchModal(data,false)
            SetModalEtqContador("")
            SetSearchType("Formulario");
            PiezaDAs = ObtenerJsonPiezasDA();
            var parametros = {
                "id"            : $('#IdForm').text().trim(),
                "Documento"     : $('#DocumentoPlanificado').val().trim(),
                "Correctivo"    : $('#idCorrectivo').text().trim(),
                "Preventivo"    : $('#idPreventivo').text().trim(),
                "fec_eje"       : $('#FechaEjecucion').val().trim(),
                "PiezaDAs"      : PiezaDAs,
                "Observacion"   : $('#ObservacionCP').val().trim(),
                "Url"           : $('#FormularioActual').attr("action")
            }

            console.log(parametros);
            if(!Guardando){
                EstablecerBuscador();
                Guardando = true;
                GuardarFormulario(parametros);
            }
        }
        
    });

    $('#OrigenPlanificado').on('change',function(){
        EsconderMantenimientos();
        
        BorrarMantenimientos();
        mostrarMantenimientos();
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

        if($('#EstatusPlanificado').val().trim() == "Aprobado"){
            
            Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn  btn-danger">
            <span class="fa fa-times "></span>
            Cerrar
            </button>`;

            Cuerpo = `No se puede eliminar <strong>Mantenimiento Correctivo Planificado</strong> debido a que el estatus ha cambiado a 
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

    function AdvertenciaCambiarBien(opcion){

        Botones = `
        <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn  btn-danger">
          <span class="fa fa-times "></span>
          Cerrar
        </button>`;

        Cuerpo = `No se puede ${opcion} <strong>Bien</strong> debido a que tiene asociado al menos una pieza.`;


        var parametros = {
            "Titulo":"Advetencia",
            "Cuerpo": Cuerpo,
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
    }

    function AgregarBotoneraPlanificado(Tipo){
        
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

        switch(Tipo){
            case "Solicitado":
                html = btnBuscar + btnEditar + btnAgregar + btnAprobar + btnEliminar;
            break;
            case "Aprobado":
                html = btnBuscar  + btnAgregar;
            break;
            default:
                html = btnAgregar;

        }
        if(html != ""){
            $('.botoneraFormulario').children().remove();
            $('.botoneraFormulario').append(html);
        }
    }

    function AprobarAjuste(parametros){

        MostrarEstatus(6); 

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

                MostrarEstatus(7,true); 

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

    function BorrarMantenimientos(){
        
        $('#idCorrectivo').text("");
        $('#ManCorrectivo').val("");
        $('#idPreventivo').text("");
        $('#ManPreventivo').val("");
        $('#nomBien').val("");
        
        $('#OrigenPlanificado').removeAttr("disabled");
        $('#OrigenPlanificado').removeAttr("readonly");
    }
    
    function BuscarCorrectivo(){
        SetSearchThead(thCorrectivos);

        parametros = {
            "Lista": $('#listaBusquedaCorrectivo').html().trim(),
            "Tipo": Correctivo,
        }

        idBuscadorActual = $('#idCorrectivo').text().trim();
        nombreBuscadorActual = $('#ManCorrectivo').val().trim();

        SetSearchModal(parametros,true,true);
    }
    
    function BuscarPreventivo(){
        SetSearchThead(thPreventivos);

        parametros = {
            "Lista": $('#listaBusquedaPreventivo').html().trim(),
            "Tipo": Preventivo,
        }

        idBuscadorActual = $('#idPreventivo').text().trim();
        nombreBuscadorActual = $('#ManPreventivo').val().trim();

        SetSearchModal(parametros,true,true);
    }

    function ClearForm(){
        
        $('#IdForm').text(''); 
        BorrarMantenimientos()
        $('#alertaFormularioActual').hide();
        $('#TablaAgregarPiezas > tbody').children().remove();
        $('#TablaPiezasDañadas > tbody').children().remove();

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

    function Editar(){

        if($('#EstatusPlanificado').val().trim() == "Aprobado"){
            
            Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn  btn-danger">
            <span class="fa fa-times "></span>
            Cerrar
            </button>`;

            Cuerpo = `No se puede editar <strong>Mantenimiento Correctivo Planificado</strong> debido a que el estatus ha cambiado a 
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
    
            $('#EstatusPlanificado').attr("disabled", "disabled");
            $('#EstatusPlanificado').attr("readonly", "readonly");
            
            $('#OrigenPlanificado').attr("disabled", "disabled");
            $('#OrigenPlanificado').attr("readonly", "readonly");

            $('#nomBien').attr("disabled", "disabled");
            $('#nomBien').attr("readonly", "readonly");

            $(window).scrollTop(0);
        }
    }

    function EsconderMantenimientos(){
        $('#divCorrectivo').hide();
        $('#divPreventivo').hide();
        $('#divBien').hide();
    }

    function EstablecerBuscador(){
        SetSearchThead(thCorrectivoPlanificado);
    }

    function GetUrlBusquedaOpcion(opcion){
        switch(opcion){
            case PiezaDA:
                controlador = "piezas/busqueda";
            break;
            case Correctivo:
                controlador = "correctivo/busquedaRealizado";
            break;
            case Preventivo:
                controlador = "preventivo/busquedaRealizado";
            break;
            case Falla:
                controlador = "fallas/busqueda";
            break;
        }

        return $('#UrlBase').text() + "/" + controlador + ""
    }
    
    function GuardarEstadoActualFormulario(){
        dataInputs = [];
        idActual =$('#IdForm').text().trim();
        idCorrectivo = $('#idCorrectivo').text().trim();
        idPreventivo = $('#idPreventivo').text().trim();
        idBien = $('#idBien').text().trim();

        PiezasDanadas = $('#TablaPiezasDañadas > tbody').html();
        
        $('.formulario-siama form .form-control').each(function(){
            dataInputs.push($(this).val().trim());
        })
    }
    
    function LlenarFormulario(data){

        $('#TablaPiezasDañadas > tbody').children().remove();
        AgregarBotoneraPlanificado(data['Estatus']);

        $('#IdForm').text(data['id']);
        $('#DocumentoPlanificado').val(data['Documento']);
        $('#EstatusPlanificado').val(data['Estatus']);
        $('#idCorrectivo').text(data['idCorrectivo']);
        $('#ManCorrectivo').val(data['mco_doc']);
        $('#idPreventivo').text(data['idPreventivo']);
        $('#ManPreventivo').val(data['man_doc']);
        $('#OrigenPlanificado').val(data['Origen']);
        $('#FechaEjecucion').val(data['Ejecucion']);
        $('#idBien').val(data['idBien']);
        $('#nomBien').val(data['nomBien']);
        $('#ObservacionCP').val(data['Observaciones']);
        $('#TablaPiezasDañadas > tbody:last-child').append(data['Piezas']);

        if(data['Documento'] == "")
            EsconderMantenimientos();
        else
            mostrarMantenimientos();
    }

    function LlenarFormularioRequest(data){
      
        var parametros = {
            "id"                : data['cpl_id'],
            "Documento"         : data['documento'],
            "idBien"            : data['idBien'],
            "nomBien"           : data['bie_nom'],
            "Observaciones"     : data['observaciones'],
            "Estatus"           : data['estatus'],
            "Origen"            : data['origen'],
            "Ejecucion"         : data['fec_eje'],
            "idCorrectivo"      : data['mco_id'],
            "idPreventivo"      : data['man_id'],
            "man_doc"           : data['man_doc'],
            "mco_doc"           : data['mco_doc'],
            "Piezas"            : data['Piezas']
        }

        console.log(parametros)
        LlenarFormulario(parametros);
        
    }

    function mostrarMantenimientos(){
        
        valor = $('#OrigenPlanificado').val().trim();

        if( valor!= ""){
            $('#divBien').show();
            
            if(valor == "Mantenimiento Correctivo")
                $('#divCorrectivo').show();
            else
                $('#divPreventivo').show();
        }
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

    function RestablecerEstadoAnteriorFormulario(){
    
        var parametros = {
            "id"            : idActual.trim(),
            "idBien"        : idBien.trim(),
            "idCorrectivo"  : idCorrectivo.trim(),
            "idPreventivo"  : idPreventivo.trim(),
            "Documento"     : dataInputs[0].trim(),  
            "Estatus"       : dataInputs[1].trim(),
            "Origen"        : dataInputs[2].trim(),
            "mco_doc"       : dataInputs[3].trim(),
            "man_doc"       : dataInputs[4].trim(),
            "nomBien"       : dataInputs[5].trim(),
            "Ejecucion"     : dataInputs[6].trim(),
            "Observaciones" : dataInputs[7].trim(),
            "Piezas"        : PiezasDanadas
        }

        LlenarFormulario(parametros);
    }

    function SetSearchModal(data,buscar =true,verFecha = false,condiciones = {}){
        SetSearchType(data['Tipo']);
        
        switch(data['Tipo']){
            case PiezaDA:
                controlador = "Piezas del Bien";
            break;
            case Correctivo:
                controlador = "Mantenimientos Correctivos Realizados";
            break;
            case Preventivo:
                controlador = "Mantenimientos Preventivos Realizados";
            break;
            case Falla:
                controlador = "Fallas";
            break;
            case CorrectivoPlanificado:
                controlador = "Mantenimientos Correctivos Planificados";
            break;
        }

        SetModalEtqContador(controlador)
        SetSearchCOB(data['Lista']);


        SetSearchTitle('Busqueda ' + controlador);
        PrimeraVezBusqueda = true;
        SetUrlBusqueda(GetUrlBusquedaOpcion(data['Tipo']));

        if(buscar)
            Busqueda(1,verFecha,condiciones);
    }

    window.AccionGuardar = function(data){
        LlenarFormularioRequest(data['Datos']);
    }

    window.AccionEliminarFormulario = function(data){
        
        if(data['Datos']['cpl_id'] == ""){
            ClearForm();
            AgregarBotoneraPrimariaNULL();
        }else{
            LlenarFormularioRequest(data['Datos']);
        }
    }
    
    window.BuscarFalla = function(tipo){

        SetOrigenBuscador(origenFuncion);
        SetSearchThead(thFallas);

        parametros = {
            "Lista": $('#listaBusquedaFalla').html().trim(),
            "Tipo": tipo,
        }

        idBuscadorActual = $('#idFalla').text().trim();
        nombreBuscadorActual = $('#nomFalla').val().trim();

        SetSearchModal(parametros,true,false);
    }
    
    window.BuscarPieza = function(tipo){

        SetOrigenBuscador(origenFuncion);
        SetSearchThead(thPiezas);

        parametros = {
            "Lista": $('#listaBusquedaPieza').html().trim(),
            "Tipo": tipo,
        }

        condiciones = {
            "Bien"          : $('#idBien').text().trim(),
            "PiezasBien"    : true
        }

        idBuscadorActual = $('#idPieza').text().trim();
        nombreBuscadorActual = $('#nomPiezaDA').val().trim();

        console.log(condiciones);
        SetSearchModal(parametros,true,false,condiciones);
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
            case PiezaDA:
                $('#idPieza').text(fila.find("td:eq(0)").text().trim());
                $('#InvPieza').text(fila.find("td:eq(3)").text().trim());
                $('#nomPiezaDA').val(fila.find("td:eq(1)").text().trim());
            break;
            case Falla:
                $('#idFalla').text(fila.find("td:eq(0)").text().trim());
                $('#nomFalla').val(fila.find("td:eq(1)").text().trim());
            break;
            case Correctivo:
                $('#idCorrectivo').text(fila.find("td:eq(0)").text().trim());
                $('#ManCorrectivo').val(fila.find("td:eq(1)").text().trim());
                $('#idBien').text(fila.find("td:eq(3)").text().trim());
                $('#nomBien').val(fila.find("td:eq(4)").text().trim());
                $('#OrigenPlanificado').attr("disabled", "disabled");
                $('#OrigenPlanificado').attr("readonly", "readonly");
            break;
            case Preventivo:
                $('#idPreventivo').text(fila.find("td:eq(0)").text().trim());
                $('#ManPreventivo').val(fila.find("td:eq(1)").text().trim());
                $('#idBien').text(fila.find("td:eq(3)").text().trim());
                $('#nomBien').val(fila.find("td:eq(4)").text().trim());
                $('#OrigenPlanificado').attr("disabled", "disabled");
                $('#OrigenPlanificado').attr("readonly", "readonly");
            break;
        }

        if(GetSearchType() != "Formulario"  ){
            $('#SiamaModalBusqueda').modal('hide');
        }
    }
});