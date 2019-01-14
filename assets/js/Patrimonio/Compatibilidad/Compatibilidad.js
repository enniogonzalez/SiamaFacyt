

var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){
    
    const Compatibilidad = "Compatibilidad";
    const Bienes = "Bienes";

    var idActual ="";
    var dataInputs= [];
    var idBuscadorActual = "";
    var nombreBuscadorActual = "";
    var idBieCompatibilidad = "";
    var Agregados = "";
    var Quitados = "";

    EstablecerBuscador();

    $('#CancelarModalBuscar').on('click',function(){
        switch(GetSearchType()){
            case TipoPiezaAgregar:
                $('#idTPAgrega').text(idBuscadorActual.trim());
                $('#nomTPAgregar').val(nombreBuscadorActual.trim());
            break;
            case TipoPiezaQuitar:
                $('#idTPQuitar').text(idBuscadorActual.trim());
                $('#nomTPQuitar').val(nombreBuscadorActual.trim());
            break;
            case Bienes:
                $('#idBieCompatibilidad').text(idBuscadorActual.trim());
                $('#nomBieCompatibilidad').val(nombreBuscadorActual.trim());
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
    
    $('#nomBieCompatibilidad').on('click',function(){

        if(ExisteQuitado() || ExisteAgregado() )
            AdvertenciaCambiarBien("cambiar");
        else
            BuscarBien();
    });

    $('.BuscarBienCompatibilidad').on('click',function(){
        if(ExisteQuitado() || ExisteAgregado() )
            AdvertenciaCambiarBien("cambiar");
        else
            BuscarBien();
    });

    $('.BorrarBienCompatibilidad').on('click',function(){
        
        if(ExisteQuitado() || ExisteAgregado() )
            AdvertenciaCambiarBien("borrar");
        else{
            $('#idBieCompatibilidad').text("");
            $('#nomBieCompatibilidad').val("");
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
            "Cuerpo": "<h4>¿Est&aacute; usted seguro de querer eliminar la Compatibilidad?</h4>",
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
    })

    $('.botoneraFormulario').on('click','#BuscarRegistro',function(){
        SetSearchType('Formulario');
        SetSearchTitle('Busqueda Compatibilidad');
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
        
        $('#EstatusCompatibilidad').val("Solicitado");
        $('#EstatusCompatibilidad').attr("disabled", "disabled");
        $('#EstatusCompatibilidad').attr("readonly", "readonly");
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
            AprobarCompatibilidad(parametros)
        }
    });

    $('.botoneraFormulario').on('click','#CancelarRegistro',function(){
        ClearForm();
        RestablecerEstadoAnteriorFormulario();
        EstablecerBuscador();
        DeshabilitarFormulario();

        AgregarBotoneraCorrectiva($('#EstatusCompatibilidad').val().trim());

        parametros = {
            "Lista": $('#listaBusquedaFormulario').html().trim(),
            "Tipo": Compatibilidad
        }

        SetSearchModal(parametros,false)
        SetModalEtqContador("")
        SetSearchType("Formulario");
    })

    $('.botoneraFormulario').on('click','#GuardarRegistro',function(){
        var Valido = true;
        var Agregados = [];
        var Quitados = [];

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
            Agregados = ObtenerJsonPAgregadas();
            Quitados = ObtenerJsonPQuitadas();

            Valido = (Agregados.length + Quitados.length) > 0;

            if(!Valido){
                SetAlertaFormulario("No se puede guardar Compatibilidad si no posee piezas a agregar y/o quitar.");
            }
        }

        if(Valido){
            var data = {
                "Lista": $('#listaBusquedaFormulario').html().trim(),
                "Tipo": Compatibilidad
            }
    
            SetSearchModal(data,false)
            SetModalEtqContador("")
            SetSearchType("Formulario");

            var parametros = {
                "id"            : $('#IdForm').text().trim(),
                "Estatus"       : $('#EstatusCompatibilidad').val().trim(),
                "Documento"     : $('#DocumentoCompatibilidad').val().trim(),
                "Bien"          : $('#idBieCompatibilidad').text().trim(),
                "Agregados"     : Agregados,
                "Quitados"      : Quitados,
                "Observacion"   : $('#ObservacionCompatibilidad').val().trim(),
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

        if($('#EstatusCompatibilidad').val().trim() == "Aprobado"){
            
            Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn  btn-danger">
            <span class="fa fa-times "></span>
            Cerrar
            </button>`;

            Cuerpo = `No se puede editar <strong>Compatibilidad</strong> debido a que el estatus ha cambiado a 
            <strong>aprobado</strong>.`;

            var parametros = {
                "Titulo"    : "Advetencia",
                "Cuerpo"    : Cuerpo,
                "Botones"   : Botones
            }

            ModalAdvertencia(parametros);
        }else{
            GuardarEstadoActualFormulario();
            HabilitarFormulario()
    
            $('#EstatusCompatibilidad').attr("disabled", "disabled");
            $('#EstatusCompatibilidad').attr("readonly", "readonly");
            
            $(window).scrollTop(0);
        }
    }

    function AccionEliminar(){

        if($('#EstatusCompatibilidad').val().trim() == "Aprobado"){
            
            Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn  btn-danger">
            <span class="fa fa-times "></span>
            Cerrar
            </button>`;

            Cuerpo = `No se puede eliminar <strong>Compatibilidad</strong> debido a que el estatus ha cambiado a 
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

    function AprobarCompatibilidad(parametros){

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


        idBuscadorActual = $('#idBieCompatibilidad').text().trim();
        nombreBuscadorActual = $('#nomBieCompatibilidad').val().trim();
        
        condiciones = {
            "BienesDisponibles":true
        }
        SetSearchModal(parametros,true,condiciones)

    }
    
    function AdvertenciaCambiarBien(opcion){

        Botones = `
        <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn  btn-danger">
          <span class="fa fa-times "></span>
          Cerrar
        </button>`;

        Cuerpo = `No se puede ${opcion} <strong>Bien</strong> debido a que tiene asociado 
        el <strong>Agregado</strong> o <strong>Quitado</strong> de al menos un tipo de pieza.`;


        var parametros = {
            "Titulo":"Advetencia",
            "Cuerpo": Cuerpo,
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
    }

    function GetUrlBusquedaOpcion(opcion){
        switch(opcion){
            case TipoPiezaAgregar:
                controlador = "tipopieza/busqueda";
            break;
            case TipoPiezaQuitar:
                controlador = "tipopieza/busquedaDisponible";
            break;
            case Compatibilidad:
                controlador = "Compatibilidad/busqueda";
            break;
            case Bienes:
                controlador = "bienes/busqueda";
            break;
        }

        return $('#UrlBase').text() + "/" + controlador + ""
    }

    function SetSearchModal(data,buscar =true,condiciones = {}){
        SetSearchType(data['Tipo']);
        
        switch(data['Tipo']){
            case TipoPiezaAgregar:
                controlador = "Tipo de Piezas Disponibles";
            break;
            case TipoPiezaQuitar:
                controlador = "Tipo de Piezas del Bien";
            break;
            case Compatibilidad:
                controlador = "Compatibilidad";
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

    function EstablecerBuscador(){
        SetSearchThead(thCompatibilidad);
    }

    function ClearForm(){
        
        $('#IdForm').text(''); 
        $('#idBieCompatibilidad').text('');
        $('#alertaFormularioActual').hide();
        $('#TablaAgregarTipos > tbody').children().remove();
        $('#TablaQuitarTipos > tbody').children().remove();

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
        idBieCompatibilidad = $('#idBieCompatibilidad').text().trim();

        Agregados = $('#TablaAgregarTipos > tbody').html();
        Quitados = $('#TablaQuitarTipos > tbody').html();
        $('.formulario-sigma form .form-control').each(function(){
            dataInputs.push($(this).val().trim());
        })
    }

    function RestablecerEstadoAnteriorFormulario(){
        
        var parametros = {
            "id"            : idActual.trim(),
            "idBien"        : idBieCompatibilidad.trim(),     
            "Documento"     : dataInputs[0].trim(),  
            "Estatus"       : dataInputs[1].trim(),
            "nomBien"       : dataInputs[2].trim(),
            "Observaciones" : dataInputs[3].trim(),
            "Agregados"       : Agregados,
            "Quitados"  : Quitados
        }

        LlenarFormulario(parametros);
    }
    
    function LlenarFormulario(data){
        $('#TablaAgregarTipos > tbody').children().remove();
        $('#TablaQuitarTipos > tbody').children().remove();


        AgregarBotoneraCorrectiva(data['Estatus']);

        $('#IdForm').text(data['id']);
        $('#DocumentoCompatibilidad').val(data['Documento']);
        $('#EstatusCompatibilidad').val(data['Estatus']);
        $('#idBieCompatibilidad').text(data['idBien']);
        $('#nomBieCompatibilidad').val(data['nomBien']);
        $('#ObservacionCompatibilidad').val(data['Observaciones']);
        $('#TablaAgregarTipos > tbody:last-child').append(data['Agregados']);
        $('#TablaQuitarTipos > tbody:last-child').append(data['Quitados']);

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

    function LlenarFormularioRequest(data){
        
        var parametros = {
            "id"            : data['com_id'],
            "Documento"     : data['documento'],
            "idBien"        : data['bie_id'],
            "nomBien"       : data['bie_nom'],
            "Observaciones" : data['observaciones'],
            "Agregados"     : data['Agregados'],
            "Estatus"       : data['estatus'],   
            "Quitados"      : data['Quitados']
        }

        LlenarFormulario(parametros);
        
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

        switch(Tipo){
            case "Solicitado":
                html = btnBuscar + btnEditar + btnAgregar + btnAprobar + btnEliminar;
            break;
            case "Aprobado":
                html = btnBuscar  + btnAgregar;
            break;
            default:
                html = ""

        }
        if(html != ""){
            $('.botoneraFormulario').children().remove();
            $('.botoneraFormulario').append(html);
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

    window.AccionGuardar = function(data){
        LlenarFormularioRequest(data['Datos']);
    }
    
    window.BuscarTipoPieza = function(tipo){


        SetOrigenBuscador(origenFuncion);
        SetSearchThead(thTipoPieza);
        parametros = {
            "Lista": $('#listaBusquedaTipoPieza').html().trim(),
            "Tipo": tipo,
        }

        condiciones = {
            "Bien"      : $('#idBieCompatibilidad').text().trim(),
            "delBien"   : false,
        }

        switch(tipo){
            case TipoPiezaAgregar:
                idBuscadorActual = $('#idTPAgrega').text().trim();
                nombreBuscadorActual = $('#nomTPAgregar').val().trim();
            break;
            case TipoPiezaQuitar:
                idBuscadorActual = $('#idTPQuitar').text().trim();
                nombreBuscadorActual = $('#nomTPQuitar').val().trim();
                condiciones['delBien'] = true;
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
            case TipoPiezaAgregar:
                $('#idTPAgrega').text(fila.find("td:eq(0)").text().trim());
                $('#nomTPAgregar').val(fila.find("td:eq(1)").text().trim());
            break;
            case TipoPiezaQuitar:
                $('#idTPQuitar').text(fila.find("td:eq(0)").text().trim());
                $('#nomTPQuitar').val(fila.find("td:eq(1)").text().trim());
            break;
            case Bienes:
                $('#idBieCompatibilidad').text(fila.find("td:eq(0)").text().trim());
                $('#nomBieCompatibilidad').val(fila.find("td:eq(1)").text().trim());
            break;
        }

        if(GetSearchType() != "Formulario"  ){
            $('#SigmaModalBusqueda').modal('hide');

            //Prevenir solapamientos de modales
            if(GetSearchType() != Bienes)
                setTimeout(function(){ $('#SigmaModalFunciones').modal('show');}, 400);
        }
    }

    window.AccionEliminarFormulario = function(data){
        
        if(data['Datos']['com_id'] == ""){
            ClearForm();
            AgregarBotoneraPrimariaNULL();
        }else{
            LlenarFormularioRequest(data['Datos']);
        }
    }
});