

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

    EstablecerBuscador()
    
    $('#ParPad').on('click',function(){
        BuscarPadre();
    });

    $('.BuscarPadre').on('click',function(){
        BuscarPadre();
    });

    $('.BorrarPadre').on('click',function(){
        $('#idPad').text('');
        $('#ParPad').val('');
    });

    $('#CancelarModalBuscar').on('click',function(){
        if(GetSearchType() == "Padre"){
            $('#ParPad').val(PadreActual)
            $('#idPad').text(idPadreActual)
        }
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
            "Cuerpo": "<h4>¿Est&aacute; usted seguro de querer borrar la partida?</h4>",
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
    })

    $('.botoneraFormulario').on('click','#BuscarRegistro',function(){
        SetSearchType('Formulario');
        SetSearchTitle('Busqueda Partidas');
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
            "Url":$('#ControladorActual').text().trim()+"/eliminar"
        }
        Eliminar(parametros)
    });

    $('.botoneraFormulario').on('click','#EditarRegistro',function(){
        GuardarEstadoActualFormulario();
        HabilitarFormulario()
        $('#NombrePartida').focus();
    });

    $('.botoneraFormulario').on('click','#AgregarRegistro',function(){
        GuardarEstadoActualFormulario();
        ClearForm();
        HabilitarFormulario()
        $('#codigoPartida').focus();
    })

    $('.botoneraFormulario').on('click','#CancelarRegistro',function(){
        ClearForm();
        ReestablecerEstadoAnteriorFormulario();
        DeshabilitarFormulario();
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

            var parametros = {
                "id": $('#IdForm').text().trim(),
                "Nombre": $('#NombrePartida').val().trim(),
                "Padre": ($('#idPad').text().trim() == '-1' ? "":$('#idPad').text().trim()),
                "Codigo": $('#codigoPartida').val().trim(),
                "Observacion": $('#Observacion').val(),
                "Url": $('#FormularioActual').attr("action")
            }
            if(!Guardando){
                Guardando = true;
                GuardarFormulario(parametros);
            }
        }
        
    });

    function BuscarPadre(){
        idPadreActual = $('#idPad').text().trim();
        PadreActual = $('#ParPad').val().trim();
        SetSearchType('Padre');
        SetSearchTitle('Busqueda Partidas Padres');
        PrimeraVezBusqueda = true;
        SetUrlBusqueda($('#ControladorActual').text().trim()+"/busqueda");
        Busqueda(1);
    }

    function EstablecerBuscador(){
        SetSearchThead(thPartidas);
    }

    function ClearForm(){
        
        $('#idPad').text(''); 
        $('#IdForm').text(''); 
        $('#alertaFormularioActual').hide();

        $('.formulario-siama form .form-control').each(function(){
            $(this).removeClass('is-invalid');
            if($(this).hasClass('texto'))
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
        idPadreActual = $('#idPad').text().trim();
        $('.formulario-siama form .form-control').each(function(){
            dataInputs.push($(this).val().trim());
        })
    }

    function ReestablecerEstadoAnteriorFormulario(){
        var parametros = {
            "id": idActual.trim(),
            "codigo": dataInputs[0].trim(),
            "Nombre": dataInputs[1].trim(),
            "ParPad": dataInputs[2].trim(),
            "Observacion": dataInputs[3].trim(),
            "idPad":idPadreActual.trim(),
        }
        LlenarFormulario(parametros);
    }
    
    function LlenarFormulario(data){
        $('#IdForm').text(data['id']); 
        $('#NombrePartida').val(data['Nombre']);
        $('#codigoPartida').val(data['codigo']);
        $('#Observacion').val(data['Observacion']);
        $('#idPad').text(data['idPad'] == "-1" ? "": data['idPad']);
        $('#ParPad').val(data['ParPad']);
    }

    window.InterfazElegirBuscador = function(fila){
        if(GetSearchType() == "Formulario"){
            var parametros = {
                "id": fila.find('td:eq(0)').text().trim(),
                "idPad":fila.find('td:eq(1)').text().trim(),
                "ParPad": fila.find('td:eq(2)').text().trim(),
                "codigo": fila.find('td:eq(3)').text().trim(),
                "Nombre": fila.find('td:eq(4)').text().trim(),
                "Observacion": fila.find('td:eq(5)').text()
            }
            LlenarFormulario(parametros);
        }else if(GetSearchType() == "Padre"){
            $('#ParPad').val(fila.find('td:eq(4)').text().trim())
            $('#idPad').text(fila.find('td:eq(0)').text().trim())
        }
        $('#SiamaModalBusqueda').modal('hide');
    }

    window.AccionEliminarFormulario = function(data){
        if(data['Datos']['par_id'] == ""){
            ClearForm();
            AgregarBotoneraPrimariaNULL();
        }else{
            var parametros = {
                "id":           data['Datos']['par_id'].trim(),
                "codigo":       data['Datos']['codigo'].trim(),
                "Nombre":       data['Datos']['nombre'].trim(),
                "ParPad":       data['Datos']['nombrepadre'].trim(),
                "Observacion":  data['Datos']['observaciones'].trim(),
                "idPad":        data['Datos']['idpad'].trim(),
            }
    
            LlenarFormulario(parametros);
        }
    }
});