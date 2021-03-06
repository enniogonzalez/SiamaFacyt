

var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){

    var idActual ="";
    var dataInputs= [];

    EstablecerBuscador()


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
            "Cuerpo": "<h4>¿Est&aacute; usted seguro de querer eliminar la herramienta?</h4>",
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
    })

    $('.botoneraFormulario').on('click','#BuscarRegistro',function(){
        SetSearchType('Formulario');
        SetSearchTitle('Busqueda Herramientas');
        PrimeraVezBusqueda = true;
        DeshabilitarBotonera();
        SetUrlBusqueda($('#ControladorActual').text().trim()+"/busqueda");
        Busqueda(1);
        
        setTimeout(function(){
            HabilitarBotonera();
        }, 900);
    })

    $('#SigmaModalAdvertencias').on('click','#ConfirmarEliminacion',function(){
        var parametros = {
            "id": $('#IdForm').text().trim(),
            "Url": $('#ControladorActual').text().trim()+"/eliminar"
        }
        Eliminar(parametros)
    });

    $('.botoneraFormulario').on('click','#EditarRegistro',function(){
        GuardarEstadoActualFormulario();
        HabilitarFormulario()
        $('#nombreHerramienta').focus();
    });

    $('.botoneraFormulario').on('click','#AgregarRegistro',function(){

        GuardarEstadoActualFormulario();
        ClearForm();
        HabilitarFormulario()
        $('#nombreHerramienta').focus();
    })

    $('.botoneraFormulario').on('click','#CancelarRegistro',function(){
        ClearForm();
        RestablecerEstadoAnteriorFormulario();
        DeshabilitarFormulario();
    })

    $('.botoneraFormulario').on('click','#GuardarRegistro',function(){
        
        var Valido = ValidarFormulario();

        if(Valido){
            var parametros = {
                "id":           $('#IdForm').text().trim(),
                "Nombre":       $('#nombreHerramienta').val().trim(),
                "Observacion":  $('#Observacion').val().trim(),
                "Url":          $('#FormularioActual').attr("action")
            }
            
            if(!Guardando){
                Guardando = true;
                GuardarFormulario(parametros);
            }
        }
        
    });

    function EstablecerBuscador(){
        SetSearchThead(thHerramientas);
    }

    function ClearForm(){
        
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
    
    function GuardarEstadoActualFormulario(){
        dataInputs = [];
        idActual =$('#IdForm').text().trim();
        $('.formulario-sigma form .form-control').each(function(){
            dataInputs.push($(this).val().trim());
        })
    }

    function RestablecerEstadoAnteriorFormulario(){
        var parametros = {
            "id":           idActual.trim(),
            "Nombre":       dataInputs[0].trim(),
            "Observacion":  dataInputs[1].trim()
        }
        LlenarFormulario(parametros);
    }
    
    function LlenarFormulario(data){
        $('#IdForm').text(data['id']);
        $('#nombreHerramienta').val(data['Nombre']);
        $('#Observacion').val(data['Observacion']);
    }

    window.InterfazElegirBuscador = function(fila){
        var parametros = {
            "id":           fila.find('td:eq(0)').text().trim(),
            "Nombre":       fila.find('td:eq(1)').text().trim(),
            "Observacion":  fila.find('td:eq(2)').text().trim()
        }
        LlenarFormulario(parametros);
        $('#SigmaModalBusqueda').modal('hide');
    }

    window.AccionEliminarFormulario = function(data){
        
        if(data['Datos']['her_id'] == ""){
            ClearForm();
            AgregarBotoneraPrimariaNULL();
        }else{
            var parametros = {
                "id":           data['Datos']['her_id'].trim(),
                "Nombre":       data['Datos']['nombre'].trim(),
                "Observacion":  data['Datos']['observaciones'].trim()
            }
    
            LlenarFormulario(parametros);
        }
    }
});