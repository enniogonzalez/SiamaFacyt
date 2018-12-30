<?php

    class Plantilla extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->model('Mantenimiento/plantilla_model' , 'plantilla_model');
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
        }

        /************************************/
        /*              Privadas            */
        /************************************/

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
                        .   "<td style='display:none;'>" . $elemento['plm_id'] . "</td>"
                        .   "<td>" . $elemento['documento'] . "</td>"
                        .   "<td>" . $elemento['estatus'] . "</td>"
                        .   "<td>" . $elemento['nombre'] . "</td>"
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
                "bie_nom"       =>"",
                "inv_uc"        =>"",
                "estatus"       =>"",
                "solicitante"   =>"",
                "aprobador"     =>"",
                "fec_cre"       =>"",
                "fec_apr"       =>"",
                "Tareas"        =>[],
                "observaciones" => ""
            );

            if($respuesta)
                $data = $respuesta;

            return $data;
        }

        private function FormatearRequest($respuesta){

            $data = array(
                "plm_id"        =>"",
                "documento"     =>"",
                "bie_id"        =>"",
                "bie_nom"       =>"",
                "estatus"       =>"",
                "frecuencia"    =>"",
                "fec_ult"       =>"",
                "observaciones" =>"",
                "Tareas"        =>""
            );

            if($respuesta)
                $data = $respuesta;
            return $data;
        }

        private function FormatearRequestMantenimiento($respuesta){

            $data = array(
                "plm_id"        =>"",
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

        private function ValidarPermiso(){
            if(!$this->session->userdata("Permisos")['Mantenimiento']){
                show_404();
            }
        } 


        /************************************/
        /*              Publicas            */
        /************************************/

        public function aprobar(){
            
            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }
            $this->ValidarPermiso();

            if($this->plantilla_model->PuedeAprobar($this->input->post("id"))){
                $aprobar = $this->plantilla_model->AprobarMantenimiento($this->input->post("id"));
            
                $Datos = $this->plantilla_model->Obtener($this->input->post("id"));
                echo json_encode(array("isValid"=>true,
                    "Mensaje"=>"Se ha aprobado correctamente el mantenimiento correctivo",
                    "Tipo" => $this->input->post("Tipo"),
                    "Datos"=>$Datos
                ));
            }else{
                
                $usuarios = $this->plantilla_model->ObtenerUsuarios($this->input->post("id"));
                $html = "<strong>Usuario Solicitante:</strong> " . $usuarios['cre_nom'];
                echo json_encode(array("isValid"=>false,
                    "Mensaje"=>"El Documento actual no puede ser aprobado por la misma persona que lo ha solicitado.<br><br>" . $html
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

            $Condiciones = $this->input->post("Condiciones");
            $disponibles = false;
            if(isset($Condiciones)){
                $disponibles = $Condiciones['Disponibles'] == "true" ? true:false;
            }

            $respuesta = $this->FormatearBusqueda($this->plantilla_model->Busqueda($busqueda,$ordenamiento,$inicio,$fin,$disponibles));

            echo json_encode(array("isValid"=>true,"Datos"=>$respuesta));
        }

        public function busquedaBien(){

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

            $Condiciones = $this->input->post("Condiciones");
            $disponibles = false;
            if(isset($Condiciones)){
                $disponibles = $Condiciones['Disponibles'] == "true" ? true:false;
            }

            $respuesta = $this->FormatearBusqueda($this->plantilla_model->Busqueda($busqueda,$ordenamiento,$inicio,$fin,$disponibles));

            echo json_encode(array("isValid"=>true,"Datos"=>$respuesta));
        }

        public function eliminar(){
            
            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }
            $this->ValidarPermiso();

            if($this->plantilla_model->PuedeEliminar($this->input->post("id"))){
                $eliminar = $this->plantilla_model->Eliminar($this->input->post("id"));
                $data = $this->FormatearRequest($this->plantilla_model->Obtener());
                echo json_encode(array("isValid"=>true,"Datos"=>$data));
            }else{
                $usuarios = $this->plantilla_model->ObtenerUsuarios($this->input->post("id"));
                $html = "<strong>Usuario Solicitante:</strong> " . $usuarios['cre_nom'];
                echo json_encode(array("isValid"=>false,
                    "Mensaje"=>"El Documento actual solo puede ser eliminado por la persona que lo ha solicitado.<br/><br/>" .$html
                ));
            }

        }
        
        public function guardar(){

            if(!$this->session->userdata("nombre") || $this->input->post("Bien") == ""){
                redirect(site_url(''));
            }
            $this->ValidarPermiso();

            $parametros = array(
                "plm_id"      => $this->input->post("id"),
                "Bie_Id"        => $this->input->post("Bien"),
                "Documento"     => $this->input->post("Documento"),
                "Frecuencia"    => $this->input->post("Frecuencia"),
                "Fec_Ult"       => $this->input->post("UltMan"),
                "Tareas"        => $this->input->post("Tareas"),
                "Estatus"       => $this->input->post("Estatus"),
                "Observaciones" => trim($this->input->post("Observacion"))
            );
            
            if($parametros['Estatus']  = "" || $parametros['Estatus'] == "Solicitado"){
                if($this->input->post("id") == ""){
                    if($this->plantilla_model->ExisteDocumento($parametros['Documento'])){
                        echo json_encode(array(
                            "isValid"=>false,
                            "Mensaje"=>"Ya existe una Plantilla de Mantenimiento registrada con el mismo codigo Documento.",
                            "id"=>""));
                    }else{
                        $id = $this->plantilla_model->Insertar($parametros);
                        $Datos = $this->plantilla_model->Obtener($id);
    
                        echo json_encode(array(
                            "isValid"=>true,
                            "Mensaje"=>"Se ha guardado la Plantilla de Mantenimiento",
                            "id"=>$id,
                            "Datos"=>$Datos
                        ));
                    }
                }else{
                    if($this->plantilla_model->ExisteDocumento($parametros['Documento'],$parametros['plm_id'])){
                        echo json_encode(array(
                            "isValid"=>false,
                            "Mensaje"=>"Ya existe una Plantilla de Mantenimiento registrada con el mismo codigo Documento.",
                            "id"=>$this->input->post("id")));
                    }else{
                        $respuesta = $this->plantilla_model->Actualizar($parametros);
                        $Datos = $this->plantilla_model->Obtener($this->input->post("id"));
    
                        echo json_encode(array(
                            "isValid"=>true,
                            "Mensaje"=>"Se ha actualizado la plantilla de mantenimiento exitosamente",
                            "id"=>$this->input->post("id"),
                            "Datos"=>$Datos
                        ));
                    }
                }
            }
            

        }

        public function imprimir($id){
            $this->ValidarPermiso();
            $data['datos'] = $this->FormatearImpresion($this->plantilla_model->ObtenerInfoPDF($id));
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/repPlaMan',$data);
        }

        public function obtener(){

            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }

            $this->ValidarPermiso();

            $data = $this->FormatearRequest($this->plantilla_model->Obtener($this->input->post("id")));
            echo json_encode(array("isValid"=>true,"Datos"=>$data,"Caso" =>  $this->input->post("Caso") ));
        }

        //Funcion usada para obtener un mantenimiento preventivo 
        //a partir de una plantilla de mantenimiento
        public function obtenerMantenimiento(){

            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }

            $data = $this->FormatearRequestMantenimiento($this->plantilla_model->ObtenerMantenimiento($this->input->post("id")));
            echo json_encode(array("isValid"=>true,"Datos"=>$data));
        }

        public function reversar(){
            
            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }
            $this->ValidarPermiso();

            if($this->plantilla_model->PuedeAprobar($this->input->post("id"))){
                $reversar = $this->plantilla_model->ReversarMantenimiento($this->input->post("id"));
                
                $Datos = $this->plantilla_model->Obtener($this->input->post("id"));
                echo json_encode(array("isValid"=>true,
                    "Mensaje"=>"Se ha aprobado correctamente el mantenimiento correctivo",
                    "Tipo" => $this->input->post("Tipo"),
                    "Datos"=>$Datos
                ));
            }else{
                $usuarios = $this->plantilla_model->ObtenerUsuarios($this->input->post("id"));
                $html = "<strong>Usuario Aprobador:</strong> " . $usuarios['apr_nom'];
                echo json_encode(array("isValid"=>false,
                    "Mensaje"=>"El Documento actual solo puede ser desaprobado por la misma persona que lo ha aprobado.<br><br>" . $html
                ));

            }

        }

        public function view(){
            
            if(!$this->session->userdata("nombre")){
                redirect(site_url(''));
            }

            $this->ValidarPermiso();
            
            $data = $this->FormatearRequest($this->plantilla_model->Obtener());

            $JsFile =   "<script src=\"". base_url() . "assets/js/Mantenimiento/Plantilla/Plantilla.js\"></script>"
                    .   "<script src=\"". base_url() . "assets/js/Mantenimiento/Plantilla/Tarea.js\"></script>";

            $datafile['JsFile'] = $JsFile ;

            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');
            $ld = $this->listasdesplegables_model->Obtener('','COB-PLANTI');

            $listaBusquedaBien   = $this->listasdesplegables_model->Obtener('','COB-BIENES');
            $listaBusquedaTipoPieza= $this->listasdesplegables_model->Obtener('','COB-TIPOPI');
            $listaBusquedaHerramienta= $this->listasdesplegables_model->Obtener('','COB-HERRAM');

            $dataLD['OrdenarBusqueda'] = $this->liblistasdesplegables->FormatearListaDesplegable($ld);
            $data['listaBusquedaFormulario'] = $dataLD['OrdenarBusqueda'];
            $data['listaBusquedaBien'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaBien);
            $data['listaBusquedaTipoPieza'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaTipoPieza);
            $data['listaBusquedaHerramienta'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaHerramienta);

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/Mantenimiento/plantilla',$data);
            $this->load->view('plantillas/7-footer');

        }

    }

?>