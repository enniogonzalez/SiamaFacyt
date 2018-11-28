<?php

    class Configurarusuario extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->model('Sistema/usuarios_model' , 'usuarios_model');
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
        }

        private function FormatearRequest($respuesta){

            $data = array(
                "loc_id"        =>"",
                "nombre"        =>"",
                "ubicacion"     =>"",
                "tipo"          =>"",
                "observaciones" =>"",
                "cap_amp"       =>"0.00",
                "idpad"         =>"-1",
                "nombrepadre"   =>"",
            );

            if($respuesta)
                $data = $respuesta;
            
            return $data;
        }

        public function view(){
            
            if(!$this->session->userdata("nombre")){
                redirect(site_url(''));
            }
            
            $JsFile = "<script src=\"". base_url() . "assets/js/Sistema/ConfigurarUsuario.js\"></script>";
            
            $datafile['JsFile'] = $JsFile ;

            $dataLD['OrdenarBusqueda'] = "";

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/Sistema/configurarusuario');
            $this->load->view('plantillas/7-footer');


        }

        public function guardar(){
            
            if(!$this->session->userdata("nombre") || $this->input->post("Nombre") == ""){
                redirect(site_url(''));
            }

            $parametros = array(
                "idActual"  => $this->session->userdata("usu_id"),
                "Nombre"    => $this->input->post("Nombre"),
                "Correo"    => $this->input->post("Correo"),
            );

            if($this->usuarios_model->ExisteCorreo($parametros['Correo'],$parametros['idActual'])){
                echo json_encode(array(
                    "isValid"=>false,
                    "Mensaje"=>"Ya existe un usuario registrado con el mismo correo.",
                    "id"=>""));
            }else{
                $respuesta = $this->usuarios_model->Configurar($parametros);

                $data = [
                    "usu_id"        => $this->session->userdata("usu_id"),
                    "username"      => $this->session->userdata("username"),
                    "nombre"        => $this->input->post("Nombre"),
                    "cargo"         => $this->session->userdata("cargo"),
                    "correo"        => $this->input->post("Correo"),
                    "observaciones" => $this->session->userdata("observaciones"),
                    "Permisos"      => $this->session->userdata("Permisos"),
                ];

                $this->session->set_userdata($data);

                echo json_encode(array(
                    "isValid"=>true,
                    "Mensaje"=>"Se ha modificado el usuario exitosamente",
                    "id"=>$this->session->userdata("usu_id")));
            }
        }

    }


?>