<?php

    class Cambiosestatus extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->model('Patrimonio/cambiosestatus_model' , 'cambiosestatus_model');
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
        }

        private function FormatearRequest($respuesta){

            $data = array(
                "cam_id"        =>"",
                "documento"     =>"",
                "bie_id"        =>"",
                "bie_nom"       =>"",
                "doc_estatus"   =>"",
                "bie_estatus"   =>"",
                "observaciones" =>"",
                "PiezaCEs"     =>"",
            );

            if($respuesta)
                $data = $respuesta;
            return $data;
        }

        private function ValidarPermiso(){
            if(!$this->session->userdata("Permisos")['Patrimonio']){
                show_404();
            }
        } 

        public function view(){
            
            if(!$this->session->userdata("nombre")){
                redirect(site_url(''));
            }

            $this->ValidarPermiso();
            
            $data = $this->FormatearRequest($this->cambiosestatus_model->Obtener());

            $JsFile =   "<script src=\"". base_url() . "assets/js/Patrimonio/CambiosEstatus/CambiosEstatus.js\"></script>"
                    .   "<script src=\"". base_url() . "assets/js/Patrimonio/CambiosEstatus/PiezasEstatus.js\"></script>";

            $datafile['JsFile'] = $JsFile ;

            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');
            $ld = $this->listasdesplegables_model->Obtener('','COB-CAMBIO');

            $listaBusquedaBien   = $this->listasdesplegables_model->Obtener('','COB-BIENES');
            $listaBusquedaPieza= $this->listasdesplegables_model->Obtener('','COB-PIEZAS');
            $listaBusquedaFalla= $this->listasdesplegables_model->Obtener('','COB-FALLAS');

            
            $dataLD['OrdenarBusqueda'] = $this->liblistasdesplegables->FormatearListaDesplegable($ld);
            $data['listaBusquedaFormulario'] = $dataLD['OrdenarBusqueda'];
            $data['listaBusquedaBien'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaBien);
            $data['listaBusquedaPieza'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaPieza);
            $data['listaBusquedaFalla'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaFalla);

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/Patrimonio/cambiosestatus',$data);
            $this->load->view('plantillas/7-footer');

        }

        public function obtener(){

            $this->ValidarPermiso();

            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }
            $data = $this->FormatearRequest($this->cambiosestatus_model->Obtener($this->input->post("id")));
            echo json_encode(array("isValid"=>true,"Datos"=>$data,"Caso" =>  $this->input->post("Caso") ));

        }

        public function guardar(){

            $this->ValidarPermiso();

            if(!$this->session->userdata("nombre") || $this->input->post("Bien") == ""){
                redirect(site_url(''));
            }

            $parametros = array(
                "idActual"      => $this->input->post("id"),
                "Bie_Id"        => $this->input->post("Bien"),
                "Documento"     => $this->input->post("Documento"),
                "PiezaCEs"      => $this->input->post("PiezaCEs"),
                "Bie_estatus"   => $this->input->post("Bie_estatus"),
                "Observaciones" => trim($this->input->post("Observacion"))
            );
            
            if($this->input->post("id") == ""){
                if($this->cambiosestatus_model->ExisteDocumento($parametros['Documento'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe un Cambio de Estatus registrado con el mismo Documento.",
                        "id"=>""));
                }else{
                    $id = $this->cambiosestatus_model->Insertar($parametros);
                    $Datos = $this->cambiosestatus_model->Obtener($id);

                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha guardado el Cambio de Estatus exitosamente",
                        "id"=>$id,
                        "Datos"=>$Datos
                    ));
                }
            }else{
                if($this->cambiosestatus_model->ExisteDocumento($parametros['Documento'],$parametros['idActual'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe un Cambio de Estatus registrado con el mismo Documento.",
                        "id"=>$this->input->post("id")));
                }else{
                    $respuesta = $this->cambiosestatus_model->Actualizar($parametros);
                    $Datos = $this->cambiosestatus_model->Obtener($this->input->post("id"));

                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha actualizado el Cambio de Estatus exitosamente",
                        "id"=>$this->input->post("id"),
                        "Datos"=>$Datos
                    ));
                }
            }
            

        }

        public function eliminar(){
            
            $this->ValidarPermiso();

            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }

            if($this->cambiosestatus_model->PuedeEliminar($this->input->post("id"))){
                $eliminar = $this->cambiosestatus_model->Eliminar($this->input->post("id"));

                $data = $this->FormatearRequest($this->cambiosestatus_model->Obtener());
                echo json_encode(array("isValid"=>true,"Datos"=>$data));
            }else{
                $usuarios = $this->cambiosestatus_model->ObtenerUsuarios($this->input->post("id"));
                $html = "<strong>Usuario Solicitante:</strong> " . $usuarios['cre_nom'];
                echo json_encode(array("isValid"=>false,
                    "Mensaje"=>"El Documento actual solo puede ser eliminado por la persona que lo ha solicitado.<br/><br/>" .$html
                ));
            }

        }

        public function busqueda(){

            $this->ValidarPermiso();

            if(!$this->session->userdata("nombre") || $this->input->post("Pagina") == ""){
                redirect(site_url(''));
            }

            $busqueda = $this->input->post("Busqueda") ;
            $pagina = (int) $this->input->post("Pagina") ;
            $regXpag = (int) $this->input->post("RegistrosPorPagina") ;
            $ordenamiento = $this->input->post("Orden") ;
            
            $inicio = 1+$regXpag*($pagina-1);
            $fin = $regXpag*$pagina;

            $respuesta = $this->FormatearBusqueda($this->cambiosestatus_model->Busqueda($busqueda,$ordenamiento,$inicio,$fin));

            echo json_encode(array("isValid"=>true,"Datos"=>$respuesta));
        }

        public function aprobar(){
            
            $this->ValidarPermiso();

            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }

            if($this->cambiosestatus_model->PuedeAprobar($this->input->post("id"))){
                $aprobar = $this->cambiosestatus_model->AprobarCambioEstatus($this->input->post("id"));
            
                $Datos = $this->cambiosestatus_model->Obtener($this->input->post("id"));
                echo json_encode(array("isValid"=>true,
                    "Mensaje"=>"Se ha aprobado correctamente el cambio de ajuste",
                    "Tipo" => $this->input->post("Tipo"),
                    "Datos"=>$Datos
                ));
            }else{
                
                $usuarios = $this->cambiosestatus_model->ObtenerUsuarios($this->input->post("id"));
                $html = "<strong>Usuario Solicitante:</strong> " . $usuarios['cre_nom'];
                echo json_encode(array("isValid"=>false,
                    "Mensaje"=>"El Documento actual no puede ser aprobado por la misma persona que lo ha solicitado.<br><br>" . $html
                ));

            }

        }

        public function imprimir($id){
            $this->ValidarPermiso();
            $data['datos'] = $this->FormatearImpresion($this->cambiosestatus_model->ObtenerInfoPDF($id));
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/repCambiosEstatus',$data);
        }

        private function FormatearImpresion($respuesta){

            $data = array(
                "documento"     =>"",
                "cam_id"        =>"",
                "bie_nom"       =>"",
                "inv_uc"        =>"",
                "doc_estatus"   =>"",
                "bie_estatus"   =>"",
                "solicitante"   =>"",
                "fec_cre"       =>"",
                "fec_apr"       =>"",
                "aprobador"     =>"",
                "PiezaCEs"      =>[],
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
                        .   "<td style='display:none;'>" . $elemento['cam_id'] . "</td>"
                        .   "<td>" . $elemento['documento'] . "</td>"
                        .   "<td>" . $elemento['doc_estatus'] . "</td>"
                        .   "<td>" . $elemento['nombre'] . "</td>"
                        .   "<td>" . $elemento['bie_estatus'] . "</td>"
                        ."</tr>";
                }
                
                $data['Listas'] = $htmlListas;
                $data['Registros'] = $registros;
            }

            return $data;
        }
    }


?>