

var paginas = 0;
var RegistrosPorPagina = 10;
var PagesxNav = 5;
var PrimeraVezBusqueda = true;
var Guardando = false;

$(function(){

    //Variables globales para controlar el estado actual
    //del formulario en caso de que se cancele la edicion
    //o el agregado de una nueva lista

    var idActual = "";
    var codigoActual = "";
    var nombreActual = "";
    var descripcionActual = "";
    var tbodyActual = "";

    EstablecerBuscador();

    //Hacer click en alguna fila de la tabla de listas desplegables
    $('#TablaListasDesplegables tbody').on('click','tr',function(){
        
        //Se busca el indice de la fila que esta activa
        var indexAnt = $('.tr-activa-siama').index();
        //Se busca el indice de la fila que fue seleccionada
        var indexAct = $(this).index();
        //Se remueve la clase activa de la fila que esta activa
        $('.tr-activa-siama').removeClass('tr-activa-siama');

        //En caso de que los dos indices encontrado anteriormente
        //sean diferentes, de agrega la clase activa a la fila seleccionada
        //esto con la intension de que si se selecciona la misma fila
        //activa, la misma se desactive
        if(indexAnt != indexAct)
            $(this).addClass('tr-activa-siama');

    });
    
    //Hacer click en el boton de editar de cualquier fila de la tabla
    //de listas desplegables
    $('#TablaListasDesplegables').on('click','.editarOpcionLD',function(){

        //Se remueve la clase activa de la fila que esta activa
        $('.tr-activa-siama').removeClass('tr-activa-siama');

        //Se agrega la clase activa a la fila actual, esto para evitar
        //que se le quite la clase activa a una fila que esta activa
        //y se quiera editar (o sea, no se quiere quitar la seleccion)
        $(this).parent('tr').addClass('tr-activa-siama');

        //Se crea cuerpo html que va a tener la ventana modal de edicion
        var html = `
        <form class="form-horizontal" id="formEditarLD">
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Valor:</label>
                <div class="col-md-9">
                    <input type="text" required class="form-control editLD" id="ValorLD" 
                        value="${ $(this).parent('tr').find('td:eq(0)').text().trim() }" 
                        placeholder ="Ingresar Valor"
                        maxlength="50"
                    >
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Opci&oacute;n:</label>
                <div class="col-md-9">
                    <input type="text" required class="form-control editLD" id="OpcionLD" 
                        value="${ $(this).parent('tr').find('td:eq(1)').text().trim() }" maxlength="100"
                        placeholder ="Ingresar Opci&oacute;n"
                    >
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Descripci&oacute;n:</label>
                <div class="col-md-9">
                    <input type="text" class="form-control editLD"  
                        value="${ $(this).parent('tr').find('td:eq(2)').text().trim() }" 
                        placeholder ="Ingresar Descripci&oacute;n"
                    >
                </div>
            </div>
        </form>`;
        
        //Se crea los botones que va a tener la ventana modal de edicion
        Botones = `
        <button type="submit" id ="GuardarEdicion" title="Guardar Cambios" type="button" style="margin:5px;" class="btn  btn-success">
          <span class="fa fa-floppy-o"></span>
          Guardar
        </button>
        <button data-dismiss="modal" title="Cancelar Edici&oacute;n" type="button" style="margin:5px;" class="btn  btn-danger">
          <span class="fa fa-ban "></span>
          Cancelar
        </button>`;

        //${ data['inicio'] + data['observacion'] + data['fin']}

        //Se crea los parametros necesarios para llenar la ventana modal
        var parametros = {
            "Titulo":"Editar Lista Desplegable",
            "Cuerpo": html,
            "Columna": 0,
            "Fila":$(this).parent('tr').index(),
            "Botones":Botones
        }

        //Llamar funcion que establece ventana modal segun parametros
        ModalEditarFuncion(parametros);

        //Una vez establecida la ventana modal, se hace autofocus al primer
        //campo, que en este caso es el campo de valor
        setTimeout(function(){ $('.editLD')[0].focus(); }, 600);

    });
    
    //Hacer click en boton de guardar edicion perteneciente a la ventana modal
    $('.modal-footer').on('click','#GuardarEdicion',function(){

        var valido = true;

        $('.invalid-feedback').text('Campo Obligatorio');
        $('.contenedorAlertaModal').hide();

        if(!$('.editLD')[0].checkValidity()){
            $('#ValorLD').addClass('is-invalid');
            valido = false;
        }else if($('#ValorLD').hasClass('is-invalid')){
            $('#ValorLD').removeClass('is-invalid');
        }

        if(!$('.editLD')[1].checkValidity()){
            $('#OpcionLD').addClass('is-invalid');
            valido = false;
        }else if($('#OpcionLD').hasClass('is-invalid')){
            $('#OpcionLD').removeClass('is-invalid');
        }

        if(valido){
            
            ValorActual =$('.editLD')[0].value.trim();
            fila = $('#mhOptionR').text().trim();
            $("#TablaListasDesplegables").find('> tbody > tr').each(function () {

                if(fila != $(this).index() && ValorActual == $(this).find('td:eq(0)').text().trim()){
                    valido = false;
                    $('.editLD')[0].focus(); 
                    $('.invalid-feedback').text('');
                    $('#ValorLD').addClass('is-invalid');
                    $('#alertaModal').text('No puede haber mas de una opcion con el mismo campo "Valor"');
                    $('.contenedorAlertaModal').show();
                    return false;
                }
            });
        }

        if(valido){
            
            var row = parseInt($('#mhOptionR').text());
            var valor = $('.editLD')[0].value.trim();
            var opcion = $('.editLD')[1].value.trim();
            var descripcion = $('.editLD')[2].value.trim();
            $('#TablaListasDesplegables tbody tr').eq(row).find('td:eq(0)').text(valor);
            $('#TablaListasDesplegables tbody tr').eq(row).find('td:eq(1)').text(opcion);
            $('#TablaListasDesplegables tbody tr').eq(row).find('td:eq(2)').text(descripcion);
            CerrarFunciones();
        }

    });

    //Hacer click al boton de eliminar registro de la tabla de listas desplegables
    $('#eliminarOpcionLD').on('click',function(){
        //Buscar que fila esta activa para eliminar
        var index = $('.tr-activa-siama').index();

        //Si hay alguna fila activa, se elimina
        if(index >= 0)
            $('#TablaListasDesplegables tbody tr').eq(index).remove();

    });
    
    //Hacer click al boton de eliminar registro de la tabla de listas desplegables
    $('#agregarOpcionLD').on('click',function(){
        //Llamar a funcion que agrega fila en la tabla de listas desplegables
        AgregarOpcionLD();

    });

    //Agregar nueva lista desplegable
    $('.botoneraFormulario').on('click','#AgregarRegistro',function(){

        //se guarda esta actual del formulario, por si se cancela
        //el agregado
        GuardarEstadoActualFormulario();

        //Se limpia y se habilida el formulario para que el usuario pueda
        //ingresar data
        clearLDform();
        HabilitarFormulario()

        $('#CodigoLD').focus();
    })
    
    $('.botoneraFormulario').on('click','#BuscarRegistro',function(){
        
        SetSearchTitle('Busqueda Listas Desplegables');
        PrimeraVezBusqueda = true;
        DeshabilitarBotonera();
        SetUrlBusqueda($('#ControladorActual').text().trim()+"/busqueda");
        Busqueda(1);
        
        setTimeout(function(){
            HabilitarBotonera();
        }, 900);
        
    })

    //Editar lista desplegable que se esta viendo en pantalla
    $('.botoneraFormulario').on('click','#EditarRegistro',function(){
        GuardarEstadoActualFormulario();
        HabilitarFormulario()
        $('#CodigoLD').focus();
    })
    
    $('#SiamaModalAdvertencias').on('click','#ConfirmarEliminacion',function(){
        var parametros = {
            "id": $('#IdForm').text().trim(),
            "Url": $('#ControladorActual').text().trim()+"/eliminar"
        }

        console.log(parametros)
        Eliminar(parametros)
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
            "Cuerpo": "<h4>¿Est&aacute; usted seguro de querer eliminar la Lista Desplegable?</h4>",
            "Botones":Botones
        }

        ModalAdvertencia(parametros);
    })
    
    $('.botoneraFormulario').on('click','#GuardarRegistro',function(){
        var Opciones = [];
        var valido = true;
        
        $('#alertaFormularioLD').hide();
        if($('#CodigoLD').val().trim()==""){
            valido = false;
            $('#CodigoLD').addClass('is-invalid');
        }else if($('#CodigoLD').hasClass('is-invalid')){
            $('#CodigoLD').removeClass('is-invalid');
        }

        if($('#NombreLD').val().trim()==""){
            valido = false;
            $('#NombreLD').addClass('is-invalid');
        }else if($('#NombreLD').hasClass('is-invalid')){
            $('#NombreLD').removeClass('is-invalid');
        }
        
        if(valido){
            $("#TablaListasDesplegables").find('> tbody > tr').each(function () {
    
                if($(this).find('td:eq(0)').text().trim() != ''){
                    Opciones.push({ 
                        "Valor"         : $(this).find('td:eq(0)').text(),
                        "Opcion"        : $(this).find('td:eq(1)').text(),
                        "Descripcion"   : $(this).find('td:eq(2)').text() 
                    });
                }else{
                    $(this).remove();
                }
            });

            if(Opciones.length == 0){
                $('#alertaFormularioLD').text("La lista desplegable debe contener Opciones");
                $('#alertaFormularioLD').show();
                valido = false;
            }
        }

        if(valido){
            var parametros = {
                "idActual":$('#IdForm').text().trim(),
                "Codigo": $('#CodigoLD').val(),
                "Nombre": $('#NombreLD').val(),
                "Descripcion": $('#DescripcionLD').val(),
                "Opciones": Opciones,
                "Url":$('#FormularioActual').attr("action")
            }

            GuardarLD(parametros);
        }
        
    });

    $('.botoneraFormulario').on('click','#CancelarRegistro',function(){
        clearLDform();
        RestablecerEstadoAnteriorFormulario();
        DeshabilitarFormulario()
    });

    function GuardarLD(parametros){

        if( idActual == $('#IdForm').text().trim() &&
            codigoActual == $('#CodigoLD').val().trim() &&
            nombreActual == $('#NombreLD').val().trim() &&
            descripcionActual == $('#DescripcionLD').val().trim() &&
            tbodyActual == $('#TablaListasDesplegables tbody').html().trim()){

            DeshabilitarFormulario();
            MostrarEstatus(2); 
            setTimeout(function(){
                CerrarEstatus();
            }, 6000);
                
        }else{
            if(!Guardando){
                Guardando = true;
                GuardarFormulario(parametros);
            }
        }

    }

    function GuardarEstadoActualFormulario(){
        idActual =$('#IdForm').text().trim();
        codigoActual = $('#CodigoLD').val().trim();
        nombreActual = $('#NombreLD').val().trim();
        descripcionActual = $('#DescripcionLD').val().trim();
        tbodyActual = $('#TablaListasDesplegables tbody').html().trim();
    }

    function LlenarFormulario(data){
        $('#IdForm').text(data['id']); 
        $('#CodigoLD').val(data['Codigo']);
        $('#NombreLD').val(data['Nombre']);
        $('#DescripcionLD').val(data['Descripcion']);
        $('#TablaListasDesplegables tbody').children().remove();
        $('#TablaListasDesplegables tbody').append(data['Cuerpo']);
    }

    function RestablecerEstadoAnteriorFormulario(){
        var parametros = {
            "id": idActual.trim(),
            "Codigo": codigoActual.trim(),
            "Nombre": nombreActual.trim(),
            "Descripcion": descripcionActual.trim(),
            "Cuerpo": tbodyActual
        }
        LlenarFormulario(parametros);
    }
   
    function clearLDform(){
        
        $('#IdForm').text(''); 
        $('#alertaFormularioActual').hide();
        $('#CodigoLD').removeClass('is-invalid');
        $('#NombreLD').removeClass('is-invalid');
        $('#CodigoLD').val('');  
        $('#NombreLD').val(''); 
        $('#DescripcionLD').val(''); 
        $('#TablaListasDesplegables > tbody').children().remove();

    }

    function AgregarOpcionLD(){
        //Agregar registro a la tabla de listas desplegables al final
        $('#TablaListasDesplegables > tbody:last-child').append(`
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="2" class ="editarOpcionLD" style="text-align: center;cursor: pointer;">
                    <span class="fa fa-pencil fa-lg"></span>
                </td>
            </tr>
        `);
    }

    function EstablecerBuscador(){
        html = `
            <tr>
                <th style="width:20%;">Codigo</th>
                <th style="width:40%;">Nombre</th>
                <th style="width:40%;">
                    Descripcion
                </th>
            </tr>
        `;
        SetSearchThead(html);
    }

    //Interfaces
    
    window.InterfazElegirBuscador = function(fila){
        Opciones = JSON.parse(fila.find('td:eq(1)').text().trim());
        Cuerpo = ""
        for (var i = 0; i < Opciones.length; i++){
            Cuerpo = Cuerpo + "<tr>"

            var obj = Opciones[i];
            for (var key in obj){
                Cuerpo = Cuerpo + `<td>${obj[key]}</td>`;
            }
            Cuerpo = Cuerpo + `
                <td colspan="2" class ="editarOpcionLD" style="text-align: center;cursor: pointer;">
                    <span class="fa fa-pencil fa-lg"></span>
                </td>
            </tr>`;
        }

        var parametros = {
            "id": fila.find('td:eq(0)').text().trim(),
            "Codigo": fila.find('td:eq(2)').text().trim(),
            "Nombre": fila.find('td:eq(3)').text().trim(),
            "Descripcion": fila.find('td:eq(4)').text().trim(),
            "Cuerpo": Cuerpo
        }

        LlenarFormulario(parametros);
        $('#SiamaModalBusqueda').modal('hide');

    }

    window.AccionEliminarFormulario = function(data){

        if(data['Datos']['ld_id'] == "")
        AgregarBotoneraPrimariaNULL();

        var parametros = {
            "id": data['Datos']['ld_id'].trim(),
            "Codigo": data['Datos']['codigo'].trim(),
            "Nombre": data['Datos']['nombre'].trim(),
            "Descripcion": data['Datos']['descripcion'].trim(),
            "Cuerpo": data['Datos']['opciones']
        }

        LlenarFormulario(parametros);
    }
});