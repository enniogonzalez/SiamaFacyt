
var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){

    const Partidas    = "Partidas";

    /************************************/
    /*      Inicio Buscadores           */
    /************************************/


    /************************************/
    /*      Manejo Partidas               */
    /************************************/
    $('#nomPar').on('click',function(){
        BuscarPartida();
    });

    $('.BuscarPartida').on('click',function(){
        BuscarPartida();
    });

    $('.BorrarPartida').on('click',function(){
        $('#idPar').text("");
        $('#nomPar').val("");
    });

    
    /************************************/
    /*          Fin Buscadores          */
    /************************************/
    
    $('#ImprimirReporte').on('click',function(){
        parametros ={
            "Partida"  : $('#idPar').text(),
        }

        url = $('#FormularioActual').attr("action")+$('#reporte').val();
        post(url, parametros);
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


    function BuscarPartida(){

        SetSearchThead(thPartidas);

        parametros = {
            "Lista": $('#listaBusquedaPartidas').html().trim(),
            "Tipo": Partidas
        }

        idBuscadorActual = $('#idPar').text().trim();
        nombreBuscadorActual = $('#nomPar').val().trim();
        SetSearchModal(parametros)

    }

    function SetSearchModal(data,buscar =true,condiciones = {}){
        SetSearchType(data['Tipo']);
        
        switch(data['Tipo']){
            case Partidas:
                controlador = "Partidas";
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
            case Partidas:
                controlador = "partidas";
            break;
        }

        return $('#UrlBase').text() + "/" + controlador + "/busqueda"
    }
    
    window.InterfazElegirBuscador = function(fila){
        
        switch(GetSearchType()){
            case Partidas:
                $('#idPar').text(fila.find("td:eq(0)").text().trim());
                $('#nomPar').val(fila.find("td:eq(4)").text().trim());
            break;
        }

        $('#SigmaModalBusqueda').modal('hide');

    }
});