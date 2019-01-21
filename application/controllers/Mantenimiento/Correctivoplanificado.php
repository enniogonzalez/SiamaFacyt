<?php

    class Correctivoplanificado extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->model('Mantenimiento/correctivoplanificado_model' , 'correctivoplanificado_model');
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
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
                        .   "<td style='display:none;'>" . $elemento['cpl_id'] . "</td>"
                        .   "<td>" . $elemento['documento'] . "</td>"
                        .   "<td>" . $elemento['estatus'] . "</td>"
                        .   "<td>" . $elemento['origen'] . ": " . $elemento['ori_doc'] . "</td>"
                        .   "<td style='display:none;'>" . $elemento['bie_id'] . "</td>"
                        .   "<td>" . $elemento['bie_nom'] . "</td>"
                        .   "<td>" . $elemento['fec_eje'] . "</td>"
                        ."</tr>";
                }
                
                $data['Listas'] = $htmlListas;
                $data['Registros'] = $registros;
            }

            return $data;
        }

        private function FormatearImpresion($respuesta){

            $data = array(
                "documento"     =>"",
                "cpl_id"        =>"",
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

        private function FormatearRequest($respuesta){

            $data = array(
                "cpl_id"        =>"",
                "documento"     =>"",
                "bie_nom"       =>"",
                "bie_id"       =>"",
                "estatus"       =>"",
                "fec_eje"       =>"",
                "man_id"        =>"",
                "man_doc"       =>"",
                "mco_id"        =>"",
                "mco_doc"       =>"",
                "origen"        =>"",
                "observaciones" =>"",
                "Piezas"        =>"",
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

        public function aprobar(){
            
            $this->ValidarPermiso();

            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }

            if($this->correctivoplanificado_model->PuedeAprobar($this->input->post("id"))){
                $aprobar = $this->correctivoplanificado_model->AprobarMantenimiento($this->input->post("id"));
            
                $Datos = $this->correctivoplanificado_model->Obtener($this->input->post("id"));
                echo json_encode(array("isValid"=>true,
                    "Mensaje"=>"Se ha aprobado correctamente el Mantenimiento Correctivo Planificado",
                    "Tipo" => $this->input->post("Tipo"),
                    "Datos"=>$Datos
                ));
            }else{
                
                $usuarios = $this->correctivoplanificado_model->ObtenerUsuarios($this->input->post("id"));
                $html = "<strong>Usuario Solicitante:</strong> " . $usuarios['cre_nom'];
                echo json_encode(array("isValid"=>false,
                    "Mensaje"=>"El Documento actual no puede ser aprobado por la misma persona que lo ha solicitado.<br><br>" . $html
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

            $fec_ini = $this->input->post("Fec_Ini");
            $fec_fin = $this->input->post("Fec_Fin");
            
            $inicio = 1+$regXpag*($pagina-1);
            $fin = $regXpag*$pagina;
            $Condiciones = $this->input->post("Condiciones");
            $SoloAprobados = false;

            if(isset($Condiciones)){
                $SoloAprobados = $Condiciones['SoloAprobados'] == "true" ? true:false;
            }
            
            $datos = array(
                "busqueda"          => $busqueda,
                "orden"             => $ordenamiento,
                "inicio"            => $inicio,
                "fin"               => $fin,
                "fec_ini"           => $fec_ini,
                "SoloAprobados"     => $SoloAprobados,
                "fec_fin"           => $fec_fin,
            );
            
            $respuesta = $this->FormatearBusqueda($this->correctivoplanificado_model->Busqueda($datos));

            echo json_encode(array("isValid"=>true,"Datos"=>$respuesta));
        }

        public function eliminar(){
            
            $this->ValidarPermiso();

            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }

            if($this->correctivoplanificado_model->PuedeEliminar($this->input->post("id"))){
                $eliminar = $this->correctivoplanificado_model->Eliminar($this->input->post("id"));

                $data = $this->FormatearRequest($this->correctivoplanificado_model->Obtener());
                echo json_encode(array("isValid"=>true,"Datos"=>$data));
            }else{
                $usuarios = $this->correctivoplanificado_model->ObtenerUsuarios($this->input->post("id"));
                $html = "<strong>Usuario Solicitante:</strong> " . $usuarios['cre_nom'];
                echo json_encode(array("isValid"=>false,
                    "Mensaje"=>"El Documento actual solo puede ser eliminado por la persona que lo ha solicitado.<br/><br/>" .$html
                ));
            }

        }

        public function guardar(){

            $this->ValidarPermiso();

            if(!$this->session->userdata("nombre") || $this->input->post("fec_eje") == ""){
                redirect(site_url(''));
            }

            $parametros = array(
                "cpl_id"      => $this->input->post("id"),
                "Documento"     => $this->input->post("Documento"),
                "mco_id"        => $this->input->post("Correctivo"),
                "man_id"        => $this->input->post("Preventivo"),
                "fec_eje"       => $this->input->post("fec_eje"),
                "PiezaDAs"      => $this->input->post("PiezaDAs"),
                "Observaciones" => trim($this->input->post("Observacion"))
            );
            
            if($this->input->post("id") == ""){
                if($this->correctivoplanificado_model->ExisteDocumento($parametros['Documento'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe un Cambio de Estatus registrado con el mismo Documento.",
                        "id"=>""));
                }else{
                    $id = $this->correctivoplanificado_model->Insertar($parametros);
                    $Datos = $this->correctivoplanificado_model->Obtener($id);

                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha guardado el Cambio de Estatus exitosamente",
                        "id"=>$id,
                        "Datos"=>$Datos
                    ));
                }
            }else{
                if($this->correctivoplanificado_model->ExisteDocumento($parametros['Documento'],$parametros['cpl_id'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe un Cambio de Estatus registrado con el mismo Documento.",
                        "id"=>$this->input->post("id")));
                }else{
                    $respuesta = $this->correctivoplanificado_model->Actualizar($parametros);
                    $Datos = $this->correctivoplanificado_model->Obtener($this->input->post("id"));

                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha actualizado el Cambio de Estatus exitosamente",
                        "id"=>$this->input->post("id"),
                        "Datos"=>$Datos
                    ));
                }
            }
            

        }

        public function imprimir($id){
            $this->ValidarPermiso();
            $data['datos'] = $this->FormatearImpresion($this->correctivoplanificado_model->ObtenerInfoPDF($id));
            // echo json_encode($data);
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Formatos/formatoManCorPla',$data);
        }

        public function obtener(){

            $this->ValidarPermiso();

            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }
            $data = $this->FormatearRequest($this->correctivoplanificado_model->Obtener($this->input->post("id")));
            echo json_encode(array("isValid"=>true,"Datos"=>$data,"Caso" =>  $this->input->post("Caso") ));

        }

        public function view(){
            
            if(!$this->session->userdata("nombre")){
                redirect(site_url(''));
            }

            $this->ValidarPermiso();
            
            $data = $this->FormatearRequest($this->correctivoplanificado_model->Obtener());

            $JsFile =   "<script src=\"". base_url() . "assets/js/Mantenimiento/CorrectivoPlanificado/CorrectivoPlanificado.js\"></script>"
                    .   "<script src=\"". base_url() . "assets/js/Mantenimiento/CorrectivoPlanificado/PiezasCorrectivo.js\"></script>";

            $datafile['JsFile'] = $JsFile ;

            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');
            $ld = $this->listasdesplegables_model->Obtener('','COB-CORPLA');

            $listaBusquedaCorrectivo = $this->listasdesplegables_model->Obtener('','COB-CORREC');
            $listaBusquedaPreventivo = $this->listasdesplegables_model->Obtener('','COB-PREVEN');
            $listaBusquedaPieza = $this->listasdesplegables_model->Obtener('','COB-PIEZAS');
            $listaBusquedaFalla = $this->listasdesplegables_model->Obtener('','COB-FALLAS');

            
            $dataLD['OrdenarBusqueda'] = $this->liblistasdesplegables->FormatearListaDesplegable($ld);
            $data['listaBusquedaFormulario'] = $dataLD['OrdenarBusqueda'];
            $data['listaBusquedaCorrectivo'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaCorrectivo);
            $data['listaBusquedaPreventivo'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaPreventivo);
            $data['listaBusquedaPieza'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaPieza);
            $data['listaBusquedaFalla'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaFalla);

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/Mantenimiento/correctivoplanificado',$data);
            $this->load->view('plantillas/7-footer');

        }
    }


?>