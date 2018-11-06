
var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){

    const Bienes            = "Bienes";
    const Localizaciones    = "Localizaciones";
    const Usuarios          = "Usuarios";
    const Proveedores       = "Proveedores";

    /************************************/
    /*      Inicio Buscadores           */
    /************************************/
    /************************************/
    /*          Manejo Bienes           */
    /************************************/

    $('#nomBie').on('click',function(){
        BuscarBien();
    });

    $('.BuscarBien').on('click',function(){
        BuscarBien();
    });

    $('.BorrarBien').on('click',function(){
        $('#idBie').text("");
        $('#nomBie').val("");
    });

    /************************************/
    /*      Manejo Localizaciones       */
    /************************************/
    $('#nomLoc').on('click',function(){
        BuscarLocalizacion();
    });

    $('.BuscarLocalizacion').on('click',function(){
        BuscarLocalizacion();
    });

    $('.BorrarLocalizacion').on('click',function(){
        $('#idLoc').text("");
        $('#nomLoc').val("");
    });

    /************************************/
    /*          Manejo Usuarios         */
    /************************************/
    $('#nomUsu').on('click',function(){
        BuscarUsuario();
    });

    $('.BuscarUsuario').on('click',function(){
        BuscarUsuario();
    });

    $('.BorrarUsuario').on('click',function(){
        $('#idUsu').text("");
        $('#nomUsu').val("");
    });
    
    /************************************/
    /*      Manejo Proveedores          */
    /************************************/
    $('#nomPro').on('click',function(){
        BuscarProveedor();
    });

    $('.BuscarProveedor').on('click',function(){
        BuscarProveedor();
    });

    $('.BorrarProveedor').on('click',function(){
        $('#idPro').text("");
        $('#nomPro').val("");
    });

    /************************************/
    /*          Fin Buscadores          */
    /************************************/
    
    $('#ImprimirReporte').on('click',function(){
        parametros ={
            "Inicio"        : $('#InicioPreventivo').val(),
            "Fin"           : $('#FinPreventivo').val(),
            "Usuario"       : $('#idUsu').text(),
            "Proveedor"     : $('#idPro').text(),
            "Bien"          : $('#idBie').text(),
            "Localizacion"  : $('#idLoc').text(),
        }

        url = $('#FormularioActual').attr("action")+$('#reporte').val();
        post(url, parametros);
        // console.log(parametros);
    })

    function post(path, parameters) {
        var form = $('<form></form>');
    
        form.attr("method", "post");
        form.attr("target", "_blank");
        form.attr("action", path);
    
        $.each(parameters, function(key, value) {
            var field = $('<input></input>');
    
            field.attr("type", "hidden");
            field.attr("name", key);
            field.attr("value", value);
    
            form.append(field);
        });
    
        // The form needs to be a part of the document in
        // order for us to be able to submit it.
        $(document.body).append(form);
        form.submit();
    }

    function BuscarProveedor(){

        SetSearchThead(thProveedores);

        parametros = {
            "Lista": $('#listaBusquedaProveedor').html().trim(),
            "Tipo": Proveedores
        }

        idBuscadorActual = $('#idPro').text().trim();
        nombreBuscadorActual = $('#nomPro').val().trim();
        SetSearchModal(parametros)

    }

    function BuscarUsuario(){

        SetSearchThead(thUsuarios);
        parametros = {
            "Lista": $('#listaBusquedaUsuario').html().trim(),
            "Tipo": Usuarios,
        }

        idBuscadorActual = $('#idUsu').text().trim();
        nombreBuscadorActual = $('#nomUsu').val().trim();
        SetSearchModal(parametros)

    }

    function BuscarLocalizacion(){

        SetSearchThead(thLocalizaciones);

        parametros = {
            "Lista": $('#listaBusquedaLocalizacion').html().trim(),
            "Tipo": Localizaciones
        }

        idBuscadorActual = $('#idLoc').text().trim();
        nombreBuscadorActual = $('#nomLoc').val().trim();
        SetSearchModal(parametros)

    }

    function BuscarBien(){

        SetSearchThead(thBienes);

        parametros = {
            "Lista": $('#listaBusquedaBien').html().trim(),
            "Tipo": Bienes
        }

        idBuscadorActual = $('#idBie').text().trim();
        nombreBuscadorActual = $('#nomBie').val().trim();

        condiciones = {
            "BienesDisponibles":false
        }

        SetSearchModal(parametros,true,condiciones)

    }

    function SetSearchModal(data,buscar =true,condiciones = {}){
        SetSearchType(data['Tipo']);
        
        switch(data['Tipo']){
            case Bienes:
                controlador = "Bienes";
            break;
            case Localizaciones:
                controlador = "Localizaciones";
            break;
            case Usuarios:
                controlador = "Usuarios";
            break;
            case Proveedores:
                controlador = "Proveedores";
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
    
    function GetUrlBusquedaOpcion(opcion){
        switch(opcion){
            case Bienes:
                controlador = "bienes";
            break;
            case Localizaciones:
                controlador = "localizaciones";
            break;
            case Usuarios:
                controlador = "usuarios";
            break;
            case Proveedores:
                controlador = "proveedores";
            break;
        }

        return $('#UrlBase').text() + "/" + controlador + "/busqueda"
    }
    
    window.InterfazElegirBuscador = function(fila){
        
        switch(GetSearchType()){
            case Bienes:
                $('#idBie').text(fila.find("td:eq(0)").text().trim());
                $('#nomBie').val(fila.find("td:eq(1)").text().trim());
            break;
            case Localizaciones:
                $('#idLoc').text(fila.find("td:eq(0)").text().trim());
                $('#nomLoc').val(fila.find("td:eq(3)").text().trim());
            break;
            case Usuarios:
                $('#idUsu').text(fila.find("td:eq(0)").text().trim());
                $('#nomUsu').val(fila.find("td:eq(3)").text().trim());
            break;
            case Proveedores:
                $('#idPro').text(fila.find("td:eq(0)").text().trim());
                $('#nomPro').val(fila.find("td:eq(6)").text().trim());
            break;
        }

        $('#SiamaModalBusqueda').modal('hide');

    }
});