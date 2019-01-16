<?php

    class Mantenimientorep extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
            $this->load->model('Reportes/mantenimientorep_model' , 'mantenimientorep_model');
        }

        private function ValidarPermiso(){
            if(!$this->session->userdata("Permisos")['Reportes'] || !$this->session->userdata("Permisos")['RepMantenimiento']){
                show_404();
            }
        }
        
        public function view(){
            $this->ValidarPermiso();
            
            $JsFile = "<script src=\"". base_url() . "assets/js/Reportes/Mantenimientorep.js\"></script>";
            
            $datafile['JsFile'] = $JsFile ;

            

            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');

            
            $listaBusquedaLocalizacion  = $this->listasdesplegables_model->Obtener('','COB-LOCALI');
            $listaBusquedaBien          = $this->listasdesplegables_model->Obtener('','COB-BIENES');
            $listaBusquedaObrero       = $this->listasdesplegables_model->Obtener('','COB-OBRERO');
            $listaBusquedaProveedor     = $this->listasdesplegables_model->Obtener('','COB-PROVEE');

            $data['listaBusquedaLocalizacion']  = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaLocalizacion);
            $data['listaBusquedaBien']          = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaBien);
            $data['listaBusquedaObrero']       = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaObrero);
            $data['listaBusquedaProveedor']     = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaProveedor);

            $dataLD['OrdenarBusqueda'] = "";

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/Reportes/mantenimientorep',$data);
            $this->load->view('plantillas/7-footer');
        }

        public function RepManObr(){
            $this->ValidarPermiso();

            $parametros = array(
                "Inicio"        => $this->input->post("Inicio"),
                "Fin"           => $this->input->post("Fin"),
                "Obrero"        => $this->input->post("Obrero"),
                "Proveedor"     => $this->input->post("Proveedor"),
                "Bien"          => $this->input->post("Bien"),
                "Localizacion"  => $this->input->post("Localizacion"),
            );

            $data['datos'] = $this->mantenimientorep_model->RepManObr($parametros);
            $data['parametros'] = $this->mantenimientorep_model->ObtenerParametros($parametros);

            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/repManObr',$data);
        }

        public function RepManPro(){
            $this->ValidarPermiso();

            $parametros = array(
                "Inicio"        => $this->input->post("Inicio"),
                "Fin"           => $this->input->post("Fin"),
                "Obrero"       => $this->input->post("Obrero"),
                "Proveedor"     => $this->input->post("Proveedor"),
                "Bien"          => $this->input->post("Bien"),
                "Localizacion"  => $this->input->post("Localizacion"),
            );

            $data['datos'] = $this->mantenimientorep_model->RepManPro($parametros);
            $data['parametros'] = $this->mantenimientorep_model->ObtenerParametros($parametros);
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/repManPro',$data);

        }

        public function RepManBie(){
            
            $this->ValidarPermiso();

            $parametros = array(
                "Inicio"        => $this->input->post("Inicio"),
                "Fin"           => $this->input->post("Fin"),
                "Obrero"       => $this->input->post("Obrero"),
                "Proveedor"     => $this->input->post("Proveedor"),
                "Bien"          => $this->input->post("Bien"),
                "Localizacion"  => $this->input->post("Localizacion"),
            );

            $data['datos'] = $this->mantenimientorep_model->RepManBie($parametros);
            
            $data['parametros'] = $this->mantenimientorep_model->ObtenerParametros($parametros);
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/repManBie',$data);

        }

        public function RepManLoc(){
            
            $this->ValidarPermiso();

            $parametros = array(
                "Inicio"        => $this->input->post("Inicio"),
                "Fin"           => $this->input->post("Fin"),
                "Obrero"       => $this->input->post("Obrero"),
                "Proveedor"     => $this->input->post("Proveedor"),
                "Bien"          => $this->input->post("Bien"),
                "Localizacion"  => $this->input->post("Localizacion"),
            );

            $data['datos'] = $this->mantenimientorep_model->RepManLoc($parametros);
            $data['parametros'] = $this->mantenimientorep_model->ObtenerParametros($parametros);
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/repManLoc',$data);

        }
    }


?>