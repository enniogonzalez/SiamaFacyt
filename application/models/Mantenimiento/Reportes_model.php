
<?php

    class Reportes_model extends CI_Model{
        
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
                $filtros = "WHERE " . $filtros;
            }

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

        public function RepManUsu($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $filtros = "";

            if($data['Inicio'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Renglon.fec_ini >= '" . $data['Inicio'] . "'";
            }

            if($data['Fin'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Renglon.fec_fin <= '" . $data['Fin'] . "'";
            }

            if($data['Usuario'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Usu.usu_id = '" . $data['Usuario'] . "'";
            }

            if($data['Bien'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Bie.bie_id = '" . $data['Bien'] . "'";
            }


            if($data['Localizacion'] != ""){
                $filtros .= ($filtros == "" ? "": " AND ") . " Bie.loc_id = '" . $data['Localizacion'] . "'";
            }

            if($filtros != ""){
                $filtros = "WHERE " . $filtros;
            }

            $query ="
            SELECT 	'Cambios Correctivos' Opcion,
                            MCO.documento,
                            Renglon.fec_ini,
                            Renglon.fec_fin,
                            Renglon.estatus,
                            BIE.nombre Bien,
                            PIE.nombre Pieza,
                            PIE2.nombre pca,
                            Usu.usu_id,
                            Usu.nombre Usuario
            FROM cambiocorrectivo Renglon
                JOIN mantenimientocorrectivo MCO ON MCO.mco_id = Renglon.mco_id
                JOIN Bienes BIE ON BIE.bie_id = MCO.bie_id
                JOIN piezas PIE ON PIE.pie_id = Renglon.pda_id
                JOIN piezas PIE2 ON PIE2.pie_id = Renglon.pca_id
                JOIN usuarios Usu ON Usu.usu_id = Renglon.usu_id
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
                            Usu.usu_id,
                            Usu.nombre Usuario
            FROM reparacioncorrectiva Renglon
                JOIN mantenimientocorrectivo MCO ON MCO.mco_id = Renglon.mco_id
                JOIN Bienes BIE ON BIE.bie_id = MCO.bie_id
                JOIN piezas PIE ON PIE.pie_id = Renglon.pie_id
                JOIN usuarios Usu ON Usu.usu_id = Renglon.usu_id
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
                    Usu.usu_id,
                    Usu.nombre Usuario
            FROM mantenimientotarea Renglon
                JOIN mantenimiento MAN ON MAN.man_id = Renglon.man_id
                JOIN Bienes BIE ON BIE.bie_id = MAN.bie_id
                JOIN piezas PIE ON PIE.pie_id = Renglon.pie_id
                JOIN usuarios Usu ON Usu.usu_id = Renglon.usu_id
            " . $filtros . "
            
            ORDER BY Usuario ASC,usu_id ASC, opcion ASC, documento ASC";

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

            if($data['Usuario'] != "" || $data['Proveedor'] != ""){
                $ejecutor = "";

                if($data['Usuario'] != ""){
                    $ejecutor = "Renglon.usu_id = '" . $data['Usuario'] . "'";
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
                $filtros = "WHERE " . $filtros;
            }

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

            if($data['Usuario'] != "" || $data['Proveedor'] != ""){
                $ejecutor = "";

                if($data['Usuario'] != ""){
                    $ejecutor = "Renglon.usu_id = '" . $data['Usuario'] . "'";
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
                $filtros = "WHERE " . $filtros;
            }

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