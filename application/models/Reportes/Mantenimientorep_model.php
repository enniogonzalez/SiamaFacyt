<?php

    class Mantenimientorep_model extends CI_Model{
        
        private function ObtenerObrero($id){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query ="
            SELECT 	nombre
            FROM Obreros 
            WHERE obr_id = " . $id;

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
        
        private function ObtenerProveedor($id){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query ="
            SELECT 	raz_soc
            FROM proveedores 
            WHERE pro_id = " . $id;

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
            
            if($data['Obrero'] != ""){
                $campo = $this->ObtenerObrero($data['Obrero']);

                if(count($campo) > 0){
                    $campo = $campo['nombre'];
                }else{
                    $campo = "";
                }
                $parametros[$i] = array("Obrero",$campo);
                $i++;
            }
            
            if($data['Proveedor'] != ""){
                $campo = $this->ObtenerProveedor($data['Proveedor']);

                if(count($campo) > 0){
                    $campo = $campo['raz_soc'];
                }else{
                    $campo = "";
                }
                $parametros[$i] = array("Proveedor",$campo);
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

                $parametros[$i] = array("Localizacion",$campo);
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

        public function RepManPro($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $filtros = "";

            if($data['Inicio'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Renglon.fec_ini >= '" . $data['Inicio'] . "'";
            }

            if($data['Fin'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Renglon.fec_fin <= '" . $data['Fin'] . "'";
            }

            if($data['Proveedor'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Pro.pro_id = '" . $data['Proveedor'] . "'";
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
            SELECT 	'Cambios Correctivos' Opcion,
                            MCO.documento,
                            Renglon.fec_ini,
                            Renglon.fec_fin,
                            Renglon.estatus,
                            BIE.nombre Bien,
                            PIE.nombre Pieza,
                            PIE2.nombre pca,
                            Pro.pro_id,
                            Pro.raz_soc Proveedor
            FROM cambiocorrectivo Renglon
                JOIN mantenimientocorrectivo MCO ON MCO.mco_id = Renglon.mco_id
                JOIN Bienes BIE ON BIE.bie_id = MCO.bie_id
                JOIN Localizaciones LOC ON LOC.loc_id = BIE.loc_id
                JOIN piezas PIE ON PIE.pie_id = Renglon.pda_id
                JOIN piezas PIE2 ON PIE2.pie_id = Renglon.pca_id
                JOIN proveedores Pro ON Pro.pro_id = Renglon.pro_id
            " . $filtros . "
            
            UNION
            
            SELECT 	'Reparaciones Correctivas' Opcion,
                            MCO.documento,
                            Renglon.fec_ini,
                            Renglon.fec_fin,
                            Renglon.estatus,
                            BIE.nombre Bien,
                            PIE.nombre Pieza,
                            '' pca,
                            Pro.pro_id,
                            Pro.raz_soc Proveedor
            FROM reparacioncorrectiva Renglon
                JOIN mantenimientocorrectivo MCO ON MCO.mco_id = Renglon.mco_id
                JOIN Bienes BIE ON BIE.bie_id = MCO.bie_id
                JOIN Localizaciones LOC ON LOC.loc_id = BIE.loc_id
                JOIN piezas PIE ON PIE.pie_id = Renglon.pie_id
                JOIN proveedores Pro ON Pro.pro_id = Renglon.pro_id
            " . $filtros . "
            
            UNION
            
            SELECT 	'Tareas Preventivas' Opcion,
                    MAN.documento,
                    Renglon.fec_ini,
                    Renglon.fec_fin,
                    Renglon.estatus,
                    BIE.nombre Bien,
                    PIE.nombre Pieza,
                    '' pca,
                    Pro.pro_id,
                    Pro.raz_soc Proveedor
            FROM mantenimientotarea Renglon
                JOIN mantenimiento MAN ON MAN.man_id = Renglon.man_id
                JOIN Bienes BIE ON BIE.bie_id = MAN.bie_id
                JOIN Localizaciones LOC ON LOC.loc_id = BIE.loc_id
                JOIN piezas PIE ON PIE.pie_id = Renglon.pie_id
                JOIN proveedores Pro ON Pro.pro_id = Renglon.pro_id
            " . $filtros . "
            
            ORDER BY Proveedor ASC,pro_id ASC, opcion ASC, documento ASC";

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

        public function RepManObr($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $filtros = "";

            if($data['Inicio'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Renglon.fec_ini >= '" . $data['Inicio'] . "'";
            }

            if($data['Fin'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Renglon.fec_fin <= '" . $data['Fin'] . "'";
            }

            if($data['Obrero'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Obr.obr_id = '" . $data['Obrero'] . "'";
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
            SELECT 	'Cambios Correctivos' Opcion,
                            MCO.documento,
                            Renglon.fec_ini,
                            Renglon.fec_fin,
                            Renglon.estatus,
                            BIE.nombre Bien,
                            PIE.nombre Pieza,
                            PIE2.nombre pca,
                            Obr.obr_id,
                            Obr.nombre Obrero
            FROM cambiocorrectivo Renglon
                JOIN mantenimientocorrectivo MCO ON MCO.mco_id = Renglon.mco_id
                JOIN Bienes BIE ON BIE.bie_id = MCO.bie_id
                JOIN Localizaciones LOC ON LOC.loc_id = BIE.loc_id
                JOIN piezas PIE ON PIE.pie_id = Renglon.pda_id
                JOIN piezas PIE2 ON PIE2.pie_id = Renglon.pca_id
                JOIN obreros Obr ON Obr.obr_id = Renglon.obr_id
            " . $filtros . "
            
            UNION
            
            SELECT 	'Reparaciones Correctivas' Opcion,
                            MCO.documento,
                            Renglon.fec_ini,
                            Renglon.fec_fin,
                            Renglon.estatus,
                            BIE.nombre Bien,
                            PIE.nombre Pieza,
                            '' pca,
                            Obr.obr_id,
                            Obr.nombre Obrero
            FROM reparacioncorrectiva Renglon
                JOIN mantenimientocorrectivo MCO ON MCO.mco_id = Renglon.mco_id
                JOIN Bienes BIE ON BIE.bie_id = MCO.bie_id
                JOIN Localizaciones LOC ON LOC.loc_id = BIE.loc_id
                JOIN piezas PIE ON PIE.pie_id = Renglon.pie_id
                JOIN obreros Obr ON Obr.obr_id = Renglon.obr_id
            " . $filtros . "
            
            UNION
            
            SELECT 	'Tareas Preventivas' Opcion,
                    MAN.documento,
                    Renglon.fec_ini,
                    Renglon.fec_fin,
                    Renglon.estatus,
                    BIE.nombre Bien,
                    PIE.nombre Pieza,
                    '' pca,
                    Obr.obr_id,
                    Obr.nombre Obrero
            FROM mantenimientotarea Renglon
                JOIN mantenimiento MAN ON MAN.man_id = Renglon.man_id
                JOIN Bienes BIE ON BIE.bie_id = MAN.
                JOIN Localizaciones LOC ON LOC.loc_id = BIE.loc_id
                JOIN piezas PIE ON PIE.pie_id = Renglon.pie_id
                JOIN obreros Obr ON Obr.obr_id = Renglon.obr_id
            " . $filtros . "
            
            ORDER BY Obrero ASC,obr_id ASC, opcion ASC, documento ASC";

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

        public function RepManBie($data){
                 
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $filtros = "";

            if($data['Inicio'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Documento.fec_ini >= '" . $data['Inicio'] . "'";
            }

            if($data['Fin'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Documento.fec_fin <= '" . $data['Fin'] . "'";
            }

            if($data['Obrero'] != "" || $data['Proveedor'] != ""){
                $ejecutor = "";

                if($data['Obrero'] != ""){
                    $ejecutor = "Renglon.obr_id = '" . $data['Obrero'] . "'";
                }

                if($data['Proveedor'] != ""){
                    $ejecutor .= ($ejecutor == "" ? "": " OR ") . " Renglon.pro_id = '" . $data['Proveedor'] . "'";
                }

                $filtros .= ($filtros == "" ? "": " AND ") . "(" . $ejecutor . ")";
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
            SELECT 	DISTINCT 'Mantenimiento Correctivo' Opcion,
                    Documento.documento,
                    Documento.fec_ini,
                    Documento.fec_fin,
                    Documento.estatus,
                    BIE.bie_id,
                    BIE.nombre Bien
            FROM cambiocorrectivo Renglon
                JOIN mantenimientocorrectivo Documento ON Documento.mco_id = Renglon.mco_id
                JOIN Bienes BIE ON BIE.bie_id = Documento.bie_id
                JOIN Localizaciones LOC ON LOC.loc_id = BIE.loc_id
                " . $filtros . "
            
            UNION
            
            SELECT 	DISTINCT 'Mantenimiento Correctivo' Opcion,
                    Documento.documento,
                    Documento.fec_ini,
                    Documento.fec_fin,
                    Documento.estatus,
                    BIE.bie_id,
                    BIE.nombre Bien
            FROM reparacioncorrectiva Renglon
                JOIN mantenimientocorrectivo Documento ON Documento.mco_id = Renglon.mco_id
                JOIN Bienes BIE ON BIE.bie_id = Documento.bie_id
                JOIN Localizaciones LOC ON LOC.loc_id = BIE.loc_id
                " . $filtros . "
            
            UNION
            
            SELECT 	DISTINCT 'Mantenimiento Preventivo' Opcion,
                    Documento.documento,
                    Documento.fec_ini,
                    Documento.fec_fin,
                    Documento.estatus,
                    BIE.bie_id,
                    BIE.nombre Bien
            FROM mantenimientotarea Renglon
                JOIN mantenimiento Documento ON Documento.man_id = Renglon.man_id
                JOIN Bienes BIE ON BIE.bie_id = Documento.bie_id
                JOIN Localizaciones LOC ON LOC.loc_id = BIE.loc_id
                " . $filtros . "
            
            ORDER BY Bien ASC,bie_id ASC, opcion ASC, documento ASC";

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

        public function RepManLoc($data){
                 
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $filtros = "";

            if($data['Inicio'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Documento.fec_ini >= '" . $data['Inicio'] . "'";
            }

            if($data['Fin'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Documento.fec_fin <= '" . $data['Fin'] . "'";
            }

            if($data['Obrero'] != "" || $data['Proveedor'] != ""){
                $ejecutor = "";

                if($data['Obrero'] != ""){
                    $ejecutor = "Renglon.obr_id = '" . $data['Obrero'] . "'";
                }

                if($data['Proveedor'] != ""){
                    $ejecutor .= ($ejecutor == "" ? "": " OR ") . " Renglon.pro_id = '" . $data['Proveedor'] . "'";
                }

                $filtros .= ($filtros == "" ? "": " AND ") . "(" . $ejecutor . ")";
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
            SELECT 	DISTINCT 'Mantenimiento Correctivo' Opcion,
                    Documento.documento,
                    Documento.fec_ini,
                    Documento.fec_fin,
                    Documento.estatus,
                    LOC.loc_id,
                    LOC.nombre Localizacion,
                    BIE.nombre Bien
            FROM cambiocorrectivo Renglon
                JOIN mantenimientocorrectivo Documento ON Documento.mco_id = Renglon.mco_id
                JOIN Bienes BIE ON BIE.bie_id = Documento.bie_id
                JOIN Localizaciones LOC ON LOC.loc_id = BIE.loc_id
            " . $filtros . "
            
            UNION
            
            SELECT 	DISTINCT 'Mantenimiento Correctivo' Opcion,
                    Documento.documento,
                    Documento.fec_ini,
                    Documento.fec_fin,
                    Documento.estatus,
                    LOC.loc_id,
                    LOC.nombre Localizacion,
                    BIE.nombre Bien
            FROM reparacioncorrectiva Renglon
                JOIN mantenimientocorrectivo Documento ON Documento.mco_id = Renglon.mco_id
                JOIN Bienes BIE ON BIE.bie_id = Documento.bie_id
                JOIN Localizaciones LOC ON LOC.loc_id = BIE.loc_id
            " . $filtros . "
            
            UNION
            
            SELECT 	DISTINCT 'Mantenimiento Preventivo' Opcion,
                    Documento.documento,
                    Documento.fec_ini,
                    Documento.fec_fin,
                    Documento.estatus,
                    LOC.loc_id,
                    LOC.nombre Localizacion,
                    BIE.nombre Bien
            FROM mantenimientotarea Renglon
                JOIN mantenimiento Documento ON Documento.man_id = Renglon.man_id
                JOIN Bienes BIE ON BIE.bie_id = Documento.bie_id
                JOIN Localizaciones LOC ON LOC.loc_id = BIE.loc_id
            " . $filtros . "
            
            ORDER BY Localizacion ASC,loc_id ASC, opcion ASC, documento ASC";

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