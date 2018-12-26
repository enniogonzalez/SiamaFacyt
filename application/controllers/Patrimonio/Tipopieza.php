<?php

    class Tipopieza extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->model("Patrimonio/tipopieza_model",'tipopieza_model');
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
        }

        private function FormatearRequest($respuesta){

            $data = array(
                "tpi_id"        =>"",
                "nombre"        =>"",
                "observaciones" =>"",
            );

            if($respuesta)
                $data = $respuesta;
            return $data;
        }

        private function ValidarPermiso(){
            if(!$this->session->userdata("Permisos")['Mantenimiento']){
                show_404();
            }
        }
        
        public function view(){
            
            if(!$this->session->userdata("nombre")){
                redirect(site_url(''));
            }

            $this->ValidarPermiso();
            
            $data = $this->FormatearRequest($this->tipopieza_model->Obtener());

            $JsFile = "<script src=\"". base_url() . "assets/js/Patrimonio/TipoPieza.js\"></script>";
            
            $datafile['JsFile'] = $JsFile ;

            
            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');
            $ld = $this->listasdesplegables_model->Obtener('','COB-TIPOPI');

            $dataLD['OrdenarBusqueda'] = $this->liblistasdesplegables->FormatearListaDesplegable($ld);

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/Patrimonio/tipopieza',$data);
            $this->load->view('plantillas/7-footer');

        }

        public function guardar(){

            $this->ValidarPermiso();

            if(!$this->session->userdata("nombre") || $this->input->post("Nombre") == ""){
                redirect(site_url(''));
            }

            $parametros = array(
                "idActual"      => $this->input->post("id"),
                "Nombre"        => $this->input->post("Nombre"),
                "Observaciones" => trim($this->input->post("Observacion"))
            );
            
            if($this->input->post("id") == ""){
                if($this->tipopieza_model->ExisteNombre($parametros['Nombre'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe un Tipo de Pieza registrado con el mismo nombre.",
                        "id"=>""));
                }else{
                    $respuesta = $this->tipopieza_model->Insertar($parametros);
                    $insertado = $this->tipopieza_model->Obtener();
                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha insertado Tipo de Pieza exitosamente",
                        "id"=>$insertado['tpi_id']));
                }
            }else{
                if($this->tipopieza_model->ExisteNombre($parametros['Nombre'],$parametros['idActual'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe un Tipo de Pieza registrado con el mismo nombre.",
                        "id"=>""));
                }else{
                    $respuesta = $this->tipopieza_model->Actualizar($parametros);
                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha editado Tipo de Pieza exitosamente",
                        "id"=>$this->input->post("id")));
                }

            }
        }

        public function eliminar(){
            
            $this->ValidarPermiso();
            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }

            $eliminar = $this->tipopieza_model->Eliminar($this->input->post("id"));
            $data = $this->FormatearRequest($this->tipopieza_model->Obtener());
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
            $Condiciones = $this->input->post("Condiciones");
            
            $inicio = 1+$regXpag*($pagina-1);
            $fin = $regXpag*$pagina;
            $bien = "";
            $delBien = false;

            if(isset($Condiciones)){
                $delBien = $Condiciones['delBien'] == "true" ? true:false;
                $bien = $Condiciones['Bien'];
            }

            $data = array(
                "busqueda"      => $busqueda,
                "orden"         => $ordenamiento,
                "inicio"        => $inicio,
                "fin"           => $fin,
                "bien"          => $bien,
                "delBien"       => $delBien
            );

            $respuesta = $this->FormatearBusqueda($this->tipopieza_model->Busqueda($data));

            echo json_encode(array("isValid"=>true,"Datos"=>$respuesta));
        }

        public function busquedaDisponible(){

            if(!$this->session->userdata("nombre") || $this->input->post("Pagina") == ""){
                redirect(site_url(''));
            }

            $busqueda = $this->input->post("Busqueda") ;
            $pagina = (int) $this->input->post("Pagina") ;
            $regXpag = (int) $this->input->post("RegistrosPorPagina") ;
            $ordenamiento = $this->input->post("Orden") ;
            $Condiciones = $this->input->post("Condiciones");
            
            $inicio = 1+$regXpag*($pagina-1);
            $fin = $regXpag*$pagina;
            $bien = $Condiciones['Bien'];

            $data = array(
                "busqueda"      => $busqueda,
                "orden"         => $ordenamiento,
                "inicio"        => $inicio,
                "fin"           => $fin,
                "bien"          => $bien
            );

            $respuesta = $this->FormatearBusqueda($this->tipopieza_model->BusquedaDisponible($data));

            echo json_encode(array("isValid"=>true,"Datos"=>$respuesta));
        }

        public function imprimir($id){
            $data['datos'] = $this->FormatearImpresion($this->tipopieza_model->ObtenerInfoPDF($id));
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Formatos/formatoTipoPieza',$data);
        }

        private function FormatearImpresion($respuesta){

            $data = array(
                "tpi_id"        =>"",
                "nombre"        =>"",
                "ubicacion"     =>"",
                "tipo"          =>"",
                "cap_amp"       =>"",
                "nombrepadre"   =>"",
                "observaciones" => ""
            );

            if($respuesta)
                $data = $respuesta;

            return $data;
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
                        .   "<td style='display:none;'>" . $elemento['tpi_id'] . "</td>"
                        .   "<td>" . $elemento['nombre'] . "</td>"
                        .   "<td style='display:none;'>" . $elemento['observaciones'] . "</td>"
                        ."</tr>";
                }
                
                $data['Listas'] = $htmlListas;
                $data['Registros'] = $registros;
            }

            return $data;
        }

    }


?>