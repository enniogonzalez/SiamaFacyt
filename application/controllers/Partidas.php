<?php

    class Partidas extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->model('partidas_model');
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
        }

        private function FormatearRequest($respuesta){

            $data = array(
                "par_id"        =>"",
                "codigo"        =>"",
                "nombre"        =>"",
                "observaciones" =>"",
                "idpad"         =>"-1",
                "nombrepadre"   =>"",
            );

            if($respuesta)
                $data = $respuesta;
            return $data;
        }

        public function view(){
            
            if(!$this->session->userdata("nombre")){
                redirect(site_url(''));
            }
            
            $data = $this->FormatearRequest($this->partidas_model->Obtener());

            $JsFile = "<script src=\"". base_url() . "assets/js/Partidas.js\"></script>";
            
            $datafile['JsFile'] = $JsFile ;

            
            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');
            $ld = $this->listasdesplegables_model->Obtener('','COB-PARTID');

            $dataLD['OrdenarBusqueda'] = $this->liblistasdesplegables->FormatearListaDesplegable($ld);

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/partidas',$data);
            $this->load->view('plantillas/7-footer');


        }

        public function guardar(){

            if(!$this->session->userdata("nombre") || $this->input->post("Nombre") == ""){
                redirect(site_url(''));
            }

            $parametros = array(
                "idActual" => $this->input->post("id"),
                "idPad" =>$this->input->post("Padre"),
                "Nombre" => $this->input->post("Nombre"),
                "Codigo" => $this->input->post("Codigo"),
                "Observaciones" => trim($this->input->post("Observacion"))
            );
            
            if($this->input->post("id") == ""){
                if($this->partidas_model->ExisteCodigo($parametros['Codigo'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe una partida registrada con el mismo codigo.",
                        "id"=>""));
                }else{
                    $respuesta = $this->partidas_model->Insertar($parametros);
                    $insertado = $this->partidas_model->Obtener();
                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha insertado Localizacion exitosamente",
                        "id"=>$insertado['par_id']));
                }
            }else{
                if($this->partidas_model->ExisteCodigo($parametros['Codigo'],$parametros['idActual'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe una partida registrada con el mismo codigo.",
                        "id"=>$parametros['idActual']));
                }else{
                    $respuesta = $this->partidas_model->Actualizar($parametros);
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

            $eliminar = $this->partidas_model->Eliminar($this->input->post("id"));
            $data = $this->FormatearRequest($this->partidas_model->Obtener());
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

            if($this->input->post("Opcion") == "Padre")
                $id = $this->input->post("IdActual");
            else
                $id = "";

            $respuesta = $this->FormatearBusqueda($this->partidas_model->Busqueda($busqueda,$ordenamiento,$inicio,$fin,$id));

            echo json_encode(array("isValid"=>true,"Datos"=>$respuesta));
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
                        .   "<td style='display:none;'>" . $elemento['par_id'] . "</td>"
                        .   "<td style='display:none;'>" . $elemento['idpad'] . "</td>"
                        .   "<td style='display:none;'>" . $elemento['nombrepadre'] . "</td>"
                        .   "<td>" . $elemento['codigo'] . "</td>"
                        .   "<td>" . $elemento['nombre'] . "</td>"
                        .   "<td>" . $elemento['observaciones'] . "</td>"
                        ."</tr>";
                }
                
                $data['Listas'] = $htmlListas;
                $data['Registros'] = $registros;
            }

            return $data;
        }


    }


?>