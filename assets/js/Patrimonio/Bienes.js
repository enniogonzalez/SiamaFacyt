

var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){

    const Proveedores = "Proveedores";
    const Localizaciones = "Localizaciones";
    const Partidas = "Partidas";
    const Marcas = "Marcas";
    const Custodios = "Custodios";
    const Bienes = "Bienes";

    var idActual ="";
    var dataInputs= [];
    var idBuscadorActual = "";
    var nombreBuscadorActual = "";
    var idMarca = "";
    var idProveedor = "";
    var idLocalizacion = "";
    var idPartidas = "";
    var idCustodio = "";

    EstablecerBuscador();


    $('#CancelarModalBuscar').on('click',function(){
        switch(GetSearchType()){
            case Proveedores:
                $('#idPro').text(idBuscadorActual.trim());
                $('#nomPro').val(nombreBuscadorActual.trim());
            break;
            case Localizaciones:
                $('#idLoc').text(idBuscadorActual.trim());
                $('#nomLoc').val(nombreBuscadorActual.trim());
            break;
            case Partidas:
                $('#idPar').text(idBuscadorActual.trim());
                $('#nomPar').val(nombreBuscadorActual.trim());
            break;
            case Marcas:
                $('#idMar').text(idBuscadorActual.trim());
                $('#nomMar').val(nombreBuscadorActual.trim());
            break;
            case Custodios:
                $('#idCus').text(idBuscadorActual.trim());
                $('#nomCus').val(nombreBuscadorActual.trim());
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
    /*          Manejo Custodio         */
    /************************************/

    $('#nomCus').on('click',function(){
        BuscarCustodio();
    });

    $('.BuscarCustodio').on('click',function(){
        BuscarCustodio();
    });

    $('.BorrarCustodio').on('click',function(){
        $('#idCus').text("");
        $('#nomCus').val("");
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
            "Cuerpo": "<h4>¿Est&aacute; usted seguro de querer borrar el Bien?</h4>",
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
    })

    $('.botoneraFormulario').on('click','#BuscarRegistro',function(){
        SetSearchType('Formulario');
        SetSearchTitle('Busqueda Bienes');
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
        $('#NombreBien').focus();
        $(window).scrollTop(0);
    });

    $('.botoneraFormulario').on('click','#AgregarRegistro',function(){
        GuardarEstadoActualFormulario();
        ClearForm();
        HabilitarFormulario()
        $('#NombreBien').focus();
        $(window).scrollTop(0);
    })

    $('.botoneraFormulario').on('click','#CancelarRegistro',function(){
        ClearForm();
        ReestablecerEstadoAnteriorFormulario();
        EstablecerBuscador();
        DeshabilitarFormulario();

        parametros = {
            "Lista": $('#listaBusquedaFormulario').html().trim(),
            "Tipo": Bienes
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
                "Tipo": Bienes
            }
    
            SetSearchModal(data,false)
            SetModalEtqContador("")
            SetSearchType("Formulario");



            var parametros = {
                "id":               $('#IdForm').text().trim(),
                "Estatus":          $('#estatusBien').val().trim(),
                "Nombre":           $('#NombreBien').val().trim(),
                "Modelo":           $('#modeloBien').val().trim(),
                "Serial":           $('#serialBien').val().trim(),
                "Inventario":       $('#invBien').val().trim(),
                "Marca":            $('#idMar').text().trim(),
                "Proveedor":        $('#idPro').text().trim(),
                "Localizacion":     $('#idLoc').text().trim(),
                "Partidas":         $('#idPar').text().trim(),
                "Custodio":         $('#idCus').text().trim(),
                "Fabricacion":      $('#fabBien').val().trim(),
                "fAdquisicion":     $('#fAdqBien').val().trim(),
                "Instalacion":      $('#Instalacion').val().trim(),
                "tAdquisicion":     $('#tAdqBien').val().trim(),
                "Alimentacion":     $('#Alimentacion').val().trim(),
                "Uso":              $('#UsoBien').val().trim(),
                "Tipo":             $('#tipoBien').val().trim(),
                "Tecnologia":       $('#tecBien').val().trim(),
                "Riesgo":           $('#riesgoBien').val().trim(),
                "mVoltaje":         $('#mVol').val().trim(),
                "uVoltaje":         $('#uVol').val().trim(),
                "mAmperaje":        $('#mAmp').val().trim(),
                "uAmperaje":        $('#uAmp').val().trim(),
                "mPotencia":        $('#mPot').val().trim(),
                "uPotencia":        $('#uPot').val().trim(),
                "mFrecuencia":      $('#mFre').val().trim(),
                "uFrecuencia":      $('#uFre').val().trim(),
                "mCapacidad":       $('#mCap').val().trim(),
                "uCapacidad":       $('#uCap').val().trim(),
                "mPresion":         $('#mPre').val().trim(),
                "uPresion":         $('#uPre').val().trim(),
                "mFlujo":           $('#mFlu').val().trim(),
                "uFlujo":           $('#uFlu').val().trim(),
                "mTemperatura":     $('#mTem').val().trim(),
                "uTemperatura":     $('#uTem').val().trim(),
                "mPeso":            $('#mPes').val().trim(),
                "uPeso":            $('#uPes').val().trim(),
                "mVelocidad":       $('#mVel').val().trim(),
                "uVelocidad":       $('#uVel').val().trim(),
                "Recomendacion":    $('#Recomendacion').val().trim(),
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

        idBuscadorActual = $('#idMar').text().trim();
        nombreBuscadorActual = $('#nomMar').val().trim();
        SetSearchModal(parametros)

    }

    function BuscarCustodio(){

        SetSearchThead(thUsuarios);
        parametros = {
            "Lista": $('#listaBusquedaCustodio').html().trim(),
            "Tipo": Custodios,
        }

        idBuscadorActual = $('#idCus').text().trim();
        nombreBuscadorActual = $('#nomCus').val().trim();
        SetSearchModal(parametros)

    }

    function GetUrlBusquedaOpcion(opcion){
        switch(opcion){
            case Proveedores:
                controlador = "proveedores";
            break;
            case Localizaciones:
                controlador = "localizaciones";
            break;
            case Partidas:
                controlador = "partidas";
            break;
            case Marcas:
                controlador = "marcas";
            break;
            case Custodios:
                controlador = "usuarios";
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

        SetSearchThead(thBienes);
    }

    function ClearForm(){
        
        $('#IdForm').text(''); 
        $('#alertaFormularioActual').hide();

        $('.formulario-siama form .form-control').each(function(){
            $(this).removeClass('is-invalid');
            if($(this).hasClass('texto'))
                $(this).val('')
            else if($(this).hasClass('lista'))
                $(this)[0].selectedIndex = 0;
            else if($(this).hasClass('fecha'))
                $(this).val('')
            else if ($(this).hasClass('decimal'))
                $(this).val('0.00')
        })
    }
    
    function GuardarEstadoActualFormulario(){
        dataInputs = [];
        idActual =$('#IdForm').text().trim();

        idMarca         = $('#idMar').text().trim();
        idProveedor     = $('#idPro').text().trim();
        idLocalizacion  = $('#idLoc').text().trim();
        idPartidas      = $('#idPar').text().trim();
        idCustodio      = $('#idCus').text().trim();

        $('.formulario-siama form .form-control').each(function(){
            dataInputs.push($(this).val().trim());
        })
    }

    function ReestablecerEstadoAnteriorFormulario(){
        var parametros = {
            "id":               idActual.trim(),
            "idMarca":          idMarca.trim(),       
            "idProveedor":      idProveedor.trim(),    
            "idLocalizacion":   idLocalizacion.trim(), 
            "idPartidas":       idPartidas.trim(),     
            "idCustodio":       idCustodio.trim(),     
            "Estatus":          dataInputs[0].trim(),
            "Nombre":           dataInputs[1].trim(),
            "Modelo":           dataInputs[2].trim(),
            "Serial":           dataInputs[3].trim(),
            "Inventario":       dataInputs[4].trim(),
            "Marca":            dataInputs[5].trim(),
            "Proveedor":        dataInputs[6].trim(),
            "Localizacion":     dataInputs[7].trim(),
            "Partidas":         dataInputs[8].trim(),
            "Custodio":         dataInputs[9].trim(),
            "Fabricacion":      dataInputs[10].trim(),
            "fAdquisicion":     dataInputs[11].trim(),
            "Instalacion":      dataInputs[12].trim(),
            "tAdquisicion":     dataInputs[13].trim(),
            "Alimentacion":     dataInputs[14].trim(),
            "Uso":              dataInputs[15].trim(),
            "Tipo":             dataInputs[16].trim(),
            "Tecnologia":       dataInputs[17].trim(),
            "Riesgo":           dataInputs[18].trim(),
            "mVoltaje":         dataInputs[19].trim(),
            "uVoltaje":         dataInputs[20].trim(),
            "mAmperaje":        dataInputs[21].trim(),
            "uAmperaje":        dataInputs[22].trim(),
            "mPotencia":        dataInputs[23].trim(),
            "uPotencia":        dataInputs[24].trim(),
            "mFrecuencia":      dataInputs[25].trim(),
            "uFrecuencia":      dataInputs[26].trim(),
            "mCapacidad":       dataInputs[27].trim(),
            "uCapacidad":       dataInputs[28].trim(),
            "mPresion":         dataInputs[29].trim(),
            "uPresion":         dataInputs[30].trim(),
            "mFlujo":           dataInputs[31].trim(),
            "uFlujo":           dataInputs[32].trim(),
            "mTemperatura":     dataInputs[33].trim(),
            "uTemperatura":     dataInputs[34].trim(),
            "mPeso":            dataInputs[35].trim(),
            "uPeso":            dataInputs[36].trim(),
            "mVelocidad":       dataInputs[37].trim(),
            "uVelocidad":       dataInputs[38].trim(),
            "Recomendacion":    dataInputs[39].trim(),
            "Observacion":      dataInputs[40].trim()
        }
        LlenarFormulario(parametros);
    }
    
    function LlenarFormulario(data){

        $('#IdForm').text(data['id']);
        $('#idMar').text(data['idMarca']);
        $('#idPro').text(data['idProveedor']);
        $('#idLoc').text(data['idLocalizacion']);
        $('#idPar').text(data['idPartidas']);
        $('#idCus').text(data['idCustodio']);
        $('#estatusBien').val(data['Estatus']);
        $('#NombreBien').val(data['Nombre']);
        $('#modeloBien').val(data['Modelo']);
        $('#serialBien').val(data['Serial']);
        $('#invBien').val(data['Inventario']);
        $('#nomMar').val(data['Marca']);
        $('#nomPro').val(data['Proveedor']);
        $('#nomLoc').val(data['Localizacion']);
        $('#nomPar').val(data['Partidas']);
        $('#nomCus').val(data['Custodio']);
        $('#fabBien').val(data['Fabricacion']);
        $('#fAdqBien').val(data['fAdquisicion']);
        $('#Instalacion').val(data['Instalacion']);
        $('#tAdqBien').val(data['tAdquisicion']);
        $('#Alimentacion').val(data['Alimentacion']);
        $('#UsoBien').val(data['Uso']);
        $('#tipoBien').val(data['Tipo']);
        $('#tecBien').val(data['Tecnologia']);
        $('#riesgoBien').val(data['Riesgo']);
        $('#mVol').val(data['mVoltaje']);
        $('#uVol').val(data['uVoltaje']);
        $('#mAmp').val(data['mAmperaje']);
        $('#uAmp').val(data['uAmperaje']);
        $('#mPot').val(data['mPotencia']);
        $('#uPot').val(data['uPotencia']);
        $('#mFre').val(data['mFrecuencia']);
        $('#uFre').val(data['uFrecuencia']);
        $('#mCap').val(data['mCapacidad']);
        $('#uCap').val(data['uCapacidad']);
        $('#mPre').val(data['mPresion']);
        $('#uPre').val(data['uPresion']);
        $('#mFlu').val(data['mFlujo']);
        $('#uFlu').val(data['uFlujo']);
        $('#mTem').val(data['mTemperatura']);
        $('#uTem').val(data['uTemperatura']);
        $('#mPes').val(data['mPeso']);
        $('#uPes').val(data['uPeso']);
        $('#mVel').val(data['mVelocidad']);
        $('#uVel').val(data['uVelocidad']);
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
            "id":               data['Datos']['bie_id'].trim(),
            "idMarca":          data['Datos']['mar_id'].trim(),
            "idProveedor":      data['Datos']['pro_id'].trim(), 
            "idLocalizacion":   data['Datos']['loc_id'].trim(), 
            "idPartidas":       data['Datos']['par_id'].trim(), 
            "idCustodio":       data['Datos']['custodio'].trim(), 
            "Estatus":          data['Datos']['estatus'].trim(),
            "Nombre":           data['Datos']['nombre'].trim(),
            "Modelo":           data['Datos']['modelo'].trim(),
            "Serial":           data['Datos']['bie_ser'].trim(),
            "Inventario":       data['Datos']['inv_uc'].trim(),
            "Marca":            data['Datos']['nommar'].trim(),
            "Proveedor":        data['Datos']['nompro'].trim(),
            "Localizacion":     data['Datos']['nomloc'].trim(),
            "Partidas":         data['Datos']['nompar'].trim(),
            "Custodio":         data['Datos']['nomcus'].trim(),
            "Fabricacion":      data['Datos']['fec_fab'].trim(),
            "fAdquisicion":     data['Datos']['fec_adq'].trim(),
            "Instalacion":      data['Datos']['fec_ins'].trim(),
            "tAdquisicion":     data['Datos']['tip_adq'].trim(),
            "Alimentacion":     data['Datos']['fue_ali'].trim(),
            "Uso":              data['Datos']['cla_uso'].trim(),
            "Tipo":             data['Datos']['tipo'].trim(),
            "Tecnologia":       data['Datos']['tec_pre'].trim(),
            "Riesgo":           data['Datos']['riesgo'].trim(),
            "mVoltaje":         data['Datos']['med_vol'].trim(),
            "uVoltaje":         data['Datos']['uni_vol'].trim(),
            "mAmperaje":        data['Datos']['med_amp'].trim(),
            "uAmperaje":        data['Datos']['uni_amp'].trim(),
            "mPotencia":        data['Datos']['med_pot'].trim(),
            "uPotencia":        data['Datos']['uni_pot'].trim(),
            "mFrecuencia":      data['Datos']['med_fre'].trim(),
            "uFrecuencia":      data['Datos']['uni_fre'].trim(),
            "mCapacidad":       data['Datos']['med_cap'].trim(),
            "uCapacidad":       data['Datos']['uni_cap'].trim(),
            "mPresion":         data['Datos']['med_pre'].trim(),
            "uPresion":         data['Datos']['uni_pre'].trim(),
            "mFlujo":           data['Datos']['med_flu'].trim(),
            "uFlujo":           data['Datos']['uni_flu'].trim(),
            "mTemperatura":     data['Datos']['med_tem'].trim(),
            "uTemperatura":     data['Datos']['uni_tem'].trim(),
            "mPeso":            data['Datos']['med_pes'].trim(),
            "uPeso":            data['Datos']['uni_pes'].trim(),
            "mVelocidad":       data['Datos']['med_vel'].trim(),
            "uVelocidad":       data['Datos']['uni_vel'].trim(),
            "Recomendacion":    data['Datos']['rec_fab'].trim(),
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
            case Localizaciones:
                $('#idLoc').text(fila.find("td:eq(0)").text().trim());
                $('#nomLoc').val(fila.find("td:eq(3)").text().trim());
            break;
            case Partidas:
                $('#idPar').text(fila.find("td:eq(0)").text().trim());
                $('#nomPar').val(fila.find("td:eq(4)").text().trim());
            break;
            case Marcas:
                $('#idMar').text(fila.find("td:eq(0)").text().trim());
                $('#nomMar').val(fila.find("td:eq(1)").text().trim());
            break;
            case Custodios:
                $('#idCus').text(fila.find("td:eq(0)").text().trim());
                $('#nomCus').val(fila.find("td:eq(3)").text().trim());
            break;

        }

        if(GetSearchType() != "Formulario")
            $('#SiamaModalBusqueda').modal('hide');
    }

    window.AccionEliminarFormulario = function(data){
        
        if(data['Datos']['bie_id'] == ""){
            ClearForm();
            AgregarBotoneraPrimariaNULL();
        }else{
            LlenarFormularioRequest(data);
        }
    }
});