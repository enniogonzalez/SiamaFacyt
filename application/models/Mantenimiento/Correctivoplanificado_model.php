
<?php
    class Correctivoplanificado_model extends CI_Model{
        
        /************************************/
        /*          Mantenimiento           */
        /************************************/

        public function Actualizar($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " UPDATE CorrectivoPlanificado "
                . " SET man_id =". (($data['man_id'] == "") ? "null" : ("'" .str_replace("'", "''", $data['man_id']) . "'"))
                . ", mco_id = " . (($data['mco_id'] == "") ? "null" : ("'" .str_replace("'", "''", $data['mco_id']) . "'"))
                . ", fec_eje = '" . str_replace("'", "''",$data['fec_eje']) 
                . "', Usu_Mod = " . $this->session->userdata("usu_id") 
                . ", Fec_Mod = NOW()" 
                . ", Observaciones = "
                . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
                . " WHERE CPL_ID = '" . str_replace("'", "''",$data['cpl_id']) . "';";

            //Ejecutar Query
            $result = pg_query($query);

            
            if ($result){
                $TransCambio = $this->InsertarPiezasDA($data['PiezaDAs'],$data['cpl_id']);
            }

            /************************************/
            /*         Inicio Auditorias        */
            /************************************/
            if($result){

                $datos = array(
                    'Opcion' => 'Actualizar',
                    'Tabla' => 'CorrectivoPlanificado', 
                    'Tab_id' => $data['cpl_id'],
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

            $query = "  UPDATE CorrectivoPlanificado  
                        SET estatus = 'Aprobado'
                            , Usu_Apr = " . $this->session->userdata("usu_id") . "
                            , Usu_Mod = " . $this->session->userdata("usu_id") . "
                            , Fec_Apr = Now()
                            , Fec_Mod = Now()
                        WHERE cpl_id = '" .str_replace("'", "''",$id) . "';";
                
            //Ejecutar Query
            $result = pg_query($query);

            if($result){
                $result = $this->ObtenerTAprobado($id);
            }

            if($result){
                $query = " DELETE FROM Alertas WHERE Tabla = 'CorrectivoPlanificado' AND TAB_ID = " . $id;
                $result = pg_query($query);
            }

            if($result){
                $datosActual['estatus'] = 'Aprobado';
                $datos = array(
                    'Opcion' => 'Aprobar',
                    'Tabla' => 'CorrectivoPlanificado', 
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

        public function Busqueda($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            $condicion ="";


            if( $data['fec_ini'] != "" && $data['fec_fin'] != ""){
                $condicion = "((CPL.fec_eje BETWEEN '" . $data['fec_ini'] ."' AND '" . $data['fec_fin'] ."')
                            OR (CPL.fec_eje BETWEEN '" . $data['fec_ini'] ."' AND '" . $data['fec_fin'] ."'))";
            }

            if($data['busqueda'] != ""){
                $condicion = ($condicion == "" ? "": $condicion . " AND ")
                            . "(LOWER(cpl.documento) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(cpl.estatus) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR CONCAT((CASE
                                        WHEN CPL.man_id is not null THEN 'mantenimiento preventivo'
                                        WHEN CPL.mco_id is not null THEN 'mantenimiento correctivo'
                                    END),': ',LOWER(COALESCE(man.documento,mco.documento,'')))  like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(B.nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(CPL.estatus) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%')";
            }
            
            if($condicion != ""){
                $condicion =  " WHERE " . $condicion;
            }

            //Query para buscar usuario
            $query ="   SELECT  *
                        FROM (
                            SELECT  CPL.CPL_ID,	
                                    CPL.Documento,
                                    B.nombre Bie_Nom,
                                    B.bie_id,
                                    CPL.Estatus,
                                    COALESCE(MAN.documento,'') man_doc,
                                    COALESCE(MCO.documento,'') mco_doc,
                                    CASE
                                        WHEN CPL.man_id is not null THEN 'Mantenimiento Preventivo'
                                        WHEN CPL.mco_id is not null THEN 'Mantenimiento Correctivo'
                                    END Origen,
                                    COALESCE(man.documento,mco.documento,'') ori_doc,
                                    to_char(CPL.fec_eje,'DD/MM/YYYY') fec_eje,
                                    COALESCE(CPL.Observaciones,'') Observaciones,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $data['orden'] .") Fila
                            FROM CorrectivoPlanificado CPL
                                LEFT JOIN Mantenimiento MAN ON MAN.man_id = CPL.man_id
                                LEFT JOIN MantenimientoCorrectivo MCO ON MCO.mco_id = CPL.mco_id
                                JOIN Bienes B ON B.bie_id = COALESCE(MAN.bie_id,MCO.bie_id)
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

            $query = " DELETE FROM CorrectivoPlanificado "
                . " WHERE mco_id = '" .str_replace("'", "''",$id) . "';";
                
            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            if ($result){
                $query = " DELETE FROM Alertas WHERE Tabla = 'CorrectivoPlanificado' AND TAB_ID = " . $id;
                $result = pg_query($query);
            }

            if($result){
                $datos = array(
                    'Opcion' => 'Eliminar',
                    'Tabla' => 'CorrectivoPlanificado', 
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
            $query =" SELECT * FROM CorrectivoPlanificado WHERE LOWER(documento) ='" . mb_strtolower(str_replace("'", "''",$documento)) . "' " ;

            if($id != "")
                $query = $query . " AND CPL_ID <>'" . str_replace("'", "''",$id) . "' " ;

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

            $query = " INSERT INTO CorrectivoPlanificado ( Documento, Estatus, man_id, mco_id,
                                                            fec_eje,Usu_Cre,Usu_Mod, Observaciones) 
                        VALUES('"
            . str_replace("'", "''",$data['Documento'])    . "','Solicitado',"
            .(($data['man_id'] == "") ? "null" : ("'" .str_replace("'", "''", $data['man_id']) . "'")) . ","
            .(($data['mco_id'] == "") ? "null" : ("'" .str_replace("'", "''", $data['mco_id']) . "'")) . ",'"
            . str_replace("'", "''",$data['fec_eje'])    . "',"
            . $this->session->userdata("usu_id")    . ","
            . $this->session->userdata("usu_id")    . ","
            . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
            . ") RETURNING cpl_id;";

            //Ejecutar Query
            $result = pg_query($query);

            $new_id = "";

            if($result){
                $row = pg_fetch_row($result); 
                $new_id = $row['0']; 
            }

            if($result){
                $result = pg_query($this->ObtenerTransaccionDocumento($new_id));
            }

            if($result){
                $result = $this->InsertarPiezasDA($data['PiezaDAs'],$new_id);
            }

            // $query = "  SELECT  CPL.Documento,
            //                     to_char(CPL.Fec_Cre,'DD/MM/YYYY') Fecha,
            //                     BIE.nombre BIE_NOM,
            //                     USU.Nombre USU_NOM,
            //                     LOC.nombre LOC_NOM
            //             FROM CorrectivoPlanificado CPL
            //                 JOIN Bienes BIE ON BIE.BIE_ID = CPL.BIE_ID
            //                 JOIN Usuarios USU ON USU.USU_ID = CPL.USU_CRE
            //                 JOIN Localizaciones LOC ON LOC.LOC_ID = BIE.LOC_ID
            //             WHERE CPL.mco_id = " . $new_id;

            // $documento = "";
            // if($result){
            //     $result = pg_query($query);

            //     if($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
            //         $documento = $line['documento'];
            //         $titulo = "Mantenimiento Correctivo Solicitado " . $line['documento'];

            //         $descripcion = "<table style=\"width:100%\"><tr><td style=\"width:30%\"><strong>Documento:</strong></td><td style=\"width:70%\">" . $line['documento'] . "</td></tr>";
            //         $descripcion .= "<td><strong>Bien:</strong> </td><td>" . $line['bie_nom'] . "</td></tr>";
            //         $descripcion .= "<td><strong>Localizaci&oacute;n:</strong> </td><td>" . $line['loc_nom'] . "</td></tr>";
            //         $descripcion .= "<td><strong>Solicitante:</strong> </td><td>" . $line['usu_nom'] . "</td></tr>";
            //         $descripcion .= "<td><strong>Fecha:</strong> </td><td>" . $line['fecha'] . "</td></tr></table>";

            //         $MensajeCorreo = "<strong>Documento:</strong> " . $line['documento'] . "<br/>";
            //         $MensajeCorreo .= "<strong>Bien:</strong> " . $line['bie_nom'] . "<br/>";
            //         $MensajeCorreo .= "<strong>Localizaci&oacute;n:</strong> " . $line['loc_nom'] . "<br/>";
            //         $MensajeCorreo .= "<strong>Solicitante:</strong> " . $line['usu_nom'] . "<br/>";
            //         $MensajeCorreo .= "<strong>Fecha:</strong> " . $line['fecha'];
                    
            //         $correoMasivo = array(
            //             "id"        => $new_id,
            //             "Opcion"    => "Mantenimiento Correctivo",
            //             "Tabla"     => "CorrectivoPlanificado",
            //             "Estatus"   => "Solicitado",
            //             "Titulo"    => $titulo,
            //             "Menu"      => "Mantenimiento", 
            //             "Cuerpo"    =>$MensajeCorreo
            //         );

            //         $query = "INSERT INTO Alertas(Titulo, Menu, Tabla, TAB_ID,Usu_Cre,Descripcion)
            //             VALUES('" . $titulo . "','Mantenimiento','CorrectivoPlanificado',"
            //             . $new_id . ","
            //             .$this->session->userdata("usu_id") . ",'"
            //             . str_replace("'", "''",$descripcion) . "')";
                        
            //         $result = pg_query($query);
            //     }else{
            //         $result = false;
            //     }
            // }
            $documento = "hola";
            /************************************/
            /*         Inicio Auditorias        */
            /************************************/
            if($result){
                $data['Documento'] = $documento;
                $data['cpl_id'] = $new_id;
                $datos = array(
                    'Opcion' => 'Insertar',
                    'Tabla' => 'CorrectivoPlanificado', 
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

            //$this->alertas_model->EnviarCorreo($correoMasivo);
            
            return $new_id;
        }

        public function Obtener($id = '',$array = false){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  CPL.CPL_ID,	
                                CPL.Documento,
                                B.nombre Bie_Nom,
                                B.bie_id,
                                CPL.Estatus,
	                            COALESCE(CAST(CPL.man_id AS varchar(100)),'') man_id,
                                COALESCE(MAN.documento,'') man_doc,
	                            COALESCE(CAST(CPL.mco_id AS varchar(100)),'') mco_id,
                                CASE
                                    WHEN CPL.man_id is not null THEN 'Mantenimiento Preventivo'
                                    WHEN CPL.mco_id is not null THEN 'Mantenimiento Correctivo'
                                END Origen,
                                COALESCE(MCO.documento,'') mco_doc,
                                CPL.fec_eje,
                                COALESCE(CPL.Observaciones,'') Observaciones
                        FROM CorrectivoPlanificado CPL
                            LEFT JOIN Mantenimiento MAN ON MAN.man_id = CPL.man_id
                            LEFT JOIN MantenimientoCorrectivo MCO ON MCO.mco_id = CPL.mco_id
                            JOIN Bienes B ON B.bie_id = COALESCE(MAN.bie_id,MCO.bie_id)
                            
                        ";

            if($id != ''){
                $query = $query . " WHERE CPL.CPL_ID = '" . $id . "'";
            }

            $query = $query . " ORDER BY CPL.CPL_ID DESC LIMIT 1;";

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
                $piezas = $this->ObtenerPiezas($retorno['cpl_id']);

                if($array){
                    $retorno['Piezas'] = $piezas['Array'];
                }else{
                    $retorno['Piezas'] = $piezas['html'];
                }
            }


            return $retorno;
        }

        public function ObtenerInfoPDF($id){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  CPL.Documento,
                                CPL.CPL_ID,
                                B.nombre Bie_Nom,		
                                B.Inv_UC,		
                                CPL.Estatus,
                                CRE.Nombre Solicitante,
                                COALESCE(APR.Nombre,'') Aprobador,
                                to_char(CPL.Fec_Ini,'DD/MM/YYYY') Fec_ini,
                                to_char(CPL.Fec_Fin,'DD/MM/YYYY') Fec_Fin,
                                to_char(CPL.Fec_Cre,'DD/MM/YYYY') Fec_Cre,
                                COALESCE(to_char(CPL.Fec_Apr,'DD/MM/YYYY'),'') Fec_Apr,
                                COALESCE(CPL.Observaciones,'') Observaciones
                        FROM CorrectivoPlanificado CPL
                            JOIN Bienes B ON B.bie_id = CPL.bie_id
                            JOIN Usuarios CRE ON CRE.usu_id = CPL.usu_cre
                            LEFT JOIN Usuarios APR ON APR.usu_id = CPL.usu_apr
                        WHERE CPL.CPL_ID = '" . $id . "'";

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

        private function ObtenerTransaccionDocumento($id){

            $documento = substr("0000000000" . trim( $id),-10);
            $query = "UPDATE CorrectivoPlanificado "
                    . "SET  Documento = '" . $documento
                    ."' WHERE CPL_ID = " . $id;
            return $query;
        }

        public function ObtenerUsuarios($id){
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  CPL.usu_cre,
                                CRE.nombre cre_nom,
                                COALESCE(CPL.usu_apr,-1) usu_apr,
                                COALESCE(APR.nombre,'') apr_nom
                        FROM CorrectivoPlanificado CPL
                            JOIN Usuarios CRE ON CRE.usu_id = CPL.usu_cre
                            LEFT JOIN Usuarios APR ON APR.usu_id = CPL.usu_apr
                        WHERE CPL_ID = '" . $id . "'
                        ORDER BY CPL_ID DESC LIMIT 1;";

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
                        FROM CorrectivoPlanificado 
                        WHERE CPL_ID = " . str_replace("'", "''",$id) . "
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
                        FROM CorrectivoPlanificado 
                        WHERE CPL_ID = " . str_replace("'", "''",$id) . "
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

        /************************************/
        /*      Piezas Dañadas             */
        /************************************/

        private function InsertarPiezasDA($piezas,$correctivo){

            $query = "DELETE FROM CorrectivoPlanificadoPieza WHERE CPL_ID = " . $correctivo;
            
            //Ejecutar Query
            $result = pg_query($query);

            if(isset($piezas) && $result){
                foreach ($piezas as $data) {

                    $query = "INSERT INTO CorrectivoPlanificadoPieza( cpl_id,pie_id,fal_id,
                                                            Usu_Cre,Usu_Mod,Observaciones)"
                            . "VALUES('"
                            . str_replace("'", "''",$correctivo)    . "','"
                            . str_replace("'", "''",$data['pie_id']) . "','"
                            . str_replace("'", "''",$data['fal_id']) . "',"
                            . $this->session->userdata("usu_id")    . ","
                            . $this->session->userdata("usu_id")    . ",'"
                            . str_replace("'", "''",$data['Observacion'])    . "');";

                    //Ejecutar Query
                    $result = pg_query($query);
                    if(!$result){
                        break;
                    }
                }
            }

            return $result;
        }
        
        private function ObtenerCambioPDF($correctivo){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            		
            //Query para buscar usuario
            $query ="   SELECT  PDA.Nombre PDA_NOM,			
                                COALESCE(USU.Nombre,'') USU_NOM,		
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
                            LEFT JOIN Usuarios USU ON USU.USU_ID = CCO.USU_ID
                            LEFT JOIN Proveedores PRO ON PRO.PRO_ID = CCO.PRO_ID
                        WHERE CCO.CPL_ID = " . $correctivo;


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

        private function ObtenerPiezas($correctivo){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            		
            //Query para buscar usuario
            $query ="   SELECT  CPP.pie_id,
                                P.nombre pie_nom,
                                F.fal_id,
                                F.nombre fal_nom,
                                CPP.observaciones 
                        FROM CorrectivoPlanificadoPieza CPP
                            JOIN Piezas P ON P.pie_id = CPP.pie_id
                            JOIN Fallas F ON F.fal_id = CPP.fal_id
                        WHERE CPP.CPL_ID = " . $correctivo;


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $html = "";
            $retorno = [];

            while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                array_push($retorno,$line);
                $html = $html
                    . "<tr>"
                    . "    <td style=\"display:none;\">" . $line['pie_id'] . "</td>"
                    . "    <td>" . $line['pie_nom'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['fal_id'] . "</td>"
                    . "    <td>" . $line['fal_nom'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['observaciones'] . "</td>"
                    . "    <td colspan=\"2\" class =\"editarPiezaDA\" title =\"Editar Pieza Dañada\" style=\"text-align: center;cursor: pointer;\">"
                    . "        <span class=\"fa fa-pencil fa-lg\"></span>"
                    . "    </td>"
                    . "</tr>";
            }

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);


            return array("Array"=> $retorno, "html" => $html);
        }

        private function ObtenerTAprobado($correctivo){
            $query = "
                UPDATE Piezas 
                    SET Estatus = 'Inactivo'
                WHERE pie_id IN (   SELECT pie_id 
                                    FROM CorrectivoPlanificadoPieza
                                    WHERE cpl_id = " . $correctivo . ");";

            //Ejecutar Query
            $result = pg_query($query);

            return $result;
        }

    }

?>