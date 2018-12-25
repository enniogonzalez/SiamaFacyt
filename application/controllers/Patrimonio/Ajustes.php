<?php

    class Ajustes extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->model('Patrimonio/ajustes_model' , 'ajustes_model');
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
                        .   "<td style='display:none;'>" . $elemento['aju_id'] . "</td>"
                        .   "<td>" . $elemento['documento'] . "</td>"
                        .   "<td>" . $elemento['nombre'] . "</td>"
                        .   "<td>" . $elemento['estatus'] . "</td>"
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
                "aju_id"        =>"",
                "bie_nom"       =>"",
                "inv_uc"        =>"",
                "estatus"       =>"",
                "solicitante"   =>"",
                "fec_cre"       =>"",
                "fec_apr"   =>"",
                "aprobador"     =>"",
                "Agregados"     =>[],
                "Quitados"      =>[],
                "observaciones" => ""
            );

            if($respuesta)
                $data = $respuesta;

            return $data;
        }

        private function FormatearRequest($respuesta){

            $data = array(
                "aju_id"        =>"",
                "documento"     =>"",
                "bie_id"        =>"",
                "bie_nom"       =>"",
                "estatus"       =>"",
                "observaciones" =>"",
                "Quitados"      =>"",
                "Agregados"     =>"",
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

        public function aprobar(){
            
            $this->ValidarPermiso();

            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }

            if($this->ajustes_model->PuedeAprobar($this->input->post("id"))){
                $aprobar = $this->ajustes_model->AprobarAjuste($this->input->post("id"));
            
                $Datos = $this->ajustes_model->Obtener($this->input->post("id"));
                echo json_encode(array("isValid"=>true,
                    "Mensaje"=>"Se ha aprobado correctamente el ajuste",
                    "Tipo" => $this->input->post("Tipo"),
                    "Datos"=>$Datos
                ));
            }else{
                
                $usuarios = $this->ajustes_model->ObtenerUsuarios($this->input->post("id"));
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
            
            $inicio = 1+$regXpag*($pagina-1);
            $fin = $regXpag*$pagina;

            $respuesta = $this->FormatearBusqueda($this->ajustes_model->Busqueda($busqueda,$ordenamiento,$inicio,$fin));

            echo json_encode(array("isValid"=>true,"Datos"=>$respuesta));
        }

        public function eliminar(){
            
            $this->ValidarPermiso();

            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }

            if($this->ajustes_model->PuedeEliminar($this->input->post("id"))){
                $eliminar = $this->ajustes_model->Eliminar($this->input->post("id"));

                $data = $this->FormatearRequest($this->ajustes_model->Obtener());
                echo json_encode(array("isValid"=>true,"Datos"=>$data));
            }else{
                $usuarios = $this->ajustes_model->ObtenerUsuarios($this->input->post("id"));
                $html = "<strong>Usuario Solicitante:</strong> " . $usuarios['cre_nom'];
                echo json_encode(array("isValid"=>false,
                    "Mensaje"=>"El Documento actual solo puede ser eliminado por la persona que lo ha solicitado.<br/><br/>" .$html
                ));
            }

        }

        public function guardar(){

            $this->ValidarPermiso();

            if(!$this->session->userdata("nombre") || $this->input->post("Bien") == ""){
                redirect(site_url(''));
            }

            $parametros = array(
                "aju_id"      => $this->input->post("id"),
                "Bie_Id"        => $this->input->post("Bien"),
                "Documento"     => $this->input->post("Documento"),
                "Agregados"     => $this->input->post("Agregados"),
                "Quitados"      => $this->input->post("Quitados"),
                "Estatus"       => $this->input->post("Estatus"),
                "Observaciones" => trim($this->input->post("Observacion"))
            );
            
            if($this->input->post("id") == ""){
                if($this->ajustes_model->ExisteDocumento($parametros['Documento'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe un Ajuste registrado con el mismo codigo Documento.",
                        "id"=>""));
                }else{
                    $id = $this->ajustes_model->Insertar($parametros);
                    $Datos = $this->ajustes_model->Obtener($id);

                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha guardado el Ajuste exitosamente",
                        "id"=>$id,
                        "Datos"=>$Datos
                    ));
                }
            }else{
                if($this->ajustes_model->ExisteDocumento($parametros['Documento'],$parametros['aju_id'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe un Ajuste registrado con el mismo codigo Documento.",
                        "id"=>$this->input->post("id")));
                }else{
                    $respuesta = $this->ajustes_model->Actualizar($parametros);
                    $Datos = $this->ajustes_model->Obtener($this->input->post("id"));

                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha actualizado el Ajuste exitosamente",
                        "id"=>$this->input->post("id"),
                        "Datos"=>$Datos
                    ));
                }
            }
            

        }

        public function imprimir($id){
            $this->ValidarPermiso();
            $data['datos'] = $this->FormatearImpresion($this->ajustes_model->ObtenerInfoPDF($id));
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/repAjustes',$data);
        }

        public function obtener(){

            $this->ValidarPermiso();

            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }
            $data = $this->FormatearRequest($this->ajustes_model->Obtener($this->input->post("id")));
            echo json_encode(array("isValid"=>true,"Datos"=>$data,"Caso" =>  $this->input->post("Caso") ));

        }
        
        public function view(){
            
            if(!$this->session->userdata("nombre")){
                redirect(site_url(''));
            }

            $this->ValidarPermiso();
            
            $data = $this->FormatearRequest($this->ajustes_model->Obtener());

            $JsFile =   "<script src=\"". base_url() . "assets/js/Patrimonio/Ajustes/Ajustes.js\"></script>"
                    .   "<script src=\"". base_url() . "assets/js/Patrimonio/Ajustes/Agregar.js\"></script>"
                    .   "<script src=\"". base_url() . "assets/js/Patrimonio/Ajustes/Quitar.js\"></script>";

            $datafile['JsFile'] = $JsFile ;

            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');
            $ld = $this->listasdesplegables_model->Obtener('','COB-AJUSTE');

            $listaBusquedaBien   = $this->listasdesplegables_model->Obtener('','COB-BIENES');
            $listaBusquedaPieza= $this->listasdesplegables_model->Obtener('','COB-PIEZAS');

            
            $dataLD['OrdenarBusqueda'] = $this->liblistasdesplegables->FormatearListaDesplegable($ld);
            $data['listaBusquedaFormulario'] = $dataLD['OrdenarBusqueda'];
            $data['listaBusquedaBien'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaBien);
            $data['listaBusquedaPieza'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaPieza);

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/Patrimonio/ajustes',$data);
            $this->load->view('plantillas/7-footer');

        }

    }


?>