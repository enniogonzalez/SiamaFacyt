<?php

    class Listasdesplegables extends CI_Controller{

        
        public function __construct(){
            parent::__construct();
            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
        }

        private function FormatearRequestLD($respuesta){

            $data = array(
                "ld_id"     =>"",
                "codigo"    =>"",
                "nombre"    =>"",
                "descripcion" => "",
                "opciones" => ""
            );

            if($respuesta){
                $data = $respuesta;

                $Opciones = json_decode($data['opciones'],true);
                $htmlOpciones = "";

                foreach ($Opciones as $elemento) {
                    $htmlOpciones = $htmlOpciones
                        ."<tr>"
                        .   "<td>" . $elemento['Valor'] . "</td>"
                        .   "<td>" . $elemento['Opcion'] . "</td>"
                        .   "<td>" . $elemento['Descripcion'] . "</td>"
                        .   "<td colspan='2' class ='editarOpcionLD' style='text-align: center;cursor: pointer;'>"
                        .   "    <span class='fa fa-pencil fa-lg'></span>"
                        .   "</td>"
                        ."</tr>";
                }
                
                $data['opciones'] = $htmlOpciones;
            }

            return $data;
        }

        private function ValidarPermiso(){
            if(!$this->session->userdata("Permisos")['Sistema']){
                show_404();
            }
        }

        public function view(){
            
            if(!$this->session->userdata("nombre")){
                redirect(site_url(''));
            }
            $this->ValidarPermiso();

            $respuesta = $this->listasdesplegables_model->Obtener();

            $data = $this->FormatearRequestLD($respuesta);
            
            $ld = $this->listasdesplegables_model->Obtener('','COB-LISDES');
            $dataLD['OrdenarBusqueda'] = $this->liblistasdesplegables->FormatearListaDesplegable($ld);

            $JsFile = "<script src=\"". base_url() . "assets/js/Sistema/ListasDesplegables.js\"></script>";
            
            $datafile['JsFile'] = $JsFile ;

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/Sistema/listasdesplegables',$data);
            $this->load->view('plantillas/7-footer');
            
        }

        public function guardar(){
            
            if(!$this->session->userdata("nombre")){
                redirect(site_url(''));
            }

            $this->ValidarPermiso();

            //Si no se llama a la funcion desde un post
            //Se redirreciona
            if($this->input->post("Codigo") == ""){
                redirect(site_url(''));
            }
            
            $parametros = array(
                "ld_id" => $this->input->post("idActual"),
                "Codigo" => $this->input->post("Codigo"),
                "Nombre" => $this->input->post("Nombre"),
                "Descripcion" => $this->input->post("Descripcion"),
                "Opciones" => json_encode($this->input->post("Opciones"))
            );
            
            if($parametros['ld_id'] == ""){
                if($this->listasdesplegables_model->ExisteCodigo($parametros['Codigo'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe una lista desplegable registrada con el mismo codigo.",
                        "id"=>""));
                }else{
                    $respuesta = $this->listasdesplegables_model->Insertar($parametros);
                    $id = $this->listasdesplegables_model->ObtenerId($parametros("Codigo"));
    
                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha insertado Lista Desplegable exitosamente",
                        "id"=>$id['ld_id']));
                }
            }else{
                if($this->listasdesplegables_model->ExisteCodigo($parametros['Codigo'],$parametros['ld_id'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe una lista desplegable registrada con el mismo codigo.",
                        "id"=>$parametros['ld_id']));
                }else{
                    $respuesta = $this->listasdesplegables_model->Actualizar($parametros);
                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha insertado Lista Desplegable exitosamente",
                        "id"=>$parametros['ld_id']));
                }
            }

        }

        public function eliminar(){
            
            if(!$this->session->userdata("nombre")){
                redirect(site_url(''));
            }

            $this->ValidarPermiso();

            //Si no se llama a la funcion desde un post
            //Se redirreciona
            if($this->input->post("id") == ""){
                redirect(site_url(''));
            }

            $eliminar = $this->listasdesplegables_model->Eliminar($this->input->post("id"));
            $respuesta = $this->listasdesplegables_model->Obtener();
            $data = $this->FormatearRequestLD($respuesta);

            if($eliminar)
                echo json_encode(array("isValid"=>true,"Datos"=>$data));
            else
                echo json_encode(array("isValid"=>false,"Mensaje"=>"Ha ocurrido un error al intentar eliminar la lista desplegable"));

        }

        public function busqueda(){

            if(!$this->session->userdata("nombre")){
                redirect(site_url(''));
            }

            if($this->input->post("Pagina") == ""){
                redirect(site_url(''));
            }

            $busqueda = $this->input->post("Busqueda") ;
            $pagina = (int) $this->input->post("Pagina") ;
            $regXpag = (int) $this->input->post("RegistrosPorPagina") ;
            $ordenamiento = $this->input->post("Orden") ;
            
            $inicio = 1+$regXpag*($pagina-1);
            $fin = $regXpag*$pagina;

            $respuesta = $this->FormatearBusqueda($this->listasdesplegables_model->BusquedaLD($busqueda,$ordenamiento,$inicio,$fin));

            echo json_encode(array("isValid"=>true,"Datos"=>$respuesta));
        }

        public function ObtenerLista(){
            
            if(!$this->session->userdata("nombre") || $this->input->post("Codigo") == ""){
                redirect(site_url(''));
            }

            
            $ld = $this->listasdesplegables_model->Obtener('',$this->input->post("Codigo"));
            $htmlLD = $this->liblistasdesplegables->FormatearListaDesplegable($ld);

            echo json_encode(array(
                "isValid"=>true,
                "Lista"=>$htmlLD,
                "Tipo"=>$this->input->post("Tipo")
            ));
        }

        public function imprimir($id){
            $this->ValidarPermiso();
            $data['datos'] = $this->FormatearImpresion($this->listasdesplegables_model->Obtener($id));
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Formatos/formatoListaDesplegable',$data);
        }

        private function FormatearImpresion($respuesta){

            $data = array(
                "ld_id"         =>"",
                "codigo"        =>"",
                "nombre"        =>"",
                "descripcion"   => "",
                "opciones"      => [""]
            );

            if($respuesta){
                $data = $respuesta;
                $data['opciones'] = json_decode($data['opciones'],true);
            }

            return $data;
        }


        private function FormatearBusqueda($datos){
            
            $data = array(
                "Listas"     =>"",
                "Registros" => ""
            );

            if($datos){

                $htmlListas = "";
                $registros = 0;
                foreach ($datos as $elemento) {
                    $registros = $elemento['registros'];
                    $htmlListas = $htmlListas
                        ."<tr>"
                        .   "<td style='display:none;'>" . $elemento['ld_id'] . "</td>"
                        .   "<td style='display:none;'>" . $elemento['opciones'] . "</td>"
                        .   "<td>" . $elemento['codigo'] . "</td>"
                        .   "<td>" . $elemento['nombre'] . "</td>"
                        .   "<td>" . $elemento['descripcion'] . "</td>"
                        ."</tr>";
                }
                
                $data['Listas'] = $htmlListas;
                $data['Registros'] = $registros;
            }

            return $data;
        }
    }

?>