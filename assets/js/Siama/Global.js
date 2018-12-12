function validateEmail(email) {
    var re = /(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/;
    return re.test(email);
  }

$(function(){
    $(window).scrollTop(0);
    
    // var hash = CryptoJS.MD5("MessAge");
    // console.log(hash.toString())

    setInterval(function(){
        BuscarCantidadAlerta();
    },60000)

    $('.buscador').on('keypress',function(){
        return false;
    })
    
    $('#SeccionImprimir').on('click',"#Imprimir",function(){
        if($('#IdForm').text().trim() != "")
            window.open($('#ControladorActual').text().trim() + "/imprimir/" + $('#IdForm').text().trim(), '_blank');
    })

    //Funcion a sobre escribir
    window.AccionGuardar = function(data){}

    window.ActivarCeldaTabla = function(fila){

        //Se busca el indice de la fila que esta activa
        var indexAnt = $('.tr-activa-siama').index();
        //Se busca el indice de la fila que fue seleccionada
        var indexAct = $(fila).index();
        //Se remueve la clase activa de la fila que esta activa
        $('.tr-activa-siama').removeClass('tr-activa-siama');

        //En caso de que los dos indices encontrado anteriormente
        //sean diferentes, de agrega la clase activa a la fila seleccionada
        //esto con la intension de que si se selecciona la misma fila
        //activa, la misma se desactive
        if(indexAnt != indexAct)
            $(fila).addClass('tr-activa-siama');

    }

    window.AgregarBotoneraPrimaria = function(){
        
        $('#SeccionImprimir').children().remove();
        $('.botoneraFormulario').children().remove();

        $('#SeccionImprimir').append(`
            <button type="button"  class="btn btn-primary-siama" id="Imprimir">
                <span class="fa fa-print fa-lg"></span>
                Imprimir
            </button>
        `);

        $('.botoneraFormulario').append(`
            <button  title="Buscar" type="button"  class="btn btn-primary-siama" id="BuscarRegistro">
                <span class="fa fa-search"></span>
                Buscar
            </button>

            <button title="Editar" type="button"  class="btn btn-primary-siama" id="EditarRegistro">
                <span class="fa fa-pencil-square-o"></span>
                Editar
            </button>

            <button title="Agregar" type="button"  class="btn btn-primary-siama" id="AgregarRegistro">
                <span class="fa fa-plus"></span>
                Agregar
            </button>

            <button title="Eliminar" type="button" class="btn  btn-danger" id="EliminarRegistro">
                <span class="fa fa-trash"></span>
                Eliminar
            </button>
        `);
    }

    window.AgregarBotoneraPrimariaNULL = function(){
        $('#SeccionImprimir').children().remove();
        $('.botoneraFormulario').children().remove();
        $('.botoneraFormulario').append(`
            <button title="Agregar" type="button"  class="btn btn-primary-siama" id="AgregarRegistro">
                <span class="fa fa-plus"></span>
                Agregar
            </button>
        `);
    }

    window.AgregarBotoneraSecundaria = function(){


        $('#SeccionImprimir').children().remove();
        $('.botoneraFormulario').children().remove();


        $('.botoneraFormulario').append(`
            <button title="Guardar" type="button" class="btn  btn-success" id="GuardarRegistro">
                <span class="fa fa-floppy-o"></span>
                Guardar
            </button>
            <button  title="Cancelar" type="button" class="btn  btn-danger" id="CancelarRegistro">
                <span class="fa fa-ban "></span>
                Cancelar
            </button>
        `);

    }

    window.BuscarCantidadAlerta = function(){
      
        $.ajax({
            url: $('#UrlBase').text().trim() + "/alertas/CantidadAlertas",
            type: "POST",
            dataType: 'json'
        }).done(function(data){
            if(data['isValid']){
                if(data['Cantidad'] > 0){
                    $('#CantidadAlertas').text("(" + data['Cantidad'] + ")");
                    $('.AlertasActuales').show();
                }else{
                    $('.AlertasActuales').hide();
                }
            }
        })
    }

    window.DeshabilitarBotonera = function(){
        $('.botoneraFormulario').addClass('botoneraDeshabilitada');
        $('.botoneraFormulario').find('button').each(function(){
            $(this).attr("disabled","disabled")
        })
    }

    window.DeshabilitarFormulario = function(cambiarBotonera = true){

        $('.tr-activa-siama').removeClass('tr-activa-siama');
        $('#FormularioActual').addClass('formulario-desactivado');

        $('.formulario-siama form .form-control').each(function(){
            $(this).attr("disabled", "disabled");
            $(this).attr("readonly", "readonly");
        })
        
        $('.formulario-siama table').each(function(){
            $(this).addClass('tabla-siama-desactivada')
        })
        
        if(cambiarBotonera){
            if($('#IdForm').text().trim()=="")
                AgregarBotoneraPrimariaNULL();
            else
                AgregarBotoneraPrimaria();
        }
    }

    window.Eliminar = function(parametros){

        MostrarEstatus(3); 

        $.ajax({
            url: parametros['Url'],
            type: "POST",
            data: parametros,
            dataType: 'json'
        }).done(function(data){
            if(data['isValid']){

                MostrarEstatus(4,true); 
                setTimeout(function(){
                    CerrarEstatus();
                }, 6000);
                AccionEliminarFormulario(data);
            }else{

                CerrarEstatus();
                Botones = `
                <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn  btn-danger">
                  <span class="fa fa-times "></span>
                  Cerrar
                </button>`;

                var parametros = {
                    "Titulo":"Advertencia",
                    "Cuerpo": data['Mensaje'],
                    "Botones":Botones
                }

                ModalAdvertencia(parametros,true);
            }
        }).fail(function(data){
            failAjaxRequest(data);
        });
    }

    window.GuardarFormulario = function(parametros){

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
                
                $('#IdForm').text(data['id']);
                AgregarBotoneraPrimaria()
                AccionGuardar(data);
                MostrarEstatus(2,true); 
                setTimeout(function(){
                    CerrarEstatus();
                }, 6000);
                
                BuscarCantidadAlerta();
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

    window.HabilitarBotonera = function(){
        $('.botoneraFormulario').removeClass('botoneraDeshabilitada');
        $('.botoneraFormulario').find('button').each(function(){
            $(this).removeAttr("disabled")
        })
    }

    window.HabilitarFormulario = function(cambiarBotonera = true){

        $('#FormularioActual').removeClass('formulario-desactivado');

        $('.formulario-siama form .form-control').each(function(){
            if(!$(this).hasClass('estatus')  &&  !$(this).hasClass('documento')){
                $(this).removeAttr("disabled"); 
                $(this).removeAttr("readonly");
            }
            
            if($(this).hasClass('buscador')){
                $(this).attr("readonly", "readonly");
            }
        })

        $('.formulario-siama table').each(function(){
            $(this).removeClass('tabla-siama-desactivada')
        })

        if(cambiarBotonera)
            AgregarBotoneraSecundaria();
    }

    window.SetAlertaFormulario = function (Mensaje){
        $('#alertaFormularioActual').children().remove();
        $('#alertaFormularioActual').text('');
        $('#alertaFormularioActual').append(Mensaje);
        $('#alertaFormularioActual').show();
        document.getElementsByClassName("informationPage")[0].scrollIntoView();
    }






    
});