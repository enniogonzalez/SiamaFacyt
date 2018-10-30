<?php

    class Proveedores extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->model('proveedores_model');
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
        }

        private function FormatearRequest($respuesta){

            $data = array(
                "pro_id"        =>"",
                "rif"           =>"",
                "raz_soc"       =>"",
                "reg_nac_con"   =>"",
                "direccion"     =>"",
                "telefonos"     =>"",
                "correo"        =>"",
                "observaciones" =>"",
            );

            if($respuesta)
                $data = $respuesta;
            return $data;
        }

        private function ValidarPermiso(){
            if(!$this->session->userdata("Permisos")['Proveedores']){
                show_404();
            }
        }

        public function view(){
            
            if(!$this->session->userdata("nombre")){
                redirect(site_url(''));
            }
            $this->ValidarPermiso();
            $data = $this->FormatearRequest($this->proveedores_model->Obtener());

            $JsFile = "<script src=\"". base_url() . "assets/js/Proveedores.js\"></script>";
            
            $datafile['JsFile'] = $JsFile ;

            
            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');
            $ld = $this->listasdesplegables_model->Obtener('','COB-PROVEE');

            $dataLD['OrdenarBusqueda'] = $this->liblistasdesplegables->FormatearListaDesplegable($ld);

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/proveedores',$data);
            $this->load->view('plantillas/7-footer');


        }

        public function guardar(){

            if(!$this->session->userdata("nombre") || $this->input->post("Rif") == ""){
                redirect(site_url(''));
            }
            $this->ValidarPermiso();
            $parametros = array(
                "idActual"      => $this->input->post("id"),
                "Rif"           => $this->input->post("Rif"),
                "Raz_Soc"       => $this->input->post("Nombre"),
                "Reg_Nac_Con"   => $this->input->post("RNC"),
                "Direccion"     => $this->input->post("Direccion"),
                "Telefonos"     => $this->input->post("Tlf"),
                "Correo"        => $this->input->post("Correo"),
                "Observaciones" => trim($this->input->post("Observacion"))
            );
            
            if($this->input->post("id") == ""){
                if($this->proveedores_model->ExisteRif($parametros['Rif'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe un proveedor registrado con el mismo rif.",
                        "id"=>""));
                }else{
                    $respuesta = $this->proveedores_model->Insertar($parametros);
                    $insertado = $this->proveedores_model->Obtener();
                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha insertado Localizacion exitosamente",
                        "id"=>$insertado['pro_id']));
                }
            }else{
                if($this->proveedores_model->ExisteRif($parametros['Rif'],$parametros['idActual'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe un proveedor registrado con el mismo rif.",
                        "id"=>""));
                }else{
                    $respuesta = $this->proveedores_model->Actualizar($parametros);
                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha editado Localizacion exitosamente",
                        "id"=>$this->input->post("id")));
                }

            }
        }

        public function eliminar(){
            
            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }
            $this->ValidarPermiso();

            $eliminar = $this->proveedores_model->Eliminar($this->input->post("id"));
            $data = $this->FormatearRequest($this->proveedores_model->Obtener());
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

            $respuesta = $this->FormatearBusqueda($this->proveedores_model->Busqueda($busqueda,$ordenamiento,$inicio,$fin));

            echo json_encode(array("isValid"=>true,"Datos"=>$respuesta));
        }

        public function imprimir($id){
            $this->ValidarPermiso();
            $data['datos'] = $this->FormatearRequest($this->proveedores_model->Obtener($id));
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/repProveedores',$data);
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
                        .   "<td style='display:none;'>" . $elemento['pro_id'] . "</td>"
                        .   "<td style='display:none;'>" . $elemento['reg_nac_con'] . "</td>"
                        .   "<td style='display:none;'>" . $elemento['telefonos'] . "</td>"
                        .   "<td style='display:none;'>" . $elemento['correo'] . "</td>"
                        .   "<td style='display:none;'>" . $elemento['observaciones'] . "</td>"
                        .   "<td>" . $elemento['rif'] . "</td>"
                        .   "<td>" . $elemento['raz_soc'] . "</td>"
                        .   "<td>" . $elemento['direccion'] . "</td>"
                        ."</tr>";
                }
                
                $data['Listas'] = $htmlListas;
                $data['Registros'] = $registros;
            }

            return $data;
        }


    }


?>