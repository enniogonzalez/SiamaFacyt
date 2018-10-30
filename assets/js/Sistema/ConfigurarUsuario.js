

var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){

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

        if(Valido &&  !validateEmail($('#correo').val())){
            Valido = false;
            $('#correo').addClass('is-invalid');
        }

        if(Valido){

            var parametros = {
                "Nombre"            : $('#nombreUsu').val().trim(),
                "Correo"            : $('#correo').val().trim(),
                "Url"               : $('#FormularioActual').attr("action"),
            }

            
            if(!Guardando){
                Guardando = true;
                GuardarConfiguracion(parametros);
            }
        }
        


    });

    function GuardarConfiguracion(parametros){
        
        DeshabilitarFormulario(false);
        DeshabilitarBotonera();
        MostrarEstatus(1); 

        $.ajax({
            url: parametros['Url'],
            type: 'POST',
            data: parametros,
            dataType: 'json'
        }).done(function(data){
            Guardando = false;

            HabilitarBotonera();
            if(data['isValid']){

                $('#alertaFormularioActual').hide();
                HabilitarFormulario(false);
                $('#IdForm').text(data['id']);
                AccionGuardar(data);
                MostrarEstatus(2,true); 
                setTimeout(function(){
                    CerrarEstatus();
                }, 6000);
            }else{
                CerrarEstatus();
                SetAlertaFormulario(data['Mensaje']);
                HabilitarFormulario(false);
            }
        }).fail(function(data){
            Guardando = false;
            HabilitarFormulario(false);
            failAjaxRequest(data);
        });
    }
});