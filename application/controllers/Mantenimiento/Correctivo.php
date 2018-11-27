<?php

    class Correctivo extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->model('Mantenimiento/correctivo_model' , 'correctivo_model');
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
        }

        private function FormatearRequest($respuesta){

            $data = array(
                "mco_id"        =>"",
                "documento"     =>"",
                "bie_id"        =>"",
                "bie_nom"       =>"",
                "estatus"       =>"",
                "fec_ini"       =>"",
                "fec_fin"       =>"",
                "observaciones" =>"",
                "Cambios"       =>"",
                "Reparaciones"  =>"",
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
            
            $data = $this->FormatearRequest($this->correctivo_model->Obtener());

            $JsFile =   "<script src=\"". base_url() . "assets/js/Mantenimiento/Correctivo/Correctivo.js\"></script>"
                    .   "<script src=\"". base_url() . "assets/js/Mantenimiento/Correctivo/Cambio.js\"></script>"
                    .   "<script src=\"". base_url() . "assets/js/Mantenimiento/Correctivo/Reparacion.js\"></script>";

            $datafile['JsFile'] = $JsFile ;

            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');
            $ld = $this->listasdesplegables_model->Obtener('','COB-CORREC');

            $listaBusquedaProveedor = $this->listasdesplegables_model->Obtener('','COB-PROVEE');
            $listaBusquedaBien   = $this->listasdesplegables_model->Obtener('','COB-BIENES');
            $listaBusquedaUsuario= $this->listasdesplegables_model->Obtener('','COB-USUARI');
            $listaBusquedaPieza= $this->listasdesplegables_model->Obtener('','COB-PIEZAS');
            $listaBusquedaFalla= $this->listasdesplegables_model->Obtener('','COB-FALLAS');

            
            $dataLD['OrdenarBusqueda'] = $this->liblistasdesplegables->FormatearListaDesplegable($ld);
            $data['listaBusquedaFormulario'] = $dataLD['OrdenarBusqueda'];
            $data['listaBusquedaProveedor'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaProveedor);
            $data['listaBusquedaBien'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaBien);
            $data['listaBusquedaUsuario'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaUsuario);
            $data['listaBusquedaPieza'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaPieza);
            $data['listaBusquedaFalla'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaFalla);

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();


            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/Mantenimiento/correctivo',$data);
            $this->load->view('plantillas/7-footer');

        }

        public function obtener(){
            
            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }
            
            $this->ValidarPermiso();

            $data = $this->FormatearRequest($this->correctivo_model->Obtener($this->input->post("id")));
            echo json_encode(array("isValid"=>true,"Datos"=>$data,"Caso" =>  $this->input->post("Caso") ));

        }

        public function guardar(){

            if(!$this->session->userdata("nombre") || $this->input->post("Bien") == ""){
                redirect(site_url(''));
            }

            $this->ValidarPermiso();


            $parametros = array(
                "idActual"      => $this->input->post("id"),
                "Bie_Id"        => $this->input->post("Bien"),
                "Documento"     => $this->input->post("Documento"),
                "Fec_Ini"       => $this->input->post("Inicio"),
                "Fec_Fin"       => $this->input->post("Fin"),
                "Cambios"       => $this->input->post("Cambios"),
                "Reparaciones"  => $this->input->post("Reparaciones"),
                "Estatus"       => $this->input->post("Estatus"),
                "Observaciones" => trim($this->input->post("Observacion"))
            );
            
            if($parametros['Estatus']  = "" || $parametros['Estatus'] == "Solicitado"){
                if($this->input->post("id") == ""){
                    if($this->correctivo_model->ExisteDocumento($parametros['Documento'])){
                        echo json_encode(array(
                            "isValid"=>false,
                            "Mensaje"=>"Ya existe un Mantenimiento Correctivo registrado con el mismo codigo Documento.",
                            "id"=>""));
                    }else{
                        $id = $this->correctivo_model->Insertar($parametros);
                        $Datos = $this->correctivo_model->Obtener($id);
    
                        echo json_encode(array(
                            "isValid"=>true,
                            "Mensaje"=>"Se ha guardado el Mantenimiento Correctivo exitosamente",
                            "id"=>$id,
                            "Datos"=>$Datos
                        ));
                    }
                }else{
                    if($this->correctivo_model->ExisteDocumento($parametros['Documento'],$parametros['idActual'])){
                        echo json_encode(array(
                            "isValid"=>false,
                            "Mensaje"=>"Ya existe un Mantenimiento Correctivo registrado con el mismo codigo Documento.",
                            "id"=>$this->input->post("id")));
                    }else{
                        $respuesta = $this->correctivo_model->Actualizar($parametros);
                        $Datos = $this->correctivo_model->Obtener($this->input->post("id"));
    
                        echo json_encode(array(
                            "isValid"=>true,
                            "Mensaje"=>"Se ha actualizado el Mantenimiento Correctivo exitosamente",
                            "id"=>$this->input->post("id"),
                            "Datos"=>$Datos
                        ));
                    }
                }
            }else{
                $this->correctivo_model->RealizarOperaciones($parametros);
                
                $Datos = $this->correctivo_model->Obtener($this->input->post("id"));
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

            $this->ValidarPermiso();

            if($this->correctivo_model->PuedeEliminar($this->input->post("id"))){
                $eliminar = $this->correctivo_model->Eliminar($this->input->post("id"));
                $data = $this->FormatearRequest($this->correctivo_model->Obtener());
                echo json_encode(array("isValid"=>true,"Datos"=>$data));
            }else{
                $usuarios = $this->correctivo_model->ObtenerUsuarios($this->input->post("id"));
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

            $this->ValidarPermiso();

            $busqueda = $this->input->post("Busqueda") ;
            $pagina = (int) $this->input->post("Pagina") ;
            $regXpag = (int) $this->input->post("RegistrosPorPagina") ;
            $ordenamiento = $this->input->post("Orden") ;
            
            $inicio = 1+$regXpag*($pagina-1);
            $fin = $regXpag*$pagina;

            $respuesta = $this->FormatearBusqueda($this->correctivo_model->Busqueda($busqueda,$ordenamiento,$inicio,$fin));

            echo json_encode(array("isValid"=>true,"Datos"=>$respuesta));
        }

        public function aprobar(){
            
            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }

            $this->ValidarPermiso();

            if($this->correctivo_model->PuedeAprobar($this->input->post("id"))){
                $aprobar = $this->correctivo_model->AprobarMantenimiento($this->input->post("id"));
            
                $Datos = $this->correctivo_model->Obtener($this->input->post("id"));
                echo json_encode(array("isValid"=>true,
                    "Mensaje"=>"Se ha aprobado correctamente el mantenimiento correctivo",
                    "Tipo" => $this->input->post("Tipo"),
                    "Datos"=>$Datos
                ));
            }else{
                
                $usuarios = $this->correctivo_model->ObtenerUsuarios($this->input->post("id"));
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

            $this->ValidarPermiso();

            if($this->correctivo_model->PuedeAprobar($this->input->post("id"))){
                $reversar = $this->correctivo_model->ReversarMantenimiento($this->input->post("id"));
                
                $Datos = $this->correctivo_model->Obtener($this->input->post("id"));
                echo json_encode(array("isValid"=>true,
                    "Mensaje"=>"Se ha aprobado correctamente el mantenimiento correctivo",
                    "Tipo" => $this->input->post("Tipo"),
                    "Datos"=>$Datos
                ));
            }else{
                $usuarios = $this->correctivo_model->ObtenerUsuarios($this->input->post("id"));
                $html = "<strong>Usuario Aprobador:</strong> " . $usuarios['apr_nom'];
                echo json_encode(array("isValid"=>false,
                    "Mensaje"=>"El Documento actual solo puede ser desaprobado por la misma persona que lo ha aprobado.<br><br>" . $html
                ));

            }

        }

        public function imprimir($id){
            $this->ValidarPermiso();
            $data['datos'] = $this->FormatearImpresion($this->correctivo_model->ObtenerInfoPDF($id));
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/repManCor',$data);
        }

        private function FormatearImpresion($respuesta){

            $data = array(
                "documento"     =>"",
                "mco_id"        =>"",
                "bie_nom"       =>"",
                "inv_uc"        =>"",
                "estatus"       =>"",
                "solicitante"   =>"",
                "aprobador"     =>"",
                "fec_ini"       =>"",
                "fec_fin"       =>"",
                "fec_cre"       =>"",
                "fec_apr"       =>"",
                "Cambios"       =>[],
                "Reparaciones"  =>[],
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
                        .   "<td style='display:none;'>" . $elemento['mco_id'] . "</td>"
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