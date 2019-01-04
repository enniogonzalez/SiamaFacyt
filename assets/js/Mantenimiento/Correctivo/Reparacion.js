
const ProveedorR = "ProveedorR";
const FallaR = "FallaR";
const ObreroR = "ObrerosR";
const PiezaDR = "PiezaDR";

$(function(){


    /************************************/
    /*      Inicio Buscadores           */
    /************************************/

    /************************************/
    /*      Manejo Proveedores          */
    /************************************/
    
    $('#SiamaModalFunciones').on('click','#nomProR',function(){
        BuscarProveedor(ProveedorR);
    });

    $('#SiamaModalFunciones').on('click','.BuscarProveedorR',function(){
        BuscarProveedor(ProveedorR);
    });

    $('#SiamaModalFunciones').on('click','.BorrarProveedorR',function(){
        $('#idProR').text('');
        $('#nomProR').val('');
    });

    /************************************/
    /*      Manejo Fallas             */
    /************************************/
    
    $('#SiamaModalFunciones').on('click','#nomFallaReparacion',function(){
        BuscarFalla(FallaR);
    });

    $('#SiamaModalFunciones').on('click','.BuscarFallaReparacion',function(){
        BuscarFalla(FallaR);
    });

    $('#SiamaModalFunciones').on('click','.BorrarFallaReparacion',function(){
        $('#idFallaReparacion').text('');
        $('#nomFallaReparacion').val('');
    });

    /************************************/
    /*      Manejo Obreros             */
    /************************************/

    $('#SiamaModalFunciones').on('click','#nomObrReparacion',function(){
        BuscarObrero(ObreroR);
    });

    $('#SiamaModalFunciones').on('click','.BuscarObreroR',function(){
        BuscarObrero(ObreroR);
    });

    $('#SiamaModalFunciones').on('click','.BorrarObreroR',function(){
        $('#idObrReparacion').text('');
        $('#nomObrReparacion').val('');
    });

    /************************************/
    /*      Manejo Pieza                */
    /************************************/
    
    $('#SiamaModalFunciones').on('click','#nomPiezaDR',function(){
        BuscarPieza(PiezaDR);
    });

    $('#SiamaModalFunciones').on('click','.BuscarPiezaDR',function(){
        BuscarPieza(PiezaDR);
    });

    $('#SiamaModalFunciones').on('click','.BorrarPiezaDR',function(){
        $('#idPiezaDR').text('');
        $('#nomPiezaDR').val('');
    });

    /************************************/
    /*          Fin Buscadores          */
    /************************************/

    $('#agregarReparacion').on('click',function(){

        var Botones = `
            <button data-dismiss="modal" title="Cerrar" type="button" style="margin:5px;" class="btn btn-primary-siama">
            <span class="fa fa-times-circle"></span>
            Cerrar
            </button>`;
        var alerta = false;
        
        if($('#OrigenCorrectivo').val().trim() == ""){
            alerta = true;
            Cuerpo = "No se puede agregar un <strong>Reparaci&oacute;n Correctiva</strong> debido a que no se ha seleccionado un <strong>Origen</strong>.";
        }else if($('#OrigenCorrectivo').val().trim() == "Mantenimiento Correctivo Planificado" && $('#idManCorPla').text().trim() == ""){
            alerta = true;
            Cuerpo = "No se puede agregar un <strong>Reparaci&oacute;n Correctiva</strong> debido a que no se ha seleccionado un <strong>Mantenimiento Correctivo Planificado</strong>.";
        }else if($('#OrigenCorrectivo').val().trim() == "Bien" && $('#idBieCorrectivo').text().trim() == ""){
            alerta = true;
            Cuerpo = "No se puede agregar un <strong>Reparaci&oacute;n Correctiva</strong> debido a que no se ha seleccionado un <strong>Bien</strong>.";
        }else
            AgregarReparacionCorrectiva();

        if(alerta){
            var parametros = {
                "Titulo":"Advetencia",
                "Cuerpo": Cuerpo,
                "Botones":Botones
            }

            ModalAdvertencia(parametros);
        }
    });

    $('#eliminarReparacion').on('click',function(){
        $('#TablaReparacionesCorrectivas .tr-activa-siama').remove();
    });

    $('#TablaReparacionesCorrectivas').on('click','.editarReparacion',function(){

        //Se remueve la clase activa de la fila que esta activa
        $('.tr-activa-siama').removeClass('tr-activa-siama');

        var fila = $(this).parent('tr');
        //Se agrega la clase activa a la fila actual, esto para evitar
        //que se le quite la clase activa a una fila que esta activa
        //y se quiera editar (o sea, no se quiere quitar la seleccion)
        fila.addClass('tr-activa-siama');

        //Se crea los botones que va a tener la ventana modal de edicion
        Botones = `
        <button type="submit" id ="GuardarEdicionReparacion" title="Guardar Reparacion" type="button" style="margin:5px;" class="btn  btn-success">
          <span class="fa fa-floppy-o"></span>
          Guardar
        </button>
        <button data-dismiss="modal" id="CancelarEdicionReparacion" title="Cancelar Edici&oacute;n" type="button" style="margin:5px;" class="btn  btn-danger">
          <span class="fa fa-ban "></span>
          Cancelar
        </button>`;

        var data = {
            "Titulo"            :   "Editar Reparaci&oacute;n Correctiva",
            "EstatusDoc"        :   $('#EstatusCorrectivo').val(),
            "Fila"              :   $(this).parent('tr').index(),
            "Botones"           :   Botones,
            "idPiezaDR"         :   fila.find('td:eq(1)').text().trim(),
            "nomPiezaDR"        :   fila.find('td:eq(2)').text().trim(),
            "idObrReparacion"   :   fila.find('td:eq(3)').text().trim(),
            "nomObrReparacion"  :   fila.find('td:eq(4)').text().trim(),
            "idProR"            :   fila.find('td:eq(5)').text().trim(),
            "nomProR"           :   fila.find('td:eq(6)').text().trim(),
            "InicioReparacion"  :   fila.find('td:eq(7)').text().trim(),
            "FinReparacion"     :   fila.find('td:eq(8)').text().trim(),
            "ObservacionR"      :   fila.find('td:eq(9)').text().trim(),
            "idFallaReparacion" :   fila.find('td:eq(11)').text().trim(),
            "nomFallaReparacion" :   fila.find('td:eq(12)').text().trim(),
        }
        SetModalFuncionesReparaciones(data);

        if($('#EstatusCorrectivo').val() != "Solicitado"){
            setTimeout(function(){$('#ObservacionR').focus();}, 400);
        }
        
    });

    $('#TablaReparacionesCorrectivas').on('click','.realizarReparacion',function(){
        $('.tr-activa-siama').removeClass('tr-activa-siama');
        var fila = $(this).parent('tr');
        fila.addClass('tr-activa-siama');

        if($(this).find('span').hasClass('fa-square-o')){
            $(this).find('span').removeClass('fa-square-o');
            $(this).find('span').addClass('fa-check-square-o');
            $(this).parent('tr').find('td:eq(10)').text('Realizado');
        }else{
            $(this).find('span').removeClass('fa-check-square-o');
            $(this).find('span').addClass('fa-square-o');
            $(this).parent('tr').find('td:eq(10)').text('');
        }
    });

    $('#TablaReparacionesCorrectivas tbody').on('click','tr',function(){
        ActivarCeldaTabla(this)
    });

    $('#SiamaModalFunciones').on('click','#CancelarEdicionReparacion',function(){
        ClearModalFunction();
    });

    $('#SiamaModalFunciones').on('click','#GuardarEdicionReparacion',function(){
        var Valido = true;

        $('#formEditarReparacion .Reparacion').each(function(){
            $(this).removeClass('is-invalid');
            if($(this).hasClass('obligatorio') && $(this).val().trim() == ""){

                if(Valido)
                    $(this).focus();
                
                Valido = false;
                $(this).addClass('is-invalid');
            }
        })

        //Validar que exista un Obrero o un proveedor asignado
        if(Valido && $('#nomObrReparacion').val() == "" && $('#nomProR').val() == ""){
            Valido = false;
            $('#alertaModal').focus(); 
            $('#alertaModal').text('La Reparacion Correctiva debe contar con un Obrero o un Proveedor para realizar la misma.');
            $('.contenedorAlertaModal').show();
            
            document.getElementsByClassName("contenedorAlertaModal")[0].scrollIntoView();
        }

        //Validar que no se le haga un mantenimiento correctivo a una pieza
        //dañada dos veces en el mismo mantenimiento
        if(Valido){
            
            ValorActual =$('#idPiezaDR').text().trim();
            fila = $('#mhOptionR').text().trim();
            $("#TablaReparacionesCorrectivas").find('> tbody > tr').each(function () {

                if(fila != $(this).index() && ValorActual == $(this).find('td:eq(1)').text().trim()){
                    Valido = false;
                    $('#nomPiezaDR').focus(); 
                    $('#alertaModal').text('No puede haber mas de una Reparacion Correctiva con la misma Pieza Dañada');
                    $('.contenedorAlertaModal').show();
                    return false;
                }
            });

            if(Valido && ExistePiezaCambio(ValorActual)){
                Valido = false;
                $('#nomPiezaDR').focus(); 
                $('#alertaModal').text('La pieza dañada ya fue seleccionada para realizarle un Cambio Correctivo');
                $('.contenedorAlertaModal').show();
            }
        }

        if(Valido){

            var row = parseInt($('#mhOptionR').text());
            var fila = $('#TablaReparacionesCorrectivas tbody tr').eq(row);
            fila.find('td:eq(1)').text($('#idPiezaDR').text().trim());
            fila.find('td:eq(2)').text($('#nomPiezaDR').val().trim());
            fila.find('td:eq(3)').text($('#idObrReparacion').text().trim());
            fila.find('td:eq(4)').text($('#nomObrReparacion').val().trim());
            fila.find('td:eq(5)').text($('#idProR').text().trim());
            fila.find('td:eq(6)').text($('#nomProR').val().trim());
            fila.find('td:eq(7)').text($('#InicioReparacion').val().trim());
            fila.find('td:eq(8)').text($('#FinReparacion').val().trim());
            fila.find('td:eq(9)').text($('#ObservacionR').val().trim());
            fila.find('td:eq(11)').text($('#idFallaReparacion').text().trim());
            fila.find('td:eq(12)').text($('#nomFallaReparacion').val().trim());
    
            CerrarFunciones();
        }
    });

    $('#SiamaModalFunciones').on('change','#InicioReparacion',function(){
        if($(this).val() != "" && 
        $('#FinReparacion').val() != "" 
        && $(this).val() > $('#FinReparacion').val()){
            $('#FinReparacion').val($(this).val());
        }

    });

    $('#SiamaModalFunciones').on('change','#FinReparacion',function(){
        if($(this).val() != "" && 
        $('#InicioReparacion').val() != "" 
        && $(this).val() < $('#InicioReparacion').val()){
            $('#InicioReparacion').val($(this).val());
        }

    })

    function SetModalFuncionesReparaciones(data){

        var pointer= "";
        var atributos= "";

        if(data['EstatusDoc'] != "Solicitado"){
            pointer= "pointer-events: none;";
            atributos= " readonly disabled ";
        }
        //Se crea cuerpo html que va a tener la ventana modal de edicion
        var html = `
        <form class="form-horizontal" id="formEditarReparacion">

            <div style="${pointer}">
            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Pieza Dañada:</label>
                <div class="col-lg-9">
                    <div style="width:80%;float:left;">
                        <div style="display:none;" id="idPiezaDR">${data['idPiezaDR']}</div>
                        <input type="text" title="Pieza Dañada" ${atributos} readonly
                            class="form-control texto obligatorio buscador Reparacion" id="nomPiezaDR" value="${data['nomPiezaDR']}">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:20%;float:right;padding:10px;">
                        <span title="Buscar Pieza Dañada" class="fa fa-search BuscarPiezaDR" 
                            style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Pieza Dañada" class="fa fa-trash-o BorrarPiezaDR" 
                            style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Obrero:</label>
                <div class="col-lg-9">
                    <div style="width:80%;float:left;">
                        <div style="display:none;" id="idObrReparacion">${data['idObrReparacion']}</div>
                        <input type="text" title="Obrero que realiza reparacion" ${atributos} readonly
                            class="form-control texto  buscador Reparacion" id="nomObrReparacion" 
                            value="${data['nomObrReparacion']}">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:20%;float:right;padding:10px;">
                        <span title="Buscar Obrero" class="fa fa-search BuscarObreroR" 
                            style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Obrero" class="fa fa-trash-o BorrarObreroR" 
                            style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Proveedor:</label>
                <div class="col-lg-9">
                    <div style="width:80%;float:left;">
                        <div style="display:none;" id="idProR">${data['idProR']}</div>
                        <input type="text" title="Proveedor que realiza reparacion" ${atributos} readonly
                            class="form-control texto  buscador Reparacion" id="nomProR" value="${data['nomProR']}">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:20%;float:right;padding:10px;">
                        <span title="Buscar Proveedor" class="fa fa-search BuscarProveedorR" 
                            style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Proveedor" class="fa fa-trash-o BorrarProveedorR" 
                            style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>
            
            <div class="form-group row">
                <label for="InicioReparacion" class="col-lg-3 col-form-label">Inicio:</label>
                <div class="col-lg-9">
                    <input type="date" 
                    class="form-control obligatorio fecha Reparacion" ${atributos} id="InicioReparacion" value="${data['InicioReparacion']}">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="FinReparacion" class="col-lg-3 col-form-label">Fin:</label>
                <div class="col-lg-9">
                    <input type="date" 
                    class="form-control obligatorio fecha Reparacion" ${atributos} id="FinReparacion" value="${data['FinReparacion']}">
                    <div class="invalid-feedback">Campo Obligatorio</div>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Falla:</label>
                <div class="col-lg-9">
                    <div style="width:80%;float:left;">
                        <div style="display:none;" id="idFallaReparacion">${data['idFallaReparacion']}</div>
                        <input type="text" title="Falla" ${atributos} readonly
                            class="form-control texto obligatorio buscador Cambio" id="nomFallaReparacion" value="${data['nomFallaReparacion']}">
                        <div class="invalid-feedback">Campo Obligatorio</div>
                    </div>
                    <div style="width:20%;float:right;padding:10px;">
                        <span title="Buscar Falla" class="fa fa-search BuscarFallaReparacion" 
                            style="cursor: pointer;float:left;"></span>
                        <span title="Borrar Falla" class="fa fa-trash-o BorrarFallaReparacion" 
                            style="cursor: pointer;float:right;"></span>
                    </div>
                </div>
            </div>
            </div>

            <div class="form-group row">
                <label for="ObservacionR" class="col-lg-3 col-form-label">Observaci&oacute;n:</label>
                <div class="col-lg-9">
                    <textarea  class="form-control texto" rows="3"
                    style = "resize:vertical;" id="ObservacionR">${data['ObservacionR']}</textarea>
                </div>
            </div>
        </form>`;
        

        //Se crea los parametros necesarios para llenar la ventana modal
        var parametros = {
            "Titulo":data['Titulo'],
            "Cuerpo": html,
            "Columna": 0,
            "Fila":data['Fila'],
            "Botones":data['Botones']
        }

        //Llamar funcion que establece ventana modal segun parametros
        ModalEditarFuncion(parametros);
    }

    window.AgregarReparacionCorrectiva = function(){
        //Agregar registro a la tabla de listas desplegables al final
        $('#TablaReparacionesCorrectivas > tbody:last-child').append(`
            <tr>
                <td style="display:none;"></td>
                <td style="display:none;"></td>
                <td></td>
                <td style="display:none;"></td>
                <td></td>
                <td style="display:none;"></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="display:none;"></td>
                <td style="display:none;"></td>
                <td style="display:none;"></td>
                <td style="display:none;"></td>
                <td colspan="2" class ="editarReparacion" style="text-align: center;cursor: pointer;">
                    <span class="fa fa-pencil fa-lg"></span>
                </td>
            </tr>
        `);
    }

    window.ExistePiezaReparacion = function(pieza){
        Existe = false;
        $("#TablaReparacionesCorrectivas").find('> tbody > tr').each(function () {

            if(pieza == $(this).find('td:eq(1)').text().trim()){
                Existe = true;
                return false;
            }
        });

        return Existe;
    }

    window.ExisteReparacion = function(){
        var Existe = false;

        $("#TablaReparacionesCorrectivas").find('> tbody > tr').each(function () {
            if($(this).find('td:eq(1)').text().trim() != ''){
                Existe = true;

                //Salir del ciclo
                return false;
            }
        });

        return Existe;
    }
    
    window.ObtenerJsonReparaciones = function(){
        var Reparaciones = [];
        var estatuDoc = $('#EstatusCorrectivo').val().trim();

        $("#TablaReparacionesCorrectivas").find('> tbody > tr').each(function () {
            if( $(this).find('td:eq(1)').text().trim() != '' && 
                (   estatuDoc == "Solicitado" || 
                    (   estatuDoc != "Solicitado" && 
                        $(this).find('td:eq(10)').text() == "Realizado"
                    )
                )
            ){
                Reparaciones.push({ 
                    "Id"                : $(this).find('td:eq(0)').text(),
                    "IdPiezaD"          : $(this).find('td:eq(1)').text(),
                    "IdObr"             : $(this).find('td:eq(3)').text(),
                    "IdPro"             : $(this).find('td:eq(5)').text(),
                    "Inicio"            : $(this).find('td:eq(7)').text(),
                    "Fin"               : $(this).find('td:eq(8)').text(),
                    "Observacion"       : $(this).find('td:eq(9)').text(), 
                    "Estatus"           : $(this).find('td:eq(10)').text(), 
                    "FallaReparacion"   : $(this).find('td:eq(11)').text(), 
                });
            }
        });

        return Reparaciones;
    }
});