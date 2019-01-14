

var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){
    
    const Cambios = "Cambios";
    const Bienes = "Bienes";

    var idActual ="";
    var dataInputs= [];
    var idBuscadorActual = "";
    var nombreBuscadorActual = "";
    var idBieCambios = "";
    var PiezasCambiosE = "";

    EstablecerBuscador();



    $('#CancelarModalBuscar').on('click',function(){
        switch(GetSearchType()){
            case PiezaCE:
                $('#idPieza').text(idBuscadorActual.trim());
                $('#nomPiezaCE').val(nombreBuscadorActual.trim());
            break;
            case Bienes:
                $('#idBieCambios').text(idBuscadorActual.trim());
                $('#nomBieCambios').val(nombreBuscadorActual.trim());
            break;
        }

        if(GetSearchType() != "Formulario" && GetSearchType() != Bienes){
            
            //Prevenir solapamientos de modales
            setTimeout(function(){ 
                $('#SigmaModalFunciones').modal('show');}, 400);
        }
    })

    /************************************/
    /*      Inicio Buscadores           */
    /************************************/
    

    /************************************/
    /*          Manejo Bienes           */
    /************************************/
    
    $('#nomBieCambios').on('click',function(){

        if(ExistePiezaCE())
            AdvertenciaCambiarBien("cambiar");
        else
            BuscarBien();
    });

    $('.BuscarBienCambios').on('click',function(){
        if(ExistePiezaCE())
            AdvertenciaCambiarBien("cambiar");
        else
            BuscarBien();
    });

    $('.BorrarBienCambios').on('click',function(){
        
        if(ExistePiezaCE())
            AdvertenciaCambiarBien("eliminar");
        else{
            $('#idBieCambios').text("");
            $('#nomBieCambios').val("");
            $('#estatusBien').val("");
        }
    });

    /************************************/
    /*          Fin Buscadores          */
    /************************************/

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

    $('#SigmaModalAdvertencias').on('click','#ConfirmarEliminacion',function(){
        var parametros = {
            "id": $('#IdForm').text().trim(),
            "Caso":"Eliminar",
            "Url": $('#ControladorActual').text().trim()+"/obtener"
        }
        Obtener(parametros);
    });

    $('.botoneraFormulario').on('click','#EditarRegistro',function(){
        
        var parametros = {
            "id": $('#IdForm').text().trim(),
            "Caso":"Editar",
            "Url": $('#ControladorActual').text().trim()+"/obtener"
        }
        Obtener(parametros);
    });

    $('.botoneraFormulario').on('click','#AgregarRegistro',function(){
        GuardarEstadoActualFormulario();
        ClearForm();
        HabilitarFormulario();
        
        $('#EstatusCambios').val("Solicitado");
        $('#EstatusCambios').attr("disabled", "disabled");
        $('#EstatusCambios').attr("readonly", "readonly");
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

    $('.botoneraFormulario').on('click','#CancelarRegistro',function(){
        ClearForm();
        RestablecerEstadoAnteriorFormulario();
        EstablecerBuscador();
        DeshabilitarFormulario();

        AgregarBotoneraCambios($('#EstatusCambios').val().trim());

        parametros = {
            "Lista": $('#listaBusquedaFormulario').html().trim(),
            "Tipo": Cambios
        }

        SetSearchModal(parametros,false)
        SetModalEtqContador("")
        SetSearchType("Formulario");
    })

    $('.botoneraFormulario').on('click','#GuardarRegistro',function(){
        var Valido = true;
        var Agregados = [];
        var PiezaCEs = [];

        $('.formulario-sigma form .form-control').each(function(){
            $(this).removeClass('is-invalid');
            if($(this).hasClass('obligatorio') && $(this).val().trim() == ""){

                if(Valido)
                    $(this).focus();
                
                Valido = false;
                $(this).addClass('is-invalid');
            }
        })


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
                "Bien"          : $('#idBieCambios').text().trim(),
                "PiezaCEs"      : PiezaCEs,
                "Observacion"   : $('#ObservacionCambios').val().trim(),
                "Url"           : $('#FormularioActual').attr("action")
            }

            if(!Guardando){
                EstablecerBuscador();
                Guardando = true;
                GuardarFormulario(parametros);
            }
        }
        
    });

    function Editar(){

        if($('#EstatusCambios').val().trim() == "Aprobado"){
            
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
    
            $('#EstatusCambios').attr("disabled", "disabled");
            $('#EstatusCambios').attr("readonly", "readonly");
            
            $(window).scrollTop(0);
        }
    }

    function ElegirEstatus(objeto,estatus){
        switch(estatus){
            case "Activo":
                objeto.val("Inactivo");
            break;
            case "Inactivo":
                objeto.val("Activo");
            break;
            default:
                objeto.val("");
            break;
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
    
    function BuscarBien(){
        SetSearchThead(thBienes);

        parametros = {
            "Lista": $('#listaBusquedaBien').html().trim(),
            "Tipo": Bienes
        }


        idBuscadorActual = $('#idBieCambios').text().trim();
        nombreBuscadorActual = $('#nomBieCambios').val().trim();
        
        condiciones = {
            "BienesDisponibles":true,
            "Inactivos":true
        }
        SetSearchModal(parametros,true,condiciones)

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

    function GetUrlBusquedaOpcion(opcion){
        switch(opcion){
            case PiezaCE:
                controlador = "piezas/busqueda";
            break;
            case Cambios:
                controlador = "Cambios/busqueda";
            break;
            case Bienes:
                controlador = "bienes/busqueda";
            break;
            case Falla:
                controlador = "fallas/busqueda";
            break;
        }

        return $('#UrlBase').text() + "/" + controlador + ""
    }

    function SetSearchModal(data,buscar =true,condiciones = {}){
        SetSearchType(data['Tipo']);
        
        switch(data['Tipo']){
            case PiezaCE:
                controlador = "Piezas del Bien";
            break;
            case Cambios:
                controlador = "Cambios";
            break;
            case Bienes:
                controlador = "Bienes Disponibles";
            break;
            case Falla:
                controlador = "Fallas";
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

    function EstablecerBuscador(){
        SetSearchThead(thCambioEstatus);
    }

    function ClearForm(){
        
        $('#IdForm').text(''); 
        $('#idBieCambios').text('');
        $('#alertaFormularioActual').hide();
        $('#TablaAgregarPiezas > tbody').children().remove();
        $('#TablaPiezasEstatus > tbody').children().remove();

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
    
    function GuardarEstadoActualFormulario(){
        dataInputs = [];
        idActual =$('#IdForm').text().trim();
        idBieCambios = $('#idBieCambios').text().trim();

        PiezasCambiosE = $('#TablaPiezasEstatus > tbody').html();
        
        $('.formulario-sigma form .form-control').each(function(){
            dataInputs.push($(this).val().trim());
        })
    }

    function RestablecerEstadoAnteriorFormulario(){
    
        var parametros = {
            "id"                : idActual.trim(),
            "idBien"            : idBieCambios.trim(),     
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
    
    function LlenarFormulario(data){

        $('#TablaPiezasEstatus > tbody').children().remove();
        AgregarBotoneraCambios(data['Doc_Estatus']);

        $('#IdForm').text(data['id']);
        $('#DocumentoCambios').val(data['Documento']);
        $('#EstatusCambios').val(data['Doc_Estatus']);
        $('#estatusBien').val(data['Bie_Estatus']);
        $('#idBieCambios').text(data['idBien']);
        $('#nomBieCambios').val(data['nomBien']);
        $('#ObservacionCambios').val(data['Observaciones']);
        $('#TablaPiezasEstatus > tbody:last-child').append(data['PiezasCambiosE']);

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
    
    function AccionEliminar(){

        if($('#EstatusCambios').val().trim() == "Aprobado"){
            
            Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn  btn-danger">
            <span class="fa fa-times "></span>
            Cerrar
            </button>`;

            Cuerpo = `No se puede eliminar <strong>Cambio de Estatus</strong> debido a que el estatus ha cambiado a 
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

    function AgregarBotoneraCambios(Tipo){
        
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

    window.BuscarFalla = function(tipo){
        SetOrigenBuscador(origenFuncion);

        SetSearchThead(thFallas);
        parametros = {
            "Lista": $('#listaBusquedaFalla').html().trim(),
            "Tipo": tipo,
        }

        idBuscadorActual = $('#idFalla').text().trim();
        nombreBuscadorActual = $('#nomFalla').val().trim();

        SetSearchModal(parametros)
    }
    
    window.BuscarPieza = function(tipo){

        SetOrigenBuscador(origenFuncion);
        SetSearchThead(thPiezas);

        parametros = {
            "Lista": $('#listaBusquedaPieza').html().trim(),
            "Tipo": tipo,
        }

        condiciones = {
            "Bien"          : $('#idBieCambios').text().trim(),
            "PiezasBien"    : false
        }

        switch(tipo){
            case PiezaCE:
                idBuscadorActual = $('#idPieza').text().trim();
                nombreBuscadorActual = $('#nomPiezaCE').val().trim();
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
                    "Caso":"Buscar",
                    "Url": $('#ControladorActual').text().trim()+"/obtener"
                }
                Obtener(parametros);
            break;
            case PiezaCE:
                $('#idPieza').text(fila.find("td:eq(0)").text().trim());
                $('#nomPiezaCE').val(fila.find("td:eq(1)").text().trim());
                ElegirEstatus($('#estatusPieza'),fila.find("td:eq(2)").text().trim());
                $('#InvPieza').text(fila.find("td:eq(3)").text().trim());
                $('#idFalla').text('');
                $('#nomFalla').val('');

                if($('#estatusPieza').val()=="Inactivo"){
                    $('.divFallaPieza').show();
                }else{
                    $('.divFallaPieza').hide();
                }
            break;
            case Bienes:
                $('#idBieCambios').text(fila.find("td:eq(0)").text().trim());
                $('#nomBieCambios').val(fila.find("td:eq(1)").text().trim());
                ElegirEstatus($('#estatusBien'),fila.find("td:eq(2)").text().trim());
            break;
            case Falla:
                $('#idFalla').text(fila.find("td:eq(0)").text().trim());
                $('#nomFalla').val(fila.find("td:eq(1)").text().trim());
            break;
        }

        if(GetSearchType() != "Formulario"  ){
            $('#SigmaModalBusqueda').modal('hide');

            //Prevenir solapamientos de modales
            if(GetSearchType() != Bienes)
                setTimeout(function(){ $('#SigmaModalFunciones').modal('show');}, 400);
        }
    }

});