<?php

    class Herramientas extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->model('Patrimonio/herramientas_model','herramientas_model');
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
        }

        private function FormatearRequest($respuesta){

            $data = array(
                "her_id"        =>"",
                "nombre"        =>"",
                "observaciones" =>"",
            );

            if($respuesta)
                $data = $respuesta;
            return $data;
        }

        private function ValidarPermiso(){
            if(!$this->session->userdata("Permisos")['Patrimonio']){
                show_404();
            }
        }
        
        public function view(){
            
            if(!$this->session->userdata("nombre")){
                redirect(site_url(''));
            }

            $this->ValidarPermiso();
            
            $data = $this->FormatearRequest($this->herramientas_model->Obtener());

            $JsFile = "<script src=\"". base_url() . "assets/js/Patrimonio/Herramientas.js\"></script>";
            
            $datafile['JsFile'] = $JsFile ;

            
            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');
            $ld = $this->listasdesplegables_model->Obtener('','COB-HERRAM');

            $dataLD['OrdenarBusqueda'] = $this->liblistasdesplegables->FormatearListaDesplegable($ld);

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/Patrimonio/herramientas',$data);
            $this->load->view('plantillas/7-footer');


        }

        public function guardar(){

            $this->ValidarPermiso();

            if(!$this->session->userdata("nombre") || $this->input->post("Nombre") == ""){
                redirect(site_url(''));
            }

            $parametros = array(
                "idActual"      => $this->input->post("id"),
                "Nombre"        => $this->input->post("Nombre"),
                "Observaciones" => trim($this->input->post("Observacion"))
            );
            
            if($this->input->post("id") == ""){
                if($this->herramientas_model->ExisteNombre($parametros['Nombre'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe una herramienta registrada con el mismo nombre.",
                        "id"=>""));
                }else{
                    $respuesta = $this->herramientas_model->Insertar($parametros);
                    $insertado = $this->herramientas_model->Obtener();
                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha insertado herramienta exitosamente",
                        "id"=>$insertado['her_id']));
                }
            }else{
                if($this->herramientas_model->ExisteNombre($parametros['Nombre'],$parametros['idActual'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe una herramienta registrada con el mismo nombre.",
                        "id"=>""));
                }else{
                    $respuesta = $this->herramientas_model->Actualizar($parametros);
                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha editado herramienta exitosamente",
                        "id"=>$this->input->post("id")));
                }

            }
        }

        public function eliminar(){
            
            $this->ValidarPermiso();
            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }

            $eliminar = $this->herramientas_model->Eliminar($this->input->post("id"));
            $data = $this->FormatearRequest($this->herramientas_model->Obtener());
            echo json_encode(array("isValid"=>true,"Datos"=>$data));
        }

        public function busqueda(){

            if(!$this->session->userdata("nombre") || $this->input->post("Pagina") == ""){
                redirect(site_url(''));
            }

            $busqueda = $this->input->post("Busqueda") ;
            $pagina = (int) $this->input->post("Pagina") ;
            $regXpag = (int) $this->input->post("RegistrosPorPagina") ;
            $ordenamiento = $this->input->post("Orden") ;
            
            $inicio = 1+$regXpag*($pagina-1);
            $fin = $regXpag*$pagina;

            $respuesta = $this->FormatearBusqueda($this->herramientas_model->Busqueda($busqueda,$ordenamiento,$inicio,$fin));

            echo json_encode(array("isValid"=>true,"Datos"=>$respuesta));
        }

        public function imprimir($id){
            $data['datos'] = $this->FormatearImpresion($this->herramientas_model->ObtenerInfoPDF($id));
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Formatos/formatoHerramienta',$data);
        }

        private function FormatearImpresion($respuesta){

            $data = array(
                "her_id"        =>"",
                "nombre"        =>"",
                "ubicacion"     =>"",
                "tipo"          =>"",
                "cap_amp"       =>"",
                "nombrepadre"   =>"",
                "observaciones" => ""
            );

            if($respuesta)
                $data = $respuesta;

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
                        .   "<td style='display:none;'>" . $elemento['her_id'] . "</td>"
                        .   "<td>" . $elemento['nombre'] . "</td>"
                        .   "<td style='display:none;'>" . $elemento['observaciones'] . "</td>"
                        ."</tr>";
                }
                
                $data['Listas'] = $htmlListas;
                $data['Registros'] = $registros;
            }

            return $data;
        }

    }


?>