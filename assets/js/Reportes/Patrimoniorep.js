
var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){

    const Bienes            = "Bienes";
    const Localizaciones    = "Localizaciones";
    const TipoPieza           = "TipoPieza";
    const Piezas       = "Piezas";

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
    /*          Manejo TipoPieza         */
    /************************************/
    $('#nomTPI').on('click',function(){
        BuscarTipoPieza();
    });

    $('.BuscarTipoPieza').on('click',function(){
        BuscarTipoPieza();
    });

    $('.BorrarTipoPieza').on('click',function(){
        $('#idTpi').text("");
        $('#nomTPI').val("");
    });
    
    /************************************/
    /*      Manejo Piezas          */
    /************************************/
    $('#nomPie').on('click',function(){
        BuscarPieza();
    });

    $('.BuscarPieza').on('click',function(){
        BuscarPieza();
    });

    $('.BorrarPieza').on('click',function(){
        $('#idPie').text("");
        $('#nomPie').val("");
    });

    /************************************/
    /*          Fin Buscadores          */
    /************************************/
    
    $('#ImprimirReporte').on('click',function(){
        parametros ={
            "Inicio"        : $('#InicioPreventivo').val(),
            "Fin"           : $('#FinPreventivo').val(),
            "TipoPieza"     : $('#idTpi').text(),
            "Pieza"         : $('#idPie').text(),
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

    function BuscarPieza(){

        SetSearchThead(thPiezas);

        parametros = {
            "Lista": $('#listaBusquedaPieza').html().trim(),
            "Tipo": Piezas
        }

        idBuscadorActual = $('#idPie').text().trim();
        nombreBuscadorActual = $('#nomPie').val().trim();
        SetSearchModal(parametros)

    }

    function BuscarTipoPieza(){

        SetSearchThead(thTipoPieza);
        parametros = {
            "Lista": $('#listaBusquedaTipoPieza').html().trim(),
            "Tipo": TipoPieza,
        }

        idBuscadorActual = $('#idTpi').text().trim();
        nombreBuscadorActual = $('#nomTPI').val().trim();
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
            case TipoPieza:
                controlador = "TipoPieza";
            break;
            case Piezas:
                controlador = "Piezas";
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
            case TipoPieza:
                controlador = "tipopieza";
            break;
            case Piezas:
                controlador = "piezas";
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
                $('#nomLoc').val(fila.find("td:eq(2)").text().trim());
            break;
            case TipoPieza:
                $('#idTpi').text(fila.find("td:eq(0)").text().trim());
                $('#nomTPI').val(fila.find("td:eq(1)").text().trim());
            break;
            case Piezas:
                $('#idPie').text(fila.find("td:eq(0)").text().trim());
                $('#nomPie').val(fila.find("td:eq(1)").text().trim());
            break;
        }

        $('#SigmaModalBusqueda').modal('hide');

    }
});