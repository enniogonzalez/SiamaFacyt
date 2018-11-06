<?php

    class Reportes extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
            $this->load->model('Mantenimiento/Reportes_model' , 'Reportes_model');
        }

        private function ValidarPermiso(){
            if(!$this->session->userdata("Permisos")['Mantenimiento']){
                show_404();
            }
        }
        
        public function view(){
            $this->ValidarPermiso();
            
            $JsFile = "<script src=\"". base_url() . "assets/js/Mantenimiento/Reportes.js\"></script>";
            
            $datafile['JsFile'] = $JsFile ;

            

            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');

            
            $listaBusquedaLocalizacion  = $this->listasdesplegables_model->Obtener('','COB-LOCALI');
            $listaBusquedaBien          = $this->listasdesplegables_model->Obtener('','COB-BIENES');
            $listaBusquedaUsuario       = $this->listasdesplegables_model->Obtener('','COB-USUARI');
            $listaBusquedaProveedor     = $this->listasdesplegables_model->Obtener('','COB-PROVEE');

            $data['listaBusquedaLocalizacion']  = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaLocalizacion);
            $data['listaBusquedaBien']          = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaBien);
            $data['listaBusquedaUsuario']       = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaUsuario);
            $data['listaBusquedaProveedor']     = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaProveedor);

            $dataLD['OrdenarBusqueda'] = "";

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/Mantenimiento/reportes',$data);
            $this->load->view('plantillas/7-footer');
        }

        public function RepManUsu(){
            $this->ValidarPermiso();

            $parametros = array(
                "Inicio"        => $this->input->post("Inicio"),
                "Fin"           => $this->input->post("Fin"),
                "Usuario"       => $this->input->post("Usuario"),
                "Proveedor"     => $this->input->post("Proveedor"),
                "Bien"          => $this->input->post("Bien"),
                "Localizacion"  => $this->input->post("Localizacion"),
            );

            $data['datos'] = $this->Reportes_model->RepManUsu($parametros);
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/repManUsu',$data);
        }

        public function RepManPro(){
            $this->ValidarPermiso();

            $parametros = array(
                "Inicio"        => $this->input->post("Inicio"),
                "Fin"           => $this->input->post("Fin"),
                "Usuario"       => $this->input->post("Usuario"),
                "Proveedor"     => $this->input->post("Proveedor"),
                "Bien"          => $this->input->post("Bien"),
                "Localizacion"  => $this->input->post("Localizacion"),
            );

            $data['datos'] = $this->Reportes_model->RepManPro($parametros);
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/repManPro',$data);

        }

        public function RepManBie(){
            
            $this->ValidarPermiso();

            $parametros = array(
                "Inicio"        => $this->input->post("Inicio"),
                "Fin"           => $this->input->post("Fin"),
                "Usuario"       => $this->input->post("Usuario"),
                "Proveedor"     => $this->input->post("Proveedor"),
                "Bien"          => $this->input->post("Bien"),
                "Localizacion"  => $this->input->post("Localizacion"),
            );

            $data['datos'] = $this->Reportes_model->RepManBie($parametros);
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/repManBie',$data);

        }

        public function RepManLoc(){
            
            $this->ValidarPermiso();

            $parametros = array(
                "Inicio"        => $this->input->post("Inicio"),
                "Fin"           => $this->input->post("Fin"),
                "Usuario"       => $this->input->post("Usuario"),
                "Proveedor"     => $this->input->post("Proveedor"),
                "Bien"          => $this->input->post("Bien"),
                "Localizacion"  => $this->input->post("Localizacion"),
            );

            $data['datos'] = $this->Reportes_model->RepManLoc($parametros);
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/repManLoc',$data);

        }
    }


?>