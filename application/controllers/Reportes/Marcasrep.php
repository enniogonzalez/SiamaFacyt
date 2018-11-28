<?php

    class Marcasrep extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
            $this->load->model('Reportes/marcasrep_model' , 'marcasrep_model');
        }

        private function ValidarPermiso(){

            if(!$this->session->userdata("Permisos")['Reportes'] || !$this->session->userdata("Permisos")['Marcas']){
                show_404();
            }
            
        }
        
        public function view(){
            $this->ValidarPermiso();
            
            $JsFile = "<script src=\"". base_url() . "assets/js/Reportes/Marcasrep.js\"></script>";
            
            $datafile['JsFile'] = $JsFile ;

            

            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');

            
            $listaBusquedaMarcas  = $this->listasdesplegables_model->Obtener('','COB-MARCAS');

            $data['listaBusquedaMarcas']  = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaMarcas);

            $dataLD['OrdenarBusqueda'] = "";

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/Reportes/marcasrep',$data);
            $this->load->view('plantillas/7-footer');
        }

        public function listadomarcas(){
            $this->ValidarPermiso();

            $parametros = array(
                "Marca"  => $this->input->post("Marca"),
            );

            $data['datos'] = $this->marcasrep_model->listadomarcas($parametros);
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/Listadomarcas',$data);
        }

    }


?>