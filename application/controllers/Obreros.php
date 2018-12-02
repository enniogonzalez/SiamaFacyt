<?php

    class Obreros extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->model('obreros_model');
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
        }

        private function FormatearRequest($respuesta){

            $data = array(
                "obr_id"        =>"",
                "cedula"        =>"",
                "nombre"       =>"",
                "telefonos"     =>"",
                "correo"        =>"",
                "observaciones" =>"",
            );

            if($respuesta)
                $data = $respuesta;
            return $data;
        }

        private function ValidarPermiso(){
            if(!$this->session->userdata("Permisos")['Obreros']){
                show_404();
            }
        }

        public function view(){
            
            if(!$this->session->userdata("nombre")){
                redirect(site_url(''));
            }
            $this->ValidarPermiso();
            $data = $this->FormatearRequest($this->obreros_model->Obtener());

            $JsFile = "<script src=\"". base_url() . "assets/js/Obreros.js\"></script>";
            
            $datafile['JsFile'] = $JsFile ;

            
            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');
            $ld = $this->listasdesplegables_model->Obtener('','COB-OBRERO');

            $dataLD['OrdenarBusqueda'] = $this->liblistasdesplegables->FormatearListaDesplegable($ld);

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/obreros',$data);
            $this->load->view('plantillas/7-footer');


        }

        public function guardar(){

            if(!$this->session->userdata("nombre") || $this->input->post("Cedula") == ""){
                redirect(site_url(''));
            }
            $this->ValidarPermiso();
            $parametros = array(
                "obr_id"        => $this->input->post("id"),
                "Cedula"        => $this->input->post("Cedula"),
                "Nombre"        => $this->input->post("Nombre"),
                "Telefonos"     => $this->input->post("Tlf"),
                "Correo"        => $this->input->post("Correo"),
                "Observaciones" => trim($this->input->post("Observacion"))
            );
            
            if($this->input->post("id") == ""){
                if($this->obreros_model->ExisteCedula($parametros['Cedula'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe un obrero registrado con la mismo cedula.",
                        "id"=>""));
                }else{
                    $respuesta = $this->obreros_model->Insertar($parametros);
                    $insertado = $this->obreros_model->Obtener();
                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha insertado Obrero exitosamente",
                        "id"=>$insertado['obr_id']));
                }
            }else{
                if($this->obreros_model->ExisteCedula($parametros['Cedula'],$parametros['obr_id'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe un obrero registrado con la misma cedula.",
                        "id"=>""));
                }else{
                    $respuesta = $this->obreros_model->Actualizar($parametros);
                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha editado Obrero exitosamente",
                        "id"=>$this->input->post("id")));
                }

            }
        }

        public function eliminar(){
            
            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }
            $this->ValidarPermiso();

            $eliminar = $this->obreros_model->Eliminar($this->input->post("id"));
            $data = $this->FormatearRequest($this->obreros_model->Obtener());
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

            $respuesta = $this->FormatearBusqueda($this->obreros_model->Busqueda($busqueda,$ordenamiento,$inicio,$fin));

            echo json_encode(array("isValid"=>true,"Datos"=>$respuesta));
        }

        public function imprimir($id){
            $this->ValidarPermiso();
            $data['datos'] = $this->FormatearRequest($this->obreros_model->Obtener($id));
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Formatos/formatoObrero',$data);
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
                        .   "<td style='display:none;'>" . $elemento['obr_id'] . "</td>"
                        .   "<td style='display:none;'>" . $elemento['telefonos'] . "</td>"
                        .   "<td style='display:none;'>" . $elemento['correo'] . "</td>"
                        .   "<td style='display:none;'>" . $elemento['observaciones'] . "</td>"
                        .   "<td>" . $elemento['cedula'] . "</td>"
                        .   "<td>" . $elemento['nombre'] . "</td>"
                        ."</tr>";
                }
                
                $data['Listas'] = $htmlListas;
                $data['Registros'] = $registros;
            }

            return $data;
        }


    }


?>