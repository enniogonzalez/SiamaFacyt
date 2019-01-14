$(function(){

    $('#loginform').submit((e)=>{
        e.preventDefault();

        if(!$('#inputUsuario')[0].checkValidity()){
            $('#alertaLogin').hide()
            $('#inputUsuario').addClass('is-invalid');
            $('#inputUsuario').focus();
        }else
            $('#inputUsuario').removeClass('is-invalid');

        if(!$('#inputPassword')[0].checkValidity()){
            $('#alertaLogin').hide()
            $('#inputPassword').addClass('is-invalid');

            if($('#inputUsuario')[0].checkValidity())
                $('#inputPassword').focus();
        }else
            $('#inputPassword').removeClass('is-invalid');

            
        if($('#inputUsuario')[0].checkValidity() && $('#inputPassword')[0].checkValidity()){

            parametros = {
                "inputPassword" : CryptoJS.MD5($('#inputPassword').val()).toString(),
                "inputUsuario"  : $('#inputUsuario').val()
            }

            
            $.ajax({
                url: $('#loginform').attr("action"),
                type: $('#loginform').attr("method"),
                data: parametros,
                dataType: 'json'
            }).done(function(data){
                if(data['isValid']){
                    window.location.href = data['url'];
                }else{
                    $('#alertaLogin').show();
                    $('#inputUsuario').val("");
                    $('#inputPassword').val("");
                }
            }).fail(function(data){
                console.log(data);
            });
        }
    });

});