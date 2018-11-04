<?php

    class Usuarios extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->model('Sistema/usuarios_model' , 'usuarios_model');
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
        }

        private function FormatearRequest($respuesta){

            $data = array(
                "usu_id"        => "",
                "username"      => "",
                "nombre"        => "",
                "cargo"         => "",
                "correo"        => "",
                "observaciones" => "",
                "Permisos"      => [],
            );

            if($respuesta)
                $data = $respuesta;
            return $data;
        }

        private function ValidarPermiso(){
            if(!$this->session->userdata("Permisos")['Sistema']){
                show_404();
            }
        }

        public function view(){
            
            if(!$this->session->userdata("nombre")){
                redirect(site_url(''));
            }
            $this->ValidarPermiso();
            
            $data = $this->FormatearRequest($this->usuarios_model->Obtener());

            $JsFile = "<script src=\"". base_url() . "assets/js/Sistema/Usuarios.js\"></script>";
            
            $datafile['JsFile'] = $JsFile ;

            
            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');
            $ld = $this->listasdesplegables_model->Obtener('','COB-USUARI');
            $ldCargo =$this->listasdesplegables_model->Obtener('','USU-CARGO');

            $dataLD['OrdenarBusqueda'] = $this->liblistasdesplegables->FormatearListaDesplegable($ld);
            $data['cargo'] = $this->liblistasdesplegables->FormatearListaDesplegable($ldCargo,true,$data['cargo']);

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/Sistema/usuarios',$data);
            $this->load->view('plantillas/7-footer');


        }

        public function obtener(){

            $this->ValidarPermiso();

            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }
            $data = $this->FormatearRequest($this->usuarios_model->Obtener($this->input->post("id")));
            echo json_encode(array("isValid"=>true,"Datos"=>$data));

        }

        public function guardar(){

            $this->ValidarPermiso();

            if(!$this->session->userdata("nombre") || $this->input->post("Username") == ""){
                redirect(site_url(''));
            }

            $parametros = array(
                "idActual"      => $this->input->post("id"),
                "Username"      => $this->input->post("Username"),
                "Nombre"        => $this->input->post("Nombre"),
                "Cargo"         => $this->input->post("Cargo"),
                "Correo"        => $this->input->post("Correo"),
                "Localizacion"  => ($this->input->post("Localizacion") == "true" ? true:false),
                "Mantenimiento" => ($this->input->post("Mantenimiento") == "true" ? true:false),
                "Marcas"        => ($this->input->post("Marcas") == "true" ? true:false),
                "Partidas"      => ($this->input->post("Partidas") == "true" ? true:false),
                "Patrimonio"    => ($this->input->post("Patrimonio") == "true" ? true:false),
                "Proveedores"   => ($this->input->post("Proveedores") == "true" ? true:false),
                "Sistema"       => ($this->input->post("Sistema") == "true" ? true:false),
                "Observaciones" => trim($this->input->post("Observacion"))
            );
            
            if($this->input->post("id") == ""){
                if($this->usuarios_model->ExisteUsername($parametros['Username'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe un usuario registrado con el mismo campo usuario.",
                        "id"=>""));
                }elseif($this->usuarios_model->ExisteCorreo($parametros['Correo'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe un usuario registrado con el mismo correo.",
                        "id"=>""));
                }else{
                    $respuesta = $this->usuarios_model->Insertar($parametros);
                    $insertado = $this->usuarios_model->Obtener();
                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha insertado usuario exitosamente",
                        "id"=>$insertado['usu_id']));
                }
            }else{
                if($this->usuarios_model->ExisteUsername($parametros['Username'],$parametros['idActual'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe un usuario registrado con el mismo campo usuario.",
                        "id"=>""));
                }elseif($this->usuarios_model->ExisteCorreo($parametros['Correo'],$parametros['idActual'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe un usuario registrado con el mismo correo.",
                        "id"=>""));
                }else{
                    $respuesta = $this->usuarios_model->Actualizar($parametros);
                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha editado usuario exitosamente",
                        "id"=>$this->input->post("id")));
                }

            }
        }

        public function eliminar(){
            $this->ValidarPermiso();
            
            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }

            $eliminar = $this->usuarios_model->Eliminar($this->input->post("id"));
            $data = $this->FormatearRequest($this->usuarios_model->Obtener());
            echo json_encode(array("isValid"=>true,"Datos"=>$data));
        }

        public function busqueda(){

            if(!$this->session->userdata("nombre") || $this->input->post("Pagina") == ""){
                redirect(site_url(''));
            }

            $busqueda = $this->input->post("Busqueda") ;
            $pagina = (int) $this->input->post("Pagina") ;
            $regXpag = (int) $this->input->post("RegistrosPorPagina") ;
            $ordenamiento = $this->input->post("Orden") ;
            
            $inicio = 1+$regXpag*($pagina-1);
            $fin = $regXpag*$pagina;

            $id = "";

            $respuesta = $this->FormatearBusqueda($this->usuarios_model->Busqueda($busqueda,$ordenamiento,$inicio,$fin,$id));

            echo json_encode(array("isValid"=>true,"Datos"=>$respuesta));
        }

        public function imprimir($id){

            $this->ValidarPermiso();
            
            $data['datos'] = $this->FormatearRequest($this->usuarios_model->Obtener($id));
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/repUsuarios',$data);
            
        }

        private function FormatearBusqueda($datos){
            
            $data = array(
                "Listas"     =>"",
                "Registros" => ""
            );

            if($datos){
                $htmlListas = "";
                $registros = 0;
                foreach ($datos as $elemento) {
                    $registros = $elemento['registros'];
                    $htmlListas = $htmlListas
                        ."<tr>"
                        .   "<td style='display:none;'>" . $elemento['usu_id'] . "</td>"
                        .   "<td style='display:none;'>" . $elemento['observaciones'] . "</td>"
                        .   "<td>" . $elemento['username'] . "</td>"
                        .   "<td>" . $elemento['nombre'] . "</td>"
                        .   "<td>" . $elemento['cargo'] . "</td>"
                        ."</tr>";
                }
                
                $data['Listas'] = $htmlListas;
                $data['Registros'] = $registros;
            }

            return $data;
        }
    }

?>