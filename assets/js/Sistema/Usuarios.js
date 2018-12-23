

var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){

    var idActual ="";
    var dataInputs= [];
    var permisos = [];

    EstablecerBuscador()

    $('.botoneraFormulario').on('click','#AgregarRegistro',function(){
        GuardarEstadoActualFormulario();
        ClearForm();
        HabilitarFormulario()
        $('#Usuario').focus();
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

    $('.botoneraFormulario').on('click','#CancelarRegistro',function(){
        ClearForm();
        RestablecerEstadoAnteriorFormulario();
        DeshabilitarFormulario();
    })

    $('.botoneraFormulario').on('click','#EditarRegistro',function(){
        GuardarEstadoActualFormulario();
        HabilitarFormulario()
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
            "Cuerpo": "<h4>¿Est&aacute; usted seguro de querer eliminar el usuario?</h4>",
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
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

        if(Valido && $('#correo').val().trim() != '' && !validateEmail($('#correo').val())){
            Valido = false;
            $('#correo').addClass('is-invalid');
        }

        if(Valido){

            var parametros = {
                "id"                : $('#IdForm').text().trim(),
                "Username"          : $('#Usuario').val().trim(),
                "Nombre"            : $('#nombreUsu').val().trim(),
                "Rol"               : $('#rol').val().trim(),
                "Correo"            : $('#correo').val().trim(),
                "Observacion"       : $('#Observacion').val().trim(),
                "Url"               : $('#FormularioActual').attr("action")
            }
            
            if(!Guardando){
                Guardando = true;
                GuardarFormulario(parametros);
            }
        }
        
    });

    $('#SiamaModalAdvertencias').on('click','#ConfirmarEliminacion',function(){
        var parametros = {
            "id": $('#IdForm').text().trim(),
            "Url": $('#ControladorActual').text().trim()+"/eliminar"
        }
        Eliminar(parametros)
    });

    $('#TablaPermisos').on('click','.seleccionarPermiso',function(){
        $('.tr-activa-siama').removeClass('tr-activa-siama');
        var fila = $(this).parent('tr');
        fila.addClass('tr-activa-siama');

        if($(this).find('span').hasClass('fa-square-o')){
            $(this).find('span').removeClass('fa-square-o');
            $(this).find('span').addClass('fa-check-square-o');
            $(this).parent('tr').find('td:eq(13)').text('Realizado');
        }else{
            $(this).find('span').removeClass('fa-check-square-o');
            $(this).find('span').addClass('fa-square-o');
            $(this).parent('tr').find('td:eq(13)').text('');
        }
    });

    $('#Usuario').on('keypress',function(e){
        if (e.which == 32)
            return false;

        $('#Usuario').val($('#Usuario').val().replace(/[^a-z\.\-0-9]/gi,""))
    })

    function ClearForm(){
        
        $('#IdForm').text(''); 
        $('#alertaFormularioActual').hide();
        $('.seleccionarPermiso').find('span').removeClass('fa-check-square-o');
        $('.seleccionarPermiso').find('span').removeClass('fa-square-o');
        $('.seleccionarPermiso').find('span').addClass('fa-square-o');

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

    function EstablecerBuscador(){
        html = `
            <tr>
                <th style="width:30%;">Usuario</th>
                <th style="width:40%;">Nombre</th>
                <th style="width:30%;">Rol</th>
            </tr>
        `;
        SetSearchThead(html);
    }
    
    function GuardarEstadoActualFormulario(){
        dataInputs = [];
        permisos = [];
        idActual =$('#IdForm').text().trim();
        $('.formulario-siama form .form-control').each(function(){
            dataInputs.push($(this).val().trim());
        });

    }
    
    function LlenarFormulario(data){
        $('#IdForm').text(data['id']);
        $('#Usuario').val(data['Username']);
        $('#nombreUsu').val(data['Nombre']);
        $('#rol').val(data['Rol']);
        $('#correo').val(data['Correo']);
        $('#Observacion').val(data['Observacion']);
    }

    function LlenarFormularioRequest(data){

        var parametros = {
            "id"            : data['usu_id'],
            "Username"      : data['username'],
            "Nombre"        : data['nombre'],
            "Rol"           : data['rol_id'],
            "Correo"        : data['correo'],
            "Observacion"   : data['observaciones'],
        }

        LlenarFormulario(parametros);
        
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
                LlenarFormularioRequest(data['Datos']);
                $('#SiamaModalBusqueda').modal('hide');
            }
        }).fail(function(data){
            failAjaxRequest(data);
        });
    }

    function RestablecerEstadoAnteriorFormulario(){
        var parametros = {
            "id"            : idActual.trim(),
            "Username"      : dataInputs[0].trim(),
            "Nombre"        : dataInputs[1].trim(),
            "Rol"           : dataInputs[2].trim(),
            "Correo"        : dataInputs[3].trim(),
            "Observacion"   : dataInputs[4].trim(),
        }
        LlenarFormulario(parametros);
    }

    window.AccionEliminarFormulario = function(data){
        
        if(data['Datos']['usu_id'] == ""){
            ClearForm();
            AgregarBotoneraPrimariaNULL();
        }else{

            LlenarFormularioRequest(data['Datos']);
        }
    }

    window.InterfazElegirBuscador = function(fila){
        
        var parametros = {
            "id": fila.find("td:eq(0)").text().trim(),
            "Url": $('#ControladorActual').text().trim()+"/obtener"
        }
        Obtener(parametros);
    }
});