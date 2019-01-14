

var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){

    var idActual ="";
    var idPadreActual ="";
    var dataInputs= [];
    var PadreActual = "";

    EstablecerBuscador();
    
    /************************************/
    /*      Inicio Buscadores           */
    /************************************/

    $('#LocPad').on('click',function(){
        BuscarPadre();
    });

    $('.BorrarPadre').on('click',function(){
        $('#idPad').text('');
        $('#LocPad').val('');
    });

    $('.BuscarPadre').on('click',function(){
        BuscarPadre();
    });

    /************************************/
    /*          Fin Buscadores          */
    /************************************/

    $('#CancelarModalBuscar').on('click',function(){
        if(GetSearchType() == "Padre"){
            $('#LocPad').val(PadreActual)
            $('#idPad').text(idPadreActual)
        }
    })
    
    $('.botoneraFormulario').on('click','#AgregarRegistro',function(){

        GuardarEstadoActualFormulario();

        ClearForm();
        HabilitarFormulario()

        $('#NombreLoc').focus();
    })

    $('.botoneraFormulario').on('click','#BuscarRegistro',function(){
        SetSearchType('Formulario');
        SetSearchTitle('Busqueda Localizaciones');
        PrimeraVezBusqueda = true;
        DeshabilitarBotonera();
        SetUrlBusqueda($('#ControladorActual').text().trim()+"/busqueda");
        Busqueda(1);
        
        setTimeout(function(){
            HabilitarBotonera();
        }, 900);
        
    })

    $('.botoneraFormulario').on('click','#CancelarRegistro',function(){
        ClearForm();
        RestablecerEstadoAnteriorFormulario();
        DeshabilitarFormulario()
    })

    $('.botoneraFormulario').on('click','#EditarRegistro',function(){
        GuardarEstadoActualFormulario();
        HabilitarFormulario()
        $('#NombreLoc').focus();
    })

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
            "Cuerpo": "<h4>¿Est&aacute; usted seguro de querer eliminar la localización?</h4>",
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
    })
    
    $('.botoneraFormulario').on('click','#GuardarRegistro',function(){
        var Valido = true;
        
        $('.formulario-sigma form .form-control').each(function(){
            $(this).removeClass('is-invalid');
            if($(this).hasClass('obligatorio') && $(this).val().trim() == ""){

                if(Valido)
                    $(this).focus();
                
                Valido = false;
                $(this).addClass('is-invalid');
            }
        })

        if(Valido){

            var parametros = {
                "id": $('#IdForm').text().trim(),
                "Nombre": $('#NombreLoc').val().trim(),
                "Padre": ($('#idPad').text().trim() == '-1' ? "":$('#idPad').text().trim()),
                "Ubicacion": $('#Ubicacion').val().trim(),
                "Tipo": $('#Tipo').val().trim(),
                "Observacion": $('#Observacion').val(),
                "Url": $('#FormularioActual').attr("action")
            }

            if(!Guardando){
                Guardando = true;
                GuardarFormulario(parametros);
            }
        }
        
    });

    $('#SigmaModalAdvertencias').on('click','#ConfirmarEliminacion',function(){
        var parametros = {
            "id": $('#IdForm').text().trim(),
            "Url": $('#ControladorActual').text().trim()+"/eliminar"
        }
        Eliminar(parametros)
    });

    function BuscarPadre(){
        idPadreActual = $('#idPad').text().trim();
        PadreActual = $('#LocPad').val().trim();
        SetSearchType('Padre');
        SetSearchTitle('Busqueda Localizaciones Padres');
        PrimeraVezBusqueda = true;
        SetUrlBusqueda($('#ControladorActual').text().trim()+"/busqueda");
        Busqueda(1);
    }

    function ClearForm(){
        
        $('#idPad').text(''); 
        $('#IdForm').text(''); 
        $('#alertaFormularioActual').hide();

        $('.formulario-sigma form .form-control').each(function(){
            $(this).removeClass('is-invalid');
            if($(this).hasClass('texto'))
                $(this).val('')
            else if($(this).hasClass('lista'))
                $(this)[0].selectedIndex = 0;
            else if ($(this).hasClass('decimal'))
                $(this).val('0.00')
        })
    }

    function EstablecerBuscador(){
        SetSearchThead(thLocalizaciones);
    }

    function GuardarEstadoActualFormulario(){
        dataInputs = [];
        idActual =$('#IdForm').text().trim();
        idPadreActual = $('#idPad').text().trim();
        PadreActual = $('#LocPad').val().trim();
        $('.formulario-sigma form .form-control').each(function(){
            dataInputs.push($(this).val().trim());
        })
    }
    
    function LlenarFormulario(data){
        $('#IdForm').text(data['id']); 
        $('#NombreLoc').val(data['Nombre']);
        $('#Ubicacion').val(data['Ubicacion']);
        $('#Tipo').val(data['Tipo']);
        $('#Observacion').val(data['Observacion']);
        $('#idPad').text(data['idPad'] == "-1" ? "": data['idPad']);
        $('#LocPad').val(data['LocPad']);
    }

    function RestablecerEstadoAnteriorFormulario(){
        var parametros = {
            "id": idActual.trim(),
            "Nombre": dataInputs[0].trim(),
            "Ubicacion": dataInputs[1].trim(),
            "Tipo": dataInputs[2].trim(),
            "LocPad": dataInputs[3].trim(),
            "Observacion":dataInputs[4].trim(),
            "idPad":idPadreActual.trim(),
        }
        LlenarFormulario(parametros);
    }

    window.AccionEliminarFormulario = function(data){
        
        if(data['Datos']['loc_id'] == "")
            AgregarBotoneraPrimariaNULL();

        var parametros = {
            "id":           data['Datos']['loc_id'].trim(),
            "Nombre":       data['Datos']['nombre'].trim(),
            "Ubicacion":    data['Datos']['ubicacion'].trim(),
            "Tipo":         data['Datos']['tipo'].trim(),
            "Observacion":  data['Datos']['observaciones'].trim(),
            "LocPad":       data['Datos']['nombrepadre'].trim(),
            "idPad":        data['Datos']['idpad'].trim(),
        }

        LlenarFormulario(parametros);
    }

    window.InterfazElegirBuscador = function(fila){
        if(GetSearchType() == "Formulario"){
            var parametros = {
                "id": fila.find('td:eq(0)').text().trim(),
                "Nombre": fila.find('td:eq(2)').text().trim(),
                "Ubicacion": fila.find('td:eq(3)').text().trim(),
                "Tipo": fila.find('td:eq(4)').text().trim(),
                "Observacion":fila.find('td:eq(1)').text().trim(),
                "idPad":fila.find('td:eq(5)').text().trim(),
                "LocPad":fila.find('td:eq(6)').text().trim()
            }
            LlenarFormulario(parametros);
        }else if(GetSearchType() == "Padre"){
            $('#LocPad').val(fila.find('td:eq(2)').text().trim())
            $('#idPad').text(fila.find('td:eq(0)').text().trim())
        }
        
        $('#SigmaModalBusqueda').modal('hide');
    }
});