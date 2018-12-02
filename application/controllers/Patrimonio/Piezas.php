<?php

    class Piezas extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->model('Patrimonio/piezas_model' , 'piezas_model');
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
        }

        private function FormatearRequest($respuesta){

            $data = array(
                "pie_id"        =>"",
                "bie_id"        =>"",
                "nombre"        =>"",
                "modelo"    	=>"",
                "pie_ser"       =>"",
                "inv_uc"        =>"",
                "pro_id"        =>"",
                "fec_fab"       =>"",
                "fec_adq"       =>"",
                "fec_ins"       =>"",
                "tip_adq"       =>"",
                "par_id"        =>"",
                "mar_id"        =>"",
                "tpi_id"        =>"",
                "estatus"       =>"",
                "observaciones" =>"",
                "nombie"        =>"",
                "nompar"        =>"",
                "nompro"        =>"",
                "nommar"        =>"",
                "nomtpi"        =>"",
            );

            if($respuesta){
                $data = $respuesta;
                $data['bie_id'] = ($data['bie_id'] == -1 ? "":$data['bie_id']);
            }
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
            
            $data = $this->FormatearRequest($this->piezas_model->Obtener());

            $JsFile = "<script src=\"". base_url() . "assets/js/Patrimonio/Piezas.js\"></script>";
            
            $datafile['JsFile'] = $JsFile ;

            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');
            $ld = $this->listasdesplegables_model->Obtener('','COB-PIEZAS');

            $listaBusquedaMarca     = $this->listasdesplegables_model->Obtener('','COB-MARCAS');
            $listaBusquedaProveedor = $this->listasdesplegables_model->Obtener('','COB-PROVEE');
            $listaBusquedaPartida   = $this->listasdesplegables_model->Obtener('','COB-PARTID');
            $listaBusquedaBien   = $this->listasdesplegables_model->Obtener('','COB-BIENES');
            $listaBusquedaTipoPieza   = $this->listasdesplegables_model->Obtener('','COB-TIPOPI');

            $ldAdquisicion  = $this->listasdesplegables_model->Obtener('','BIE-ADQUIS');
            $ldEstatus      = $this->listasdesplegables_model->Obtener('','BIE-ESTATU');
            
            $dataLD['OrdenarBusqueda'] = $this->liblistasdesplegables->FormatearListaDesplegable($ld);
            $data['listaBusquedaFormulario'] = $dataLD['OrdenarBusqueda'];
            $data['listaBusquedaMarca'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaMarca);
            $data['listaBusquedaProveedor'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaProveedor);
            $data['listaBusquedaPartida'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaPartida);
            $data['listaBusquedaBien'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaBien);
            $data['listaBusquedaTipoPieza'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaTipoPieza);


            $data['ldAdquisicion'] = $this->liblistasdesplegables->FormatearListaDesplegable($ldAdquisicion,true,$data['tip_adq']);
            $data['ldEstatus'] = $this->liblistasdesplegables->FormatearListaDesplegable($ldEstatus,false,$data['estatus']);

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/Patrimonio/piezas',$data);
            $this->load->view('plantillas/7-footer');


        }

        public function obtener(){
            $this->ValidarPermiso();

            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }
            $data = $this->FormatearRequest($this->piezas_model->Obtener($this->input->post("id")));
            echo json_encode(array("isValid"=>true,"Datos"=>$data));

        }

        public function guardar(){
            $this->ValidarPermiso();

            if(!$this->session->userdata("nombre") || $this->input->post("Nombre") == ""){
                redirect(site_url(''));
            }

            $parametros = array(
                "idActual"      => $this->input->post("id"),
                "Bie_Id"        => $this->input->post("Bien"),
                "Nombre"        => $this->input->post("Nombre"),
                "Modelo"        => $this->input->post("Modelo"),
                "Pie_Ser"       => $this->input->post("Serial"),
                "Inv_UC"        => $this->input->post("Inventario"),
                "Pro_Id"        => $this->input->post("Proveedor"),
                "Fec_Fab"       => $this->input->post("Fabricacion"),
                "Fec_adq"       => $this->input->post("fAdquisicion"),
                "Fec_ins"       => $this->input->post("Instalacion"),
                "Tip_Adq"       => $this->input->post("tAdquisicion"),
                "Par_Id"        => $this->input->post("Partidas"),
                "Mar_Id"        => $this->input->post("Marca"),
                "Tpi_Id"        => $this->input->post("TipoPieza"),
                "Estatus"       => $this->input->post("Estatus"),
                "Observaciones" => trim($this->input->post("Observacion"))
            );
            
            if($this->input->post("id") == ""){
                if($this->piezas_model->ExisteInventario($parametros['Inv_UC'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe una Pieza registrada con el mismo codigo de Inventario UC.",
                        "id"=>""));
                }else{
                    $respuesta = $this->piezas_model->Insertar($parametros);
                    $insertado = $this->piezas_model->Obtener();
                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha insertado Pieza exitosamente",
                        "id"=>$insertado['pie_id']));
                }
            }else{
                if($this->piezas_model->ExisteInventario($parametros['Inv_UC'],$parametros['idActual'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe una Pieza registrada con el mismo codigo de Inventario UC.",
                        "id"=>$parametros['idActual']));
                }else{
                    $respuesta = $this->piezas_model->Actualizar($parametros);
                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha editado Pieza exitosamente",
                        "id"=>$this->input->post("id")));
                }

            }
        }

        public function eliminar(){
            $this->ValidarPermiso();
            
            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }

            $eliminar = $this->piezas_model->Eliminar($this->input->post("id"));
            $data = $this->FormatearRequest($this->piezas_model->Obtener());
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
            $idBien = "";

            $Condiciones = $this->input->post("Condiciones");
            
            if(isset($Condiciones)){
                $idBien = $Condiciones['Bien'];
            }
            
            $respuesta = $this->FormatearBusqueda($this->piezas_model->Busqueda($busqueda,$ordenamiento,$inicio,$fin,$idBien));

            echo json_encode(array("isValid"=>true,"Datos"=>$respuesta));
        }

        public function busquedaDisponibles(){

            if(!$this->session->userdata("nombre") || $this->input->post("Pagina") == ""){
                redirect(site_url(''));
            }

            $busqueda = $this->input->post("Busqueda") ;
            $pagina = (int) $this->input->post("Pagina") ;
            $regXpag = (int) $this->input->post("RegistrosPorPagina") ;
            $ordenamiento = $this->input->post("Orden") ;
            
            $inicio = 1+$regXpag*($pagina-1);
            $fin = $regXpag*$pagina;
            $Condiciones = $this->input->post("Condiciones");
            $idBien = $Condiciones['Bien'];
            $PiewBie = true;

            if(isset($Condiciones) && isset($Condiciones['PiewBie'])){
                $PiewBie = $Condiciones['PiewBie'] == "true" ? true:false;
            }

            $data = array(
                "busqueda"  =>$busqueda,
                "orden"     =>$ordenamiento,
                "inicio"    =>$inicio,
                "fin"       =>$fin,
                "id"        =>$idBien,
                "PiewBie"   =>$PiewBie,
            );

            $respuesta = $this->FormatearBusqueda($this->piezas_model->busquedaDisponibles($data));

            echo json_encode(array("isValid"=>true,"Datos"=>$respuesta));
        }

        public function imprimir($id){
            $this->ValidarPermiso();
            $data['datos'] = $this->FormatearRequest($this->piezas_model->ObtenerInfoPDF($id));
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Formatos/formatoPieza',$data);
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
                        .   "<td style='display:none;'>" . $elemento['pie_id'] . "</td>"
                        .   "<td>" . $elemento['inv_uc'] . "</td>"
                        .   "<td>" . $elemento['nombre'] . "</td>"
                        .   "<td>" . $elemento['nommar'] . "</td>"
                        .   "<td>" . $elemento['nombie'] . "</td>"
                        .   "<td style='display:none;'>" . ($elemento['bie_id'] == -1? "":$elemento['bie_id']) . "</td>"
                        .   "<td>" . $elemento['estatus'] . "</td>"
                        ."</tr>";
                }
                
                $data['Listas'] = $htmlListas;
                $data['Registros'] = $registros;
            }

            return $data;
        }
    }


?>