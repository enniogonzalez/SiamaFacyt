<?php

    class Obrerosrep extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
            $this->load->model('Reportes/obrerosrep_model' , 'obrerosrep_model');
        }

        private function ValidarPermiso(){

            if(!$this->session->userdata("Permisos")['Reportes'] || !$this->session->userdata("Permisos")['RepObreros']){
                show_404();
            }
            
        }
        
        public function view(){
            $this->ValidarPermiso();
            
            $JsFile = "<script src=\"". base_url() . "assets/js/Reportes/Obrerosrep.js\"></script>";
            
            $datafile['JsFile'] = $JsFile ;

            

            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');

            
            $listaBusquedaObreros  = $this->listasdesplegables_model->Obtener('','COB-OBRERO');

            $data['listaBusquedaObreros']  = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaObreros);

            $dataLD['OrdenarBusqueda'] = "";

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/Reportes/obrerosrep',$data);
            $this->load->view('plantillas/7-footer');
        }

        public function listadoobreros(){
            $this->ValidarPermiso();

            $parametros = array(
                "Obrero"  => $this->input->post("Obrero"),
            );

            $data['datos'] = $this->obrerosrep_model->listadoobreros($parametros);
            
            if($this->input->post("Obrero") != ""){
                $param = $this->obrerosrep_model->ObtenerParametros($parametros);
                if(count($param) > 0){
                    $parametros = array(
                        "Obrero"  => $param[0]['nombre'],
                    );
                }
            }
            $data['parametros'] = $parametros;

            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/Listadoobreros',$data);
        }

    }


?>