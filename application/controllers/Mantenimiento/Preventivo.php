<?php

    class Preventivo extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->model('Mantenimiento/preventivo_model' , 'preventivo_model');
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
        }

        private function FormatearRequest($respuesta){

            $data = array(
                "man_id"        =>"",
                "documento"     =>"",
                "bie_id"        =>"",
                "bie_nom"       =>"",
                "estatus"       =>"",
                "fec_ini"       =>"",
                "fec_fin"       =>"",
                "observaciones" =>"",
                "Tareas"        =>""
            );

            if($respuesta)
                $data = $respuesta;
            return $data;
        }

        public function view(){
            
            if(!$this->session->userdata("nombre")){
                redirect(site_url(''));
            }
            
            $data = $this->FormatearRequest($this->preventivo_model->Obtener());

            $JsFile =   "<script src=\"". base_url() . "assets/js/Mantenimiento/Preventivo/Preventivo.js\"></script>"
                    .   "<script src=\"". base_url() . "assets/js/Mantenimiento/Preventivo/TareaPreventiva.js\"></script>";

            $datafile['JsFile'] = $JsFile ;

            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');
            $ld = $this->listasdesplegables_model->Obtener('','COB-PREVEN');

            $listaBusquedaPlantilla = $this->listasdesplegables_model->Obtener('','COB-PLANTI');
            $listaBusquedaBien   = $this->listasdesplegables_model->Obtener('','COB-BIENES');
            $listaBusquedaPieza= $this->listasdesplegables_model->Obtener('','COB-PIEZAS');
            $listaBusquedaProveedor = $this->listasdesplegables_model->Obtener('','COB-PROVEE');
            $listaBusquedaUsuario= $this->listasdesplegables_model->Obtener('','COB-USUARI');

            
            $dataLD['OrdenarBusqueda'] = $this->liblistasdesplegables->FormatearListaDesplegable($ld);
            $data['listaBusquedaProveedor'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaProveedor);
            $data['listaBusquedaFormulario'] = $dataLD['OrdenarBusqueda'];
            $data['listaBusquedaPlantilla'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaPlantilla);
            $data['listaBusquedaBien'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaBien);
            $data['listaBusquedaPieza'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaPieza);
            $data['listaBusquedaUsuario'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaUsuario);

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/Mantenimiento/preventivo',$data);
            $this->load->view('plantillas/7-footer');

        }

        public function obtener(){

            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }
            $data = $this->FormatearRequest($this->preventivo_model->Obtener($this->input->post("id")));
            echo json_encode(array("isValid"=>true,"Datos"=>$data));
        }

        public function guardar(){

            if(!$this->session->userdata("nombre") || $this->input->post("Bien") == ""){
                redirect(site_url(''));
            }

            $parametros = array(
                "idActual"      => $this->input->post("id"),
                "Bie_Id"        => $this->input->post("Bien"),
                "Documento"     => $this->input->post("Documento"),
                "Fec_Ini"       => $this->input->post("Inicio"),
                "Fec_Fin"       => $this->input->post("Fin"),
                "Tareas"        => $this->input->post("Tareas"),
                "Estatus"       => $this->input->post("Estatus"),
                "Observaciones" => trim($this->input->post("Observacion"))
            );
            
            if($parametros['Estatus']  = "" || $parametros['Estatus'] == "Solicitado"){
                if($this->input->post("id") == ""){
                    if($this->preventivo_model->ExisteDocumento($parametros['Documento'])){
                        echo json_encode(array(
                            "isValid"=>false,
                            "Mensaje"=>"Ya existe un Mantenimiento Correctivo registrado con el mismo codigo Documento.",
                            "id"=>""));
                    }else{
                        $id = $this->preventivo_model->Insertar($parametros);
                        $Datos = $this->preventivo_model->Obtener($id);
    
                        echo json_encode(array(
                            "isValid"=>true,
                            "Mensaje"=>"Se ha guardado el Mantenimiento Correctivo exitosamente",
                            "id"=>$id,
                            "Datos"=>$Datos
                        ));
                    }
                }else{
                    if($this->preventivo_model->ExisteDocumento($parametros['Documento'],$parametros['idActual'])){
                        echo json_encode(array(
                            "isValid"=>false,
                            "Mensaje"=>"Ya existe un Mantenimiento Correctivo registrado con el mismo codigo Documento.",
                            "id"=>$this->input->post("id")));
                    }else{
                        $respuesta = $this->preventivo_model->Actualizar($parametros);
                        $Datos = $this->preventivo_model->Obtener($this->input->post("id"));
    
                        echo json_encode(array(
                            "isValid"=>true,
                            "Mensaje"=>"Se ha actualizado el Mantenimiento Correctivo exitosamente",
                            "id"=>$this->input->post("id"),
                            "Datos"=>$Datos
                        ));
                    }
                }
            }else{
                $this->preventivo_model->RealizarOperaciones($parametros);
                
                $Datos = $this->preventivo_model->Obtener($this->input->post("id"));
                echo json_encode(array(
                    "isValid"=>true,
                    "Mensaje"=>"Se ha actualizado el Mantenimiento Correctivo exitosamente",
                    "id"=>$this->input->post("id"),
                    "Datos"=>$Datos
                ));
            }
            

        }

        public function eliminar(){
            
            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }

            if($this->preventivo_model->PuedeEliminar($this->input->post("id"))){
                $eliminar = $this->preventivo_model->Eliminar($this->input->post("id"));
                $data = $this->FormatearRequest($this->preventivo_model->Obtener());
                echo json_encode(array("isValid"=>true,"Datos"=>$data));
            }else{
                $usuarios = $this->preventivo_model->ObtenerUsuarios($this->input->post("id"));
                $html = "<strong>Usuario Solicitante:</strong> " . $usuarios['cre_nom'];
                echo json_encode(array("isValid"=>false,
                    "Mensaje"=>"El Documento actual solo puede ser eliminado por la persona que lo ha solicitado.<br/><br/>" .$html
                ));
            }

        }

        public function busqueda(){

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

            $respuesta = $this->FormatearBusqueda($this->preventivo_model->Busqueda($busqueda,$ordenamiento,$inicio,$fin,$fec_ini,$fec_fin));

            echo json_encode(array("isValid"=>true,"Datos"=>$respuesta));

        }

        public function aprobar(){
            
            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }

            if($this->preventivo_model->PuedeAprobar($this->input->post("id"))){
                $aprobar = $this->preventivo_model->AprobarMantenimiento($this->input->post("id"));
            
                $Datos = $this->preventivo_model->Obtener($this->input->post("id"));
                echo json_encode(array("isValid"=>true,
                    "Mensaje"=>"Se ha aprobado correctamente el mantenimiento correctivo",
                    "Tipo" => $this->input->post("Tipo"),
                    "Datos"=>$Datos
                ));
            }else{
                
                $usuarios = $this->preventivo_model->ObtenerUsuarios($this->input->post("id"));
                $html = "<strong>Usuario Solicitante:</strong> " . $usuarios['cre_nom'];
                echo json_encode(array("isValid"=>false,
                    "Mensaje"=>"El Documento actual no puede ser aprobado por la misma persona que lo ha solicitado.<br><br>" . $html
                ));

            }

        }

        public function reversar(){
            
            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }

            if($this->preventivo_model->PuedeAprobar($this->input->post("id"))){
                $reversar = $this->preventivo_model->ReversarMantenimiento($this->input->post("id"));
                
                $Datos = $this->preventivo_model->Obtener($this->input->post("id"));
                echo json_encode(array("isValid"=>true,
                    "Mensaje"=>"Se ha aprobado correctamente el mantenimiento correctivo",
                    "Tipo" => $this->input->post("Tipo"),
                    "Datos"=>$Datos
                ));
            }else{
                $usuarios = $this->preventivo_model->ObtenerUsuarios($this->input->post("id"));
                $html = "<strong>Usuario Aprobador:</strong> " . $usuarios['apr_nom'];
                echo json_encode(array("isValid"=>false,
                    "Mensaje"=>"El Documento actual solo puede ser desaprobado por la misma persona que lo ha aprobado.<br><br>" . $html
                ));

            }

        }

        public function imprimir($id){

            $data['datos'] = $this->FormatearImpresion($this->preventivo_model->ObtenerInfoPDF($id));

            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/repManPre',$data);
        }

        private function FormatearImpresion($respuesta){

            $data = array(
                "documento"     =>"",
                "bie_nom"       =>"",
                "inv_uc"        =>"",
                "estatus"       =>"",
                "solicitante"   =>"",
                "aprobador"     =>"",
                "fec_cre"       =>"",
                "fec_apr"       =>"",
                "fec_ini"       =>"",
                "fec_fin"       =>"",
                "Tareas"        =>[],
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
                        .   "<td style='display:none;'>" . $elemento['man_id'] . "</td>"
                        .   "<td>" . $elemento['documento'] . "</td>"
                        .   "<td>" . $elemento['nombre'] . "</td>"
                        .   "<td>" . $elemento['estatus'] . "</td>"
                        .   "<td>" . $elemento['fec_ini'] . "</td>"
                        .   "<td>" . $elemento['fec_fin'] . "</td>"
                        ."</tr>";
                }
                
                $data['Listas'] = $htmlListas;
                $data['Registros'] = $registros;
            }

            return $data;
        }
    }

?>