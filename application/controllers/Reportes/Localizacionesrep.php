<?php

    class Localizacionesrep extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
            $this->load->model('Reportes/localizacionesrep_model' , 'localizacionesrep_model');
        }

        private function ValidarPermiso(){

            if(!$this->session->userdata("Permisos")['Reportes'] || !$this->session->userdata("Permisos")['RepLocalizacion']){
                show_404();
            }
            
        }
        
        public function view(){
            $this->ValidarPermiso();
            
            $JsFile = "<script src=\"". base_url() . "assets/js/Reportes/Localizacionesrep.js\"></script>";
            
            $datafile['JsFile'] = $JsFile ;

            

            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');

            
            $listaBusquedaLocalizacion  = $this->listasdesplegables_model->Obtener('','COB-LOCALI');

            $data['listaBusquedaLocalizacion']  = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaLocalizacion);

            $dataLD['OrdenarBusqueda'] = "";

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/Reportes/localizacionesrep',$data);
            $this->load->view('plantillas/7-footer');
        }

        public function listadolocalizaciones(){

            $this->ValidarPermiso();

            $parametros = array(
                "Localizacion"  => $this->input->post("Localizacion"),
            );

            $data['datos'] = $this->localizacionesrep_model->ObtenerLocalizaciones($parametros);

            if($this->input->post("Localizacion") != ""){
                $param = $this->localizacionesrep_model->ObtenerParametros($parametros);
                if(count($param) > 0){
                    $parametros = array(
                        "Localizacion"  => $param[0]['nombre'],
                    );
                }
            }

            $data['parametros'] = $parametros;
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/Listadolocalizaciones',$data);
        }
        

        public function arbollocalizaciones(){

            $this->ValidarPermiso();

            $parametros = array(
                "Localizacion"  => $this->input->post("Localizacion"),
            );

            $data['datos'] = $this->localizacionesrep_model->ObtenerLocalizaciones($parametros);

            if($this->input->post("Localizacion") != ""){
                $param = $this->localizacionesrep_model->ObtenerParametros($parametros);
                if(count($param) > 0){
                    $parametros = array(
                        "Localizacion"  => $param[0]['nombre'],
                    );
                }
            }

            $data['parametros'] = $parametros;
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/Arbollocalizaciones',$data);
        }

    }


?>