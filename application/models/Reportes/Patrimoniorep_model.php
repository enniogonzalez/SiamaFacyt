<?php

    class Patrimoniorep_model extends CI_Model{
        
        private function ObtenerPieza($id){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query ="
            SELECT 	nombre
            FROM Piezas 
            WHERE pie_id = " . $id;

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $retorno = array();
            if($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
               $retorno = $line;
            }

            //Liberar memoria
            pg_free_result($result);
    
            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return $retorno;
        }
        
        private function ObtenerLocalizacion($id){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query ="
            SELECT 	nombre
            FROM Localizaciones 
            WHERE loc_id = " . $id;

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $retorno = array();
            if($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
               $retorno = $line;
            }

            //Liberar memoria
            pg_free_result($result);
    
            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return $retorno;
        }
        
        private function ObtenerBien($id){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query ="
            SELECT 	nombre
            FROM Bienes 
            WHERE bie_id = " . $id;

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $retorno = array();
            if($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
               $retorno = $line;
            }

            //Liberar memoria
            pg_free_result($result);
    
            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return $retorno;
        }
        
        private function ObtenerTipoPieza($id){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query ="
            SELECT 	nombre
            FROM tipopieza 
            WHERE tpi_id = " . $id;

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $retorno = array();
            if($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
               $retorno = $line;
            }

            //Liberar memoria
            pg_free_result($result);
    
            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return $retorno;
        }

        public function ObtenerParametros($data){
            
            $parametros = array();
            $i = 0;
            if($data['Inicio'] != ""){
                $parametros[$i] = array("Inicio",$data['Inicio']);
                $i++;
            }
            
            if($data['Fin'] != ""){
                $parametros[$i] = array("Fin",$data['Fin']);
                $i++;
            }
            
            if($data['Pieza'] != ""){
                $campo = $this->ObtenerPieza($data['Pieza']);

                if(count($campo) > 0){
                    $campo = $campo['nombre'];
                }else{
                    $campo = "";
                }
                $parametros[$i] = array("Pieza",$campo);
                $i++;
            }
            
            if($data['TipoPieza'] != ""){
                $campo = $this->ObtenerTipoPieza($data['TipoPieza']);

                if(count($campo) > 0){
                    $campo = $campo['nombre'];
                }else{
                    $campo = "";
                }
                $parametros[$i] = array("Tipo de Pieza",$campo);
                $i++;
            }
            
            if($data['Bien'] != ""){
                $campo = $this->ObtenerBien($data['Bien']);

                if(count($campo) > 0){
                    $campo = $campo['nombre'];
                }else{
                    $campo = "";
                }
                $parametros[$i] = array("Bien",$campo);
                $i++;
            }
            
            if($data['Localizacion'] != ""){
                
                $campo = $this->ObtenerLocalizacion($data['Localizacion']);

                if(count($campo) > 0){
                    $campo = $campo['nombre'];
                }else{
                    $campo = "";
                }

                $parametros[$i] = array("Localización",$campo);
                $i++;
            }
            
            return $parametros;
            // //Abrir conexion
            // $conexion = $this->bd_model->ObtenerConexion();

            // $query ="
            // SELECT 	nombre
            // FROM Localizaciones 
            // WHERE loc_id = " . $data['Localizacion'];

            // //Ejecutar Query
            // $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            // $retorno = array();
            // while($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
            //     array_push($retorno,$line);
            // }

            // //Liberar memoria
            // pg_free_result($result);
    
            // //liberar conexion
            // $this->bd_model->CerrarConexion($conexion);

            // return $retorno;
        }

        public function ListadoBienes($data){
                 
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $filtros = "";

            if($data['TipoPieza'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . 
                " Bie.bie_id IN (
                    SELECT bie_id
                    FROM CompatibilidadBien
                    WHERE tpi_id = '" . $data['TipoPieza'] . "')";
            }

            if($data['Pieza'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . 
                " Bie.bie_id IN (
                    SELECT bie_id
                    FROM piezas
                    WHERE pie_id = '" . $data['Pieza'] . "')";
            }

            if($data['Bien'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Bie.bie_id = '" . $data['Bien'] . "'";
            }

            if($data['Localizacion'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Bie.loc_id = '" . $data['Localizacion'] . "'";
            }

            if($filtros != ""){
                $filtros = " AND " . $filtros;
            }

            $filtros = "WHERE  (LOC.secuencia like '%" . $this->session->userdata("secuencia") ."%') " . $filtros;

            $query ="
            SELECT  Bie.nombre,
                    Bie.estatus,
                    COALESCE(Bie.Inv_UC,'') Inv_UC,
                    Loc.nombre nomLoc,
                    M.nombre nomMar
            FROM Bienes Bie
                JOIN Localizaciones Loc ON Loc.loc_id = Bie.loc_Id
                JOIN Marcas M ON M.mar_id = Bie.mar_id
                " . $filtros;

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $retorno = array();
            while($line = pg_fetch_array($result, null, PGSQL_ASSOC))
                array_push($retorno,$line);

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);


            return $retorno;
        }

        public function ListadoPiezas($data){
                 
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $filtros = "";

            if($data['TipoPieza'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Pie.tpi_id = '" . $data['TipoPieza'] . "'";
            }

            if($data['Pieza'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Pie.pie_id = '" . $data['Pieza'] . "'";
            }

            if($data['Bien'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " COALESCE(Bie.bie_id,-1) = '" . $data['Bien'] . "'";
            }

            if($data['Localizacion'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " COALESCE(Bie.loc_id,-1) = '" . $data['Localizacion'] . "'";
            }

            if($this->session->userdata("secuencia") != ""){
                $filtros .= ($filtros == "" ? "": " AND ") 
                    . " COALESCE(Bie.loc_id,-1) IN (
                            SELECT loc.loc_id
                            FROM localizaciones loc
                            WHERE loc.secuencia like '%" . $this->session->userdata("secuencia") ."%'
                        )";
            }

            if($filtros != ""){
                $filtros = " WHERE " . $filtros;
            }

            $query ="
            SELECT  Pie.nombre,
                    Pie.estatus,
                    Pie.Inv_UC,
                    TPI.nombre nomtpi,
                    COALESCE(Bie.nombre,'') nomBie,
                    M.nombre nomMar
            FROM Piezas Pie
                LEFT JOIN Bienes Bie ON Bie.bie_id = Pie.bie_id
                JOIN Marcas M ON M.mar_id = Pie.mar_id
                JOIN Tipopieza TPI ON TPI.tpi_id = Pie.tpi_id
                " . $filtros;

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $retorno = array();
            while($line = pg_fetch_array($result, null, PGSQL_ASSOC))
                array_push($retorno,$line);

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);


            return $retorno;
        }

        public function ListadoAjustes($data){
                 
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $filtros = "";

            if($data['Inicio'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " AJU.fec_cre >= '" . $data['Inicio'] . "'";
            }

            if($data['Fin'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " AJU.fec_cre <= '" . $data['Fin'] . "'";
            }

            if($data['TipoPieza'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") 
                . " AJU.aju_id IN (
                        SELECT aac.aju_id
                        FROM AjustesAccion aac
                            JOIN Piezas pie ON pie.pie_id = aac.pie_id
                        WHERE pie.tpi_id = '" . $data['TipoPieza'] . "')";
            }

            if($data['Pieza'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") 
                . " AJU.aju_id IN (
                        SELECT aac.aju_id
                        FROM AjustesAccion aac
                            JOIN Piezas pie ON pie.pie_id = aac.pie_id
                        WHERE pie.pie_id = '" . $data['Pieza'] . "')";
            }

            if($data['Bien'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Bie.bie_id = '" . $data['Bien'] . "'";
            }

            if($data['Localizacion'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Bie.loc_id = '" . $data['Localizacion'] . "'";
            }

            if($this->session->userdata("secuencia") != ""){
                $filtros .= ($filtros == "" ? "": " AND ") 
                    . " (LOC.secuencia like '%" . $this->session->userdata("secuencia") ."%')";
            }

            if($filtros != ""){
                $filtros = " WHERE " . $filtros;
            }

            $query ="
                SELECT  AJU.Documento,
                        AJU.Estatus,
                        to_char(AJU.Fec_Cre,'DD/MM/YYYY') Fecha,
                        Bie.nombre
                FROM Ajustes AJU
                    JOIN Bienes Bie ON Bie.Bie_Id = AJU.Bie_Id
                    JOIN Localizaciones loc ON loc.loc_id = bie.loc_id
                " . $filtros;

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $retorno = array();
            while($line = pg_fetch_array($result, null, PGSQL_ASSOC))
                array_push($retorno,$line);

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);


            return $retorno;
        }

        public function ListadoCambios($data){
                 
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $filtros = "";

            if($data['Inicio'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " CAM.fec_cre >= '" . $data['Inicio'] . "'";
            }

            if($data['Fin'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " CAM.fec_cre <= '" . $data['Fin'] . "'";
            }

            if($data['TipoPieza'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") 
                . " CAM.cam_id IN (
                        SELECT cep.cam_id
                        FROM CambioEstatusPieza cep
                            JOIN Piezas pie ON pie.pie_id = cep.pie_id
                        WHERE pie.tpi_id = '" . $data['TipoPieza'] . "')";
            }

            if($data['Pieza'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") 
                . " CAM.cam_id IN (
                        SELECT cep.cam_id
                        FROM CambioEstatusPieza cep
                            JOIN Piezas pie ON pie.pie_id = cep.pie_id
                        WHERE pie.pie_id = '" . $data['Pieza'] . "')";
            }

            if($data['Bien'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Bie.bie_id = '" . $data['Bien'] . "'";
            }

            if($data['Localizacion'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Bie.loc_id = '" . $data['Localizacion'] . "'";
            }

            if($this->session->userdata("secuencia") != ""){
                $filtros .= ($filtros == "" ? "": " AND ") 
                    . " (LOC.secuencia like '%" . $this->session->userdata("secuencia") ."%')";
            }

            if($filtros != ""){
                $filtros = " WHERE " . $filtros;
            }

            $query ="
                SELECT  CAM.cam_id,
                        CAM.Documento,
                        CAM.doc_estatus,
                        CAM.bie_estatus,
                        to_char(CAM.Fec_Cre,'DD/MM/YYYY') Fecha,
                        Bie.nombre
                FROM CambiosEstatus CAM
                    JOIN Bienes Bie ON Bie.Bie_Id = CAM.Bie_Id
                    JOIN Localizaciones loc ON loc.loc_id = bie.loc_id
                " . $filtros;

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $retorno = array();
            while($line = pg_fetch_array($result, null, PGSQL_ASSOC))
                array_push($retorno,$line);

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);


            return $retorno;
        }

        public function ListadoCompatibilidad($data){
                 
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $filtros = "";

            if($data['Inicio'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " COM.fec_cre >= '" . $data['Inicio'] . "'";
            }

            if($data['Fin'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " COM.fec_cre <= '" . $data['Fin'] . "'";
            }

            if($data['TipoPieza'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") 
                . " COM.com_id IN (
                        SELECT cac.com_id
                        FROM CompatibilidadAccion cac
                        WHERE cac.tpi_id = '" . $data['TipoPieza'] . "')";
            }

            if($data['Bien'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Bie.bie_id = '" . $data['Bien'] . "'";
            }

            if($data['Localizacion'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Bie.loc_id = '" . $data['Localizacion'] . "'";
            }

            if($this->session->userdata("secuencia") != ""){
                $filtros .= ($filtros == "" ? "": " AND ") 
                    . " (LOC.secuencia like '%" . $this->session->userdata("secuencia") ."%')";
            }

            if($filtros != ""){
                $filtros = " WHERE " . $filtros;
            }

            $query ="
                SELECT  COM.com_id,
                        COM.Documento,
                        COM.Estatus,
                        to_char(COM.Fec_Cre,'DD/MM/YYYY') Fecha,
                        Bie.nombre
                FROM Compatibilidad COM
                    JOIN Bienes Bie ON Bie.Bie_Id = COM.Bie_Id
                    JOIN Localizaciones loc ON loc.loc_id = bie.loc_id
                " . $filtros;

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $retorno = array();
            while($line = pg_fetch_array($result, null, PGSQL_ASSOC))
                array_push($retorno,$line);

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);


            return $retorno;
        }
    }

?>