
var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){

    const Obreros    = "Obreros";

    /************************************/
    /*      Inicio Buscadores           */
    /************************************/


    /************************************/
    /*      Manejo Obreros               */
    /************************************/
    $('#nomObr').on('click',function(){
        BuscarObrero();
    });

    $('.BuscarObrero').on('click',function(){
        BuscarObrero();
    });

    $('.BorrarObrero').on('click',function(){
        $('#idObr').text("");
        $('#nomObr').val("");
    });

    
    /************************************/
    /*          Fin Buscadores          */
    /************************************/
    
    $('#ImprimirReporte').on('click',function(){
        parametros ={
            "Obrero"  : $('#idObr').text(),
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


    function BuscarObrero(){

        SetSearchThead(thObreros);

        parametros = {
            "Lista": $('#listaBusquedaObreros').html().trim(),
            "Tipo": Obreros
        }

        idBuscadorActual = $('#idObr').text().trim();
        nombreBuscadorActual = $('#nomObr').val().trim();
        SetSearchModal(parametros)

    }

    function SetSearchModal(data,buscar =true,condiciones = {}){
        SetSearchType(data['Tipo']);
        
        switch(data['Tipo']){
            case Obreros:
                controlador = "Obreros";
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
            case Obreros:
                controlador = "obreros";
            break;
        }

        return $('#UrlBase').text() + "/" + controlador + "/busqueda"
    }
    
    window.InterfazElegirBuscador = function(fila){
        
        switch(GetSearchType()){
            case Obreros:
                $('#idObr').text(fila.find("td:eq(0)").text().trim());
                $('#nomObr').val(fila.find("td:eq(5)").text().trim());
            break;
        }

        $('#SigmaModalBusqueda').modal('hide');

    }
});