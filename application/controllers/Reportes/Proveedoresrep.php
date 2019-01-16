<?php

    class Proveedoresrep extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
            $this->load->model('Reportes/proveedoresrep_model' , 'proveedoresrep_model');
        }

        private function ValidarPermiso(){

            if(!$this->session->userdata("Permisos")['Reportes'] || !$this->session->userdata("Permisos")['RepProveedores']){
                show_404();
            }
            
        }
        
        public function view(){
            $this->ValidarPermiso();
            
            $JsFile = "<script src=\"". base_url() . "assets/js/Reportes/Proveedoresrep.js\"></script>";
            
            $datafile['JsFile'] = $JsFile ;

            

            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');

            
            $listaBusquedaProveedores  = $this->listasdesplegables_model->Obtener('','COB-PROVEE');

            $data['listaBusquedaProveedores']  = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaProveedores);

            $dataLD['OrdenarBusqueda'] = "";

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/Reportes/proveedoresrep',$data);
            $this->load->view('plantillas/7-footer');
        }

        public function listadoproveedores(){
            $this->ValidarPermiso();

            $parametros = array(
                "Proveedor"  => $this->input->post("Proveedor"),
            );

            $data['datos'] = $this->proveedoresrep_model->listadoproveedores($parametros);
            // var_dump($data);
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/Listadoproveedores',$data);
        }

    }


?>