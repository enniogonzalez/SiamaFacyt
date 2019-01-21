<?php

    class Partidasrep extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
            $this->load->model('Reportes/partidasrep_model' , 'partidasrep_model');
        }

        private function ValidarPermiso(){

            if(!$this->session->userdata("Permisos")['Reportes'] || !$this->session->userdata("Permisos")['RepPartidas']){
                show_404();
            }
            
        }
        
        public function view(){
            $this->ValidarPermiso();
            
            $JsFile = "<script src=\"". base_url() . "assets/js/Reportes/Partidasrep.js\"></script>";
            
            $datafile['JsFile'] = $JsFile ;

            

            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');

            
            $listaBusquedaPartidas  = $this->listasdesplegables_model->Obtener('','COB-PARTID');

            $data['listaBusquedaPartidas']  = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaPartidas);

            $dataLD['OrdenarBusqueda'] = "";

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/Reportes/partidasrep',$data);
            $this->load->view('plantillas/7-footer');
        }

        public function listadopartidas(){
            $this->ValidarPermiso();

            $parametros = array(
                "Partida"  => $this->input->post("Partida"),
            );

            $data['datos'] = $this->partidasrep_model->listadopartidas($parametros);
            
            if($this->input->post("Partida") != ""){
                $param = $this->partidasrep_model->ObtenerParametros($parametros);
                if(count($param) > 0){
                    $parametros = array(
                        "Partida"  => $param[0]['nombre'],
                    );
                }
            }
            $data['parametros'] = $parametros;

            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/Listadopartidas',$data);
        }

    }


?>