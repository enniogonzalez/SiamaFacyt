<?php

    class Alertas extends CI_Controller{

        public function __construct(){
            parent::__construct();
        }

        private function FormatearRequest($respuesta){

            $data = array(
                "pro_id"        =>"",
                "rif"           =>"",
                "raz_soc"       =>"",
                "reg_nac_con"   =>"",
                "direccion"     =>"",
                "telefonos"     =>"",
                "correo"        =>"",
                "observaciones" =>"",
            );

            if($respuesta)
                $data = $respuesta;
            return $data;
        }

        public function view(){
            
            if(!$this->session->userdata("nombre")){
                redirect(site_url(''));
            }
            
            $datafile['JsFile'] = "<link rel=\"stylesheet\" type=\"text/css\" href=\""
                . base_url() ."assets/css/alertas.css\">";
            $dataLD['OrdenarBusqueda'] = "";

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $data['Alertas'] = $this->FormatearAlertas($this->alertas_model->Obtener());
            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/alertas',$data);
            $this->load->view('plantillas/7-footer');


        }

        public function CantidadAlertas(){

            if(!$this->session->userdata("nombre")){
                redirect(site_url(''));
            }

            $data = $this->alertas_model->CantidadAlertas();
            echo json_encode(array("isValid"=>true,"Cantidad"=>$data));
        }

        private function FormatearAlertas($datos){
            
            $html = "";

            if($datos){

                foreach ($datos as $elemento) {
                    $html = $html
                        . "<div class = \"Cuerpo-Alerta\">"
                        . "    <div class = \"Cabecera-Alerta\">"
                        . $elemento['titulo']
                        . "    </div>"
                        . "    <div class=\"Informacion-Alerta\">"
                        . $elemento['descripcion']
                        . "    </div>"
                        . "</div>";
                }
                
            }

            return $html;
        }

    }


?>