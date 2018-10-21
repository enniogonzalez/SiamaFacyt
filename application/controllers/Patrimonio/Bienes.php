<?php

    class Bienes extends CI_Controller{

        public function __construct(){
            parent::__construct();
            $this->load->model('Patrimonio/bienes_model' , 'bienes_model');
            $this->load->library('liblistasdesplegables','liblistasdesplegables');
        }

        private function FormatearRequest($respuesta,$PiezaArray = false){
            if($PiezaArray){
                $Pieza = array();
            }else{
                $Pieza = "";
            }


            $data = array(
                "bie_id"        =>"",
                "nombre"        =>"",
                "modelo"         =>"",
                "bie_ser"       =>"",
                "inv_uc"        =>"",
                "pro_id"        =>"",
                "fec_fab"       =>"",
                "fec_adq"       =>"",
                "fec_ins"       =>"",
                "tip_adq"       =>"",
                "loc_id"        =>"",
                "par_id"        =>"",
                "mar_id"        =>"",
                "custodio"      =>"",
                "fue_ali"       =>"",
                "cla_uso"       =>"",
                "tipo"          =>"",
                "med_vol"       =>"",
                "uni_vol"       =>"",
                "med_amp"       =>"",
                "uni_amp"       =>"",
                "med_pot"       =>"",
                "uni_pot"       =>"",
                "med_fre"       =>"",
                "uni_fre"       =>"",
                "med_cap"       =>"",
                "uni_cap"       =>"",
                "med_pre"       =>"",
                "uni_pre"       =>"",
                "med_flu"       =>"",
                "uni_flu"       =>"",
                "med_tem"       =>"",
                "uni_tem"       =>"",
                "med_pes"       =>"",
                "uni_pes"       =>"",
                "med_vel"       =>"",
                "uni_vel"       =>"",
                "tec_pre"       =>"",
                "riesgo"        =>"",
                "estatus"       =>"",
                "rec_fab"       =>"",
                "observaciones" =>"",
                "nompar"        =>"",
                "nompro"        =>"",
                "nomloc"        =>"",
                "nommar"        =>"",
                "Piezas"        =>$Pieza,
                "nomcus"        =>"",
            );

            if($respuesta)
                $data = $respuesta;
            return $data;
        }

        public function view(){
            
            if(!$this->session->userdata("nombre")){
                redirect(site_url(''));
            }
            
            $data = $this->FormatearRequest($this->bienes_model->Obtener());

            $JsFile = "<script src=\"". base_url() . "assets/js/Patrimonio/Bienes.js\"></script>";
            
            $datafile['JsFile'] = $JsFile ;


            
            $this->load->model('Sistema/listasdesplegables_model' , 'listasdesplegables_model');
            $ld = $this->listasdesplegables_model->Obtener('','COB-BIENES');
            $listaBusquedaMarca = $this->listasdesplegables_model->Obtener('','COB-MARCAS');
            $listaBusquedaProveedor = $this->listasdesplegables_model->Obtener('','COB-PROVEE');
            $listaBusquedaLocalizacion = $this->listasdesplegables_model->Obtener('','COB-LOCALI');
            $listaBusquedaPartida = $this->listasdesplegables_model->Obtener('','COB-PARTID');
            $listaBusquedaCustodio= $this->listasdesplegables_model->Obtener('','COB-USUARI');


            $ldVoltio       = $this->listasdesplegables_model->Obtener('','UNI-VOLTAJ');
            $ldAmperaje     = $this->listasdesplegables_model->Obtener('','UNI-AMPERA');
            $ldPotencia     = $this->listasdesplegables_model->Obtener('','UNI-POTENC');
            $ldFrecuencia   = $this->listasdesplegables_model->Obtener('','UNI-FRECUE');
            $ldCapacidad    = $this->listasdesplegables_model->Obtener('','UNI-CAPACI');
            $ldPresion      = $this->listasdesplegables_model->Obtener('','UNI-PRESIO');
            $ldFlujo        = $this->listasdesplegables_model->Obtener('','UNI-FLUJO');
            $ldTemperatura  = $this->listasdesplegables_model->Obtener('','UNI-TEMPER');
            $ldPeso         = $this->listasdesplegables_model->Obtener('','UNI-PESO');
            $ldVelocidad    = $this->listasdesplegables_model->Obtener('','UNI-VELOCI');
            $ldAdquisicion  = $this->listasdesplegables_model->Obtener('','BIE-ADQUIS');
            $ldAlimentacion = $this->listasdesplegables_model->Obtener('','BIE-ALIMEN');
            $ldUso          = $this->listasdesplegables_model->Obtener('','BIE-USO');
            $ldTipo         = $this->listasdesplegables_model->Obtener('','BIE-TIPO');
            $ldTecnologia   = $this->listasdesplegables_model->Obtener('','BIE-TECNOL');
            $ldRiesgo       = $this->listasdesplegables_model->Obtener('','BIE-RIESGO');
            $ldEstatus      = $this->listasdesplegables_model->Obtener('','BIE-ESTATU');
            
            $dataLD['OrdenarBusqueda'] = $this->liblistasdesplegables->FormatearListaDesplegable($ld);
            $data['listaBusquedaFormulario'] = $dataLD['OrdenarBusqueda'];
            $data['listaBusquedaMarca'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaMarca);
            $data['listaBusquedaProveedor'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaProveedor);
            $data['listaBusquedaLocalizacion'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaLocalizacion);
            $data['listaBusquedaPartida'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaPartida);
            $data['listaBusquedaCustodio'] = $this->liblistasdesplegables->FormatearListaDesplegable($listaBusquedaCustodio);


            $data['ldVoltio'] = $this->liblistasdesplegables->FormatearListaDesplegable($ldVoltio,false,$data['uni_vol']);
            $data['ldAmperaje'] = $this->liblistasdesplegables->FormatearListaDesplegable($ldAmperaje,false,$data['uni_amp']);
            $data['ldPotencia'] = $this->liblistasdesplegables->FormatearListaDesplegable($ldPotencia,false,$data['uni_pot']);
            $data['ldFrecuencia'] = $this->liblistasdesplegables->FormatearListaDesplegable($ldFrecuencia,false,$data['uni_fre']);
            $data['ldCapacidad'] = $this->liblistasdesplegables->FormatearListaDesplegable($ldCapacidad,false,$data['uni_cap']);
            $data['ldPresion'] = $this->liblistasdesplegables->FormatearListaDesplegable($ldPresion,false,$data['uni_pre']);
            $data['ldFlujo'] = $this->liblistasdesplegables->FormatearListaDesplegable($ldFlujo,false,$data['uni_flu']);
            $data['ldTemperatura'] = $this->liblistasdesplegables->FormatearListaDesplegable($ldTemperatura,false,$data['uni_tem']);
            $data['ldPeso'] = $this->liblistasdesplegables->FormatearListaDesplegable($ldPeso,false,$data['uni_pes']);
            $data['ldVelocidad'] = $this->liblistasdesplegables->FormatearListaDesplegable($ldVelocidad,false,$data['uni_vel']);
            $data['ldAdquisicion'] = $this->liblistasdesplegables->FormatearListaDesplegable($ldAdquisicion,true,$data['tip_adq']);
            $data['ldAlimentacion'] = $this->liblistasdesplegables->FormatearListaDesplegable($ldAlimentacion,true,$data['fue_ali']);
            $data['ldUso'] = $this->liblistasdesplegables->FormatearListaDesplegable($ldUso,true,$data['cla_uso']);
            $data['ldTipo'] = $this->liblistasdesplegables->FormatearListaDesplegable($ldTipo,true,$data['tipo']);
            $data['ldTecnologia'] = $this->liblistasdesplegables->FormatearListaDesplegable($ldTecnologia,true,$data['tec_pre']);
            $data['ldRiesgo'] = $this->liblistasdesplegables->FormatearListaDesplegable($ldRiesgo,true,$data['riesgo']);
            $data['ldEstatus'] = $this->liblistasdesplegables->FormatearListaDesplegable($ldEstatus,false,$data['estatus']);

            $dataAlerta['cantAlertas'] = $this->alertas_model->CantidadAlertas();

            $this->load->view('plantillas/1-header', $datafile);
            $this->load->view('plantillas/2-barranavegacion',$dataAlerta);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('plantillas/3-iniciomain');
            $this->load->view('plantillas/4-barramenu');
            $this->load->view('plantillas/5-iniciopagina');
            $this->load->view('paginas/Patrimonio/bienes',$data);
            $this->load->view('plantillas/7-footer');


        }

        public function obtener(){

            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }
            $data = $this->FormatearRequest($this->bienes_model->Obtener($this->input->post("id")));
            echo json_encode(array("isValid"=>true,"Datos"=>$data));

        }

        public function guardar(){

            if(!$this->session->userdata("nombre") || $this->input->post("Nombre") == ""){
                redirect(site_url(''));
            }


            $parametros = array(
                "idActual"      => $this->input->post("id"),
                "Nombre"        => $this->input->post("Nombre"),
                "Modelo"        => $this->input->post("Modelo"),
                "Bie_Ser"       => $this->input->post("Serial"),
                "Inv_UC"        => $this->input->post("Inventario"),
                "Pro_Id"        => $this->input->post("Proveedor"),
                "Fec_Fab"       => $this->input->post("Fabricacion"),
                "Fec_adq"       => $this->input->post("fAdquisicion"),
                "Fec_ins"       => $this->input->post("Instalacion"),
                "Tip_Adq"       => $this->input->post("tAdquisicion"),
                "Loc_Id"        => $this->input->post("Localizacion"),
                "Par_Id"        => $this->input->post("Partidas"),
                "Mar_Id"        => $this->input->post("Marca"),
                "Custodio"      => $this->input->post("Custodio"),
                "Fue_Ali"       => $this->input->post("Alimentacion"),
                "Cla_Uso"       => $this->input->post("Uso"),
                "Tipo"          => $this->input->post("Tipo"),
                "med_vol"       => $this->input->post("mVoltaje"),
                "uni_vol"       => $this->input->post("uVoltaje"),
                "med_amp"       => $this->input->post("mAmperaje"),
                "uni_amp"       => $this->input->post("uAmperaje"),
                "med_pot"       => $this->input->post("mPotencia"),
                "uni_pot"       => $this->input->post("uPotencia"),
                "med_fre"       => $this->input->post("mFrecuencia"),
                "uni_fre"       => $this->input->post("uFrecuencia"),
                "med_cap"       => $this->input->post("mCapacidad"),
                "uni_cap"       => $this->input->post("uCapacidad"),
                "med_pre"       => $this->input->post("mPresion"),
                "uni_pre"       => $this->input->post("uPresion"),
                "med_flu"       => $this->input->post("mFlujo"),
                "uni_flu"       => $this->input->post("uFlujo"),
                "med_tem"       => $this->input->post("mTemperatura"),
                "uni_tem"       => $this->input->post("uTemperatura"),
                "med_pes"       => $this->input->post("mPeso"),
                "uni_pes"       => $this->input->post("uPeso"),
                "med_vel"       => $this->input->post("mVelocidad"),
                "uni_vel"       => $this->input->post("uVelocidad"),
                "Tec_Pre"       => $this->input->post("Tecnologia"),
                "Riesgo"        => $this->input->post("Riesgo"),
                "Rec_Fab"       => $this->input->post("Recomendacion"),
                "Estatus"       => $this->input->post("Estatus"),
                "Observaciones" => trim($this->input->post("Observacion"))
            );
            
            if($this->input->post("id") == ""){
                if($this->bienes_model->ExisteInventario($parametros['Inv_UC'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe un Bien registrado con el mismo codigo de Inventario UC.",
                        "id"=>""));
                }else{
                    $respuesta = $this->bienes_model->Insertar($parametros);
                    $insertado = $this->bienes_model->Obtener();
                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha insertado Bien exitosamente",
                        "id"=>$insertado['bie_id']));
                }
            }else{
                if($this->bienes_model->ExisteInventario($parametros['Inv_UC'],$parametros['idActual'])){
                    echo json_encode(array(
                        "isValid"=>false,
                        "Mensaje"=>"Ya existe un Bien registrado con el mismo codigo de Inventario UC.",
                        "id"=>$parametros['idActual']));
                }else{
                    $respuesta = $this->bienes_model->Actualizar($parametros);
                    echo json_encode(array(
                        "isValid"=>true,
                        "Mensaje"=>"Se ha editado Bien exitosamente",
                        "id"=>$this->input->post("id")));
                }

            }
        }

        public function eliminar(){
            
            if(!$this->session->userdata("nombre") || $this->input->post("id") == ""){
                redirect(site_url(''));
            }

            $eliminar = $this->bienes_model->Eliminar($this->input->post("id"));
            $data = $this->FormatearRequest($this->bienes_model->Obtener());
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
            $BienesDisponibles = false;

            $Condiciones = $this->input->post("Condiciones");
            if(isset($Condiciones)){
                $BienesDisponibles = $Condiciones['BienesDisponibles'] == "true" ? true:false;
            }

            $respuesta = $this->FormatearBusqueda($this->bienes_model->Busqueda($busqueda,$ordenamiento,$inicio,$fin,$BienesDisponibles));

            echo json_encode(array("isValid"=>true,"Datos"=>$respuesta));
        }

        public function imprimir($id){
            
            $data['datos'] = $this->FormatearRequest($this->bienes_model->ObtenerInfoPDF($id),true);
            $this->load->library('tcpdf/Pdf');
            $this->load->view('Reportes/repBienes',$data);
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
                        .   "<td style='display:none;'>" . $elemento['bie_id'] . "</td>"
                        .   "<td>" . $elemento['nombre'] . "</td>"
                        .   "<td>" . $elemento['inv_uc'] . "</td>"
                        .   "<td>" . $elemento['nomloc'] . "</td>"
                        .   "<td>" . $elemento['nommar'] . "</td>"
                        ."</tr>";
                }
                
                $data['Listas'] = $htmlListas;
                $data['Registros'] = $registros;
            }

            return $data;
        }


    }


?>