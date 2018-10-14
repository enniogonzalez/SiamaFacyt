

var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){

    var idActual ="";
    var dataInputs= [];

    EstablecerBuscador()

    $('#Usuario').on('keypress',function(){
        $('#Usuario').val($('#Usuario').val().replace(/[^a-z\.\-0-9]/gi,""))
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
            "Cuerpo": "<h4>¿Est&aacute; usted seguro de querer borrar el usuario?</h4>",
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
    })

    $('.botoneraFormulario').on('click','#BuscarRegistro',function(){
        SetSearchType('Formulario');
        SetSearchTitle('Busqueda Usuarios');
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
    });

    $('.botoneraFormulario').on('click','#AgregarRegistro',function(){
        GuardarEstadoActualFormulario();
        ClearForm();
        HabilitarFormulario()
        $('#Usuario').focus();
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
                "id":           $('#IdForm').text().trim(),
                "Username":     $('#Usuario').val().trim(),
                "Nombre":       $('#nombreUsu').val().trim(),
                "Cargo":        $('#Cargo').val().trim(),
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
        html = `
            <tr>
                <th style="width:30%;">Usuario</th>
                <th style="width:40%;">Nombre</th>
                <th style="width:30%;">Cargo</th>
            </tr>
        `;
        SetSearchThead(html);
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
            else if ($(this).hasClass('decimal'))
                $(this).val('0.00')
        })
    }
    
    function GuardarEstadoActualFormulario(){
        dataInputs = [];
        idActual =$('#IdForm').text().trim();
        $('.formulario-siama form .form-control').each(function(){
            dataInputs.push($(this).val().trim());
        })
    }

    function ReestablecerEstadoAnteriorFormulario(){
        var parametros = {
            "id":           idActual.trim(),
            "Username":     dataInputs[0].trim(),
            "Nombre":       dataInputs[1].trim(),
            "Cargo":        dataInputs[2].trim(),
            "Observacion":  dataInputs[3].trim()
        }
        LlenarFormulario(parametros);
    }
    
    function LlenarFormulario(data){
        $('#IdForm').text(data['id']);
        $('#Usuario').val(data['Username']);
        $('#nombreUsu').val(data['Nombre']);
        $('#Cargo').val(data['Cargo']);
        $('#Observacion').val(data['Observacion']);
    }

    window.InterfazElegirBuscador = function(fila){
        var parametros = {
            "id":           fila.find('td:eq(0)').text().trim(),
            "Username":     fila.find('td:eq(2)').text().trim(),
            "Nombre":       fila.find('td:eq(3)').text().trim(),
            "Cargo":        fila.find('td:eq(4)').text().trim(),
            "Observacion":  fila.find('td:eq(1)').text().trim(),
        }
        LlenarFormulario(parametros);
    }

    window.AccionEliminarFormulario = function(data){
        
        if(data['Datos']['usu_id'] == ""){
            ClearForm();
            AgregarBotoneraPrimariaNULL();
        }else{
            var parametros = {
                "id":           data['Datos']['usu_id'].trim(),
                "Username":     data['Datos']['username'].trim(),
                "Nombre":       data['Datos']['nombre'].trim(),
                "Cargo":        data['Datos']['cargo'].trim(),
                "Observacion":  data['Datos']['observaciones'].trim(),
            }
            LlenarFormulario(parametros);
        }
    }
});