
var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){

    const Localizaciones    = "Localizaciones";

    /************************************/
    /*      Inicio Buscadores           */
    /************************************/


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
    /*          Fin Buscadores          */
    /************************************/
    
    $('#ImprimirReporte').on('click',function(){
        parametros ={
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

    function SetSearchModal(data,buscar =true,condiciones = {}){
        SetSearchType(data['Tipo']);
        
        switch(data['Tipo']){
            case Localizaciones:
                controlador = "Localizaciones";
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
            case Localizaciones:
                controlador = "localizaciones";
            break;
        }

        return $('#UrlBase').text() + "/" + controlador + "/busqueda"
    }
    
    window.InterfazElegirBuscador = function(fila){
        
        switch(GetSearchType()){
            case Localizaciones:
                $('#idLoc').text(fila.find("td:eq(0)").text().trim());
                $('#nomLoc').val(fila.find("td:eq(3)").text().trim());
            break;
        }

        $('#SigmaModalBusqueda').modal('hide');

    }
});