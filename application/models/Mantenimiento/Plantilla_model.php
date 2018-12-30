
<?php
    class Plantilla_model extends CI_Model{
        

        /************************************/
        /*          Mantenimiento           */
        /************************************/
   
        private function ObtenerTransaccionDocumento($id){

            $documento = substr("0000000000" . trim( $id),-10);
            $query = "UPDATE PlantillaMantenimiento "
                    . "SET  Documento = '" . $documento
                    ."' WHERE PLM_ID = " . $id;
            return $query;
        }

        public function Actualizar($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " UPDATE PlantillaMantenimiento "
                . " SET Bie_Id ='". str_replace("'", "''",$data['Bie_Id']) 
                . "', Documento = " 
                . (($data['Documento'] == "") ? "Documento" : ("'" .str_replace("'", "''", $data['Documento']) . "'")) 
                . ", Frecuencia = '" . str_replace("'", "''",$data['Frecuencia']) 
                . "', Fec_Ult = '" . str_replace("'", "''",$data['Fec_Ult']) 
                . "', Usu_Mod = " . $this->session->userdata("usu_id") 
                . ", Fec_Mod = NOW()" 
                . ", Observaciones = "
                . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
                . " WHERE PLM_ID = '" . str_replace("'", "''",$data['plm_id']) . "';";


            
            //Ejecutar Query
            $result = pg_query($query);


            if ($result){
                $result = $this->InsertarTareas($data['Tareas'],$data['plm_id']);
            }

            if($result){

                $datos = array(
                    'Opcion' => 'Actualizar',
                    'Tabla' => 'PlantillaMantenimiento', 
                    'Tab_id' => $data['plm_id'],
                    'Datos' => json_encode($data)
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

        public function AprobarMantenimiento($id){
            
            $datosActual = $this->Obtener($id,true);

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = "  UPDATE PlantillaMantenimiento  
                        SET estatus = 'Aprobado'
                            , Usu_Apr = " . $this->session->userdata("usu_id") . "
                            , Usu_Mod = " . $this->session->userdata("usu_id") . "
                            , Fec_Apr = Now()
                            , Fec_Mod = Now()
                        WHERE PLM_ID = '" .str_replace("'", "''",$id) . "';";
                
            //Ejecutar Query
            $result = pg_query($query);
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($result){
                $query = " DELETE FROM Alertas WHERE Tabla = 'PlantillaMantenimiento' AND TAB_ID = " . $id;
                $result = pg_query($query);
            }

            if($result){
                $datosActual['estatus'] = 'Aprobado';
                $datos = array(
                    'Opcion' => 'Aprobar',
                    'Tabla' => 'PlantillaMantenimiento', 
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

        public function Busqueda($busqueda,$orden,$inicio,$fin,$disponibles = false){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            $condicion ="";


            if($busqueda != ""){
                $condicion = "(LOWER(B.nombre) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(PLM.documento) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(PLM.estatus) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%')";
            }
            
            if($disponibles){
                $condicion .= ($condicion == "" ? "": " AND ") . " PLM.estatus = 'Aprobado'";
            }

            if($condicion != ""){
                $condicion = ($condicion == "" ? "": "WHERE ") . $condicion;
            }
            //Query para buscar usuario
            $query ="   SELECT  PLM_Id,
                                nombre,
                                Documento,
                                Estatus,
                                Registros
                        FROM (
                            SELECT  PLM.PLM_Id,
                                    PLM.Documento,
                                    PLM.Estatus,
                                    B.nombre,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $orden .") Fila
                            FROM PlantillaMantenimiento PLM
                                JOIN Bienes B ON B.Bie_Id = PLM.Bie_Id
                            " . $condicion . "

                        ) LD
                        WHERE Fila BETWEEN ". $inicio . " AND " . $fin . "
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

            $query = " DELETE FROM PlantillaMantenimiento "
                . " WHERE PLM_ID = '" .str_replace("'", "''",$id) . "';";
                
            //Ejecutar Query
            $result = pg_query($query);
            
            if ($result){
                $query = " DELETE FROM Alertas WHERE Tabla = 'PlantillaMantenimiento' AND TAB_ID = " . $id;
                $result = pg_query($query);
            }

            if($result){
                $datos = array(
                    'Opcion' => 'Eliminar',
                    'Tabla' => 'PlantillaMantenimiento', 
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
            $query =" SELECT * FROM PlantillaMantenimiento WHERE LOWER(documento) ='" . strtolower(str_replace("'", "''",$documento)) . "' " ;

            if($id != "")
                $query = $query . " AND PLM_ID <>'" . str_replace("'", "''",$id) . "' " ;

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

            $query = " INSERT INTO PlantillaMantenimiento ( Documento,Bie_Id, Estatus, Frecuencia,Fec_Ult, 
                                                            Usu_Cre,Usu_Mod, Observaciones) 
                        VALUES('"
            . str_replace("'", "''",$data['Documento'])    . "','"
            . str_replace("'", "''",$data['Bie_Id'])    . "','Solicitado',"
            . str_replace("'", "''",$data['Frecuencia'])    . ",'"
            . str_replace("'", "''",$data['Fec_Ult'])    . "',"
            . $this->session->userdata("usu_id")    . ","
            . $this->session->userdata("usu_id")    . ","
            . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
            . ") RETURNING plm_id;";

            //Ejecutar Query
            $result = pg_query($query);

            $new_id = "";
            
            if($result){
                $row = pg_fetch_row($result); 
                $new_id = $row['0']; 
            }

            if ($result){

                $result = $this->InsertarTareas($data['Tareas'],$new_id);

                if($data['Documento'] == "")
                    $result = pg_query($this->ObtenerTransaccionDocumento($new_id));

                // for($i = 0; $result && $i < count($TransTarea); $i++){
                //     $result = pg_query($TransTarea[$i]);
                // }
                    

                $query = "  SELECT  PLM.Documento,
                                    to_char(PLM.Fec_Cre,'DD/MM/YYYY') Fecha,
                                    BIE.nombre BIE_NOM,
                                    USU.Nombre USU_NOM,
                                    LOC.nombre LOC_NOM
                            FROM PlantillaMantenimiento PLM
                                JOIN Bienes BIE ON BIE.BIE_ID = PLM.BIE_ID
                                JOIN Usuarios USU ON USU.USU_ID = PLM.USU_CRE
                                JOIN Localizaciones LOC ON LOC.LOC_ID = BIE.LOC_ID
                            WHERE PLM.plm_id = " . $new_id;

                $documento = "";
                if($result){
                    $result = pg_query($query);

                    if($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                        $documento = $line['documento'];
                        $titulo = "Plantilla de Mantenimiento Solicitada " . $line['documento'];
                        
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
                            "Opcion"    => "Plantilla de Mantenimiento",
                            "Tabla"     => "PlantillaMantenimiento",
                            "Estatus"   => "Solicitado",
                            "Titulo"    => $titulo,
                            "Menu"      => "Mantenimiento", 
                            "Cuerpo"    =>$MensajeCorreo
                        );

                        $query = "INSERT INTO Alertas(Titulo, Menu, Tabla, TAB_ID,Usu_Cre,Descripcion)
                            VALUES('" . $titulo . "','Mantenimiento','PlantillaMantenimiento',"
                            . $new_id . ","
                            .$this->session->userdata("usu_id") . ",'"
                            .  str_replace("'", "''",$descripcion) . "')";
                            
                        $result = pg_query($query);
                    }else{
                        $result = false;
                    }
                }
            }


            if($result){
                $data['Documento'] = $documento;
                $data['plm_id'] = $new_id;
                $datos = array(
                    'Opcion' => 'Insertar',
                    'Tabla' => 'PlantillaMantenimiento', 
                    'Tab_id' => $new_id,
                    'Datos' => json_encode($data)
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

            //$this->alertas_model->EnviarCorreo($correoMasivo);

            return $new_id;
        }

        public function Obtener($id = '',$array = false){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  PLM.PLM_ID,		
                                PLM.BIE_ID,
                                PLM.Documento,
                                PLM.Fec_Ult,
                                B.nombre Bie_Nom,		
                                PLM.Estatus,		
                                PLM.Frecuencia,
                                COALESCE(PLM.Observaciones,'') Observaciones
                        FROM PlantillaMantenimiento PLM
                            JOIN Bienes B ON B.bie_id = PLM.bie_id";

            if($id != ''){
                $query = $query . " WHERE PLM_ID = '" . $id . "'";
            }

            $query = $query . " ORDER BY PLM_ID DESC LIMIT 1;";

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
                $tareas = $this->ObtenerTareas($retorno['plm_id'],$retorno['estatus']);

                if($array){
                    $retorno['Tareas'] = $tareas['Array'];
                }else{
                    $retorno['Tareas'] = $tareas['html'];
                }
            }

            return $retorno;
        }

        public function ObtenerInfoPDF($id){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  PLM.PLM_ID,	
                                PLM.Documento,
                                B.nombre Bie_Nom,	
                                B.Inv_UC,			
                                CRE.Nombre Solicitante,
                                COALESCE(APR.Nombre,'') Aprobador,
                                to_char(PLM.Fec_Cre,'DD/MM/YYYY') Fec_Cre,
                                COALESCE(to_char(PLM.Fec_Apr,'DD/MM/YYYY'),'') Fec_Apr,	
                                PLM.Estatus,		
                                PLM.Frecuencia,
                                COALESCE(PLM.Observaciones,'') Observaciones
                        FROM PlantillaMantenimiento PLM
                            JOIN Bienes B ON B.bie_id = PLM.bie_id
                            JOIN Usuarios CRE ON CRE.usu_id = PLM.usu_cre
                            LEFT JOIN Usuarios APR ON APR.usu_id = PLM.usu_apr
                        WHERE PLM_ID = '" . $id . "'";

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
                $retorno['Tareas'] = $this->ObtenerTareasPDF($retorno['plm_id']);
            }


            return $retorno;
        }

        public function ObtenerMantenimiento($id){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  ''  man_id,
                                ''  documento,	
                                PLM.BIE_ID,
                                PLM.PLM_ID,
                                B.nombre Bie_Nom,
                                'Solicitado' estatus,
                                ''  fec_ini,
                                ''  fec_fin,
                                ''  Observaciones
                        FROM PlantillaMantenimiento PLM
                            JOIN Bienes B ON B.bie_id = PLM.bie_id
                        WHERE PLM_ID = '" . $id . "'
                        ORDER BY PLM_ID DESC LIMIT 1;";

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
                $retorno['Tareas'] = $this->ObtenerTareasMantenimiento($retorno['plm_id']);
            }


            return $retorno;
        }

        public function ObtenerUsuarios($id){
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  PLM.usu_cre,
                                CRE.nombre cre_nom,
                                COALESCE(PLM.usu_apr,-1) usu_apr,
                                COALESCE(APR.nombre,'') apr_nom
                        FROM PlantillaMantenimiento PLM
                            JOIN Usuarios CRE ON CRE.usu_id = PLM.usu_cre
                            LEFT JOIN Usuarios APR ON APR.usu_id = PLM.usu_apr
                        WHERE PLM_ID = '" . $id . "'
                        ORDER BY PLM_ID DESC LIMIT 1;";

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
            $query ="   SELECT 1 
                        FROM PlantillaMantenimiento 
                        WHERE PLM_ID = " . str_replace("'", "''",$id) . "
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
            $query ="   SELECT 1 
                        FROM PlantillaMantenimiento 
                        WHERE PLM_ID = " . str_replace("'", "''",$id) . "
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
            $query ="   SELECT 1 
                        FROM PlantillaMantenimiento 
                        WHERE PLM_ID = " . str_replace("'", "''",$id) . "
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

            $query = "  UPDATE PlantillaMantenimiento  
                        SET estatus = 'Solicitado'
                            , Usu_Apr = null
                            , Usu_Mod = " . $this->session->userdata("usu_id") . "
                            , Fec_Apr = null
                            , Fec_Mod = Now()
                        WHERE PLM_ID = '" .str_replace("'", "''",$id) . "';";

                
            //Ejecutar Query
            $result = pg_query($query);
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($result){


                $query = "  SELECT  PLM.Documento,
                                    to_char(PLM.Fec_Cre,'DD/MM/YYYY') Fecha,
                                    BIE.nombre BIE_NOM,
                                    USU.Nombre USU_NOM,
                                    LOC.nombre LOC_NOM
                            FROM PlantillaMantenimiento PLM
                                JOIN Bienes BIE ON BIE.BIE_ID = PLM.BIE_ID
                                JOIN Usuarios USU ON USU.USU_ID = PLM.USU_CRE
                                JOIN Localizaciones LOC ON LOC.LOC_ID = BIE.LOC_ID
                            WHERE PLM.plm_id = " . $id;

                if($result){
                    $result = pg_query($query);

                    if($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                        $titulo = "Plantilla de Mantenimiento Solicitada " . $line['documento'];

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
                            "Opcion"    => "Plantilla de Mantenimiento",
                            "Tabla"     => "PlantillaMantenimiento",
                            "Estatus"   => "Solicitado",
                            "Titulo"    => $titulo,
                            "Menu"      => "Mantenimiento", 
                            "Cuerpo"    =>$MensajeCorreo
                        );

                        $query = "INSERT INTO Alertas(Titulo, Menu, Tabla, TAB_ID,Usu_Cre,Descripcion)
                            VALUES('" . $titulo . "','Mantenimiento','PlantillaMantenimiento',"
                            . $id . ","
                            .$this->session->userdata("usu_id") . ",'"
                            .  str_replace("'", "''",$descripcion) . "')";
                            
                        $result = pg_query($query);
                    }else{
                        $result = false;
                    }
                }

            }

            if($result){
                $datosActual['estatus'] = 'Solicitado';
                $datos = array(
                    'Opcion' => 'Reversar',
                    'Tabla' => 'PlantillaMantenimiento', 
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

            //$this->alertas_model->EnviarCorreo($correoMasivo);

            return true;
        }

        /************************************/
        /*              Tareas             */
        /************************************/

        private function InsertarTareas($tareas,$plantilla){

            $query = "DELETE FROM PlantillaMantenimientoTarea WHERE PLM_ID = " . $plantilla;

            $result = pg_query($query);

            if(isset($tareas)){
                foreach ($tareas as $data) {

                    $query = "INSERT INTO PlantillaMantenimientoTarea( PLM_ID, tpi_id, Titulo, Minutos, Descripcion,
                                                                    Usu_Cre, Usu_Mod, Observaciones) "
                            . "VALUES('"
                            . str_replace("'", "''",$plantilla)    . "','"
                            . str_replace("'", "''",$data['IdPieza']) . "','"
                            . str_replace("'", "''",$data['Titulo']) . "',"
                            . str_replace("'", "''",$data['Minutos']) . ",'"
                            . str_replace("'", "''",$data['Descripcion'])    . "',"
                            . $this->session->userdata("usu_id")    . ","
                            . $this->session->userdata("usu_id")    . ","
                            . (($data['Observacion'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observacion']) . "'"))
                            . ") RETURNING PMT_ID;";
                    
                    $result = pg_query($query);

                    if($result){
                        
                        if($result){
                            $row = pg_fetch_row($result); 
                            $new_id = $row['0']; 
                            $result = $this->InsertarHerramientas($data['Herramientas'],$new_id);
                        }
                        
                    }
                    
                    if(!$result){
                        return false;
                    }
                }
            }

            return $result;
        }

        private function ObtenerTareas($plantilla,$estatus){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            		
            //Query para buscar usuario
            $query ="   SELECT  PMT.PMT_ID,			
                                PMT.PLM_ID,			
                                PMT.tpi_id,	
                                TPI.Nombre TPI_NOM,		
                                PMT.Titulo,	
                                PMT.Minutos,	
                                PMT.Descripcion,
                                COALESCE(PMT.Observaciones,'') Observaciones
                        FROM PlantillaMantenimientoTarea PMT
                            JOIN TipoPieza TPI ON TPI.tpi_id = PMT.tpi_id
                        WHERE PMT.PLM_ID = " . $plantilla;


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $html = "";
            $retorno = [];

            while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){

                $line['Herramientas'] = $this->ObtenerHerramientaJson($line['pmt_id']);
                array_push($retorno,$line);

                $html = $html
                    . "<tr>"
                    . "    <td style=\"display:none;\">" . $line['pmt_id'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['tpi_id'] . "</td>"
                    . "    <td>" . $line['tpi_nom'] . "</td>"
                    . "    <td>" . $line['titulo'] . "</td>"
                    . "    <td>" . $line['minutos'] . "</td>"
                    . "    <td style=\"display:none;\">" . json_encode($line['Herramientas']) . "</td>"
                    . "    <td style=\"display:none;\">" . $line['descripcion'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['observaciones'] . "</td>";

                    if($estatus == "Solicitado"){
                        $html = $html
                        . "    <td colspan=\"2\" class =\"editarTarea\" title =\"Editar Tarea\" style=\"text-align: center;cursor: pointer;\">"
                        . "        <span class=\"fa fa-pencil fa-lg\"></span>"
                        . "    </td>";
                    }else{
                        $html = $html
                        . "    <td colspan=\"2\" style=\"display:none;2\"></td>";

                    }
                    $html = $html . "</tr>";
            }

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return array("Array"=> $retorno, "html" => $html);
        }
        
        private function ObtenerTareasMantenimiento($plantilla){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            		
            //Query para buscar usuario
            $query ="   SELECT  PMT.PMT_ID,			
                                PMT.PLM_ID,			
                                PIE.pie_id,	
                                PIE.Nombre PIE_NOM,		
                                PMT.Titulo,	
                                PMT.Minutos,	
                                PMT.Descripcion,
                                COALESCE(PMT.Observaciones,'') Observaciones
                        FROM PlantillaMantenimientoTarea PMT
                            JOIN Piezas PIE ON PIE.tpi_id = PMT.tpi_id
                        WHERE PMT.PLM_ID = " . $plantilla . "
                        ORDER BY PIE.pie_id ASC, PMT.titulo ASC;";

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());


            $html = "";
            //Si existe registro, se guarda. Sino se guarda false
            while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){

                $herramientas = json_encode($this->ObtenerHerramientaJson( $line['pmt_id']));
                $html = $html
                    . "<tr>"
                    . "    <td style=\"display:none;\"></td>"
                    . "    <td style=\"display:none;\">" . $line['pie_id'] . "</td>"
                    . "    <td>" . $line['pie_nom'] . "</td>"
                    . "    <td>" . $line['titulo'] . "</td>"
                    . "    <td style=\"display:none;\"></td>"
                    . "    <td></td>"
                    . "    <td style=\"display:none;\"></td>"
                    . "    <td></td>"
                    . "    <td style=\"display:none;\">" . $herramientas . "</td>"
                    . "    <td style=\"display:none;\">" . $line['descripcion'] . "</td>"
                    . "    <td style=\"display:none;\"></td>"
                    . "    <td style=\"display:none;\"></td>"
                    . "    <td style=\"display:none;\">0</td>"
                    . "    <td style=\"display:none;\"></td>"
                    . "    <td style=\"display:none;\">" . $line['minutos'] . "</td>"
                    . "    <td style=\"display:none;\">Solicitado</td>"
                    . "    <td colspan=\"2\" class =\"editarTarea\" title =\"Editar Tarea\" style=\"text-align: center;cursor: pointer;\">"
                    . "        <span class=\"fa fa-pencil fa-lg\"></span>"
                    . "    </td>"
                    . "</tr>";
                
            }

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);


            return $html;
        }
        
        private function ObtenerTareasPDF($plantilla){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            		
            //Query para buscar usuario
            $query ="   SELECT  PIE.Nombre PIE_NOM,		
                                PMT.Titulo,	
                                PMT.Minutos,	
                                PMT.Descripcion,
                                COALESCE(PMT.Observaciones,'') Observaciones
                        FROM PlantillaMantenimientoTarea PMT
                            JOIN Piezas PIE ON PIE.tpi_id = PMT.tpi_id
                        WHERE PMT.PLM_ID = " . $plantilla;


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

        /************************************/
        /*          Herramientas            */
        /************************************/

        private function InsertarHerramientas($herramientas,$plantillaTarea){

            if(isset($herramientas)){
                foreach ($herramientas as $data) {

                    $query = "INSERT INTO PlantillaTareaHerramienta( PMT_ID, HER_ID) "
                            . "VALUES('"
                            . str_replace("'", "''",$plantillaTarea)    . "','"
                            . str_replace("'", "''",$data['Id'])
                            . "');";
                    
                    $result = pg_query($query);

                    if(!$result){
                        return false;
                    }
                }
            }

            return $result;
        }

        private function ObtenerHerramientaJson($plantillaTarea){

            //Query para buscar usuario
            $query ="   SELECT 	HER.her_id,
                                HER.nombre
                        FROM herramientas HER  
                            JOIN PlantillaTareaHerramienta PLT ON HER.her_id = PLT.her_id
                        WHERE PLT.PMT_ID = " . $plantillaTarea;


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            $retorno = [];

            while ($line = pg_fetch_array($result, null, PGSQL_ASSOC))
                array_push($retorno,array("Id" => $line["her_id"], "Herramienta" => $line["nombre"]));

            return $retorno;
        }
    }

?>