
var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){

    const Proveedores    = "Proveedores";

    /************************************/
    /*      Inicio Buscadores           */
    /************************************/


    /************************************/
    /*      Manejo Proveedores               */
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
            "Proveedor"  : $('#idPro').text(),
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


    function BuscarProveedor(){

        SetSearchThead(thProveedores);

        parametros = {
            "Lista": $('#listaBusquedaProveedores').html().trim(),
            "Tipo": Proveedores
        }

        idBuscadorActual = $('#idPro').text().trim();
        nombreBuscadorActual = $('#nomPro').val().trim();
        SetSearchModal(parametros)

    }

    function SetSearchModal(data,buscar =true,condiciones = {}){
        SetSearchType(data['Tipo']);
        
        switch(data['Tipo']){
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
            case Proveedores:
                controlador = "proveedores";
            break;
        }

        return $('#UrlBase').text() + "/" + controlador + "/busqueda"
    }
    
    window.InterfazElegirBuscador = function(fila){
        
        switch(GetSearchType()){
            case Proveedores:
                $('#idPro').text(fila.find("td:eq(0)").text().trim());
                $('#nomPro').val(fila.find("td:eq(6)").text().trim());
            break;
        }

        $('#SigmaModalBusqueda').modal('hide');

    }
});