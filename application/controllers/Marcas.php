<?php

    class Marcas extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->model('marcas_model');
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
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
                        .   "<td style='display:none;'>" . $elemento['mar_id'] . "</td>"
                        .   "<td>" . $elemento['nombre'] . "</td>"
                        .   "<td style='display:none;'>" . $elemento['observaciones'] . "</td>"
                        ."</tr>";
                }
                
                $data['Listas'] = $htmlListas;
                $data['Registros'] = $registros;
            }

            return $data;
        }

        private function FormatearImpresion($respuesta){

            $data = array(
                "mar_id"        =>"",
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

        private function FormatearRequest($respuesta){

            $data = array(
                "mar_id"        =>"",
                "nombre"        =>"",
                "observaciones" =>"",
            );

            if($respuesta)
                $data = $respuesta;
            return $data;
        }

        private function ValidarPermiso(){
            if(!$this->session->userdata("Permisos")['Marcas']){
                show_404();
            }
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

            $respuesta = $this->FormatearBusqueda($this->marcas_model->Busqueda($busqueda,$ordenamiento,$inicio,$fin));

            echo json_encode(array("isValid"=>true,"Datos"=>$respuesta));
        }

        public function eliminar(){
            
            $this->ValidarPermiso();
            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }

            $eliminar = $this->marcas_model->Eliminar($this->input->post("id"));
            $data = $this->FormatearRequest($this->marcas_model->Obtener());
            echo json_encode(array("isValid"=>true,"Datos"=>$data));
        }

        public function guardar(){

            $this->ValidarPermiso();

            if(!$this->session->userdata("nombre") || $this->input->post("Nombre") == ""){
                redirect(site_url(''));
            }

            $parametros = array(
                "mar_id"      => $this->input->post("id"),
                "Nombre"        => $this->input->post("Nombre"),
                "Observaciones" => trim($this->input->post("Observacion"))
            );
            
            if($this->input->post("id") == ""){
                if($this->marcas_model->ExisteNombre($parametros['Nombre'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe una marca registrada con el mismo nombre.",
                        "id"=>""));
                }else{
                    $respuesta = $this->marcas_model->Insertar($parametros);
                    $insertado = $this->marcas_model->Obtener();
                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha insertado marca exitosamente",
                        "id"=>$insertado['mar_id']));
                }
            }else{
                if($this->marcas_model->ExisteNombre($parametros['Nombre'],$parametros['mar_id'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe una marca registrada con el mismo nombre.",
                        "id"=>""));
                }else{
                    $respuesta = $this->marcas_model->Actualizar($parametros);
                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha editado marca exitosamente",
                        "id"=>$this->input->post("id")));
                }

            }
        }

        public function imprimir($id){
            $data['datos'] = $this->FormatearImpresion($this->marcas_model->ObtenerInfoPDF($id));
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Formatos/formatoMarca',$data);
        }
        
        public function view(){
            
            if(!$this->session->userdata("nombre")){
                redirect(site_url(''));
            }

            $this->ValidarPermiso();
            
            $data = $this->FormatearRequest($this->marcas_model->Obtener());

            $JsFile = "<script src=\"". base_url() . "assets/js/Marcas.js\"></script>";
            
            $datafile['JsFile'] = $JsFile ;

            
            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');
            $ld = $this->listasdesplegables_model->Obtener('','COB-MARCAS');

            $dataLD['OrdenarBusqueda'] = $this->liblistasdesplegables->FormatearListaDesplegable($ld);

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/marcas',$data);
            $this->load->view('plantillas/7-footer');


        }

    }


?>