<?php
    class Correctivo_model extends CI_Model{
        
        /************************************/
        /*          Mantenimiento           */
        /************************************/

        private function ObtenerTransaccionDocumento($id){

            $documento = substr("0000000000" . trim( $id),-10);
            $query = "UPDATE MantenimientoCorrectivo "
                    . "SET  Documento = '" . $documento
                    ."' WHERE MCO_ID = " . $id;
            return $query;
        }

        public function Actualizar($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " UPDATE MantenimientoCorrectivo "
                . " SET Bie_Id = " 
                . (($data['Bie_Id'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Bie_Id']) . "'")) 
                . "', cpl_id = " 
                . (($data['cpl_id'] == "") ? "null" : ("'" .str_replace("'", "''", $data['cpl_id']) . "'")) 
                . "', Fec_Ini = '" . str_replace("'", "''",$data['Fec_Ini']) 
                . "', Documento = " 
                . (($data['Documento'] == "") ? "Documento" : ("'" .str_replace("'", "''", $data['Documento']) . "'")) 
                . ", Fec_Fin = '" . str_replace("'", "''",$data['Fec_Fin']) 
                . "', Usu_Mod = " . $this->session->userdata("usu_id") 
                . ", Fec_Mod = NOW()" 
                . ", Observaciones = "
                . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
                . " WHERE MCO_ID = '" . str_replace("'", "''",$data['idActual']) . "';";

            //Ejecutar Query
            $result = pg_query($query);

            if ($result){
                
                $TransCambio = $this->ObtenerTransaccionesCambios($data['Cambios'],$data['idActual']);
                $TransReparacion = $this->ObtenerTransaccionesReparaciones($data['Reparaciones'],$data['idActual']);

                for($i = 0; $result && $i < count($TransCambio); $i++){
                    $result = pg_query($TransCambio[$i]);
                }
                    
                for($i = 0; $result && $i < count($TransReparacion); $i++){
                    $result = pg_query($TransReparacion[$i]);
                }

            }

            if($data['cpl_id'] != "" && $result){
                
                $query = "
                    DELETE FROM Alertas 
                    WHERE tabla = 'CorrectivoPlanificado' 
                        AND tab_id = '" . str_replace("'", "''",$data['cpl_id']) . "'
                        AND titulo like '%Ejecutar%'
                ";
                
                $result = pg_query($query);
            }

            /************************************/
            /*         Inicio Auditorias        */
            /************************************/
            if($result){

                $datos = array(
                    'Opcion' => 'Actualizar',
                    'Tabla' => 'MantenimientoCorrectivo', 
                    'Tab_id' => $data['idActual'],
                    'Datos' => json_encode($data)
                );
                
                $result = $this->auditorias_model->Insertar($datos);
            }

            /************************************/
            /*         Fin Auditorias           */
            /************************************/

            if(!$result){
                $error = pg_last_error();
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                die($error);
            }else
                pg_query("COMMIT") or die("Transaction commit failed");

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return true;
        }

        public function AprobarMantenimiento($id){
            
            $datosActual = $this->Obtener($id,true);

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = "  UPDATE MantenimientoCorrectivo  
                        SET estatus = 'Aprobado'
                            , Usu_Apr = " . $this->session->userdata("usu_id") . "
                            , Usu_Mod = " . $this->session->userdata("usu_id") . "
                            , Fec_Apr = Now()
                            , Fec_Mod = Now()
                        WHERE mco_id = '" .str_replace("'", "''",$id) . "';";
                
            //Ejecutar Query
            $result = pg_query($query);
            
            if ($result){
                $result = pg_query($this->ObtenerTransaccionesCambiosAprobada($id));

                if($result)
                    $result = pg_query($this->ObtenerTransaccionesReparacionesAprobada($id));

                $query = " DELETE FROM Alertas WHERE Tabla = 'MantenimientoCorrectivo' AND TAB_ID = " . $id;
            
                if($result)
                    $result = pg_query($query);

                    $query = "  SELECT  MCO.Documento,
                                        to_char(MCO.Fec_Cre,'DD/MM/YYYY') Fecha,
                                        BIE.nombre BIE_NOM,
                                        BIE.bie_id,
                                        USU.Nombre USU_NOM,
                                        COALESCE(APR.Nombre) apr_nom,
                                        LOC.nombre LOC_NOM,
                                        Loc.secuencia
                                FROM MantenimientoCorrectivo MCO
                                    LEFT JOIN CorrectivoPlanificado CPL ON CPL.cpl_id = MCO.cpl_id
                                    LEFT JOIN Mantenimiento MAN ON MAN.man_id = CPL.man_id
                                    LEFT JOIN MantenimientoCorrectivo MCO2 ON MCO2.mco_id = CPL.mco_id
                                    JOIN Bienes BIE ON BIE.BIE_ID = COALESCE(MAN.bie_id,MCO2.bie_id,MCO.BIE_ID)
                                    JOIN Usuarios USU ON USU.USU_ID = MCO.USU_CRE
                                    LEFT JOIN Usuarios APR ON APR.usu_id = MCO.USU_apr
                                    JOIN Localizaciones LOC ON LOC.LOC_ID = BIE.LOC_ID
                            WHERE MCO.mco_id = " . $id;

                if($result){
                    $result = pg_query($query);

                    if($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                        $titulo = "Mantenimiento Correctivo Aprobado " . $line['documento'];
                        
                        $descripcion = "<table style=\"width:100%\"><tr><td style=\"width:30%\"><strong>Documento:</strong></td><td style=\"width:70%\">" . $line['documento'] . "</td></tr>";
                        $descripcion .= "<td><strong>Bien:</strong> </td><td>" . $line['bie_nom'] . "</td></tr>";
                        $descripcion .= "<td><strong>Localizaci&oacute;n:</strong> </td><td>" . $line['loc_nom'] . "</td></tr>";
                        $descripcion .= "<td><strong>Solicitante:</strong> </td><td>" . $line['usu_nom'] . "</td></tr>";
                        $descripcion .= "<td><strong>Aprobador:</strong> </td><td>" . $line['apr_nom'] . "</td></tr>";
                        $descripcion .= "<td><strong>Fecha:</strong> </td><td>" . $line['fecha'] . "</td></tr></table>";

                        $MensajeCorreo = "<strong>Documento:</strong> " . $line['documento'] . "<br/>";
                        $MensajeCorreo .= "<strong>Bien:</strong> " . $line['bie_nom'] . "<br/>";
                        $MensajeCorreo .= "<strong>Localizaci&oacute;n:</strong> " . $line['loc_nom'] . "<br/>";
                        $MensajeCorreo .= "<strong>Solicitante:</strong> " . $line['usu_nom'] . "<br/>";
                        $MensajeCorreo .= "<strong>Aprobador:</strong> " . $line['apr_nom'] . "<br/>";
                        $MensajeCorreo .= "<strong>Fecha:</strong> " . $line['fecha'];
                        
                        $correoMasivo = array(
                            "id"        => $id,
                            "Opcion"    => "Mantenimiento Correctivo",
                            "Tabla"     => "MantenimientoCorrectivo",
                            "Estatus"   => "Aprobado",
                            "Secuencia" => $line['secuencia'],
                            "Titulo"    => $titulo,
                            "Menu"      => "Mantenimiento", 
                            "Cuerpo"    =>$MensajeCorreo
                        );

                        $query = "INSERT INTO Alertas(Titulo,bie_id, Menu, Tabla, TAB_ID,Usu_Cre,Descripcion)
                            VALUES('" . $titulo . "',"
                            . $line['bie_id'] .",'Mantenimiento','MantenimientoCorrectivo',"
                            . $id . ","
                            .$this->session->userdata("usu_id") . ",'"
                            . str_replace("'", "''",$descripcion)  . "')";
                            
                        $result = pg_query($query);
                    }else{
                        $result = false;
                    }
                }
                
            }


            /************************************/
            /*         Inicio Auditorias        */
            /************************************/
            if($result){
                $datosActual['estatus'] = 'Aprobado';
                $datos = array(
                    'Opcion' => 'Aprobar',
                    'Tabla' => 'MantenimientoCorrectivo', 
                    'Tab_id' => $id,
                    'Datos' => json_encode($datosActual)
                );
                
                $result = $this->auditorias_model->Insertar($datos);
            }
            
            /************************************/
            /*         Fin Auditorias           */
            /************************************/
            if(!$result){
                $error = pg_last_error();
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                die($error);
            }else
                pg_query("COMMIT") or die("Transaction commit failed");

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            $this->alertas_model->EnviarCorreo($correoMasivo);

            return true;
        }

        public function Busqueda($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            $condicion ="";


            if($data['busqueda'] != ""){
                $condicion = "(LOWER(B.nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(MCO.documento) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(MCO.estatus) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%')";
            }
            
            if( $data['fec_ini'] != "" && $data['fec_fin'] != ""){
                $condicion = ($condicion =="" ? "":" AND ")
                            . " ((MCO.fec_ini BETWEEN '" . $data['fec_ini'] ."' AND '" . $data['fec_fin'] ."')
                            OR (MCO.fec_fin BETWEEN '" . $data['fec_ini'] ."' AND '" . $data['fec_fin'] ."'))";
            }

            if($condicion !=""){
                $condicion = "WHERE " . $condicion;
            }
            
            $query ="   SELECT  *
                        FROM (
                            SELECT  MCO.MCO_Id,
                                    MCO.Documento,
                                    MCO.Estatus,
                                    to_char(MCO.Fec_Ini,'DD/MM/YYYY') Fec_Ini,
                                    to_char(MCO.Fec_Fin,'DD/MM/YYYY') Fec_Fin,
                                    B.nombre,
                                    B.bie_id,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $data['orden'] .") Fila
                            FROM MantenimientoCorrectivo MCO
                                LEFT JOIN CorrectivoPlanificado CPL ON CPL.cpl_id = MCO.cpl_id
                                LEFT JOIN Mantenimiento MAN ON MAN.man_id = CPL.man_id
                                LEFT JOIN MantenimientoCorrectivo MCO2 ON MCO2.mco_id = CPL.mco_id
                                JOIN Bienes B ON B.BIE_ID = COALESCE(MAN.bie_id,MCO2.bie_id,MCO.BIE_ID)
                            " . $condicion . "

                        ) LD
                        WHERE Fila BETWEEN ". $data['inicio'] . " AND " . $data['fin'] . "
                        ORDER BY Fila ASC;";

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($result){
                $retorno = [];
                while($line = pg_fetch_array($result, null, PGSQL_ASSOC))
                    array_push($retorno,$line);

            } else
                $retorno = false;

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return $retorno;
        }

        public function BusquedaRealizado($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            $condicion ="";


            if($data['busqueda'] != ""){
                $condicion = "AND (LOWER(B.nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(MCO.documento) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(MCO.estatus) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%')";
            }
            
            if( $data['fec_ini'] != "" && $data['fec_fin'] != ""){
                $condicion = " AND ((MCO.fec_ini BETWEEN '" . $data['fec_ini'] ."' AND '" . $data['fec_fin'] ."')
                            OR (MCO.fec_fin BETWEEN '" . $data['fec_ini'] ."' AND '" . $data['fec_fin'] ."'))";
            }


            $condicion = "WHERE MCO.estatus = 'Realizado' " . $condicion;

            //Query para buscar usuario
            $query ="   SELECT  *
                        FROM (
                            SELECT  MCO.MCO_Id,
                                    MCO.Documento,
                                    MCO.Estatus,
                                    to_char(MCO.Fec_Ini,'DD/MM/YYYY') Fec_Ini,
                                    to_char(MCO.Fec_Fin,'DD/MM/YYYY') Fec_Fin,
                                    B.nombre,
                                    B.bie_id,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $data['orden'] .") Fila
                            FROM MantenimientoCorrectivo MCO
                                JOIN Bienes B ON B.Bie_Id = MCO.Bie_Id
                            " . $condicion . "

                        ) LD
                        WHERE Fila BETWEEN ". $data['inicio'] . " AND " . $data['fin'] . "
                        ORDER BY Fila ASC;";

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($result){
                $retorno = [];
                while($line = pg_fetch_array($result, null, PGSQL_ASSOC))
                    array_push($retorno,$line);

            } else
                $retorno = false;

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return $retorno;
        }

        public function Eliminar($id){
      
            $datosActual = $this->Obtener($id,true);

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " DELETE FROM MantenimientoCorrectivo "
                . " WHERE mco_id = '" .str_replace("'", "''",$id) . "';";
                
            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            if ($result){
                $query = " DELETE FROM Alertas WHERE Tabla = 'MantenimientoCorrectivo' AND TAB_ID = " . $id;
                $result = pg_query($query);
            }

            if($result){
                $datos = array(
                    'Opcion' => 'Eliminar',
                    'Tabla' => 'MantenimientoCorrectivo', 
                    'Tab_id' => $id,
                    'Datos' => json_encode($datosActual)
                );
                
                $result = $this->auditorias_model->Insertar($datos);
            }

            if(!$result){
                $error = pg_last_error();
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                die($error);
            }else
                pg_query("COMMIT") or die("Transaction commit failed");

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return true;

        }

        public function ExisteDocumento($documento,$id=""){

            if($documento == "")
                return false;

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query =" SELECT * FROM MantenimientoCorrectivo WHERE LOWER(documento) ='" . mb_strtolower(str_replace("'", "''",$documento)) . "' " ;

            if($id != "")
                $query = $query . " AND MCO_ID <>'" . str_replace("'", "''",$id) . "' " ;

            $query = $query . ";" ;

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Si existe registro, se guarda. Sino se guarda false
            if (pg_num_rows($result) > 0) 
                $retorno = true;
            else
                $retorno = false;

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return $retorno;
        }

        public function Insertar($data){
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " INSERT INTO MantenimientoCorrectivo ( Documento,Bie_Id,cpl_id,Estatus, Fec_Ini, 
                                                            Fec_Fin,Usu_Cre,Usu_Mod, Observaciones) 
                        VALUES('"
            . str_replace("'", "''",$data['Documento'])    . "',"
            . (($data['Bie_Id'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Bie_Id']) . "'")) . ","
            . (($data['cpl_id'] == "") ? "null" : ("'" .str_replace("'", "''", $data['cpl_id']) . "'")) . ","
            . "'Solicitado','"
            . str_replace("'", "''",$data['Fec_Ini'])    . "','"
            . str_replace("'", "''",$data['Fec_Fin'])    . "',"
            . $this->session->userdata("usu_id")    . ","
            . $this->session->userdata("usu_id")    . ","
            . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
            . ") RETURNING mco_id;";

            //Ejecutar Query
            $result = pg_query($query);

            $new_id = "";

            if($result){
                $row = pg_fetch_row($result); 
                $new_id = $row['0']; 
            }

            if($data['cpl_id'] != "" && $result){
                
                $query = "
                    DELETE FROM Alertas 
                    WHERE tabla = 'CorrectivoPlanificado' 
                        AND tab_id = '" . str_replace("'", "''",$data['cpl_id']) . "'
                        AND titulo like '%Ejecutar%'
                ";
                
                $result = pg_query($query);
            }

            if ($result){

                $TransCambio = $this->ObtenerTransaccionesCambios($data['Cambios'],$new_id);
                $TransReparacion = $this->ObtenerTransaccionesReparaciones($data['Reparaciones'],$new_id);

                if($data['Documento'] == "")
                    $result = pg_query($this->ObtenerTransaccionDocumento($new_id));

                for($i = 0; $result && $i < count($TransCambio); $i++){
                    $result = pg_query($TransCambio[$i]);
                }
                    
                for($i = 0; $result && $i < count($TransReparacion); $i++){
                    $result = pg_query($TransReparacion[$i]);
                }

            }

            $query = "  SELECT  MCO.Documento,
                                to_char(MCO.Fec_Cre,'DD/MM/YYYY') Fecha,
                                COALESCE(BIE.nombre,'') BIE_NOM,
                                BIE.bie_id,
                                USU.Nombre USU_NOM,
                                LOC.nombre LOC_NOM,
                                Loc.secuencia
                        FROM MantenimientoCorrectivo MCO
                            LEFT JOIN CorrectivoPlanificado CPL ON CPL.cpl_id = MCO.cpl_id
                            LEFT JOIN Mantenimiento MAN ON MAN.man_id = CPL.man_id
                            LEFT JOIN MantenimientoCorrectivo MCO2 ON MCO2.mco_id = CPL.mco_id
                            JOIN Bienes BIE ON BIE.BIE_ID = COALESCE(MAN.bie_id,MCO2.bie_id,MCO.BIE_ID)
                            JOIN Usuarios USU ON USU.USU_ID = MCO.USU_CRE
                            JOIN Localizaciones LOC ON LOC.LOC_ID = BIE.LOC_ID
                        WHERE MCO.mco_id = " . $new_id;

            $documento = "";
            if($result){
                
                $result = pg_query($query);

                if($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                    
                    $documento = $line['documento'];
                    $titulo = "Mantenimiento Correctivo Solicitado " . $line['documento'];

                    $descripcion = "<table style=\"width:100%\"><tr><td style=\"width:30%\"><strong>Documento:</strong></td><td style=\"width:70%\">" . $line['documento'] . "</td></tr>";
                    $descripcion .= "<td><strong>Bien:</strong> </td><td>" . $line['bie_nom'] . "</td></tr>";
                    $descripcion .= "<td><strong>Localizaci&oacute;n:</strong> </td><td>" . $line['loc_nom'] . "</td></tr>";
                    $descripcion .= "<td><strong>Solicitante:</strong> </td><td>" . $line['usu_nom'] . "</td></tr>";
                    $descripcion .= "<td><strong>Fecha:</strong> </td><td>" . $line['fecha'] . "</td></tr></table>";

                    $MensajeCorreo = "<strong>Documento:</strong> " . $line['documento'] . "<br/>";
                    $MensajeCorreo .= "<strong>Bien:</strong> " . $line['bie_nom'] . "<br/>";
                    $MensajeCorreo .= "<strong>Localizaci&oacute;n:</strong> " . $line['loc_nom'] . "<br/>";
                    $MensajeCorreo .= "<strong>Solicitante:</strong> " . $line['usu_nom'] . "<br/>";
                    $MensajeCorreo .= "<strong>Fecha:</strong> " . $line['fecha'];
                    
                    $correoMasivo = array(
                        "id"        => $new_id,
                        "Opcion"    => "Mantenimiento Correctivo",
                        "Tabla"     => "MantenimientoCorrectivo",
                        "Estatus"   => "Solicitado",
                        "Secuencia" => $line['secuencia'],
                        "Titulo"    => $titulo,
                        "Menu"      => "Mantenimiento", 
                        "Cuerpo"    =>$MensajeCorreo
                    );

                    $query = "INSERT INTO Alertas(Titulo,bie_id, Menu, Tabla, TAB_ID,Usu_Cre,Descripcion)
                        VALUES('" . $titulo . "',"
                        . $line['bie_id'] .",'Mantenimiento','MantenimientoCorrectivo',"
                        . $new_id . ","
                        .$this->session->userdata("usu_id") . ",'"
                        . str_replace("'", "''",$descripcion) . "')";
                        
                    $result = pg_query($query);
                }else{
                    $result = false;
                }
            }

            /************************************/
            /*         Inicio Auditorias        */
            /************************************/
            if($result){
                
                $data['Documento'] = $documento;
                $data['idActual'] = $new_id;
                $datos = array(
                    'Opcion' => 'Insertar',
                    'Tabla' => 'MantenimientoCorrectivo', 
                    'Tab_id' => $new_id,
                    'Datos' => json_encode($data)
                );
                
                $result = $this->auditorias_model->Insertar($datos);
            }
            
            /************************************/
            /*         Fin Auditorias           */
            /************************************/

            if(!$result){
                
                $error = pg_last_error();
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                die($error);
            }else
                pg_query("COMMIT") or die("Transaction commit failed");

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            $this->alertas_model->EnviarCorreo($correoMasivo);
            ;
            return $new_id;
        }

        public function Obtener($id = '',$array = false){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  MCO.MCO_ID,	
	                            COALESCE(CAST(B.BIE_ID AS varchar(100)),'') BIE_ID,
                                COALESCE(B.nombre,'') Bie_Nom,		
	                            COALESCE(CAST(BIE.BIE_ID AS varchar(100)),'') BIE_ID_2,
                                COALESCE(BIE.nombre,'') Bie_Nom_2,		
                                COALESCE(CPL.documento,'') doc_cpl,	
	                            COALESCE(CAST(MCO.cpl_id AS varchar(100)),'') cpl_id,
                                MCO.Documento,
                                MCO.Estatus,		
                                MCO.Fec_Ini,		
                                MCO.Fec_Fin,
                                COALESCE(MCO.Observaciones,'') Observaciones
                        FROM MantenimientoCorrectivo MCO
                            LEFT JOIN CorrectivoPlanificado CPL ON CPL.cpl_id = MCO.cpl_id
                            LEFT JOIN Mantenimiento MAN ON MAN.man_id = CPL.man_id
                            LEFT JOIN MantenimientoCorrectivo MCO2 ON MCO2.mco_id = CPL.mco_id
                            LEFT JOIN Bienes BIE ON BIE.BIE_ID = COALESCE(MAN.bie_id,MCO2.bie_id)
                            LEFT JOIN Bienes B ON B.bie_id = MCO.bie_id";

            if($id != ''){
                $query = $query . " WHERE MCO.MCO_ID = '" . $id . "'";
            }

            $query = $query . " ORDER BY MCO.MCO_ID DESC LIMIT 1;";

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) 
                $retorno = $line;
            else
                $retorno = false;

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            if($retorno){
                $cambios = $this->ObtenerCambiosHTML($retorno['mco_id']);
                $reparaciones = $this->ObtenerReparacionesHTML($retorno['mco_id']);

                if($array){
                    $retorno['Cambios'] = $cambios['Array'];
                    $retorno['Reparaciones'] = $reparaciones['Array'];
                }else{
                    $retorno['Cambios'] = $cambios['html'];
                    $retorno['Reparaciones'] = $reparaciones['html'];
                }
            }


            return $retorno;
        }

        public function ObtenerInfoPDF($id){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  MCO.Documento,
                                MCO.MCO_ID,
                                B.nombre Bie_Nom,		
                                B.Inv_UC,		
                                MCO.Estatus,
                                CRE.Nombre Solicitante,
                                CASE
                                    WHEN MCO.cpl_id is not null then 'Mantenimiento Correctivo Planificado'
                                    ELSE 'Bien'
                                END origen,
                                COALESCE(CPL.documento,'') doc_ori,
                                COALESCE(APR.Nombre,'') Aprobador,
                                to_char(MCO.Fec_Ini,'DD/MM/YYYY') Fec_ini,
                                to_char(MCO.Fec_Fin,'DD/MM/YYYY') Fec_Fin,
                                to_char(MCO.Fec_Cre,'DD/MM/YYYY') Fec_Cre,
                                COALESCE(to_char(MCO.Fec_Apr,'DD/MM/YYYY'),'') Fec_Apr,
                                COALESCE(MCO.Observaciones,'') Observaciones
                        FROM MantenimientoCorrectivo MCO
                            LEFT JOIN CorrectivoPlanificado CPL ON CPL.cpl_id = MCO.cpl_id
                            LEFT JOIN Mantenimiento MAN ON MAN.man_id = CPL.man_id
                            LEFT JOIN MantenimientoCorrectivo MCO2 ON MCO2.mco_id = CPL.mco_id
                            JOIN Bienes B ON B.bie_id = COALESCE(MAN.bie_id,MCO2.bie_id,MCO.bie_id) 
                            JOIN Usuarios CRE ON CRE.usu_id = MCO.usu_cre
                            LEFT JOIN Usuarios APR ON APR.usu_id = MCO.usu_apr
                        WHERE MCO.MCO_ID = '" . $id . "'";


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) 
                $retorno = $line;
            else
                $retorno = false;

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            if($retorno){
                $retorno['Cambios'] = $this->ObtenerCambioPDF($retorno['mco_id']);
                $retorno['Reparaciones'] = $this->ObtenerReparacionesPDF($retorno['mco_id']);
            }


            return $retorno;
        }

        public function ObtenerUsuarios($id){
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  MCO.usu_cre,
                                CRE.nombre cre_nom,
                                COALESCE(MCO.usu_apr,-1) usu_apr,
                                COALESCE(APR.nombre,'') apr_nom
                        FROM MantenimientoCorrectivo MCO
                            JOIN Usuarios CRE ON CRE.usu_id = MCO.usu_cre
                            LEFT JOIN Usuarios APR ON APR.usu_id = MCO.usu_apr
                        WHERE MCO_ID = '" . $id . "'
                        ORDER BY MCO_ID DESC LIMIT 1;";

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) 
                $retorno = $line;
            else
                $retorno = false;

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);


            return $retorno;

        }

        public function PuedeAprobar($id){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query ="   SELECT * 
                        FROM MantenimientoCorrectivo 
                        WHERE MCO_ID = " . str_replace("'", "''",$id) . "
                            AND Usu_Cre <> ". $this->session->userdata("usu_id");


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Si existe registro, se guarda. Sino se guarda false
            if (pg_num_rows($result) > 0) 
                $retorno = true;
            else
                $retorno = false;

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return $retorno;
        }

        public function PuedeEliminar($id){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query ="   SELECT * 
                        FROM MantenimientoCorrectivo 
                        WHERE MCO_ID = " . str_replace("'", "''",$id) . "
                            AND Usu_Cre = ". $this->session->userdata("usu_id");


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Si existe registro, se guarda. Sino se guarda false
            if (pg_num_rows($result) > 0) 
                $retorno = true;
            else
                $retorno = false;

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return $retorno;
        }

        public function PuedeReversar($id){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query ="   SELECT * 
                        FROM MantenimientoCorrectivo 
                        WHERE MCO_ID = " . str_replace("'", "''",$id) . "
                            AND Usu_Apr = ". $this->session->userdata("usu_id");


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Si existe registro, se guarda. Sino se guarda false
            if (pg_num_rows($result) > 0) 
                $retorno = true;
            else
                $retorno = false;

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return $retorno;
        }

        public function ReversarMantenimiento($id){
            
            $datosActual = $this->Obtener($id,true);

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = "  UPDATE MantenimientoCorrectivo  
                        SET estatus = 'Solicitado'
                            , Usu_Apr = null
                            , Usu_Mod = " . $this->session->userdata("usu_id") . "
                            , Fec_Apr = null
                            , Fec_Mod = Now()
                        WHERE mco_id = '" .str_replace("'", "''",$id) . "';";

                
            //Ejecutar Query
            $result = pg_query($query);
            
            if ($result){
                $result = pg_query($this->ObtenerTransaccionesCambiosReversada($id));

                if($result)
                    $result = pg_query($this->ObtenerTransaccionesReparacionesReversada($id));


                $query = " DELETE FROM Alertas WHERE Tabla = 'MantenimientoCorrectivo' AND TAB_ID = " . $id;
                
                if($result)
                    $result = pg_query($query);

                $query = "  SELECT  MCO.Documento,
                                    to_char(MCO.Fec_Cre,'DD/MM/YYYY') Fecha,
                                    COALESCE(BIE.nombre,'') BIE_NOM,
                                    BIE.bie_id,
                                    USU.Nombre USU_NOM,
                                    LOC.nombre LOC_NOM,
                                    Loc.secuencia
                            FROM MantenimientoCorrectivo MCO
                                LEFT JOIN CorrectivoPlanificado CPL ON CPL.cpl_id = MCO.cpl_id
                                LEFT JOIN Mantenimiento MAN ON MAN.man_id = CPL.man_id
                                LEFT JOIN MantenimientoCorrectivo MCO2 ON MCO2.mco_id = CPL.mco_id
                                JOIN Bienes BIE ON BIE.BIE_ID = COALESCE(MAN.bie_id,MCO2.bie_id,MCO.BIE_ID)
                                JOIN Usuarios USU ON USU.USU_ID = MCO.USU_CRE
                                JOIN Localizaciones LOC ON LOC.LOC_ID = BIE.LOC_ID
                            WHERE MCO.mco_id = " . $id;

                if($result){
                    $result = pg_query($query);

                    if($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                        $titulo = "Mantenimiento Correctivo Solicitado " . $line['documento'];
                        $descripcion = "<table style=\"width:100%\"><tr><td style=\"width:30%\"><strong>Documento:</strong></td><td style=\"width:70%\">" . $line['documento'] . "</td></tr>";
                        $descripcion .= "<td><strong>Bien:</strong> </td><td>" . $line['bie_nom'] . "</td></tr>";
                        $descripcion .= "<td><strong>Localizaci&oacute;n:</strong> </td><td>" . $line['loc_nom'] . "</td></tr>";
                        $descripcion .= "<td><strong>Solicitante:</strong> </td><td>" . $line['usu_nom'] . "</td></tr>";
                        $descripcion .= "<td><strong>Fecha:</strong> </td><td>" . $line['fecha'] . "</td></tr></table>";


                        $MensajeCorreo = "<strong>Documento:</strong> " . $line['documento'] . "<br/>";
                        $MensajeCorreo .= "<strong>Bien:</strong> " . $line['bie_nom'] . "<br/>";
                        $MensajeCorreo .= "<strong>Localizaci&oacute;n:</strong> " . $line['loc_nom'] . "<br/>";
                        $MensajeCorreo .= "<strong>Solicitante:</strong> " . $line['usu_nom'] . "<br/>";
                        $MensajeCorreo .= "<strong>Fecha:</strong> " . $line['fecha'];
                        
                        $correoMasivo = array(
                            "id"        => $id,
                            "Opcion"    => "Mantenimiento Correctivo",
                            "Tabla"     => "MantenimientoCorrectivo",
                            "Estatus"   => "Solicitado",
                            "Titulo"    => $titulo,
                            "Menu"      => "Mantenimiento", 
                            "Cuerpo"    =>$MensajeCorreo
                        );

                        $query = "INSERT INTO Alertas(Titulo,bie_id, Menu, Tabla, TAB_ID,Usu_Cre,Descripcion)
                            VALUES('" . $titulo . "',"
                        . $line['bie_id'] .",'Mantenimiento','MantenimientoCorrectivo',"
                            . $id . ","
                            .$this->session->userdata("usu_id") . ",'"
                            . str_replace("'", "''",$descripcion)  . "')";
                            
                        $result = pg_query($query);
                    }else{
                        $result = false;
                    }
                }
            }

            /************************************/
            /*         Inicio Auditorias        */
            /************************************/
            if($result){
                $datosActual['estatus'] = 'Solicitado';
                $datos = array(
                    'Opcion' => 'Reversar',
                    'Tabla' => 'MantenimientoCorrectivo', 
                    'Tab_id' => $id,
                    'Datos' => json_encode($datosActual)
                );
                
                $result = $this->auditorias_model->Insertar($datos);
            }

            if(!$result){
                $error = pg_last_error();
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                die($error);
            }else
                pg_query("COMMIT") or die("Transaction commit failed");

            /************************************/
            /*         Fin Auditorias           */
            /************************************/

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            $this->alertas_model->EnviarCorreo($correoMasivo);

            return true;
        }

        public function RealizarOperaciones($data){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $bien = ($data['Bie_Pla']=="" ? $data['Bie_Id']:$data['Bie_Pla']);

            $TransCambio = $this->ObtenerTransaccionesCambiosRealizados($data['Cambios'],$data['idActual'],$bien);
            $TransReparacion = $this->ObtenerTranRepRealizadas($data['Reparaciones'],$data['idActual']);

            $result = true;

            for($i = 0; $result && $i < count($TransCambio); $i++){
                $result = pg_query($TransCambio[$i]);
            }

            if(!$result){
                echo $TransCambio[$i-1];
            }

            for($i = 0; $result && $i < count($TransReparacion); $i++){
                $result = pg_query($TransReparacion[$i]);
            }

            $query = "  UPDATE MantenimientoCorrectivo
                        SET Estatus = 'Afectado',
                            Observaciones = " . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'")) . "
                        WHERE mco_id = " . $data['idActual'] . "
                            AND EXISTS(
                                SELECT 1 
                                FROM CambioCorrectivo 
                                WHERE MCO_ID = " . $data['idActual'] . "
                                    AND Estatus <> 'Realizado'
                        
                                UNION
                        
                                SELECT 1 
                                FROM ReparacionCorrectiva 
                                WHERE MCO_ID = " . $data['idActual'] . "
                                    AND Estatus <> 'Realizado'
                            );
            ";

            if($result){
                $result = pg_query($query);
            }

            $query = "  UPDATE MantenimientoCorrectivo
                        SET Estatus = 'Realizado'
                        WHERE mco_id = " . $data['idActual'] . "
                            AND NOT EXISTS(
                                SELECT 1 
                                FROM CambioCorrectivo 
                                WHERE MCO_ID = " . $data['idActual'] . "
                                    AND Estatus <> 'Realizado'
                        
                                UNION
                        
                                SELECT 1 
                                FROM ReparacionCorrectiva 
                                WHERE MCO_ID = " . $data['idActual'] . "
                                    AND Estatus <> 'Realizado'
                            );
            ";

            if($result){
                $result = pg_query($query);
            }

            $query = " DELETE FROM Alertas WHERE Tabla = 'MantenimientoCorrectivo' AND TAB_ID = " . $data['idActual'];
            
            if($result){
                $result = pg_query($query);
            }

            $query = "  SELECT  MCO.Documento,
                                to_char(MCO.Fec_Cre,'DD/MM/YYYY') Fecha,
                                BIE.nombre BIE_NOM,
                                BIE.bie_id,
                                USU.Nombre USU_NOM,
                                COALESCE(APR.Nombre) apr_nom,
                                LOC.nombre LOC_NOM,
                                Loc.secuencia
                        FROM MantenimientoCorrectivo MCO
                            LEFT JOIN CorrectivoPlanificado CPL ON CPL.cpl_id = MCO.cpl_id
                            LEFT JOIN Mantenimiento MAN ON MAN.man_id = CPL.man_id
                            LEFT JOIN MantenimientoCorrectivo MCO2 ON MCO2.mco_id = CPL.mco_id
                            JOIN Bienes BIE ON BIE.BIE_ID = COALESCE(MAN.bie_id,MCO2.bie_id,MCO.BIE_ID)
                            JOIN Usuarios USU ON USU.USU_ID = MCO.USU_CRE
                            LEFT JOIN Usuarios APR ON APR.usu_id = MCO.USU_apr
                            JOIN Localizaciones LOC ON LOC.LOC_ID = BIE.LOC_ID
                        WHERE MCO.mco_id = " . $data['idActual'];

            if($result){
                $result = pg_query($query);
                
                if($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                    $titulo = "Mantenimiento Correctivo Afectado " . $line['documento'];
              
                    $descripcion = "<table style=\"width:100%\"><tr><td style=\"width:30%\"><strong>Documento:</strong></td><td style=\"width:70%\">" . $line['documento'] . "</td></tr>";
                    $descripcion .= "<td><strong>Bien:</strong> </td><td>" . $line['bie_nom'] . "</td></tr>";
                    $descripcion .= "<td><strong>Localizaci&oacute;n:</strong> </td><td>" . $line['loc_nom'] . "</td></tr>";
                    $descripcion .= "<td><strong>Solicitante:</strong> </td><td>" . $line['usu_nom'] . "</td></tr>";
                    $descripcion .= "<td><strong>Aprobador:</strong> </td><td>" . $line['apr_nom'] . "</td></tr>";
                    $descripcion .= "<td><strong>Fecha:</strong> </td><td>" . $line['fecha'] . "</td></tr></table>";

                    $MensajeCorreo = "<strong>Documento:</strong> " . $line['documento'] . "<br/>";
                    $MensajeCorreo .= "<strong>Bien:</strong> " . $line['bie_nom'] . "<br/>";
                    $MensajeCorreo .= "<strong>Localizaci&oacute;n:</strong> " . $line['loc_nom'] . "<br/>";
                    $MensajeCorreo .= "<strong>Solicitante:</strong> " . $line['usu_nom'] . "<br/>";
                    $MensajeCorreo .= "<strong>Aprobador:</strong> " . $line['apr_nom'] . "<br/>";
                    $MensajeCorreo .= "<strong>Fecha:</strong> " . $line['fecha'];
                    
                    $correoMasivo = array(
                        "id"        => $data['idActual'],
                        "Opcion"    => "Mantenimiento Correctivo",
                        "Tabla"     => "MantenimientoCorrectivo",
                        "Estatus"   => "Afectado",
                        "Secuencia" => $line['secuencia'],
                        "Titulo"    => $titulo,
                        "Menu"      => "Mantenimiento", 
                        "Cuerpo"    =>$MensajeCorreo
                    );

                    $query = "INSERT INTO Alertas(Titulo,bie_id, Menu, Tabla, TAB_ID,Usu_Cre,Descripcion)
                        VALUES('" . $titulo . "',"
                        . $line['bie_id'] .",'Mantenimiento','MantenimientoCorrectivo',"
                        . $data['idActual'] . ","
                        .$this->session->userdata("usu_id") . ",'"
                        . str_replace("'", "''",$descripcion) . "')";
                        
                    $result = pg_query($query);
                }else{
                    $result = false;
                }
            }
             
            $query = "  DELETE FROM ALERTAS 
                        WHERE Tabla = 'MantenimientoCorrectivo' AND TAB_ID = " . $data['idActual'] ."
                            AND EXISTS (
                                SELECT 1
                                FROM MantenimientoCorrectivo
                                where mco_id = " . $data['idActual'] ."
                                    AND Estatus = 'Realizado'
                            );";

            if($result){
                $result = pg_query($query);
            }
                
            if(!$result){
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                die(pg_last_error());
            }else
                pg_query("COMMIT") or die("Transaction commit failed");

                
            //Liberar memoria
            pg_free_result($result);
            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);


            $this->alertas_model->EnviarCorreo($correoMasivo);
            
            /************************************/
            /*         Inicio Auditorias        */
            /************************************/

            $datosActual = $this->Obtener($data['idActual'],true);

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            
            $datos = array(
                'Opcion' => 'Realizar Operaciones',
                'Tabla' => 'MantenimientoCorrectivo', 
                'Tab_id' => $data['idActual'],
                'Datos' => json_encode($datosActual)
            );
            
            $result = $this->auditorias_model->Insertar($datos);
            

            if(!$result){
                $error = pg_last_error();
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                die($error);
            }else
                pg_query("COMMIT") or die("Transaction commit failed");

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            /************************************/
            /*         Fin Auditorias           */
            /************************************/

            return true;

        }

        /************************************/
        /*              Cambios             */
        /************************************/

        private function ObtenerCambiosHTML($correctivo){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            		
            $query ="   SELECT  CCO.CCO_ID,			
                                CCO.MCO_ID,			
                                CCO.PDA_ID,	
                                PDA.Nombre PDA_NOM,	
                                PDA.tpi_id,
                                COALESCE(CCO.OBR_ID,-1) OBR_ID,			
                                COALESCE(OBR.Nombre,'') OBR_NOM,			
                                COALESCE(CCO.PRO_ID,-1) PRO_ID,			
                                COALESCE(PRO.Raz_Soc,'') PRO_NOM,			
                                CCO.BIE_ID,			
                                CCO.PCA_ID,	
                                PCA.Nombre PCA_NOM,			
                                CCO.ESTATUS,			
                                CCO.Fec_Ini,			
                                CCO.Fec_Fin,
                                CCO.fal_id,		
                                FAL.nombre falla,	
                                CCO.usu_cre,			
                                CCO.Fec_Cre,			
                                CCO.usu_mod,			
                                CCO.Fec_Mod,	
                                COALESCE(CCO.Observaciones,'') Observaciones
                        FROM CambioCorrectivo CCO
                            JOIN Piezas PDA ON PDA.PIE_ID = CCO.PDA_ID
                            JOIN Piezas PCA ON PCA.PIE_ID = CCO.PCA_ID
                            JOIN Fallas FAL ON FAL.fal_id = CCO.fal_id
                            LEFT JOIN Obreros OBR ON OBR.OBR_ID = CCO.OBR_ID
                            LEFT JOIN Proveedores PRO ON PRO.PRO_ID = CCO.PRO_ID
                        WHERE CCO.MCO_ID = " . $correctivo;

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $html = "";
            $retorno = [];

            while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                array_push($retorno,$line);
                $html = $html
                    . "<tr>"
                    . "    <td style=\"display:none;\">" . $line['cco_id'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['pda_id'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['tpi_id'] . "</td>"
                    . "    <td>" . $line['pda_nom'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['bie_id'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['pca_id'] . "</td>"
                    . "    <td>" . $line['pca_nom'] . "</td>"
                    . "    <td style=\"display:none;\">" . ($line['obr_id'] == -1 ? "" : $line['obr_id'] ). "</td>"
                    . "    <td>" . $line['obr_nom'] . "</td>"
                    . "    <td style=\"display:none;\">" . ($line['pro_id'] == -1 ? "" : $line['pro_id'] ) . "</td>"
                    . "    <td>" . $line['pro_nom'] . "</td>"
                    . "    <td>" . $line['fec_ini'] . "</td>"
                    . "    <td>" . $line['fec_fin'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['observaciones'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['fal_id'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['falla'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['estatus'] . "</td>";

                if($line['estatus'] == "Solicitado"){
                    $html = $html
                        . "    <td colspan=\"2\" class =\"editarCambio\" title =\"Editar Cambio\" style=\"text-align: center;cursor: pointer;\">"
                        . "        <span class=\"fa fa-pencil fa-lg\"></span>"
                        . "    </td>";
                }elseif($line['estatus'] == "Aprobado"){
                    $html = $html
                        . "    <td  class =\"editarCambio\" title =\"Editar Cambio\" style=\"text-align: center;cursor: pointer;border-right:none;\">"
                        . "        <span class=\"fa fa-pencil fa-lg\"></span>"
                        . "    </td>"
                        . "    <td class =\"realizarCambio\" title =\"Marcar Cambio como realizado\""
                        . "     style=\"text-align: center;cursor: pointer;\">"
                        . "        <span class=\"fa fa-square-o fa-lg\"></span>"
                        . "    </td>";
                }elseif($line['estatus'] == "Realizado"){
                    $html = $html
                        . "    <td colspan=\"2\" style=\"text-align: center;\" title =\"Cambio Realizado\">"
                        . "        <span class=\"fa fa-check fa-lg\"></span>"
                        . "    </td>";
                }

                $html = $html . "</tr>";
            }

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);


            return array("Array"=> $retorno, "html" => $html);
        }
        
        private function ObtenerCambioPDF($correctivo){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            		
            $query ="   SELECT  PDA.Nombre PDA_NOM,			
                                COALESCE(OBR.Nombre,'') OBR_NOM,		
                                COALESCE(PRO.Raz_Soc,'') PRO_NOM,
                                PCA.Nombre PCA_NOM,			
                                CCO.ESTATUS,
                                CCO.fal_id,
                                FAL.nombre falla,
                                COALESCE(to_char(CCO.Fec_Ini,'DD/MM/YYYY'),'') Fec_Ini,			
                                COALESCE(to_char(CCO.Fec_Fin,'DD/MM/YYYY'),'') Fec_Fin,				
                                COALESCE(CCO.Observaciones,'') Observaciones
                        FROM CambioCorrectivo CCO
                            JOIN Piezas PDA ON PDA.PIE_ID = CCO.PDA_ID
                            JOIN Piezas PCA ON PCA.PIE_ID = CCO.PCA_ID
                            JOIN Fallas FAL ON FAL.fal_id = CCO.fal_id
                            LEFT JOIN Obreros OBR ON OBR.OBR_ID = CCO.OBR_ID
                            LEFT JOIN Proveedores PRO ON PRO.PRO_ID = CCO.PRO_ID
                        WHERE CCO.MCO_ID = " . $correctivo;


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

        private function ObtenerTransaccionesCambios($cambios,$correctivo){
            $transacciones = [];

            $query = "DELETE FROM CambioCorrectivo WHERE MCO_ID = " . $correctivo;
            array_push($transacciones,$query);

            if(isset($cambios)){
                foreach ($cambios as $data) {

                    $query = "INSERT INTO CambioCorrectivo( MCO_ID,PDA_ID,BIE_ID,PCA_ID,
                                                            OBR_ID,PRO_ID,ESTATUS,
                                                            Fec_Ini,Fec_Fin,fal_id,
                                                            Usu_Cre,Usu_Mod,Observaciones)"
                            . "VALUES('"
                            . str_replace("'", "''",$correctivo)    . "','"
                            . str_replace("'", "''",$data['IdPiezaD']) . "',"
                            . (($data['idBienPiezaC'] == "") ? "null" : ("'" .str_replace("'", "''", $data['idBienPiezaC']) . "'"))    . ",'"
                            . str_replace("'", "''",$data['IdPiezaC'])    . "',"
                            . (($data['IdObr'] == "") ? "null" : ("'" .str_replace("'", "''", $data['IdObr']) . "'")) . ","
                            . (($data['IdPro'] == "") ? "null" : ("'" .str_replace("'", "''", $data['IdPro']) . "'")). ","
                            . "'Solicitado','"
                            . str_replace("'", "''",$data['Inicio'])    . "','"
                            . str_replace("'", "''",$data['Fin'])    . "','"
                            . str_replace("'", "''",$data['FallaCambio'])    . "',"
                            . $this->session->userdata("usu_id")    . ","
                            . $this->session->userdata("usu_id")    . ",'"
                            . str_replace("'", "''",$data['Observacion'])    . "');";

                    array_push($transacciones,$query);
                }
            }

            return $transacciones;
        }
        
        private function ObtenerTransaccionesCambiosAprobada($correctivo){
            
            $query = "  UPDATE CambioCorrectivo  
                        SET estatus = 'Aprobado',
                            Usu_Mod = " . $this->session->userdata("usu_id") . ",
                            Fec_Mod = NOW() 
                        WHERE mco_id = '" .str_replace("'", "''",$correctivo) . "';";

            return $query;
        }

        private function ObtenerTransaccionesCambiosRealizados($cambios,$correctivo,$Bien){
            $transacciones = [];

            if(isset($cambios)){
                foreach ($cambios as $data) {

                    $query = "  UPDATE CambioCorrectivo  
                                SET estatus = 'Realizado',
                                    Observaciones = " . (($data['Observacion'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observacion']) . "'")) . ",
                                    Usu_Mod = " . $this->session->userdata("usu_id") . ",
                                    Fec_Mod = NOW() 
                                WHERE mco_id = " . str_replace("'", "''",$correctivo) . "
                                    AND cco_id = " . str_replace("'", "''",$data['Id']); 

                    array_push($transacciones,$query);

                    $query = "  UPDATE Piezas 
                                SET Bie_Id = " . str_replace("'", "''",$Bien) . ",
                                    Usu_Mod = " . $this->session->userdata("usu_id") . ",
                                    Fec_Mod = NOW() 
                                WHERE Pie_Id = " . str_replace("'", "''",$data['IdPiezaC']); 

                    array_push($transacciones,$query);
                    
                    $query = "  UPDATE Piezas
                                SET Bie_Id = " . (($data['idBienPiezaC'] == "") ? "null" : ("'" .str_replace("'", "''", $data['idBienPiezaC']) . "'")). ",
                                    Usu_Mod = " . $this->session->userdata("usu_id") . ",
                                    Estatus = 'Inactivo',
                                    Fec_Mod = NOW() 
                                WHERE Pie_Id = " . str_replace("'", "''",$data['IdPiezaD']); 

                    array_push($transacciones,$query);
                }
            }

            return $transacciones;
        }

        private function ObtenerTransaccionesCambiosReversada($correctivo){
            
            $query = "  UPDATE CambioCorrectivo  
                        SET estatus = 'Solicitado',
                            Usu_Mod = " . $this->session->userdata("usu_id") . ",
                            Fec_Mod = NOW() 
                        WHERE mco_id = '" .str_replace("'", "''",$correctivo) . "';";

            return $query;
        }

        /************************************/
        /*          Reparaciones            */
        /************************************/

        private function ObtenerReparacionesHTML($correctivo){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            //Query para buscar usuario
            $query ="   SELECT  RCO.RCO_ID,			
                                RCO.MCO_ID,			
                                RCO.PIE_ID,	
                                PIE.Nombre PIE_NOM,	
                                COALESCE(RCO.OBR_ID,-1) OBR_ID,			
                                COALESCE(OBR.Nombre,'') OBR_NOM,			
                                COALESCE(RCO.PRO_ID,-1) PRO_ID,			
                                COALESCE(PRO.Raz_Soc,'') PRO_NOM,	
                                RCO.ESTATUS,			
                                RCO.Fec_Ini,			
                                RCO.Fec_Fin,			
                                RCO.fal_id,			
                                FAL.nombre falla,			
                                RCO.usu_cre,			
                                RCO.Fec_Cre,			
                                RCO.usu_mod,			
                                RCO.Fec_Mod,	
                                COALESCE(RCO.Observaciones,'') Observaciones
                        FROM ReparacionCorrectiva RCO
                            JOIN Piezas PIE ON PIE.PIE_ID = RCO.PIE_ID
                            JOIN Fallas FAL ON FAL.fal_id = RCO.fal_id
                            LEFT JOIN Obreros OBR ON OBR.OBR_ID = RCO.OBR_ID
                            LEFT JOIN Proveedores PRO ON PRO.PRO_ID = RCO.PRO_ID
                        WHERE RCO.MCO_ID = " . $correctivo;


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $html = "";
            $retorno = [];

            while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                array_push($retorno,$line);
                $html = $html
                    . "<tr>"
                    . "    <td style=\"display:none;\">" . $line['rco_id'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['pie_id'] . "</td>"
                    . "    <td>" . $line['pie_nom'] . "</td>"
                    . "    <td style=\"display:none;\">" . ($line['obr_id'] == -1 ? "" : $line['obr_id'] ). "</td>"
                    . "    <td>" . $line['obr_nom'] . "</td>"
                    . "    <td style=\"display:none;\">" . ($line['pro_id'] == -1 ? "" : $line['pro_id'] ) . "</td>"
                    . "    <td>" . $line['pro_nom'] . "</td>"
                    . "    <td>" . $line['fec_ini'] . "</td>"
                    . "    <td>" . $line['fec_fin'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['observaciones'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['estatus'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['fal_id'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['falla'] . "</td>";

                    if($line['estatus'] == "Solicitado"){
                        $html = $html
                            . "    <td colspan=\"2\" class =\"editarReparacion\" title =\"Editar Reparacion\" style=\"text-align: center;cursor: pointer;\">"
                            . "        <span class=\"fa fa-pencil fa-lg\"></span>"
                            . "    </td>";
                    }elseif($line['estatus'] == "Aprobado"){
                        $html = $html
                            . "    <td  class =\"editarReparacion\" title =\"Editar Reparacion\" style=\"text-align: center;cursor: pointer;border-right:none;\">"
                            . "        <span class=\"fa fa-pencil fa-lg\"></span>"
                            . "    </td>"
                            . "    <td class =\"realizarReparacion\" title =\"Marcar Reparacion como realizada\""
                            . "     style=\"text-align: center;cursor: pointer;\">"
                            . "        <span class=\"fa fa-square-o fa-lg\"></span>"
                            . "    </td>";
                    }elseif($line['estatus'] == "Realizado"){
                        $html = $html
                            . "    <td colspan=\"2\" style=\"text-align: center;\" title =\"Reparacion realizada\">"
                            . "        <span class=\"fa fa-check fa-lg\"></span>"
                            . "    </td>";
                    }
    
                    $html = $html . "</tr>";
            }

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);


            return array("Array"=> $retorno, "html" => $html);
        }
        
        private function ObtenerReparacionesPDF($correctivo){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query ="   SELECT  PIE.Nombre PIE_NOM,			
                                COALESCE(OBR.Nombre,'') OBR_NOM,		
                                COALESCE(PRO.Raz_Soc,'') PRO_NOM,	
                                RCO.ESTATUS,	
                                FAL.nombre falla,
                                COALESCE(to_char(RCO.Fec_Ini,'DD/MM/YYYY'),'') Fec_Ini,			
                                COALESCE(to_char(RCO.Fec_Fin,'DD/MM/YYYY'),'') Fec_Fin,			
                                COALESCE(RCO.Observaciones,'') Observaciones
                        FROM ReparacionCorrectiva RCO
                            JOIN Piezas PIE ON PIE.PIE_ID = RCO.PIE_ID
                            JOIN Fallas FAL ON FAL.fal_id = RCO.fal_id
                            LEFT JOIN obreros OBR ON OBR.OBR_ID = RCO.OBR_ID
                            LEFT JOIN Proveedores PRO ON PRO.PRO_ID = RCO.PRO_ID
                        WHERE RCO.MCO_ID = " . $correctivo;


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());


            $retorno = [];
            while($line = pg_fetch_array($result, null, PGSQL_ASSOC))
                array_push($retorno,$line);

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);


            return $retorno;
        }

        private function ObtenerTranRepRealizadas($reparaciones,$correctivo){
            $transacciones = [];
            if(isset($reparaciones)){
                foreach ($reparaciones as $data) {

                    $query = "  UPDATE ReparacionCorrectiva  
                                SET estatus = 'Realizado',
                                    Observaciones = " . (($data['Observacion'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observacion']) . "'")) . ",
                                    Usu_Mod = " . $this->session->userdata("usu_id") . ",
                                    Fec_Mod = NOW() 
                                WHERE mco_id = " . str_replace("'", "''",$correctivo) . " 
                                    AND rco_id = " . str_replace("'", "''",$data['Id']); 

                    array_push($transacciones,$query);
                    
                    $query = "  UPDATE Piezas
                                SET estatus = 'Activo',
                                    Usu_Mod = " . $this->session->userdata("usu_id") . ",
                                    Fec_Mod = NOW() 
                                WHERE Pie_Id = " . str_replace("'", "''",$data['IdPiezaD']); 

                    array_push($transacciones,$query);
                }
            }

            return $transacciones;
        }

        private function ObtenerTransaccionesReparaciones($reparaciones,$correctivo){
            $transacciones = [];

            $query = "DELETE FROM ReparacionCorrectiva WHERE MCO_ID = " . $correctivo;
            array_push($transacciones,$query);

            if(isset($reparaciones)){
                foreach ($reparaciones as $data) {
                    
                    $query = "INSERT INTO ReparacionCorrectiva( MCO_ID,PIE_ID,
                                                                obr_id,PRO_ID,ESTATUS,
                                                                Fec_Ini,Fec_Fin,fal_id,
                                                                Usu_Cre,Usu_Mod,Observaciones)"
                            . "VALUES('"
                            . str_replace("'", "''",$correctivo)    . "','"
                            . str_replace("'", "''",$data['IdPiezaD'])    . "',"
                            . (($data['IdObr'] == "") ? "null" : ("'" .str_replace("'", "''", $data['IdObr']) . "'")) . ","
                            . (($data['IdPro'] == "") ? "null" : ("'" .str_replace("'", "''", $data['IdPro']) . "'")). ","
                            . "'Solicitado','"
                            . str_replace("'", "''",$data['Inicio'])    . "','"
                            . str_replace("'", "''",$data['Fin'])    . "','"
                            . str_replace("'", "''",$data['FallaReparacion'])    . "',"
                            . $this->session->userdata("usu_id")    . ","
                            . $this->session->userdata("usu_id")    . ",'"
                            . str_replace("'", "''",$data['Observacion'])    . "');";
                    array_push($transacciones,$query);
                }

            }

            return $transacciones;
        }

        private function ObtenerTransaccionesReparacionesAprobada($correctivo){
            
            $query = "  UPDATE ReparacionCorrectiva  
                        SET estatus = 'Aprobado',
                            Usu_Mod = " . $this->session->userdata("usu_id") . ",
                            Fec_Mod = NOW() 
                        WHERE mco_id = '" .str_replace("'", "''",$correctivo) . "';";

            return $query;
        }

        private function ObtenerTransaccionesReparacionesReversada($correctivo){
            
            $query = "  UPDATE ReparacionCorrectiva  
                        SET estatus = 'Solicitado',
                            Usu_Mod = " . $this->session->userdata("usu_id") . ",
                            Fec_Mod = NOW() 
                        WHERE mco_id = '" .str_replace("'", "''",$correctivo) . "';";

            return $query;
        }
    }

?>