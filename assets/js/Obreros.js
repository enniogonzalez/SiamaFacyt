

var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){

    var idActual ="";
    var dataInputs= [];

    EstablecerBuscador()

    $('.botoneraFormulario').on('click','#AgregarRegistro',function(){
        GuardarEstadoActualFormulario();
        ClearForm();
        HabilitarFormulario()
        $('#codigo').focus();
    })

    $('.botoneraFormulario').on('click','#BuscarRegistro',function(){
        SetSearchType('Formulario');
        SetSearchTitle('Busqueda Obreros');
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
        DeshabilitarFormulario();
    })

    $('.botoneraFormulario').on('click','#EditarRegistro',function(){
        GuardarEstadoActualFormulario();
        HabilitarFormulario()
        $('#codigo').attr("disabled", "disabled");
        $('#codigo').attr("readonly", "readonly");
        $('#nombreObr').focus();
    });

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
            "Cuerpo": "<h4>¿Est&aacute; usted seguro de querer borrar el obrero?</h4>",
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
    })

    $('.botoneraFormulario').on('click','#GuardarRegistro',function(){
        var Valido = ValidarFormulario();

        if(Valido && $('#correo').val().trim() != '' && !validateEmail($('#correo').val())){
            Valido = false;
            $('#correo').addClass('is-invalid');
        }

        if(Valido){
            var parametros = {
                "id":           $('#IdForm').text().trim(),
                "Cedula":       $('#cedula').val().trim(),
                "Nombre":       $('#nombreObr').val().trim(),
                "Tlf":          $('#tlf').val().trim(),
                "Correo":       $('#correo').val().trim(),
                "Observacion":  $('#Observacion').val().trim(),
                "Url":          $('#FormularioActual').attr("action")
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

    function EstablecerBuscador(){
        SetSearchThead(thObreros);
    }
    
    function GuardarEstadoActualFormulario(){
        dataInputs = [];
        idActual =$('#IdForm').text().trim();
        $('.formulario-sigma form .form-control').each(function(){
            dataInputs.push($(this).val().trim());
        })
    }
    
    function LlenarFormulario(data){
        $('#IdForm').text(data['id']);
        $('#cedula').val(data['Cedula']);
        $('#nombreObr').val(data['Nombre']);
        $('#tlf').val(data['Tlf']);
        $('#correo').val(data['Correo']);
        $('#Observacion').val(data['Observacion']);
    }

    function RestablecerEstadoAnteriorFormulario(){
        var parametros = {
            "id":           idActual.trim(),
            "Cedula":       dataInputs[0].trim(),
            "Nombre":       dataInputs[1].trim(),
            "Tlf":          dataInputs[2].trim(),
            "Correo":       dataInputs[3].trim(),
            "Observacion":  dataInputs[4].trim()
        }
        LlenarFormulario(parametros);
    }

    window.AccionEliminarFormulario = function(data){
        
        if(data['Datos']['obr_id'] == ""){
            ClearForm();
            AgregarBotoneraPrimariaNULL();
        }else{
            var parametros = {
                "id":           data['Datos']['obr_id'].trim(),
                "Cedula":       data['Datos']['cedula'].trim(),
                "Nombre":       data['Datos']['nombre'].trim(),
                "Tlf":          data['Datos']['telefonos'].trim(),
                "Correo":       data['Datos']['correo'].trim(),
                "Observacion":  data['Datos']['observaciones'].trim()
            }
    
            LlenarFormulario(parametros);
        }
    }

    window.InterfazElegirBuscador = function(fila){
        var parametros = {
            "id":           fila.find('td:eq(0)').text().trim(),
            "Tlf":          fila.find('td:eq(1)').text().trim(),
            "Correo":       fila.find('td:eq(2)').text().trim(),
            "Observacion":  fila.find('td:eq(3)').text().trim(),
            "Cedula":       fila.find('td:eq(4)').text().trim(),
            "Nombre":       fila.find('td:eq(5)').text().trim()
        }
        LlenarFormulario(parametros);
        $('#SigmaModalBusqueda').modal('hide');
    }
});