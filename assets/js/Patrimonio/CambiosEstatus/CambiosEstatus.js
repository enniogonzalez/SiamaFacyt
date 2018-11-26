

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
                $('#SiamaModalFunciones').modal('show');}, 400);
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
            AdvertenciaCambiarBien("borrar");
        else{
            $('#idBieCambios').text("");
            $('#nomBieCambios').val("");
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
            "Cuerpo": "<h4>¿Est&aacute; usted seguro de querer borrar el Cambio de Estatus?</h4>",
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

    $('#SiamaModalAdvertencias').on('click','#ConfirmarEliminacion',function(){
        var parametros = {
            "id": $('#IdForm').text().trim(),
            "Url": $('#ControladorActual').text().trim()+"/eliminar"
        }
        Eliminar(parametros)
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
        idBieCambios = $('#idBieCambios').text().trim();

        PiezasCambiosE = $('#TablaPiezasEstatus > tbody').html();
        
        $('.formulario-siama form .form-control').each(function(){
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
                $('#SiamaModalBusqueda').modal('hide');

                if(data['Caso'] == "Editar"){
                    Editar();
                }
            }
        }).fail(function(data){
            failAjaxRequest(data);
        });
    }

    function LlenarFormularioRequest(data){
      
        var parametros = {
            "id"                : data['aju_id'],
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
                $('#InvPieza').text(fila.find("td:eq(1)").text().trim());
                $('#nomPiezaCE').val(fila.find("td:eq(2)").text().trim());
            break;
            case Bienes:
                $('#idBieCambios').text(fila.find("td:eq(0)").text().trim());
                $('#nomBieCambios').val(fila.find("td:eq(1)").text().trim());
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