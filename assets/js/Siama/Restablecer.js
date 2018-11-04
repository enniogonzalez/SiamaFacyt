$(function(){

    Guardando = false;

    $('#resetform').submit((e)=>{
        e.preventDefault();

        if(!$('#inputUsuario')[0].checkValidity()){
            $('#alertaLogin').hide()
            $('#inputUsuario').addClass('is-invalid');
            $('#inputUsuario').focus();
        }else
            $('#inputUsuario').removeClass('is-invalid');

        if($('#inputUsuario')[0].checkValidity() && !Guardando){
            
            $('#inputUsuario').attr("disabled", "disabled");
            $('#inputUsuario').attr("readonly", "readonly");

            Guardando = true;
            parametros = {
                "Username":$('#inputUsuario').val()
            };

            MostrarEstatus(5); 
            $.ajax({
                url: $('#resetform').attr("action"),
                type: $('#resetform').attr("method"),
                data: parametros,
                dataType: 'json'
            }).done(function(data){
                if(data['isValid']){ 
                    CorreoEnviado(data['Correo']);
                }else{
                    $('#alertaLogin').show();
                    $('#inputUsuario').val("");
                }
                CerrarEstatus();
            }).fail(function(data){
                ErrorOcurrido();
                CerrarEstatus();
            });
        }
    });

    $('#resetpassform').submit((e)=>{
        e.preventDefault();

        usuario = $('#usu').val();
        if( $('#inputPassword').val() != $('#inputPassword2').val()) {
            $('#alertaReset').text("Las contraseñas no coinciden");
            $('#alertaReset').show();
        }else if($('#inputPassword').val().length != 10){
            $('#alertaReset').text("La contraseña debe ser de 10 caracteres");
            $('#alertaReset').show();

        }else{
            
            $('#inputPassword').attr("disabled", "disabled");
            $('#inputPassword').attr("readonly", "readonly");
            
            $('#inputPassword2').attr("disabled", "disabled");
            $('#inputPassword2').attr("readonly", "readonly");
            
            parametros = {
                "password" : CryptoJS.MD5($('#inputPassword').val()).toString(),
                "token" : $('#token').val()
            }
            
            MostrarEstatus(5); 
            $.ajax({
                url: $('#resetpassform').attr("action"),
                type: $('#resetpassform').attr("method"),
                data: parametros,
                dataType: 'json'
            }).done(function(data){
                CerrarEstatus();
                ClaveCambiada(usuario);
            }).fail(function(data){
                ErrorOcurrido();
                CerrarEstatus();
            });
        }


    });

    $('#inputPassword').on('keypress',function(e){
        if (e.which == 32)
            return false;
    })

    $('#inputPassword2').on('keypress',function(e){
        if (e.which == 32)
            return false;
    })

    function ClaveCambiada(usuario){

        $('.login-siama').children().remove();
        $('.login-siama').append(`
            <h2 class="font-weight-normal text-center">Contraseña Restablecida</h2>
            <label style="margin-bottom:0px;font-size: 
                small;text-align:center;width: 100%;">Usuario: ${usuario}
            </label>
            <hr style="margin-top:0px;">
            <p style="text-align: center;">Su contraseña ha sido restablecida satisfactoriamente</p>
        `)
    }

    function ErrorOcurrido(){

        $('.login-siama').children().remove();
        $('.login-siama').append(`
            <h2 class="font-weight-normal text-center">Ha ocurrido un error</h2>
            <hr>
            <p>Ha ocurrido un error al ejecutar petici&oacute;n, por favor comunicarse con el departamento de sistema.</p>
        `)
    }
    
    function CorreoEnviado(correo){
        $('#resetform').children().remove();
        $('#resetform').append(`
            <h2 class="font-weight-normal text-center">Correo enviado</h2>
            <hr>
            <p>Se ha enviado un correo a <strong>${correo}</strong> en donde se especifica las instrucciones para restablecer su contraseña.</p>
        `)

    }

});