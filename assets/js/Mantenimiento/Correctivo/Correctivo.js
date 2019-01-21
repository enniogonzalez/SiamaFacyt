

var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){

    const Correctivo = "Mantenimientos Correctivos";
    const Bienes = "Bienes";
    const CorrectivoPlanificado = "Mantenimientos Correctivos Planificados";

    var idActual ="";
    var dataInputs= [];
    var idBuscadorActual = "";
    var nombreBuscadorActual = "";
    var idBieCorrectivo = "";
    var idManCorPla = "";
    var idBienManCorPla = "";
    var Cambios = "";
    var Reparaciones = "";

    EstablecerBuscador();

    if($('#EstatusCorrectivo').val() != "Solicitado"){
        DesactivarCambiosReparaciones();
    }


    $('#CancelarModalBuscar').on('click',function(){
        switch(GetSearchType()){
            case ProveedorC:
                $('#idProC').text(idBuscadorActual.trim());
                $('#nomProC').val(nombreBuscadorActual.trim());
            break;
            case ProveedorR:
                $('#idProR').text(idBuscadorActual.trim());
                $('#nomProR').val(nombreBuscadorActual.trim());
            break;
            case PiezaDC:
                $('#idPiezaDC').text(idBuscadorActual.trim());
                $('#nomPiezaDC').val(nombreBuscadorActual.trim());
            break;
            case PiezaDR:
                $('#idPiezaDR').text(idBuscadorActual.trim());
                $('#nomPiezaDR').val(nombreBuscadorActual.trim());
            break;
            case ObreroC:
                $('#idObrCambio').text(idBuscadorActual.trim());
                $('#nomObrCambio').val(nombreBuscadorActual.trim());
            break;
            case ObreroR:
                $('#idObrReparacion').text(idBuscadorActual.trim());
                $('#nomObrReparacion').val(nombreBuscadorActual.trim());
            break;
            case Bienes:
                $('#idBieCorrectivo').text(idBuscadorActual.trim());
                $('#nomBieCorrectivo').val(nombreBuscadorActual.trim());
            break;
        }
        
    })

    /************************************/
    /*      Inicio Buscadores           */
    /************************************/
    
    /************************************/
    /*          Manejo Bienes           */
    /************************************/
    
    $('#nomBieCorrectivo').on('click',function(){

        if(ExisteReparacion() || ExisteCambio() )
            AdvertenciaCambiarOrigen("cambiar","Bien");
        else
            BuscarBien();
    });

    $('.BuscarBienCorrectivo').on('click',function(){
        if(ExisteReparacion() || ExisteCambio() )
            AdvertenciaCambiarOrigen("cambiar","Bien");
        else
            BuscarBien();
    });

    $('.BorrarBienCorrectivo').on('click',function(){
        
        if(ExisteReparacion() || ExisteCambio() )
            AdvertenciaCambiarOrigen("borrar","Bien");
        else{
            BorrarOrigen();
        }
    });

    /************************************/
    /*  Manejo Correctivo Planificado   */
    /************************************/
    
    $('#manCorPla').on('click',function(){
        if(ExisteReparacion() || ExisteCambio() )
            AdvertenciaCambiarOrigen("cambiar","Mantenimiento Correctivo Planificado");
        else
            BuscarCorrectivoPlanificado();
    });

    $('.BuscarManCorPla').on('click',function(){
        if(ExisteReparacion() || ExisteCambio() )
            AdvertenciaCambiarOrigen("cambiar","Mantenimiento Correctivo Planificado");
        else
            BuscarCorrectivoPlanificado();
    });

    $('.BorrarManCorPla').on('click',function(){
        if(ExisteReparacion() || ExisteCambio() )
            AdvertenciaCambiarOrigen("cambiar","Mantenimiento Correctivo Planificado");
        else
            BorrarOrigen();
    });

    /************************************/
    /*          Fin Buscadores          */
    /************************************/

    $('#FinCorrectivo').on('change',function(){
        if($(this).val() != "" && 
        $('#InicioCorrectivo').val() != "" 
        && $(this).val() < $('#InicioCorrectivo').val()){
            $('#InicioCorrectivo').val($(this).val());
        }

    })

    $('#InicioCorrectivo').on('change',function(){
        if($(this).val() != "" && 
        $('#FinCorrectivo').val() != "" 
        && $(this).val() > $('#FinCorrectivo').val()){
            $('#FinCorrectivo').val($(this).val());
        }

    });

    $('.botoneraFormulario').on('click','#AgregarRegistro',function(){
        GuardarEstadoActualFormulario();
        ClearForm();
        HabilitarFormulario();
        CambioOrigen();
        $('.fecha').val('');
        ActivarCambiosReparaciones();
        $('#EstatusCorrectivo').val("Solicitado");
        $('#EstatusCorrectivo').attr("disabled", "disabled");
        $('#EstatusCorrectivo').attr("readonly", "readonly");
        $(window).scrollTop(0);
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
        SetSearchType('Formulario');
        SetSearchTitle('Busqueda Mantenimientos Correctivos');
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

        AgregarBotoneraCorrectiva($('#EstatusCorrectivo').val().trim());

        parametros = {
            "Lista": $('#listaBusquedaFormulario').html().trim(),
            "Tipo": Correctivo
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
            "Cuerpo": "<h4>¿Est&aacute; usted seguro de querer eliminar el Cambio Correctivo?</h4>",
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
    })

    $('.botoneraFormulario').on('click','#GuardarRegistro',function(){
        var Cambios = [];
        var Reparaciones = [];

        var Valido = ValidarFormulario();

        if($('#OrigenCorrectivo').val().trim() == "Bien" && $('#nomBieCorrectivo').val().trim() == ""){
            Valido = false;
            $('#nomBieCorrectivo').addClass('is-invalid');
        }
        
        if($('#OrigenCorrectivo').val().trim() == "Mantenimiento Correctivo Planificado" && $('#manCorPla').val().trim() == ""){
            Valido = false;
            $('#manCorPla').addClass('is-invalid');
            $('#BienManCorPla').addClass('is-invalid');
        }

        if(Valido){
            Cambios = ObtenerJsonCambios();
            Reparaciones = ObtenerJsonReparaciones();

            Valido = (Cambios.length + Reparaciones.length) > 0;

            if(!Valido && $('#EstatusCorrectivo').val().trim() == "Solicitado"){
                SetAlertaFormulario("No se puede guardar Mantenimiento Correctivo si no posee una reparaci&oacute;n o un cambio.");
            }else if(!Valido){
                SetAlertaFormulario("No se puede guardar Mantenimiento Correctivo debido a que no se ha marcado como realizado algun cambio o alguna reparaci&oacute;n.");
            }
        }


        if(Valido){
            var data = {
                "Lista": $('#listaBusquedaFormulario').html().trim(),
                "Tipo": Correctivo
            }
    
            SetSearchModal(data,false)
            SetModalEtqContador("")
            SetSearchType("Formulario");

            var parametros = {
                "id":           $('#IdForm').text().trim(),
                "Estatus":      $('#EstatusCorrectivo').val().trim(),
                "Documento":    $('#DocumentoCorrectivo').val().trim(),
                "Doc_Pla":      $('#idManCorPla').text().trim(),
                "Bie_Pla":      $('#idBienManCorPla').text().trim(),
                "Bien":         $('#idBieCorrectivo').text().trim(),
                "Inicio":       $('#InicioCorrectivo').val().trim(),
                "Fin":          $('#FinCorrectivo').val().trim(),
                "Cambios":      Cambios,
                "Reparaciones": Reparaciones,
                "Observacion":  $('#ObservacionCorrectivo').val().trim(),
                "Url":          $('#FormularioActual').attr("action")
            }
            
            if(!Guardando){
                EstablecerBuscador();
                Guardando = true;
                GuardarFormulario(parametros);
            }
        }
        
    });

    $('#OrigenCorrectivo').on('change',function(){
        CambioOrigen();
    });

    $('#OrigenCorrectivo').on('click',function(e){   
        if(ExisteReparacion() || ExisteCambio() ){
            AdvertenciaCambiarOrigen("cambiar","Origen");
            e.preventDefault();
            this.blur();
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

        if($('#EstatusCorrectivo').val().trim() != "Solicitado"){
            
            Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn  btn-danger">
            <span class="fa fa-times "></span>
            Cerrar
            </button>`;

            Cuerpo = `No se puede eliminar <strong>Mantenimiento Correctivo</strong> debido a que el estatus ha cambiado a 
            <strong>${$('#EstatusCorrectivo').val().trim()}</strong>.`;

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

    function ActivarCambiosReparaciones(){
        $('#agregarCambio').show();
        $('#eliminarCambio').show();
        $('#agregarReparacion').show();
        $('#eliminarReparacion').show();
    }
  
    function AdvertenciaCambiarOrigen(opcion,origen){

        Botones = `
        <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn  btn-danger">
          <span class="fa fa-times "></span>
          Cerrar
        </button>`;

        Cuerpo = `No se puede ${opcion} <strong>${origen}</strong> debido a que tiene asociado 
        al menos un <strong>Cambio</strong> o una <strong>Reparaci&oacute;n</strong>.`;


        var parametros = {
            "Titulo":"Advetencia",
            "Cuerpo": Cuerpo,
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
    }

    function AgregarBotoneraCorrectiva(Tipo){
        
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

    function BorrarOrigen(){
        $('#idManCorPla').text('');
        $('#idBienManCorPla').text('');
        $('#idBieCorrectivo').text('');
        $('#manCorPla').val('');
        $('#BienManCorPla').val('');
        $('#nomBieCorrectivo').val('');
    }

    function BuscarBien(){

        SetSearchThead(thBienes);

        parametros = {
            "Lista": $('#listaBusquedaBien').html().trim(),
            "Tipo": Bienes
        }

        idBuscadorActual = $('#idBieCorrectivo').text().trim();
        nombreBuscadorActual = $('#nomBieCorrectivo').val().trim();
        condiciones = {
            "BienesDisponibles":true
        }
        SetSearchModal(parametros,true,condiciones)

    }

    function BuscarCorrectivoPlanificado(){

        SetSearchThead(thCorrectivoPlanificado);

        parametros = {
            "Lista": $('#listaBusquedaCorPla').html().trim(),
            "Tipo": CorrectivoPlanificado
        }

        condiciones = {
            "SoloAprobados":true
        }
        
        idBuscadorActual = $('#idManCorPla').text().trim();
        nombreBuscadorActual = $('#manCorPla').val().trim();

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

    function CambioOrigen(borrarOrigen = true){
        if(borrarOrigen){
            BorrarOrigen();
        }

        $('#divManCorPla').hide();
        $('#divBien').hide();

        switch($('#OrigenCorrectivo').val().trim()){
            case "Mantenimiento Correctivo Planificado":
                $('#divManCorPla').show();
            break;
            case "Bien":
                $('#divBien').show();
            break;

        }
    }

    function ClearForm(){
        
        $('#IdForm').text(''); 
        $('#idBieCorrectivo').text('');
        $('#alertaFormularioActual').hide();
        $('#TablaCambiosCorrectivos > tbody').children().remove();
        $('#TablaReparacionesCorrectivas > tbody').children().remove();

        $('.formulario-sigma form .form-control').each(function(){
            $(this).removeClass('is-invalid');
            if($(this).hasClass('texto') || $(this).hasClass('fecha'))
                $(this).val('');
            else if($(this).hasClass('lista'))
                $(this)[0].selectedIndex = 0;
            else if ($(this).hasClass('decimal'))
                $(this).val('0.00')
        });
    }
    
    function DesactivarCambiosReparaciones(){
        $('#agregarCambio').hide();
        $('#eliminarCambio').hide();
        $('#agregarReparacion').hide();
        $('#eliminarReparacion').hide();
    }

    function Editar(){
        
        if($('#EstatusCorrectivo').val().trim() == "Realizado"){
            
            Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn  btn-danger">
            <span class="fa fa-times "></span>
            Cerrar
            </button>`;

            Cuerpo = `No se puede editar <strong>Mantenimiento Correctivo</strong> debido a que el estatus ha cambiado a 
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

            if($('#EstatusCorrectivo').val() == "Solicitado"){
                $('#EstatusCorrectivo').attr("disabled", "disabled");
                $('#EstatusCorrectivo').attr("readonly", "readonly");
                ActivarCambiosReparaciones();
            }else{
                $('.formulario-sigma form .form-control').each(function(){
                    $(this).attr("disabled", "disabled");
                    $(this).attr("readonly", "readonly");
                })

                DesactivarCambiosReparaciones();
                $('#ObservacionCorrectivo').removeAttr("disabled"); 
                $('#ObservacionCorrectivo').removeAttr("readonly");
                setTimeout(function(){$('#ObservacionCorrectivo').focus();}, 400);
            }

            
            $(window).scrollTop(0);
        }
    }

    function EstablecerBuscador(){
        SetSearchThead(thCorrectivos);
    }

    function GetUrlBusquedaOpcion(opcion){
        switch(opcion){
            case ProveedorC:
            case ProveedorR:
                controlador = "proveedores/busqueda";
            break;
            case ObreroC:
            case ObreroR:
                controlador = "obreros/busqueda";
            break;
            case FallaC:
            case FallaR:
                controlador = "fallas/busqueda";
            break;
            case PiezaCC:
                controlador = "piezas/busquedaDisponibles";
            break;
            case PiezaDR:
            case PiezaDC:
                controlador = "piezas/" + ($('#OrigenCorrectivo').val().trim() == "Bien"?"busqueda":"busquedaCorrectivo");
            break;
            case Correctivo:
                controlador = "correctivo/busqueda";
            break;
            case Bienes:
                controlador = "bienes/busqueda";
            break;
            case CorrectivoPlanificado:
                controlador = "correctivoplanificado/busqueda";
            break;
        }
        
        return $('#UrlBase').text() + "/" + controlador
    }
    
    function GuardarEstadoActualFormulario(){
        dataInputs = [];
        idActual =$('#IdForm').text().trim();
        idBieCorrectivo = $('#idBieCorrectivo').text().trim();
        idManCorPla = $('#idManCorPla').text().trim();
        idBienManCorPla = $('#idBienManCorPla').text().trim();

        Cambios = $('#TablaCambiosCorrectivos > tbody').html();
        Reparaciones = $('#TablaReparacionesCorrectivas > tbody').html();
        $('.formulario-sigma form .form-control').each(function(){
            dataInputs.push($(this).val().trim());
        })
    }
    
    function LlenarFormulario(data){
        
        $('#TablaCambiosCorrectivos > tbody').children().remove();
        $('#TablaReparacionesCorrectivas > tbody').children().remove();

        if(data['Estatus'] != "Solicitado")
            DesactivarCambiosReparaciones();
        else
            ActivarCambiosReparaciones();

        AgregarBotoneraCorrectiva(data['Estatus']);

        $('#IdForm').text(data['id']);
        $('#idBienManCorPla').text(data['idBienManCorPla']);
        $('#idManCorPla').text(data['idManCorPla']);
        $('#DocumentoCorrectivo').val(data['Documento']);
        $('#OrigenCorrectivo').val(data['Origen']);
        $('#manCorPla').val(data['doc_pla']);
        $('#BienManCorPla').val(data['bie_pla']);
        $('#EstatusCorrectivo').val(data['Estatus']);
        $('#idBieCorrectivo').text(data['idBien']);
        $('#nomBieCorrectivo').val(data['nomBien']);
        $('#InicioCorrectivo').val(data['Inicio']);
        $('#FinCorrectivo').val(data['Fin']);
        $('#ObservacionCorrectivo').val(data['Observaciones']);
        $('#TablaCambiosCorrectivos > tbody:last-child').append(data['Cambios']);
        $('#TablaReparacionesCorrectivas > tbody:last-child').append(data['Reparaciones']);
        
        CambioOrigen(false);

    }

    function LlenarFormularioRequest(data){
        
        var parametros = {
            "id"                : data['mco_id'],
            "idBien"            : data['bie_id'],
            "idBienManCorPla"   : data['bie_id_2'],
            "idManCorPla"       : data['cpl_id'],
            "Documento"         : data['documento'],
            "Estatus"           : data['estatus'],
            "Origen"            : (data['bie_id'] == "" ? "Mantenimiento Correctivo Planificado":"Bien"),
            "doc_pla"           : data['doc_cpl'],
            "bie_pla"           : data['bie_nom_2'],
            "nomBien"           : data['bie_nom'],
            "Inicio"            : data['fec_ini'],
            "Fin"               : data['fec_fin'],
            "Observaciones"     : data['observaciones'],
            "Cambios"           : data['Cambios'],
            "Reparaciones"      : data['Reparaciones'],
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

    function RestablecerEstadoAnteriorFormulario(){
        
        var parametros = {
            "id"                : idActual.trim(),
            "idBien"            : idBieCorrectivo.trim(),
            "idBienManCorPla"   : idBienManCorPla.trim(),
            "idManCorPla"       : idManCorPla.trim(),
            "Documento"         : dataInputs[0].trim(),
            "Estatus"           : dataInputs[1].trim(),
            "Origen"            : dataInputs[2].trim(),
            "doc_pla"           : dataInputs[3].trim(),
            "bie_pla"           : dataInputs[4].trim(),
            "nomBien"           : dataInputs[5].trim(),
            "Inicio"            : dataInputs[6].trim(),
            "Fin"               : dataInputs[7].trim(),
            "Observaciones"     : dataInputs[8].trim(),
            "Cambios"           : Cambios,
            "Reparaciones"      : Reparaciones
        }

        LlenarFormulario(parametros);
    }

    function SetSearchModal(data,buscar =true,condiciones = {}){
        SetSearchType(data['Tipo']);
        
        switch(data['Tipo']){
            case ProveedorC:
            case ProveedorR:
                controlador = "Proveedores";
            break;
            case ObreroC:
            case ObreroR:
                controlador = "Obreros";
            break;
            case FallaC:
            case FallaR:
                controlador = "Fallas";
            break;
            case PiezaDC:
            case PiezaDR:
            case PiezaCC:
                controlador = "Piezas Disponibles";
            break;
            case Correctivo:
                controlador = "Mantenimientos Correctivo";
            break;
            case CorrectivoPlanificado:
                controlador = "Mantenimientos Correctivos Planificados";
            break;
            case Bienes:
                controlador = "Bienes Disponibles";
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

    window.AccionGuardar = function(data){
        LlenarFormularioRequest(data['Datos']);
    }

    window.AccionEliminarFormulario = function(data){
        
        if(data['Datos']['mco_id'] == ""){
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

        switch(tipo){
            case FallaR:
                idBuscadorActual = $('#idFallaReparacion').text().trim();
                nombreBuscadorActual = $('#nomFallaReparacion').val().trim();
            break;
            case FallaC:
                idBuscadorActual = $('#idFallaCambio').text().trim();
                nombreBuscadorActual = $('#nomFallaCambio').val().trim();
            break;
            case ObreroR:
                idBuscadorActual = $('#idObrReparacion').text().trim();
                nombreBuscadorActual = $('#nomObrReparacion').val().trim();
            break;
        }

        SetSearchModal(parametros)

    }

    window.BuscarPieza = function(tipo){


        SetSearchThead(thPiezas);
        SetOrigenBuscador(origenFuncion);

        parametros = {
            "Lista": $('#listaBusquedaPieza').html().trim(),
            "Tipo": tipo,
        }

        condiciones = {
            "Bien"          : $('#idBieCorrectivo').text().trim(),
            "PiezasBien"    : false,
            "idTipo"        : "",
        }

        switch(tipo){
            case PiezaDC:
                idBuscadorActual = $('#idPiezaDC').text().trim();
                nombreBuscadorActual = $('#nomPiezaDC').val().trim();
                condiciones['PiezasBien'] = true;
            break;
            case PiezaDR:
                idBuscadorActual = $('#idPiezaDR').text().trim();
                nombreBuscadorActual = $('#nomPiezaDR').val().trim();
                condiciones['PiezasBien'] = true;
            break;
            case PiezaCC:
                idBuscadorActual = $('#idPiezaCC').text().trim();
                nombreBuscadorActual = $('#nomPiezaCC').val().trim();
                condiciones['idTipo'] = $('#idTipoPieza').text().trim();
            break;
        }

        if($('#OrigenCorrectivo').val().trim() != "Bien"){
            if(tipo == PiezaCC){
                condiciones['Bien'] = $('#idBienManCorPla').text();
            }else{
                condiciones = {
                    "ManCorPla" : $('#idManCorPla').text().trim()
                }
            }
        }

        SetSearchModal(parametros,true,condiciones);
    }

    window.BuscarProveedor = function(tipo){

        SetSearchThead(thProveedores);
        SetOrigenBuscador(origenFuncion);

        parametros = {
            "Lista": $('#listaBusquedaProveedor').html().trim(),
            "Tipo": tipo
        }

        switch(tipo){
            case ProveedorC:
                idBuscadorActual = $('#idProC').text().trim();
                nombreBuscadorActual = $('#nomProC').val().trim();
            break;
            case ProveedorR:
                idBuscadorActual = $('#idProR').text().trim();
                nombreBuscadorActual = $('#nomProR').val().trim();
            break;
        }

        SetSearchModal(parametros)

    }

    window.BuscarObrero = function(tipo){

        SetOrigenBuscador(origenFuncion);
        SetSearchThead(thObreros);
        parametros = {
            "Lista": $('#listaBusquedaObreros').html().trim(),
            "Tipo": tipo,
        }

        switch(tipo){
            case ObreroC:
                idBuscadorActual = $('#idObrCambio').text().trim();
                nombreBuscadorActual = $('#nomObrCambio').val().trim();
            break;
            case ObreroR:
                idBuscadorActual = $('#idObrReparacion').text().trim();
                nombreBuscadorActual = $('#nomObrReparacion').val().trim();
            break;
        }

        SetSearchModal(parametros)

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
            case PiezaDC:
            
                $('#idPiezaCC').text('');
                $('#idBienPiezaCC').text('');
                $('#nomPiezaCC').val('');

                $('#idPiezaDC').text(fila.find("td:eq(0)").text().trim());
                $('#nomPiezaDC').val(fila.find("td:eq(1)").text().trim());
                $('#idTipoPieza').text(fila.find("td:eq(4)").text().trim());

                if($('#OrigenCorrectivo').val().trim() != "Bien"){
                    $('#idFallaCambio').text(fila.find("td:eq(9)").text().trim());
                    $('#nomFallaCambio').val(fila.find("td:eq(10)").text().trim());
                }
            break;
            case FallaC:
                $('#idFallaCambio').text(fila.find("td:eq(0)").text().trim());
                $('#nomFallaCambio').val(fila.find("td:eq(1)").text().trim());
            break;
            case FallaR:
                $('#idFallaReparacion').text(fila.find("td:eq(0)").text().trim());
                $('#nomFallaReparacion').val(fila.find("td:eq(1)").text().trim());
            break;
            case PiezaDR:
                $('#idPiezaDR').text(fila.find("td:eq(0)").text().trim());
                $('#nomPiezaDR').val(fila.find("td:eq(1)").text().trim());
                
                if($('#OrigenCorrectivo').val().trim() != "Bien"){
                    $('#idFallaReparacion').text(fila.find("td:eq(9)").text().trim());
                    $('#nomFallaReparacion').val(fila.find("td:eq(10)").text().trim());
                }
            break;
            case PiezaCC:
                $('#idPiezaCC').text(fila.find("td:eq(0)").text().trim());
                $('#idBienPiezaCC').text(fila.find("td:eq(8)").text().trim());
                $('#nomPiezaCC').val(fila.find("td:eq(1)").text().trim());
            break;
            case ObreroC:
                $('#idObrCambio').text(fila.find("td:eq(0)").text().trim());
                $('#nomObrCambio').val(fila.find("td:eq(5)").text().trim());
                $('#idProC').text('');
                $('#nomProC').val('');
            break;
            case ObreroR:
                $('#idObrReparacion').text(fila.find("td:eq(0)").text().trim());
                $('#nomObrReparacion').val(fila.find("td:eq(5)").text().trim());
                $('#idProR').text('');
                $('#nomProR').val('');
            break;
            case ProveedorC:
                $('#idProC').text(fila.find("td:eq(0)").text().trim());
                $('#nomProC').val(fila.find("td:eq(6)").text().trim());
                $('#idObrCambio').text('');
                $('#nomObrCambio').val('');
            break;
            case ProveedorR:
                $('#idProR').text(fila.find("td:eq(0)").text().trim());
                $('#nomProR').val(fila.find("td:eq(6)").text().trim());
                $('#idObrReparacion').text('');
                $('#nomObrReparacion').val('');
            break;
            case Bienes:
                $('#idBieCorrectivo').text(fila.find("td:eq(0)").text().trim());
                $('#nomBieCorrectivo').val(fila.find("td:eq(1)").text().trim());
            break;
            case CorrectivoPlanificado:
                $('#idManCorPla').text(fila.find("td:eq(0)").text().trim());
                $('#manCorPla').val(fila.find("td:eq(1)").text().trim());
                $('#idBienManCorPla').text(fila.find("td:eq(4)").text().trim());
                $('#BienManCorPla').val(fila.find("td:eq(5)").text().trim());
            break;
        }
        
        if(GetSearchType() != "Formulario"  ){
            $('#SigmaModalBusqueda').modal('hide');
        }
    }
});