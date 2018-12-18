

var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){
    
    const Cambios = "Cambios";
    const Correctivo = "Correctivo";
    const Preventivo = "Preventivo";

    var idActual ="";
    var dataInputs= [];
    var idBuscadorActual = "";
    var nombreBuscadorActual = "";
    var idCorrectivo = "";
    var PiezasCambiosE = "";

    EstablecerBuscador();



    $('#CancelarModalBuscar').on('click',function(){
        switch(GetSearchType()){
            case PiezaCE:
                $('#idPieza').text(idBuscadorActual.trim());
                $('#nomPiezaCE').val(nombreBuscadorActual.trim());
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

        if(ExistePiezaCE())
            AdvertenciaCambiarBien("cambiar");
        else
            BuscarCorrectivo();
    });

    $('.BuscarCorrectivo').on('click',function(){
        if(ExistePiezaCE())
            AdvertenciaCambiarBien("cambiar");
        else
            BuscarCorrectivo();
    });

    $('.BorrarCorrectivo').on('click',function(){
        
        if(ExistePiezaCE())
            AdvertenciaCambiarBien("eliminar");
        else{
            BorrarMantenimientos()
        }
    });

    /************************************/
    /*          Manejo Preventivo       */
    /************************************/
    
    $('#ManPreventivo').on('click',function(){

        if(ExistePiezaCE())
            AdvertenciaCambiarBien("cambiar");
        else
            BuscarPreventivo();
    });

    $('.BuscarPreventivo').on('click',function(){
        if(ExistePiezaCE())
            AdvertenciaCambiarBien("cambiar");
        else
            BuscarPreventivo();
    });

    $('.BorrarPreventivo').on('click',function(){
        
        if(ExistePiezaCE())
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
        SetSearchTitle('Busqueda Cambios');
        PrimeraVezBusqueda = true;
        DeshabilitarBotonera();
        SetUrlBusqueda($('#ControladorActual').text().trim()+"/busqueda");
        Busqueda(1);
        
        setTimeout(function(){
            HabilitarBotonera();
        }, 900);
    })

    $('.botoneraFormulario').on('click','#CancelarRegistro',function(){
        ClearForm();
        RestablecerEstadoAnteriorFormulario();
        EstablecerBuscador();
        DeshabilitarFormulario();

        AgregarBotoneraCambios($('#EstatusPlanificado').val().trim());

        parametros = {
            "Lista": $('#listaBusquedaFormulario').html().trim(),
            "Tipo": Cambios
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
            "Cuerpo": "<h4>¿Est&aacute; usted seguro de querer eliminar el Cambio de Estatus?</h4>",
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
    })

    $('.botoneraFormulario').on('click','#GuardarRegistro',function(){
        var Valido = true;
        var Agregados = [];
        var PiezaCEs = [];

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


        if(Valido){
            var data = {
                "Lista": $('#listaBusquedaFormulario').html().trim(),
                "Tipo": Cambios
            }
    
            SetSearchModal(data,false)
            SetModalEtqContador("")
            SetSearchType("Formulario");
            PiezaCEs = ObtenerJsonPiezasCE();
            var parametros = {
                "id"            : $('#IdForm').text().trim(),
                "Bie_estatus"   : $('#estatusBien').val().trim(),
                "Documento"     : $('#DocumentoCambios').val().trim(),
                "Bien"          : $('#idCorrectivo').text().trim(),
                "PiezaCEs"      : PiezaCEs,
                "Observacion"   : $('#ObservacionCambios').val().trim(),
                "Url"           : $('#FormularioActual').attr("action")
            }

            if(!Guardando){
                EstablecerBuscador();
                Guardando = true;
                //GuardarFormulario(parametros);
            }
        }
        
    });

    $('#OrigenPlanificado').on('change',function(){
        $('#divCorrectivo').hide();
        $('#divPreventivo').hide();
        $('#divBien').hide();
        
        BorrarMantenimientos()
        valor = $('#OrigenPlanificado').val().trim();

        if( valor!= ""){
            $('#divBien').show();
            
            if(valor == "Mantenimiento Correctivo")
                $('#divCorrectivo').show();
            else
                $('#divPreventivo').show();
        }
    });

    $('#SiamaModalAdvertencias').on('click','#ConfirmarEliminacion',function(){
        var parametros = {
            "id": $('#IdForm').text().trim(),
            "Url": $('#ControladorActual').text().trim()+"/eliminar"
        }
        Eliminar(parametros)
    });

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

    function AgregarBotoneraCambios(Tipo){
        
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
        $('#idCorrectivo').text('');
        $('#alertaFormularioActual').hide();
        $('#TablaAgregarPiezas > tbody').children().remove();
        $('#TablaPiezasEstatus > tbody').children().remove();

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

            Cuerpo = `No se puede editar <strong>Cambio de Estatus</strong> debido a que el estatus ha cambiado a 
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
            
            $(window).scrollTop(0);
        }
    }

    function EstablecerBuscador(){
        SetSearchThead(thCambioEstatus);
    }

    function GetUrlBusquedaOpcion(opcion){
        switch(opcion){
            case PiezaCE:
                controlador = "piezas/busqueda";
            break;
            case Cambios:
                controlador = "Cambios/busqueda";
            break;
            case Correctivo:
                controlador = "correctivo/busquedaRealizado";
            break;
            case Preventivo:
                controlador = "preventivo/busquedaRealizado";
            break;
        }

        return $('#UrlBase').text() + "/" + controlador + ""
    }
    
    function GuardarEstadoActualFormulario(){
        dataInputs = [];
        idActual =$('#IdForm').text().trim();
        idCorrectivo = $('#idCorrectivo').text().trim();

        PiezasCambiosE = $('#TablaPiezasEstatus > tbody').html();
        
        $('.formulario-siama form .form-control').each(function(){
            dataInputs.push($(this).val().trim());
        })
    }
    
    function LlenarFormulario(data){

        $('#TablaPiezasEstatus > tbody').children().remove();
        AgregarBotoneraCambios(data['Doc_Estatus']);

        $('#IdForm').text(data['id']);
        $('#DocumentoCambios').val(data['Documento']);
        $('#EstatusPlanificado').val(data['Doc_Estatus']);
        $('#estatusBien').val(data['Bie_Estatus']);
        $('#idCorrectivo').text(data['idBien']);
        $('#ManCorrectivo').val(data['nomBien']);
        $('#ObservacionCambios').val(data['Observaciones']);
        $('#TablaPiezasEstatus > tbody:last-child').append(data['PiezasCambiosE']);

    }

    function LlenarFormularioRequest(data){
      
        var parametros = {
            "id"                : data['cam_id'],
            "Documento"         : data['documento'],
            "idBien"            : data['bie_id'],
            "nomBien"           : data['bie_nom'],
            "Observaciones"     : data['observaciones'],
            "Doc_Estatus"       : data['doc_estatus'],   
            "Bie_Estatus"       : data['bie_estatus'],   
            "PiezasCambiosE"    : data['PiezaCEs']
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
                $('#SiamaModalBusqueda').modal('hide');

                if(data['Caso'] == "Editar"){
                    Editar();
                }
            }
        }).fail(function(data){
            failAjaxRequest(data);
        });
    }

    function RestablecerEstadoAnteriorFormulario(){
    
        var parametros = {
            "id"                : idActual.trim(),
            "idBien"            : idCorrectivo.trim(),     
            "Documento"         : dataInputs[0].trim(),  
            "Doc_Estatus"       : dataInputs[1].trim(),
            "nomBien"           : dataInputs[2].trim(),
            "Bie_Estatus"       : dataInputs[3].trim(),
            "Observaciones"     : dataInputs[4].trim(),
            "PiezasCambiosE"    : PiezasCambiosE
        }

        console.log(parametros);
        LlenarFormulario(parametros);
    }

    function SetSearchModal(data,buscar =true,verFecha = false,condiciones = {}){
        SetSearchType(data['Tipo']);
        
        switch(data['Tipo']){
            case PiezaCE:
                controlador = "Piezas del Bien";
            break;
            case Cambios:
                controlador = "Cambios";
            break;
            case Correctivo:
                controlador = "Mantenimientos Correctivos Realizados";
            break;
            case Preventivo:
                controlador = "Mantenimientos Preventivos Realizados";
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
        
        if(data['Datos']['cam_id'] == ""){
            ClearForm();
            AgregarBotoneraPrimariaNULL();
        }else{
            LlenarFormularioRequest(data['Datos']);
        }
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
        nombreBuscadorActual = $('#nomPiezaCE').val().trim();

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
            case PiezaCE:
                $('#idPieza').text(fila.find("td:eq(0)").text().trim());
                $('#InvPieza').text(fila.find("td:eq(1)").text().trim());
                $('#nomPiezaCE').val(fila.find("td:eq(2)").text().trim());
            break;
            case Correctivo:
                $('#idCorrectivo').text(fila.find("td:eq(0)").text().trim());
                $('#ManCorrectivo').val(fila.find("td:eq(1)").text().trim());
                $('#idBien').text(fila.find("td:eq(2)").text().trim());
                $('#nomBien').val(fila.find("td:eq(3)").text().trim());
                $('#OrigenPlanificado').attr("disabled", "disabled");
                $('#OrigenPlanificado').attr("readonly", "readonly");
            break;
            case Preventivo:
                $('#idPreventivo').text(fila.find("td:eq(0)").text().trim());
                $('#ManPreventivo').val(fila.find("td:eq(1)").text().trim());
                $('#idBien').text(fila.find("td:eq(2)").text().trim());
                $('#nomBien').val(fila.find("td:eq(3)").text().trim());
                $('#OrigenPlanificado').attr("disabled", "disabled");
                $('#OrigenPlanificado').attr("readonly", "readonly");
            break;
        }

        if(GetSearchType() != "Formulario"  ){
            $('#SiamaModalBusqueda').modal('hide');
        }
    }
});