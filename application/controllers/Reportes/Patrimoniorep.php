<?php

    class Patrimoniorep extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
            $this->load->model('Reportes/patrimoniorep_model' , 'patrimoniorep_model');
        }

        private function ValidarPermiso(){
            if(!$this->session->userdata("Permisos")['Reportes'] || !$this->session->userdata("Permisos")['RepPatrimonio']){
                show_404();
            }
        }
        
        public function view(){
            $this->ValidarPermiso();
            
            $JsFile = "<script src=\"". base_url() . "assets/js/Reportes/Patrimoniorep.js\"></script>";
            
            $datafile['JsFile'] = $JsFile ;

            

            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');

            
            $listaBusquedaLocalizacion  = $this->listasdesplegables_model->Obtener('','COB-LOCALI');
            $listaBusquedaBien          = $this->listasdesplegables_model->Obtener('','COB-BIENES');
            $listaBusquedaTipoPieza       = $this->listasdesplegables_model->Obtener('','COB-TIPOPI');
            $listaBusquedaPieza     = $this->listasdesplegables_model->Obtener('','COB-PIEZAS');

            $data['listaBusquedaLocalizacion']  = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaLocalizacion);
            $data['listaBusquedaBien']          = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaBien);
            $data['listaBusquedaTipoPieza']       = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaTipoPieza);
            $data['listaBusquedaPieza']     = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaPieza);

            $dataLD['OrdenarBusqueda'] = "";

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/Reportes/patrimoniorep',$data);
            $this->load->view('plantillas/7-footer');
        }

        public function Listadobienes(){

            $this->ValidarPermiso();

            $parametros = array(
                "Inicio"        => $this->input->post("Inicio"),
                "Fin"           => $this->input->post("Fin"),
                "TipoPieza"     => $this->input->post("TipoPieza"),
                "Pieza"         => $this->input->post("Pieza"),
                "Bien"          => $this->input->post("Bien"),
                "Localizacion"  => $this->input->post("Localizacion"),
            );

            $data['datos'] = $this->patrimoniorep_model->ListadoBienes($parametros);
            $data['parametros'] = $this->patrimoniorep_model->ObtenerParametros($parametros);

            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/Listadobienes',$data);
        }

        public function Listadopiezas(){

            $this->ValidarPermiso();

            $parametros = array(
                "Inicio"        => $this->input->post("Inicio"),
                "Fin"           => $this->input->post("Fin"),
                "TipoPieza"     => $this->input->post("TipoPieza"),
                "Pieza"         => $this->input->post("Pieza"),
                "Bien"          => $this->input->post("Bien"),
                "Localizacion"  => $this->input->post("Localizacion"),
            );

            $data['datos'] = $this->patrimoniorep_model->ListadoPiezas($parametros);
            $data['parametros'] = $this->patrimoniorep_model->ObtenerParametros($parametros);

            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/Listadopiezas',$data);
        }

        public function Listadoajustes(){

            $this->ValidarPermiso();

            $parametros = array(
                "Inicio"        => $this->input->post("Inicio"),
                "Fin"           => $this->input->post("Fin"),
                "TipoPieza"     => $this->input->post("TipoPieza"),
                "Pieza"         => $this->input->post("Pieza"),
                "Bien"          => $this->input->post("Bien"),
                "Localizacion"  => $this->input->post("Localizacion"),
            );

            $data['datos'] = $this->patrimoniorep_model->ListadoAjustes($parametros);
            $data['parametros'] = $this->patrimoniorep_model->ObtenerParametros($parametros);

            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/Listadoajustes',$data);
        }

        public function Listadocambiosestatus(){

            $this->ValidarPermiso();

            $parametros = array(
                "Inicio"        => $this->input->post("Inicio"),
                "Fin"           => $this->input->post("Fin"),
                "TipoPieza"     => $this->input->post("TipoPieza"),
                "Pieza"         => $this->input->post("Pieza"),
                "Bien"          => $this->input->post("Bien"),
                "Localizacion"  => $this->input->post("Localizacion"),
            );

            $data['datos'] = $this->patrimoniorep_model->ListadoCambios($parametros);
            $data['parametros'] = $this->patrimoniorep_model->ObtenerParametros($parametros);

            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/Listadocambiosestatus',$data);
        }

        public function Listadocompatibilidad(){

            $this->ValidarPermiso();

            $parametros = array(
                "Inicio"        => $this->input->post("Inicio"),
                "Fin"           => $this->input->post("Fin"),
                "TipoPieza"     => $this->input->post("TipoPieza"),
                "Pieza"         => $this->input->post("Pieza"),
                "Bien"          => $this->input->post("Bien"),
                "Localizacion"  => $this->input->post("Localizacion"),
            );

            $data['datos'] = $this->patrimoniorep_model->ListadoCompatibilidad($parametros);
            $data['parametros'] = $this->patrimoniorep_model->ObtenerParametros($parametros);

            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/Listadocompatibilidad',$data);
        }

    }


?>