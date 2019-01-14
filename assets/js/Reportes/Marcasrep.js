
var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){

    const Marcas    = "Marcas";

    /************************************/
    /*      Inicio Buscadores           */
    /************************************/


    /************************************/
    /*      Manejo Marcas               */
    /************************************/
    $('#nomMar').on('click',function(){
        BuscarMarca();
    });

    $('.BuscarMarca').on('click',function(){
        BuscarMarca();
    });

    $('.BorrarMarca').on('click',function(){
        $('#idMar').text("");
        $('#nomMar').val("");
    });

    
    /************************************/
    /*          Fin Buscadores          */
    /************************************/
    
    $('#ImprimirReporte').on('click',function(){
        parametros ={
            "Marca"  : $('#idMar').text(),
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


    function BuscarMarca(){

        SetSearchThead(thMarcas);

        parametros = {
            "Lista": $('#listaBusquedaMarcas').html().trim(),
            "Tipo": Marcas
        }

        idBuscadorActual = $('#idMar').text().trim();
        nombreBuscadorActual = $('#nomMar').val().trim();
        SetSearchModal(parametros)

    }

    function SetSearchModal(data,buscar =true,condiciones = {}){
        SetSearchType(data['Tipo']);
        
        switch(data['Tipo']){
            case Marcas:
                controlador = "Marcas";
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
            case Marcas:
                controlador = "marcas";
            break;
        }

        return $('#UrlBase').text() + "/" + controlador + "/busqueda"
    }
    
    window.InterfazElegirBuscador = function(fila){
        
        switch(GetSearchType()){
            case Marcas:
                $('#idMar').text(fila.find("td:eq(0)").text().trim());
                $('#nomMar').val(fila.find("td:eq(1)").text().trim());
            break;
        }

        $('#SigmaModalBusqueda').modal('hide');

    }
});