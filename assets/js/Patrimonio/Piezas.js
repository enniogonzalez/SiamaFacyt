

var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){

    const Proveedores = "Proveedores";
    const Partidas = "Partidas";
    const Marcas = "Marcas";
    const Piezas = "Piezas";
    const Bienes = "Bienes";

    var idActual ="";
    var dataInputs= [];
    var idBuscadorActual = "";
    var nombreBuscadorActual = "";
    var idMarca = "";
    var idProveedor = "";
    var idPartidas = "";
    var idBien = "";

    EstablecerBuscador();


    $('#CancelarModalBuscar').on('click',function(){
        switch(GetSearchType()){
            case Proveedores:
                $('#idPro').text(idBuscadorActual.trim());
                $('#nomPro').val(nombreBuscadorActual.trim());
            break;
            case Partidas:
                $('#idPar').text(idBuscadorActual.trim());
                $('#nomPar').val(nombreBuscadorActual.trim());
            break;
            case Marcas:
                $('#idMar').text(idBuscadorActual.trim());
                $('#nomMar').val(nombreBuscadorActual.trim());
            break;
            case Bienes:
                $('#idBie').text(idBuscadorActual.trim());
                $('#nomBie').val(nombreBuscadorActual.trim());
            break;
        }
    })

    /************************************/
    /*      Inicio Buscadores           */
    /************************************/
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
    /*          Manejo Partidas         */
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
    /*          Manejo Marca            */
    /************************************/

    $('#nomMar').on('click',function(){
        BuscarMarca();
    });

    $('.BuscarMarca').on('click',function(){
        BuscarMarca();
    });

    $('.BorrarPartida').on('click',function(){
        $('#idMar').text("");
        $('#nomMarca').val("");
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
            "Cuerpo": "<h4>¿Est&aacute; usted seguro de querer borrar el Pieza?</h4>",
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
    })

    $('.botoneraFormulario').on('click','#BuscarRegistro',function(){
        SetSearchType('Formulario');
        SetSearchTitle('Busqueda Piezas');
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
        GuardarEstadoActualFormulario();
        HabilitarFormulario()
        $('#NombrePieza').focus();
        
        $('#nomBie').attr("disabled", "disabled");
        $('#nomBie').attr("readonly", "readonly");
        $('#nomBie').attr("disabled", "disabled");
        $('#nomBie').attr("readonly", "readonly");
        $(window).scrollTop(0);
    });

    $('.botoneraFormulario').on('click','#AgregarRegistro',function(){
        GuardarEstadoActualFormulario();
        ClearForm();
        HabilitarFormulario()
        $('#NombrePieza').focus();
        $(window).scrollTop(0);
    })

    $('.botoneraFormulario').on('click','#CancelarRegistro',function(){
        ClearForm();
        RestablecerEstadoAnteriorFormulario();
        EstablecerBuscador();
        DeshabilitarFormulario();

        parametros = {
            "Lista": $('#listaBusquedaFormulario').html().trim(),
            "Tipo": Piezas
        }

        SetSearchModal(parametros,false)
        SetModalEtqContador("")
        SetSearchType("Formulario");
    })

    $('.botoneraFormulario').on('click','#GuardarRegistro',function(){
        var Valido = true;

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
                "Tipo": Piezas
            }
    
            SetSearchModal(data,false)
            SetModalEtqContador("")
            SetSearchType("Formulario");

            var parametros = {
                "id":               $('#IdForm').text().trim(),
                "Bien":             $('#idBie').text().trim(),
                "Estatus":          $('#estatusPieza').val().trim(),
                "Nombre":           $('#NombrePieza').val().trim(),
                "Modelo":           $('#modeloPieza').val().trim(),
                "Serial":           $('#serialPieza').val().trim(),
                "Inventario":       $('#invPie').val().trim(),
                "Marca":            $('#idMar').text().trim(),
                "Proveedor":        $('#idPro').text().trim(),
                "Partidas":         $('#idPar').text().trim(),
                "Fabricacion":      $('#fabPieza').val().trim(),
                "fAdquisicion":     $('#fAdqPieza').val().trim(),
                "Instalacion":      $('#Instalacion').val().trim(),
                "tAdquisicion":     $('#tAdqPieza').val().trim(),
                "Observacion":      $('#Observacion').val().trim(),
                "Url":              $('#FormularioActual').attr("action")
            }
            
            if(!Guardando){
                EstablecerBuscador();
                Guardando = true;
                GuardarFormulario(parametros);
            }
        }
        
    });

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

    function BuscarBien(){

        SetSearchThead(thBienes);

        parametros = {
            "Lista": $('#listaBusquedaBien').html().trim(),
            "Tipo": Bienes
        }

        idBuscadorActual = $('#idBie').text().trim();
        nombreBuscadorActual = $('#nomBie').val().trim();
        SetSearchModal(parametros)

    }

    function BuscarPartida(){

        SetSearchThead(thPartidas);

        parametros = {
            "Lista": $('#listaBusquedaPartida').html(),
            "Tipo": Partidas
        }

        idBuscadorActual = $('#idPar').text().trim();
        nombreBuscadorActual = $('#nomPar').val().trim();
        SetSearchModal(parametros)

    }

    function BuscarMarca(){

        SetSearchThead(thMarcas);
        parametros = {
            "Lista": $('#listaBusquedaMarca').html().trim(),
            "Tipo": Marcas
        }

        idBuscadorActual = $('#idPar').text().trim();
        nombreBuscadorActual = $('#nomPar').val().trim();
        SetSearchModal(parametros)

    }

    function GetUrlBusquedaOpcion(opcion){
        switch(opcion){
            case Proveedores:
                controlador = "proveedores";
            break;
            case Partidas:
                controlador = "partidas";
            break;
            case Marcas:
                controlador = "marcas";
            break;
            case Piezas:
                controlador = "Piezas";
            break;
            case Bienes:
                controlador = "bienes";
            break;
            default: 
                controlador = "proveedores";
        }

        return $('#UrlBase').text() + "/" + controlador + "/busqueda"
    }

    function SetSearchModal(data,buscar =true){
        SetSearchCOB(data['Lista']);
        SetSearchType(data['Tipo']);
        SetModalEtqContador(data['Tipo'])
        SetSearchTitle('Busqueda ' + data['Tipo']);
        PrimeraVezBusqueda = true;
        SetUrlBusqueda(GetUrlBusquedaOpcion(data['Tipo']));

        if(buscar)
            Busqueda(1);
    }

    function EstablecerBuscador(){
        SetSearchThead(thPiezas);
    }

    function ClearForm(){
        
        $('#IdForm').text(''); 
        $('#alertaFormularioActual').hide();

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

        idMarca         = $('#idMar').text().trim();
        idProveedor     = $('#idPro').text().trim();
        idPartidas      = $('#idPar').text().trim();
        idBien          = $('#idBie').text().trim();

        $('.formulario-siama form .form-control').each(function(){
            dataInputs.push($(this).val().trim());
        })
    }

    function RestablecerEstadoAnteriorFormulario(){
        var parametros = {
            "id":               idActual.trim(),
            "idMarca":          idMarca.trim(),       
            "idBien":           idBien.trim(),       
            "idProveedor":      idProveedor.trim(),    
            "idPartidas":       idPartidas.trim(),       
            "Estatus":          dataInputs[0].trim(),
            "Nombre":           dataInputs[1].trim(),
            "Modelo":           dataInputs[2].trim(),
            "Serial":           dataInputs[3].trim(),
            "Inventario":       dataInputs[4].trim(),
            "Bien":             dataInputs[5].trim(),
            "Marca":            dataInputs[6].trim(),
            "Proveedor":        dataInputs[7].trim(),
            "Partidas":         dataInputs[8].trim(),
            "Fabricacion":      dataInputs[9].trim(),
            "fAdquisicion":     dataInputs[10].trim(),
            "Instalacion":      dataInputs[11].trim(),
            "tAdquisicion":     dataInputs[12].trim(),
            "Observacion":      dataInputs[13].trim()
        }
        LlenarFormulario(parametros);
    }
    
    function LlenarFormulario(data){

        $('#IdForm').text(data['id']);
        $('#idMar').text(data['idMarca']);
        $('#idPro').text(data['idProveedor']);
        $('#idPar').text(data['idPartidas']);
        $('#idBie').text(data['idBien']);
        $('#estatusPieza').val(data['Estatus']);
        $('#NombrePieza').val(data['Nombre']);
        $('#modeloPieza').val(data['Modelo']);
        $('#serialPieza').val(data['Serial']);
        $('#invPie').val(data['Inventario']);
        $('#nomMar').val(data['Marca']);
        $('#nomPro').val(data['Proveedor']);
        $('#nomPar').val(data['Partidas']);
        $('#nomBie').val(data['Bien']);
        $('#fabPieza').val(data['Fabricacion']);
        $('#fAdqPieza').val(data['fAdquisicion']);
        $('#Instalacion').val(data['Instalacion']);
        $('#tAdqPieza').val(data['tAdquisicion']);
        $('#Recomendacion').val(data['Recomendacion']);
        $('#Observacion').val(data['Observacion']);
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
                LlenarFormularioRequest(data);
                $('#SiamaModalBusqueda').modal('hide');
            }
        }).fail(function(data){
            failAjaxRequest(data);
        });
    }

    function LlenarFormularioRequest(data){
        
        var parametros = {
            "id":               data['Datos']['pie_id'].trim(),
            "idBien":           data['Datos']['bie_id'].trim(),
            "idMarca":          data['Datos']['mar_id'].trim(),
            "idProveedor":      data['Datos']['pro_id'].trim(), 
            "idPartidas":       data['Datos']['par_id'].trim(), 
            "Estatus":          data['Datos']['estatus'].trim(),
            "Nombre":           data['Datos']['nombre'].trim(),
            "Modelo":           data['Datos']['modelo'].trim(),
            "Serial":           data['Datos']['pie_ser'].trim(),
            "Inventario":       data['Datos']['inv_uc'].trim(),
            "Bien":             data['Datos']['nombie'].trim(),
            "Marca":            data['Datos']['nommar'].trim(),
            "Proveedor":        data['Datos']['nompro'].trim(),
            "Partidas":         data['Datos']['nompar'].trim(),
            "Fabricacion":      data['Datos']['fec_fab'].trim(),
            "fAdquisicion":     data['Datos']['fec_adq'].trim(),
            "Instalacion":      data['Datos']['fec_ins'].trim(),
            "tAdquisicion":     data['Datos']['tip_adq'].trim(),
            "Observacion":      data['Datos']['observaciones'].trim()
        }

        LlenarFormulario(parametros);
    }

    window.InterfazElegirBuscador = function(fila){
        switch(GetSearchType()){
            case "Formulario":
                var parametros = {
                    "id": fila.find("td:eq(0)").text().trim(),
                    "Url": $('#ControladorActual').text().trim()+"/obtener"
                }
                Obtener(parametros);
            break;
            case Proveedores:
                $('#idPro').text(fila.find("td:eq(0)").text().trim());
                $('#nomPro').val(fila.find("td:eq(6)").text().trim());
            break;
            case Partidas:
                $('#idPar').text(fila.find("td:eq(0)").text().trim());
                $('#nomPar').val(fila.find("td:eq(4)").text().trim());
            break;
            case Marcas:
                $('#idMar').text(fila.find("td:eq(0)").text().trim());
                $('#nomMar').val(fila.find("td:eq(1)").text().trim());
            break;
            case Bienes:
                $('#idBie').text(fila.find("td:eq(0)").text().trim());
                $('#nomBie').val(fila.find("td:eq(1)").text().trim());
            break;
        }

        if(GetSearchType() != "Formulario")
            $('#SiamaModalBusqueda').modal('hide');
    }

    window.AccionEliminarFormulario = function(data){
        
        if(data['Datos']['pie_id'] == ""){
            ClearForm();
            AgregarBotoneraPrimariaNULL();
        }else{
            LlenarFormularioRequest(data);
        }
    }
});