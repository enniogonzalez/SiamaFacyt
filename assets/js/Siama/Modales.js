var origenFuncion = "Funciones"

$(function(){

    var BusquedaActiva = "";
    var OrdenamientoActivo = 0;
    var TipoOrdenamientoActivo = 0;
    var UrlBusquedaActivo = "";
    var FechaActiva = false;

    /************************************************************/
    /*              Manejo Modal Estatus                        */
    /************************************************************/
    $('#CerrarEstatusInformacion').on('click',function(){
        CerrarEstatus();
    });

    window.CerrarEstatus = function (){
        $('.estatusTransaccion').hide();
    }

    window.MostrarEstatus = function(opcion, dilay = false){
        
        $('.estatusTransaccion').hide();
        $('.estatusTransaccion').removeClass('ColorEspera');
        $('.estatusTransaccion').removeClass('ColorExitoso');

        switch(opcion){
            case 1:
                html = '<span class="fa fa-spinner"></span> Guardando...';
                css ="ColorEspera";
            break;
            case 2:
                html = '<span class="fa fa-check"></span> Registro Guardado';
                css ="ColorExitoso";
            break;
            case 3:
                html = '<span class="fa fa-spinner"></span> Eliminando...';
                css ="ColorEspera";
            break;
            case 4:
                html = '<span class="fa fa-check"></span> Registro Eliminado';
                css ="ColorExitoso";
            break;
            case 5:
                html = '<span class="fa fa-spinner"></span> Cargando...';
                css ="ColorEspera";
            break;
            case 6:
                html = '<span class="fa fa-spinner"></span> Aprobando...';
                css ="ColorEspera";
            break;
            case 7:
                html = '<span class="fa fa-check"></span> Registro Aprobado';
                css ="ColorExitoso";
            break;
            case 8:
                html = '<span class="fa fa-spinner"></span> Desaprobando...';
                css ="ColorEspera";
            break;
            case 9:
                html = '<span class="fa fa-check"></span> Registro Desaprobado';
                css ="ColorExitoso";
            break;
            default:
                html = '<span class="fa fa-spinner"></span> Guardando...';
                css ="ColorEspera";
        }

        $('#InformacionEstatus').children().remove();
        $('#InformacionEstatus').text('');
        $('#InformacionEstatus').append(html);
        $('.estatusTransaccion').addClass(css);
        
        if(dilay)
            setTimeout(function(){ $('.estatusTransaccion').show(); }, 600);
        else
            $('.estatusTransaccion').show();
        
    }

    /************************************************************/
    /*              Fin Manejo Modal Estatus                    */
    /************************************************************/

    
    /************************************************************/
    /*              Manejo Modal Funciones                      */
    /************************************************************/

    function ModalLimpiarFuncion(){
        $('#SiamaModalFunciones .contenedorAlertaModal').hide();
        $( "#SiamaModalFunciones .modal-body" ).children().remove();
        $( "#SiamaModalFunciones .modal-footer").children().remove();
        $('#SiamaModalFunciones #SiamaModalFuncionesEtiqueta').text('');

    }

    window.CerrarFunciones = function (){
        ModalLimpiarFuncion();
        $('#SiamaModalFunciones').modal('hide');
    }
    
    window.ClearModalFunction = function(){
        $('#SiamaModalFunciones .contenedorAlertaModal').hide();
        $( "#SiamaModalFunciones .modal-body" ).children().remove();
        $( "#SiamaModalFunciones .modal-footer").children().remove();
        $('#SiamaModalFunciones #SiamaModalFuncionesEtiqueta').text();
        $('#SiamaModalFunciones #mhOptionC').text();
        $('#SiamaModalFunciones #mhOptionR').text();
    }

    window.ModalEditarFuncion = function(data, mostrar = true){
        ModalLimpiarFuncion();
        $('#SiamaModalFunciones #SiamaModalFuncionesEtiqueta').append(data['Titulo']);
        $('#SiamaModalFunciones #mhOptionC').text(data['Columna']);
        $('#SiamaModalFunciones #mhOptionR').text(data['Fila']);
        $( "#SiamaModalFunciones .modal-footer" ).append(data['Botones']);
        $( "#SiamaModalFunciones .modal-body" ).append( data['Cuerpo']);

        if(mostrar){
            $('#SiamaModalFunciones').modal('show');
        }
    }

    /************************************************************/
    /*              Fin Manejo Modal Functiones                 */
    /************************************************************/

    /************************************************************/
    /*              Manejo Modal Busqueda                       */
    /************************************************************/
    
    $('#CancelarModalBuscar').on('click',function(){
        BusquedaActiva = "";
        OrdenamientoActivo = 0;
        TipoOrdenamientoActivo = 0;
        
        SetModalEtqContador("");
        
        if(GetOrigenBuscador() == origenFuncion){
            $('#SiamaModalFunciones').show();
        }
    })

    $('#ElegirModalBuscar').on('click',function(){
        if($('#TablaBusquedaFormulario .tr-activa-siama').length == 0){
            
        Botones = `
        <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn  btn-danger">
          <span class="fa fa-times "></span>
          Cerrar
        </button>`;

        var parametros = {
            "Titulo":"Atención",
            "Cuerpo": "Debe seleccionar una fila a elegir",
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
        }else{
            InterfazElegirBuscador($('#TablaBusquedaFormulario .tr-activa-siama'));
            
            SetModalEtqContador("")
        }
    })
    
    $('#fInicioBusqueda').on('change',function(){
        if($(this).val() != "" && 
        $('#fFinBusqueda').val() != "" 
        && $(this).val() > $('#fFinBusqueda').val()){
            $('#fFinBusqueda').val($(this).val());
        }else if($(this).val() != "" && $('#fFinBusqueda').val() == ""){
            $('#fFinBusqueda').val($(this).val());
        }

    });

    $('#fFinBusqueda').on('change',function(){
        if($(this).val() != "" && 
        $('#fInicioBusqueda').val() != "" 
        && $(this).val() < $('#fInicioBusqueda').val()){

            $('#fInicioBusqueda').val($(this).val());

        }else if($(this).val() != "" && $('#fInicioBusqueda').val() == ""){
            $('#fInicioBusqueda').val($(this).val());
        }

    });

    $('#FiltrarModalBuscar').on('click',function(){
        
        PrimeraVezBusqueda = true;
        BusquedaActiva = $('#TextoBusqueda').val().trim().toLowerCase();
        OrdenamientoActivo = $("#CampoOrden")[0].selectedIndex;
        TipoOrdenamientoActivo = $("#TipoOrden")[0].selectedIndex;
        parametros = JSON.parse($('#ParametrosBuscador').text());
        Busqueda(1,FechaActiva,parametros);
    })

    $('.paginacion-siama').on('click','.Next',function(){

        var ultima = false;

        $('.page-item-selected').removeClass('page-item-selected');
        if($('.Previous').hasClass('PaginationDisabled')){
            ApagarNavegacionIzquierda()
        }

        document.getElementsByClassName("Option")[0].className += " page-item-selected";

        $(".Option").each(function () {

            number = parseInt($(this).text())+PagesxNav;
            $(this).text(number)
            if(number > paginas)
                $(this).hide();

            if(number == paginas)
                ultima = true;
        });

        if(ultima )
            PrenderNavegacionDerecha();

        Busqueda($('.page-item-selected').text().trim(),FechaActiva)

    })

    $('.paginacion-siama').on('click','.Option',function(){
        $('.page-item-selected').removeClass('page-item-selected');
        $(this).addClass('page-item-selected')
        Busqueda(parseInt($(this).text().trim()),FechaActiva);
    })
    
    $('.paginacion-siama').on('click','.Previous',function(){
        var primera = false;
        $('.page-item-selected').removeClass('page-item-selected');
        document.getElementsByClassName("Option")[PagesxNav-1].className += " page-item-selected";
        $(".Option").each(function () {

            number = $(this).text() - PagesxNav;
            $(this).text(number)
            $(this).show();

            if(number == PagesxNav)
                primera = true;
        });

        if(primera )
            PrenderNavegacionIzquierda();

        
        if($('.Last').hasClass('PaginationDisabled'))
            ApagarNavegacionDerecha();
        
        Busqueda($('.page-item-selected').text().trim(),FechaActiva)

    })

    $('.paginacion-siama').on('click','.First',function(){

        ApagarNavegacionDerecha();
        i = 1;
        $(".Option").each(function () {

            $(this).text(i)
            $(this).show();
            i++;
        });

        if(i>paginas)
            PrenderNavegacionDerecha();

        $('.page-item-selected').removeClass('page-item-selected');
        document.getElementsByClassName("Option")[0].className += " page-item-selected";
        
        Busqueda(1,FechaActiva);
        PrenderNavegacionIzquierda();
    })
    
    $('.paginacion-siama').on('click','.Last',function(){

        i = 1;
        $('.page-item-selected').removeClass('page-item-selected');

        if( paginas % PagesxNav ==0){
            $(".Option").each(function () {
                $(this).text((paginas-PagesxNav + i))
                i++;
            });
            document.getElementsByClassName("Option")[PagesxNav-1].className += " page-item-selected";
        }else{
            var firstOption = parseInt(paginas / PagesxNav)*PagesxNav;
            $(".Option").each(function () {
                number = firstOption + i;
                $(this).text((number));

                if(number == paginas)
                    $(this).addClass('page-item-selected');
                if(number > paginas)
                    $(this).hide()
                i++;
            });
        }

        PrenderNavegacionDerecha();

        if($('.Previous').hasClass('PaginationDisabled'))
            ApagarNavegacionIzquierda();
        
        Busqueda($('.page-item-selected').text().trim(),FechaActiva)

    })

    $('#TablaBusquedaFormulario tbody').on('click','tr',function(){
        
        //Se busca el indice de la fila que esta activa
        var indexAnt = $('#TablaBusquedaFormulario .tr-activa-siama').index();
        //Se busca el indice de la fila que fue seleccionada
        var indexAct = $(this).index();
        //Se remueve la clase activa de la fila que esta activa
        $('#TablaBusquedaFormulario .tr-activa-siama').removeClass('tr-activa-siama');

        //En caso de que los dos indices encontrado anteriormente
        //sean diferentes, de agrega la clase activa a la fila seleccionada
        //esto con la intension de que si se selecciona la misma fila
        //activa, la misma se desactive
        if(indexAnt != indexAct)
            $(this).addClass('tr-activa-siama');

    });


    function ApagarNavegacionDerecha(){
        $('.Last').removeClass('PaginationDisabled')
        $('.Next').removeClass('PaginationDisabled')
    }

    function ApagarNavegacionIzquierda(){
        $('.Previous').removeClass('PaginationDisabled')
        $('.First').removeClass('PaginationDisabled')
    }

    function PrenderNavegacionDerecha(){
        $('.Last').addClass('PaginationDisabled')
        $('.Next').addClass('PaginationDisabled')
    }

    function PrenderNavegacionIzquierda(){
        $('.Previous').addClass('PaginationDisabled')
        $('.First').addClass('PaginationDisabled')
    }

    function SetPagination(registrosActuales){

        ApagarNavegacionDerecha();
        SetPaginationBar();

        paginas = Math.ceil(registrosActuales/RegistrosPorPagina)
        var i = 1;
        $(".Option").each(function () {
            $(this).text(i);
            if(paginas <= PagesxNav && i > paginas){
                $(this).hide();
                $(this).addClass('PaginationDisabled')
            }
            i++;
        });

        $('.page-item-selected').removeClass('page-item-selected');
        document.getElementsByClassName("Option")[0].className += " page-item-selected";

        if(paginas <= PagesxNav){
            PrenderNavegacionIzquierda();
            PrenderNavegacionDerecha();
            $('.Previous').hide('')
            $('.First').hide('')
            $('.Last').hide('')
            $('.Next').hide('')
        }
    }

    function SetPaginationBar(){
        $('.paginacion-siama').children().remove();
        html = `
            <li class="page-item-siama page-link First PaginationDisabled"><span class="fa fa-angle-double-left"></span></li>
            <li class="page-item-siama page-link Previous PaginationDisabled"><span class="fa fa-angle-left"></span></li>
        `;
        cantidad = (PagesxNav < 1) ? 5:PagesxNav;

        for(i = 1;i<= cantidad;i++){
            html = html + `
                <li class="page-item-siama page-link Option">${i}</li> 
            `;            
        }
        html = html + `
            <li class="page-item-siama page-link Next"><span class="fa fa-angle-right"></span></li>
            <li class="page-item-siama page-link Last"><span class="fa fa-angle-double-right"></span></li>
        `;

        
        $('.paginacion-siama').append(html);
    }

    window.Busqueda = function(paginaActual, verFecha = false,paramsRequest = {}){
        $('#ParametrosBuscador').text(JSON.stringify(paramsRequest));
        FechaActiva =verFecha;
        
        DeshabilitarBotonera();

        if(verFecha)
            $('.fechasBusqueda').show();
        else
            $('.fechasBusqueda').hide();

        $('#SiamaModalBusqueda .modal-content').addClass('ModalDesactivado');
        MostrarEstatus(5);
        $("#CampoOrden")[0].selectedIndex = OrdenamientoActivo;
        $("#TipoOrden")[0].selectedIndex = TipoOrdenamientoActivo;

        var parametros = {
            "IdActual"              : $('#IdForm').text().trim(),
            "Opcion"                : $('#OpcionBusqueda').text().trim(),
            "Fec_Ini"               : $("#fInicioBusqueda").val().trim(),
            "Fec_Fin"               : $("#fFinBusqueda").val().trim(),
            "Condiciones"           : paramsRequest,
            "Orden"                 : $("#CampoOrden").val().trim() + " " + $("#TipoOrden").val().trim(),
            "Busqueda"              : BusquedaActiva.trim(),
            "Pagina"                : paginaActual,
            "RegistrosPorPagina"    : RegistrosPorPagina
        }

        $.ajax({
            url: UrlBusquedaActivo,
            type: "POST",
            data: parametros,
            dataType: 'json'
        }).done(function(data){
            
            HabilitarBotonera();
            $('#SiamaModalFunciones').hide();
            if(data['isValid']){
                CerrarEstatus();
                Registros = (data['Datos']['Registros'] == "") ? 0: data['Datos']['Registros'];

                if($('#etqContador').text().trim() == "")
                    etq = $('.informationPage .container h2').text().trim();
                else
                    etq = $('#etqContador').text().trim();

                $('#RegistrosEncontrados').text("Se han encontrado "+ Registros + " " + etq);
                $('#TextoBusqueda').val(BusquedaActiva.trim())
                $('#SiamaModalBusqueda .modal-content').removeClass('ModalDesactivado');
                $('#TablaBusquedaFormulario > tbody').children().remove();
                $('#TablaBusquedaFormulario > tbody:last-child').append(data['Datos']['Listas']);
                $('#SiamaModalBusqueda').modal('show')

                if(PrimeraVezBusqueda){
                    SetPagination(data['Datos']['Registros'])
                    PrimeraVezBusqueda = false;
                }
                    
            }
        }).fail(function(data){
            $('#SiamaModalBusqueda .modal-content').removeClass('ModalDesactivado');
            failAjaxRequest(data);
        });
    }

    window.GetOrigenBuscador = function(origen){
        return $('#OrigenBuscador').text();
    }

    window.GetSearchType = function(){
        return $('#OpcionBusqueda').text();
    }

    window.SetModalEtqContador = function(etiqueta){
        $('#etqContador').text(etiqueta);
    }

    window.SetOrigenBuscador = function(origen){
        $('#OrigenBuscador').text(origen);
    }

    window.SetSearchCOB = function (COB){
        $('#CampoOrden').children().remove();
        $('#CampoOrden').append(COB);
    }

    window.SetSearchThead = function(thead){

        $('#TablaBusquedaFormulario > thead').children().remove();
        $('#TablaBusquedaFormulario > thead').append(thead);
    }

    window.SetSearchTitle = function(title){
        $('#SiamaModalBusqueda .modal-header h4').text(title);
    }

    window.SetSearchType = function(tipo){
        $('#OpcionBusqueda').text(tipo);
    }

    window.SetUrlBusqueda = function(url){
        UrlBusquedaActivo = url;
    }

    /************************************************************/
    /*              Fin Manejo Modal Busqueda                   */
    /************************************************************/
    
    window.failAjaxRequest = function(data){
        
        HabilitarBotonera();
        $('.estatusTransaccion').hide();

        Botones = `
        <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn  btn-danger">
          <span class="fa fa-times "></span>
          Cerrar
        </button>`;

        Cuerpo = data['responseText'].trim();

        if(Cuerpo.search('DOCTYPE') != -1)
            Cuerpo = "Envio incorrecto de parametros, por favor comunicarse con departamento de sistema.";
        else if(Cuerpo.search('fkey') != -1)
            Cuerpo = "No se puede eliminar registro debido a que este est&aacute; asociado a otra tabla.";

        var parametros = {
            "Titulo":"Ha ocurrido un error",
            "Cuerpo": Cuerpo,
            "Botones":Botones
        }

        
        setTimeout(function(){ ModalAdvertencia(parametros); }, 600);
        
    }
    
    window.ModalAdvertencia = function(data,dilay = false){
        
        $('#SiamaModalAdvertencias .contenedorAlertaModal').hide();
        $( "#SiamaModalAdvertencias .modal-body" ).children().remove();
        $( "#SiamaModalAdvertencias .modal-body" ).text('');
        $( "#SiamaModalAdvertencias .modal-footer").children().remove();
        $('#SiamaModalAdvertencias #SiamaModalAdvertenciasEtiqueta').text(data['Titulo']);
        $( "#SiamaModalAdvertencias .modal-footer" ).append(data['Botones']);
        $( "#SiamaModalAdvertencias .modal-body" ).append( data['Cuerpo']);

        if(dilay)
            setTimeout(function(){$('#SiamaModalAdvertencias').modal('show');},600);
        else
            $('#SiamaModalAdvertencias').modal('show');
    }

});