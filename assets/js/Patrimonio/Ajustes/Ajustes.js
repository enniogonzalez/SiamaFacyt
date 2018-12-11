

var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){
    
    const Ajustes = "Ajustes";
    const Bienes = "Bienes";

    var idActual ="";
    var dataInputs= [];
    var idBuscadorActual = "";
    var nombreBuscadorActual = "";
    var idBieAjustes = "";
    var Agregados = "";
    var Quitados = "";

    EstablecerBuscador();

    $('#CancelarModalBuscar').on('click',function(){
        switch(GetSearchType()){
            case PiezaAgregar:
                $('#idPiezaAgregar').text(idBuscadorActual.trim());
                $('#nomPiezaAgregar').val(nombreBuscadorActual.trim());
            break;
            case PiezaQuitar:
                $('#idPiezaQuitar').text(idBuscadorActual.trim());
                $('#nomPiezaQuitar').val(nombreBuscadorActual.trim());
            break;
            case Bienes:
                $('#idBieAjustes').text(idBuscadorActual.trim());
                $('#nomBieAjustes').val(nombreBuscadorActual.trim());
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
    
    $('#nomBieAjustes').on('click',function(){

        if(ExisteQuitado() || ExisteAgregado() )
            AdvertenciaCambiarBien("cambiar");
        else
            BuscarBien();
    });

    $('.BuscarBienAjustes').on('click',function(){
        if(ExisteQuitado() || ExisteAgregado() )
            AdvertenciaCambiarBien("cambiar");
        else
            BuscarBien();
    });

    $('.BorrarBienAjustes').on('click',function(){
        
        if(ExisteQuitado() || ExisteAgregado() )
            AdvertenciaCambiarBien("borrar");
        else{
            $('#idBieAjustes').text("");
            $('#nomBieAjustes').val("");
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
            "Cuerpo": "<h4>¿Est&aacute; usted seguro de querer eliminar el Ajuste?</h4>",
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
    })

    $('.botoneraFormulario').on('click','#BuscarRegistro',function(){
        SetSearchType('Formulario');
        SetSearchTitle('Busqueda Ajustes');
        PrimeraVezBusqueda = true;
        DeshabilitarBotonera();
        SetUrlBusqueda($('#ControladorActual').text().trim()+"/busqueda");
        Busqueda(1);
        
        setTimeout(function(){
            HabilitarBotonera();
        }, 900);
    })

    $('#SiamaModalAdvertencias').on('click','#ConfirmarEliminacion',function(){

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
        
        $('#EstatusAjustes').val("Solicitado");
        $('#EstatusAjustes').attr("disabled", "disabled");
        $('#EstatusAjustes').attr("readonly", "readonly");
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

        AgregarBotoneraCorrectiva($('#EstatusAjustes').val().trim());

        parametros = {
            "Lista": $('#listaBusquedaFormulario').html().trim(),
            "Tipo": Ajustes
        }

        SetSearchModal(parametros,false)
        SetModalEtqContador("")
        SetSearchType("Formulario");
    })

    $('.botoneraFormulario').on('click','#GuardarRegistro',function(){
        var Valido = true;
        var Agregados = [];
        var Quitados = [];

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
            Agregados = ObtenerJsonPAgregadas();
            Quitados = ObtenerJsonPQuitadas();

            Valido = (Agregados.length + Quitados.length) > 0;

            if(!Valido){
                SetAlertaFormulario("No se puede guardar Ajuste si no posee piezas a agregar y/o quitar.");
            }
        }

        if(Valido){
            var data = {
                "Lista": $('#listaBusquedaFormulario').html().trim(),
                "Tipo": Ajustes
            }
    
            SetSearchModal(data,false)
            SetModalEtqContador("")
            SetSearchType("Formulario");

            var parametros = {
                "id"            : $('#IdForm').text().trim(),
                "Estatus"       : $('#EstatusAjustes').val().trim(),
                "Documento"     : $('#DocumentoAjustes').val().trim(),
                "Bien"          : $('#idBieAjustes').text().trim(),
                "Agregados"     : Agregados,
                "Quitados"      : Quitados,
                "Observacion"   : $('#ObservacionAjustes').val().trim(),
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

        if($('#EstatusAjustes').val().trim() == "Aprobado"){
            
            Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn  btn-danger">
            <span class="fa fa-times "></span>
            Cerrar
            </button>`;

            Cuerpo = `No se puede editar <strong>Ajuste</strong> debido a que el estatus ha cambiado a 
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
    
            $('#EstatusAjustes').attr("disabled", "disabled");
            $('#EstatusAjustes').attr("readonly", "readonly");
            
            $(window).scrollTop(0);
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


        idBuscadorActual = $('#idBieAjustes').text().trim();
        nombreBuscadorActual = $('#nomBieAjustes').val().trim();
        
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
        el <strong>Agregado</strong> o <strong>Quitado</strong> de al menos una pieza.`;


        var parametros = {
            "Titulo":"Advetencia",
            "Cuerpo": Cuerpo,
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
    }

    function GetUrlBusquedaOpcion(opcion){
        switch(opcion){
            case PiezaAgregar:
                controlador = "piezas/busquedaAgregar";
            break;
            case PiezaQuitar:
                controlador = "piezas/busqueda";
            break;
            case Ajustes:
                controlador = "Ajustes/busqueda";
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
            case PiezaAgregar:
                controlador = "Piezas Disponibles";
            break;
            case PiezaQuitar:
                controlador = "Piezas del Bien";
            break;
            case Ajustes:
                controlador = "Ajustes";
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
        SetSearchThead(thAjustes);
    }

    function ClearForm(){
        
        $('#IdForm').text(''); 
        $('#idBieAjustes').text('');
        $('#alertaFormularioActual').hide();
        $('#TablaAgregarPiezas > tbody').children().remove();
        $('#TablaQuitarPiezas > tbody').children().remove();

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
        idBieAjustes = $('#idBieAjustes').text().trim();

        Agregados = $('#TablaAgregarPiezas > tbody').html();
        Quitados = $('#TablaQuitarPiezas > tbody').html();
        $('.formulario-siama form .form-control').each(function(){
            dataInputs.push($(this).val().trim());
        })
    }

    function RestablecerEstadoAnteriorFormulario(){
        
        var parametros = {
            "id"            : idActual.trim(),
            "idBien"        : idBieAjustes.trim(),     
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
        $('#TablaAgregarPiezas > tbody').children().remove();
        $('#TablaQuitarPiezas > tbody').children().remove();


        AgregarBotoneraCorrectiva(data['Estatus']);

        $('#IdForm').text(data['id']);
        $('#DocumentoAjustes').val(data['Documento']);
        $('#EstatusAjustes').val(data['Estatus']);
        $('#idBieAjustes').text(data['idBien']);
        $('#nomBieAjustes').val(data['nomBien']);
        $('#ObservacionAjustes').val(data['Observaciones']);
        $('#TablaAgregarPiezas > tbody:last-child').append(data['Agregados']);
        $('#TablaQuitarPiezas > tbody:last-child').append(data['Quitados']);

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

    function AccionEliminar(){

        if($('#EstatusAjustes').val().trim() == "Aprobado"){
            
            Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn  btn-danger">
            <span class="fa fa-times "></span>
            Cerrar
            </button>`;

            Cuerpo = `No se puede eliminar <strong>Ajuste</strong> debido a que el estatus ha cambiado a 
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
            "id"            : data['aju_id'],
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
                html = ""

        }
        if(html != ""){
            $('.botoneraFormulario').children().remove();
            $('.botoneraFormulario').append(html);
        }
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
            "Bien"          : $('#idBieAjustes').text().trim(),
            "PiewBie"       : false,
            "PiezasBien"    : false
        }

        switch(tipo){
            case PiezaAgregar:
                idBuscadorActual = $('#idPiezaAgregar').text().trim();
                nombreBuscadorActual = $('#nomPiezaAgregar').val().trim();
            break;
            case PiezaQuitar:
                idBuscadorActual = $('#idPiezaQuitar').text().trim();
                nombreBuscadorActual = $('#nomPiezaQuitar').val().trim();
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
            case PiezaAgregar:
                $('#idPiezaAgregar').text(fila.find("td:eq(0)").text().trim());
                $('#InvAP').text(fila.find("td:eq(3)").text().trim());
                $('#TipoAP').text(fila.find("td:eq(5)").text().trim());
                $('#nomPiezaAgregar').val(fila.find("td:eq(2)").text().trim());
            break;
            case PiezaQuitar:
                $('#idPiezaQuitar').text(fila.find("td:eq(0)").text().trim());
                $('#InvQP').text(fila.find("td:eq(3)").text().trim());
                $('#TipoQP').text(fila.find("td:eq(5)").text().trim());
                $('#nomPiezaQuitar').val(fila.find("td:eq(2)").text().trim());
            break;
            case Bienes:
                $('#idBieAjustes').text(fila.find("td:eq(0)").text().trim());
                $('#nomBieAjustes').val(fila.find("td:eq(1)").text().trim());
            break;
        }

        if(GetSearchType() != "Formulario"  ){
            $('#SiamaModalBusqueda').modal('hide');

            //Prevenir solapamientos de modales
            if(GetSearchType() != Bienes)
                setTimeout(function(){ $('#SiamaModalFunciones').modal('show');}, 400);
        }
    }

    window.AccionEliminarFormulario = function(data){
        
        if(data['Datos']['aju_id'] == ""){
            ClearForm();
            AgregarBotoneraPrimariaNULL();
        }else{
            LlenarFormularioRequest(data['Datos']);
        }
    }
});