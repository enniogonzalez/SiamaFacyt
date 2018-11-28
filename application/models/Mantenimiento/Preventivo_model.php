
<?php
    class Preventivo_model extends CI_Model{
        

        /************************************/
        /*          Mantenimiento           */
        /************************************/

        public function Insertar($data){
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " INSERT INTO Mantenimiento ( Documento,Bie_Id, Estatus,plm_id,fec_ini,
                                                    fec_fin, Usu_Cre,Usu_Mod, Observaciones) 
                        VALUES('"
            . str_replace("'", "''",$data['Documento'])    . "','"
            . str_replace("'", "''",$data['Bie_Id'])    . "','Solicitado','"
            . str_replace("'", "''",$data['plm_id'])    . "','"
            . str_replace("'", "''",$data['Fec_Ini'])    . "','"
            . str_replace("'", "''",$data['Fec_Fin'])    . "',"
            . $this->session->userdata("usu_id")    . ","
            . $this->session->userdata("usu_id")    . ","
            . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
            . ");";

            // echo $query;
            //Ejecutar Query
            $result = pg_query($query);


            //Si existe registro, se guarda. Sino se guarda false
            if ($result){

                $UltimoId = $this->ObtenerUltimoIdInsertado();
                $TransTarea = $this->ObtenerTransaccionesTareas($data['Tareas'],$UltimoId['man_id']);

                if($data['Documento'] == "")
                    $result = pg_query($this->ObtenerTransaccionDocumento($UltimoId['man_id']));

                for($i = 0; $result && $i < count($TransTarea); $i++){
                    $result = pg_query($TransTarea[$i]);
                }   
            }
            

            $query = "
                DELETE FROM Alertas 
                WHERE tabla = 'PlantillaMantenimiento' 
                    AND tab_id = '" . str_replace("'", "''",$data['plm_id']) . "'
                    AND titulo like '%Vencida%'
                ";
            
            if ($result){
                $result = pg_query($query);
            }


            $query = "  SELECT  MAN.Documento,
                                to_char(MAN.Fec_Cre,'DD/MM/YYYY') Fecha,
                                BIE.nombre BIE_NOM,
                                USU.Nombre USU_NOM,
                                LOC.nombre LOC_NOM
                        FROM Mantenimiento MAN
                            JOIN Bienes BIE ON BIE.BIE_ID = MAN.BIE_ID
                            JOIN Usuarios USU ON USU.USU_ID = MAN.USU_CRE
                            JOIN Localizaciones LOC ON LOC.LOC_ID = BIE.LOC_ID
                        WHERE MAN.man_id = " . $UltimoId['man_id'];

            if($result){
                $result = pg_query($query);
                $documento = "";
                if($line = pg_fetch_array($result, null, PGSQL_ASSOC)){

                    $documento = $line['documento'];
                    $titulo = "Mantenimiento Preventivo Solicitado " . $line['documento'];

                    $descripcion = "<table style=\"width:100%\"><tr><td style=\"width:30%\"><strong>Documento:</strong></td><td style=\"width:70%\">" . $line['documento'] . "</td></tr>";
                    $descripcion .= "<td><strong>Bien:</strong> </td><td>" . $line['bie_nom'] . "</td></tr>";
                    $descripcion .= "<td><strong>Localizaci&oacute;n:</strong> </td><td>" . $line['loc_nom'] . "</td></tr>";
                    $descripcion .= "<td><strong>Solicitante:</strong> </td><td>" . $line['usu_nom'] . "</td></tr>";
                    $descripcion .= "<td><strong>Fecha:</strong> </td><td>" . $line['fecha'] . "</td></tr></table>";

                    // $descripcion = "El d&iacute;a " . $line['fecha'] . " el usuario " . $line['usu_nom'] . " solicit&oacute; el mantenimiento preventivo "
                    //         . $line['documento'] . " para el bien " . $line['bie_nom'] . " ubicado en " .  $line['loc_nom'] . "."; 

                    $query = "INSERT INTO Alertas(Titulo, Menu, Tabla, TAB_ID,Usu_Cre,Descripcion)
                        VALUES('" . $titulo . "','Mantenimiento','Mantenimiento',"
                        . $UltimoId['man_id'] . ","
                        .$this->session->userdata("usu_id") . ",'"
                        . str_replace("'", "''",$descripcion) . "')";
                        
                    $result = pg_query($query);
                }else{
                    $result = false;
                }
            }

            if($result){
                $data['Documento'] = $documento;
                $data['idActual'] = $UltimoId['man_id'];
                $datos = array(
                    'Opcion' => 'Insertar',
                    'Tabla' => 'Mantenimiento', 
                    'Tab_id' => $UltimoId['man_id'],
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

            return $UltimoId['man_id'];
        }

        public function Actualizar($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " UPDATE Mantenimiento "
                . " SET Bie_Id ='". str_replace("'", "''",$data['Bie_Id']) 
                . "', Fec_Ini = '" . str_replace("'", "''",$data['Fec_Ini']) 
                . "', Documento = " 
                . (($data['Documento'] == "") ? "Documento" : ("'" .str_replace("'", "''", $data['Documento']) . "'")) 
                . ", Fec_Fin = '" . str_replace("'", "''",$data['Fec_Fin']) 
                . "', Usu_Mod = " . $this->session->userdata("usu_id") 
                . ", Fec_Mod = NOW()" 
                . ", Observaciones = "
                . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
                . " WHERE MAN_ID = '" . str_replace("'", "''",$data['idActual']) . "';";


            
            //Ejecutar Query
            $result = pg_query($query);


            if ($result){
                
                $TransTarea = $this->ObtenerTransaccionesTareas($data['Tareas'],$data['idActual']);

                for($i = 0; $result && $i < count($TransTarea); $i++){
                    $result = pg_query($TransTarea[$i]);
                }

            }

            if($result){

                $datos = array(
                    'Opcion' => 'Actualizar',
                    'Tabla' => 'Mantenimiento', 
                    'Tab_id' => $data['idActual'],
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

        public function RealizarOperaciones($data){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $TransTarea = $this->ObtenerTransaccionesTareasRealizados($data['Tareas'],$data['idActual'],$data['Bie_Id']);
            
            $result = true;

            for($i = 0; $result && $i < count($TransTarea); $i++){
                $result = pg_query($TransTarea[$i]);
            }

            $query = "  UPDATE Mantenimiento
                        SET Estatus = 'Afectado',
                            Observaciones = "
                            . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'")) . "
                        WHERE MAN_ID = " . $data['idActual'] . "
                            AND EXISTS(
                                SELECT 1 
                                FROM MantenimientoTarea 
                                WHERE MAN_ID = " . $data['idActual'] . "
                                    AND Estatus <> 'Realizado'
                            );
                        
                        UPDATE Mantenimiento
                        SET Estatus = 'Realizado'
                        WHERE MAN_ID = " . $data['idActual'] . "
                            AND NOT EXISTS(
                                SELECT 1 
                                FROM MantenimientoTarea 
                                WHERE MAN_ID = " . $data['idActual'] . "
                                    AND Estatus <> 'Realizado'
                            );
            
                        DELETE FROM Alertas WHERE Tabla = 'Mantenimiento' AND TAB_ID = " . $data['idActual'] . ";";
            
            if($result){
                $result = pg_query($query);
            }

            $query = "  SELECT  MAN.Documento,
                                to_char(MAN.Fec_Cre,'DD/MM/YYYY') Fecha,
                                BIE.nombre BIE_NOM,
                                USU.Nombre USU_NOM,
                                COALESCE(APR.Nombre,'') apr_nom,
                                LOC.nombre LOC_NOM
                        FROM Mantenimiento MAN
                            JOIN Bienes BIE ON BIE.BIE_ID = MAN.BIE_ID
                            JOIN Usuarios USU ON USU.USU_ID = MAN.USU_CRE
                            LEFT JOIN Usuarios APR ON APR.usu_id = MAN.USU_apr
                            JOIN Localizaciones LOC ON LOC.LOC_ID = BIE.LOC_ID
                        WHERE MAN.man_id = " . $data['idActual'];

            if($result){
                $result = pg_query($query);
                
                if($line = pg_fetch_array($result, null, PGSQL_ASSOC)){

                    $titulo = "Mantenimiento Preventivo Afectado " . $line['documento'];
              
                    $descripcion = "<table style=\"width:100%\"><tr><td style=\"width:30%\"><strong>Documento:</strong></td><td style=\"width:70%\">" . $line['documento'] . "</td></tr>";
                    $descripcion .= "<td><strong>Bien:</strong> </td><td>" . $line['bie_nom'] . "</td></tr>";
                    $descripcion .= "<td><strong>Localizaci&oacute;n:</strong> </td><td>" . $line['loc_nom'] . "</td></tr>";
                    $descripcion .= "<td><strong>Solicitante:</strong> </td><td>" . $line['usu_nom'] . "</td></tr>";
                    $descripcion .= "<td><strong>Aprobador:</strong> </td><td>" . $line['apr_nom'] . "</td></tr>";
                    $descripcion .= "<td><strong>Fecha:</strong> </td><td>" . $line['fecha'] . "</td></tr></table>";

                    // $descripcion = "El d&iacute;a " . $line['fecha'] . " el usuario " . $line['usu_nom'] . " solicit&oacute; el mantenimiento preventivo "
                    //         . $line['documento'] . " para el bien " . $line['bie_nom'] . " ubicado en " .  $line['loc_nom'] . ", el cual actualmente se encuentra afectado."; 
    
                    $query = "INSERT INTO Alertas(Titulo, Menu, Tabla, TAB_ID,Usu_Cre,Descripcion)
                        VALUES('" . $titulo . "','Mantenimiento','Mantenimiento',"
                        . $data['idActual'] . ","
                        .$this->session->userdata("usu_id") . ",'"
                        . str_replace("'", "''",$descripcion) . "')";
                        
                    $result = pg_query($query);
                }else{
                    $result = false;
                }
            }
             
            $query = "  SELECT fec_fin,plm_id
                        FROM Mantenimiento
                        where man_id = " . $data['idActual'] ."
                            AND Estatus = 'Realizado'";

                            
            if($result){
                $result = pg_query($query);

                if($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                    
                    $query = "  UPDATE PlantillaMantenimiento
                                SET Fec_Ult = '" . $line['fec_fin'] ."'
                                WHERE plm_id = '" . $line['plm_id'] ."';
                    
                                DELETE FROM ALERTAS 
                                WHERE Tabla = 'Mantenimiento' AND TAB_ID = " . $data['idActual'] .";";
                    
                    $result = pg_query($query);
                }
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


            
            /************************************/
            /*         Inicio Auditorias        */
            /************************************/

            $datosActual = $this->Obtener($data['idActual'],true);

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            
            $datos = array(
                'Opcion' => 'Realizar Operaciones',
                'Tabla' => 'Mantenimiento', 
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

        public function Obtener($id = '',$array = false){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  MAN.MAN_ID,		
                                MAN.BIE_ID,
                                MAN.plm_id,
                                MAN.Documento,
                                B.nombre Bie_Nom,		
                                MAN.Estatus,		
                                MAN.Fec_Ini,
                                MAN.Fec_Fin,
                                COALESCE(MAN.Observaciones,'') Observaciones
                        FROM Mantenimiento MAN
                            JOIN Bienes B ON B.bie_id = MAN.bie_id";

            if($id != ''){
                $query = $query . " WHERE MAN_ID = '" . $id . "'";
            }

            $query = $query . " ORDER BY MAN_ID DESC LIMIT 1;";

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
                $tarea = $this->ObtenerTareas($retorno['man_id'],$retorno['estatus']);

                if($array){
                    $retorno['Tareas'] = $tarea['Array'];
                }else{
                    $retorno['Tareas'] = $tarea['html'];
                }
            }


            return $retorno;
        }

        public function ObtenerInfoPDF($id){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  MAN.MAN_ID,		
                                MAN.Documento,
                                B.nombre Bie_Nom,
                                B.Inv_UC,			
                                CRE.Nombre Solicitante,
                                COALESCE(APR.Nombre,'') Aprobador,
                                to_char(MAN.Fec_Cre,'DD/MM/YYYY') Fec_Cre,
                                COALESCE(to_char(MAN.Fec_Apr,'DD/MM/YYYY'),'') Fec_Apr,			
                                MAN.Estatus,		
                                MAN.Fec_Ini,
                                MAN.Fec_Fin,
                                COALESCE(MAN.Observaciones,'') Observaciones
                        FROM Mantenimiento MAN
                            JOIN Bienes B ON B.bie_id = MAN.bie_id
                            JOIN Usuarios CRE ON CRE.usu_id = MAN.usu_cre
                            LEFT JOIN Usuarios APR ON APR.usu_id = MAN.usu_apr
                        WHERE MAN_ID = '" . $id . "'";

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
                $retorno['Tareas'] = $this->ObtenerTareasPDF($retorno['man_id'],$retorno['estatus']);
            }


            return $retorno;
        }

        private function ObtenerUltimoIdInsertado(){

            //Query para buscar usuario
            $query ="   SELECT MAN_ID FROM Mantenimiento 
                        WHERE Usu_cre = " . $this->session->userdata("usu_id") . "
                        ORDER BY MAN_ID DESC LIMIT 1;";

            //Ejecutar Query
            $result = pg_query($query);
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) 
                $retorno = $line;
            else
                $retorno = false;

            //Liberar memoria
            pg_free_result($result);

            return $retorno;
        }

        public function Busqueda($busqueda,$orden,$inicio,$fin,$fec_ini,$fec_fin){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            $condicion ="";

            if( $fec_ini != "" && $fec_fin != ""){
                $condicion = " ((MAN.fec_ini BETWEEN '" . $fec_ini ."' AND '" . $fec_fin ."')
                            OR (MAN.fec_fin BETWEEN '" . $fec_ini ."' AND '" . $fec_fin ."'))";
            }

            if($busqueda != ""){
                $condicion = ($condicion == "" ? "": $condicion . " AND ")
                            . " (LOWER(B.nombre) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(MAN.documento) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(MAN.estatus) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%') ";
            }

            if($condicion != ""){
                $condicion =  " WHERE " . $condicion;
            }
            
            //Query para buscar usuario
            $query ="   SELECT  MAN_ID,
                                nombre,
                                fec_ini,
                                fec_fin,
                                Documento,
                                Estatus,
                                Registros
                        FROM (
                            SELECT  MAN.MAN_ID,
                                    MAN.Documento,
                                    MAN.Estatus,
                                    to_char(MAN.Fec_Ini,'DD/MM/YYYY') Fec_Ini,
                                    to_char(MAN.Fec_Fin,'DD/MM/YYYY') Fec_Fin,
                                    B.nombre,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $orden .") Fila
                            FROM Mantenimiento MAN
                                JOIN Bienes B ON B.Bie_Id = MAN.Bie_Id
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

            $query = " DELETE FROM Mantenimiento "
                . " WHERE MAN_ID = '" .str_replace("'", "''",$id) . "';";
                
            //Ejecutar Query
            $result = pg_query($query);
            
            if ($result){
                $query = " DELETE FROM Alertas WHERE Tabla = 'Mantenimiento' AND TAB_ID = " . $id;
                $result = pg_query($query);
            }

            if($result){
                $datos = array(
                    'Opcion' => 'Eliminar',
                    'Tabla' => 'Mantenimiento', 
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

        public function ObtenerUsuarios($id){
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  MAN.usu_cre,
                                CRE.nombre cre_nom,
                                COALESCE(MAN.usu_apr,-1) usu_apr,
                                COALESCE(APR.nombre,'') apr_nom
                        FROM Mantenimiento MAN
                            JOIN Usuarios CRE ON CRE.usu_id = MAN.usu_cre
                            LEFT JOIN Usuarios APR ON APR.usu_id = MAN.usu_apr
                        WHERE MAN_ID = '" . $id . "'
                        ORDER BY MAN_ID DESC LIMIT 1;";

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

        public function ExisteDocumento($documento,$id=""){

            if($documento == "")
                return false;

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query =" SELECT * FROM Mantenimiento WHERE LOWER(documento) ='" . strtolower(str_replace("'", "''",$documento)) . "' " ;

            if($id != "")
                $query = $query . " AND MAN_ID <>'" . str_replace("'", "''",$id) . "' " ;

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

        private function ObtenerTransaccionDocumento($id){

            $documento = substr("0000000000" . trim( $id),-10);
            $query = "UPDATE Mantenimiento "
                    . "SET  Documento = '" . $documento
                    ."' WHERE MAN_ID = " . $id;
            return $query;
        }

        public function AprobarMantenimiento($id){
            
            $datosActual = $this->Obtener($id,true);

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = "  UPDATE Mantenimiento  
                        SET estatus = 'Aprobado'
                            , Usu_Apr = " . $this->session->userdata("usu_id") . "
                            , Usu_Mod = " . $this->session->userdata("usu_id") . "
                            , Fec_Apr = Now()
                            , Fec_Mod = Now()
                        WHERE MAN_ID = '" .str_replace("'", "''",$id) . "';";
                
            //Ejecutar Query
            $result = pg_query($query);
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($result){
                $result = pg_query($this->ObtenerTransaccionesTareasAprobada($id));
            }

            $query = " DELETE FROM Alertas WHERE Tabla = 'Mantenimiento' AND TAB_ID = " . $id;
            
            if($result)
                $result = pg_query($query);

            $query = "  SELECT  MAN.Documento,
                                to_char(MAN.Fec_Cre,'DD/MM/YYYY') Fecha,
                                BIE.nombre BIE_NOM,
                                USU.Nombre USU_NOM,
                                COALESCE(APR.Nombre,'') apr_nom,   
                                LOC.nombre LOC_NOM
                        FROM Mantenimiento MAN
                            JOIN Bienes BIE ON BIE.BIE_ID = MAN.BIE_ID
                            JOIN Usuarios USU ON USU.USU_ID = MAN.usu_cre
                            LEFT JOIN Usuarios APR ON APR.usu_id = MAN.USU_apr
                            JOIN Localizaciones LOC ON LOC.LOC_ID = BIE.LOC_ID
                        WHERE MAN.man_id = " . $id;

            if($result){
                $result = pg_query($query);

                if($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                    $titulo = "Mantenimiento Preventivo Aprobado " . $line['documento'];
                        
                    $descripcion = "<table style=\"width:100%\"><tr><td style=\"width:30%\"><strong>Documento:</strong></td><td style=\"width:70%\">" . $line['documento'] . "</td></tr>";
                    $descripcion .= "<td><strong>Bien:</strong> </td><td>" . $line['bie_nom'] . "</td></tr>";
                    $descripcion .= "<td><strong>Localizaci&oacute;n:</strong> </td><td>" . $line['loc_nom'] . "</td></tr>";
                    $descripcion .= "<td><strong>Solicitante:</strong> </td><td>" . $line['usu_nom'] . "</td></tr>";
                    $descripcion .= "<td><strong>Aprobador:</strong> </td><td>" . $line['apr_nom'] . "</td></tr>";
                    $descripcion .= "<td><strong>Fecha:</strong> </td><td>" . $line['fecha'] . "</td></tr></table>";

                    // $descripcion = "El d&iacute;a " . $line['fecha'] . " el usuario " . $line['usu_nom'] . " aprob&oacute; el mantenimiento preventivo "
                    //         . $line['documento'] . " para el bien " . $line['bie_nom'] . " ubicado en " .  $line['loc_nom'] . "."; 

                    $query = "INSERT INTO Alertas(Titulo, Menu, Tabla, TAB_ID,Usu_Cre,Descripcion)
                        VALUES('" . $titulo . "','Mantenimiento','Mantenimiento',"
                        . $id . ","
                        .$this->session->userdata("usu_id") . ",'"
                        . $descripcion . "')";
                        
                    $result = pg_query($query);
                }else{
                    $result = false;
                }
            }

            if($result){
                $datosActual['estatus'] = 'Aprobado';
                $datos = array(
                    'Opcion' => 'Aprobar',
                    'Tabla' => 'Mantenimiento', 
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

        public function ReversarMantenimiento($id){
            
            $datosActual = $this->Obtener($id,true);

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = "  UPDATE Mantenimiento  
                        SET estatus = 'Solicitado'
                            , Usu_Apr = null
                            , Usu_Mod = " . $this->session->userdata("usu_id") . "
                            , Fec_Apr = null
                            , Fec_Mod = Now()
                        WHERE MAN_ID = '" .str_replace("'", "''",$id) . "';";

                
            //Ejecutar Query
            $result = pg_query($query);
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($result){
                $result = pg_query($this->ObtenerTransaccionesTareasReversada($id));
            }

            $query = " DELETE FROM Alertas WHERE Tabla = 'Mantenimiento' AND TAB_ID = " . $id;
                
            if($result)
                $result = pg_query($query);

            $query = "  SELECT  MAN.Documento,
                                to_char(MAN.Fec_Cre,'DD/MM/YYYY') Fecha,
                                BIE.nombre BIE_NOM,
                                USU.Nombre USU_NOM,
                                LOC.nombre LOC_NOM
                        FROM Mantenimiento MAN
                            JOIN Bienes BIE ON BIE.BIE_ID = MAN.BIE_ID
                            JOIN Usuarios USU ON USU.USU_ID = MAN.USU_CRE
                            JOIN Localizaciones LOC ON LOC.LOC_ID = BIE.LOC_ID
                        WHERE MAN.man_id = " . $id;

            if($result){
                $result = pg_query($query);

                if($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                    $titulo = "Mantenimiento Preventivo Solicitado " . $line['documento'];

                    $descripcion = "<table style=\"width:100%\"><tr><td style=\"width:30%\"><strong>Documento:</strong></td><td style=\"width:70%\">" . $line['documento'] . "</td></tr>";
                    $descripcion .= "<td><strong>Bien:</strong> </td><td>" . $line['bie_nom'] . "</td></tr>";
                    $descripcion .= "<td><strong>Localizaci&oacute;n:</strong> </td><td>" . $line['loc_nom'] . "</td></tr>";
                    $descripcion .= "<td><strong>Solicitante:</strong> </td><td>" . $line['usu_nom'] . "</td></tr>";
                    $descripcion .= "<td><strong>Fecha:</strong> </td><td>" . $line['fecha'] . "</td></tr></table>";

                    // $descripcion = "El d&iacute;a " . $line['fecha'] . " el usuario " . $line['usu_nom'] . " solicit&oacute; el mantenimiento preventivo "
                    //         . $line['documento'] . " para el bien " . $line['bie_nom'] . " ubicado en " .  $line['loc_nom'] . "."; 

                    $query = "INSERT INTO Alertas(Titulo, Menu, Tabla, TAB_ID,Usu_Cre,Descripcion)
                        VALUES('" . $titulo . "','Mantenimiento','Mantenimiento',"
                        . $id . ","
                        .$this->session->userdata("usu_id") . ",'"
                        . str_replace("'", "''",$descripcion) . "')";
                        
                    $result = pg_query($query);
                }else{
                    $result = false;
                }
            }

            if($result){
                $datosActual['estatus'] = 'Solicitado';
                $datos = array(
                    'Opcion' => 'Reversar',
                    'Tabla' => 'Mantenimiento', 
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

        public function PuedeEliminar($id){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query ="   SELECT 1 
                        FROM Mantenimiento 
                        WHERE MAN_ID = " . str_replace("'", "''",$id) . "
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

        public function PuedeAprobar($id){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query ="   SELECT 1 
                        FROM Mantenimiento 
                        WHERE MAN_ID = " . str_replace("'", "''",$id) . "
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

        public function PuedeReversar($id){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query ="   SELECT 1 
                        FROM Mantenimiento 
                        WHERE MAN_ID = " . str_replace("'", "''",$id) . "
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

        /************************************/
        /*              Tareas             */
        /************************************/

        private function ObtenerTransaccionesTareas($tareas,$mantenimiento){

            $transacciones = [];

            $query = "DELETE FROM MantenimientoTarea WHERE MAN_ID = " . $mantenimiento;

            array_push($transacciones,$query);

            if(isset($tareas)){
                foreach ($tareas as $data) {

                    $query = "INSERT INTO MantenimientoTarea(MAN_ID, PIE_ID, Titulo, ESTATUS, USU_ID, 
                                                            PRO_ID, Min_Asi, Min_Eje, Fec_Ini, Fec_Fin, 
                                                            Descripcion, Herramientas, Usu_Cre, Usu_Mod, Observaciones) "
                            . "VALUES('"
                            . str_replace("'", "''",$mantenimiento)    . "','"
                            . str_replace("'", "''",$data['IdPieza']) . "','"
                            . str_replace("'", "''",$data['Titulo']) . "','Solicitado',"
                            . (($data['idUsuario'] == "") ? "null" : ("'" .str_replace("'", "''", $data['idUsuario']) . "'")) . ","
                            . (($data['idProveedor'] == "") ? "null" : ("'" .str_replace("'", "''", $data['idProveedor']) . "'")). ","
                            . str_replace("'", "''",$data['Min_Asi']) . ","
                            . str_replace("'", "''",$data['Min_Eje']) . ",'"
                            . str_replace("'", "''",$data['Inicio']) . "','"
                            . str_replace("'", "''",$data['Fin']) . "','"
                            . str_replace("'", "''",$data['Descripcion'])    . "','"
                            . str_replace("'", "''",$data['Herramientas'])    . "',"
                            . $this->session->userdata("usu_id")    . ","
                            . $this->session->userdata("usu_id")    . ","
                            . (($data['Observacion'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observacion']) . "'"))
                            . ");";

                    array_push($transacciones,$query);
                }
            }

            return $transacciones;
        }

        private function ObtenerTransaccionesTareasAprobada($mantenimiento){
            
            $query = "  UPDATE MantenimientoTarea  
                        SET estatus = 'Aprobado',
                            Usu_Mod = " . $this->session->userdata("usu_id") . ",
                            Fec_Mod = NOW() 
                        WHERE man_id = '" .str_replace("'", "''",$mantenimiento) . "';";

            return $query;
        }

        private function ObtenerTransaccionesTareasRealizados($cambios,$correctivo,$Bien){
            
            $transacciones = [];

            if(isset($cambios)){
                foreach ($cambios as $data) {

                    $query = "  UPDATE MantenimientoTarea  
                                SET estatus = 'Realizado',
                                    Observaciones = "
                                    . (($data['Observacion'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observacion']) . "'")) . ",
                                    min_eje  = '" . str_replace("'", "''", $data['Min_Eje']) . "',
                                    Usu_Mod = " . $this->session->userdata("usu_id") . ",
                                    Fec_Mod = NOW() 
                                WHERE man_id = " . str_replace("'", "''",$correctivo) . "
                                    AND mta_id = " . str_replace("'", "''",$data['Id']); 

                    array_push($transacciones,$query);
                }
            }

            return $transacciones;
        }

        private function ObtenerTransaccionesTareasReversada($mantenimiento){
            
            $query = "  UPDATE MantenimientoTarea  
                        SET estatus = 'Solicitado',
                            Usu_Mod = " . $this->session->userdata("usu_id") . ",
                            Fec_Mod = NOW() 
                        WHERE man_id = '" .str_replace("'", "''",$mantenimiento) . "';";

            return $query;
        }

        private function ObtenerTareas($mantenimiento,$estatus){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            		
            //Query para buscar usuario
            $query ="   SELECT  MTA.MTA_ID,			
                                MTA.MAN_ID,			
                                MTA.PIE_ID,	
                                PIE.Nombre PIE_NOM,		
                                MTA.Titulo,	
                                MTA.Min_Eje,
                                MTA.Min_Asi,
                                MTA.Fec_Ini,
                                MTA.Fec_Fin,
                                MTA.estatus,	
                                MTA.Descripcion,
                                COALESCE(MTA.USU_ID,-1) USU_ID,			
                                COALESCE(USU.Nombre,'') USU_NOM,			
                                COALESCE(MTA.PRO_ID,-1) PRO_ID,			
                                COALESCE(PRO.Raz_Soc,'') PRO_NOM,	
                                MTA.Herramientas,	
                                COALESCE(MTA.Observaciones,'') Observaciones
                        FROM MantenimientoTarea MTA
                            JOIN Piezas PIE ON PIE.PIE_ID = MTA.PIE_ID
                            LEFT JOIN Usuarios USU ON USU.USU_ID = MTA.USU_ID
                            LEFT JOIN Proveedores PRO ON PRO.PRO_ID = MTA.PRO_ID
                        WHERE MTA.MAN_ID = " . $mantenimiento . "
                        ORDER BY MTA.MTA_ID ASC";


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $html = "";
            $retorno = [];

            while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                array_push($retorno,$line);
                $html = $html
                    . "<tr>"
                    . "    <td style=\"display:none;\">" . $line['mta_id'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['pie_id'] . "</td>"
                    . "    <td>" . $line['pie_nom'] . "</td>"
                    . "    <td>" . $line['titulo'] . "</td>"
                    . "    <td style=\"display:none;\">" . ($line['usu_id'] == -1 ? "" : $line['usu_id'] ). "</td>"
                    . "    <td>" . $line['usu_nom'] . "</td>"
                    . "    <td style=\"display:none;\">" . ($line['pro_id'] == -1 ? "" : $line['pro_id'] ) . "</td>"
                    . "    <td>" . $line['pro_nom'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['herramientas'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['descripcion'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['fec_ini'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['fec_fin'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['min_eje'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['observaciones'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['min_asi'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['estatus'] . "</td>";


                    if($line['estatus'] == "Solicitado"){
                        $html = $html
                            . "    <td colspan=\"2\" class =\"editarTarea\" title =\"Editar Tarea\" style=\"text-align: center;cursor: pointer;\">"
                            . "        <span class=\"fa fa-pencil fa-lg\"></span>"
                            . "    </td>";
                    }elseif($line['estatus'] == "Aprobado"){
                        $html = $html
                            . "    <td  class =\"editarTarea\" title =\"Editar Tarea\" style=\"text-align: center;cursor: pointer;border-right:none;\">"
                            . "        <span class=\"fa fa-pencil fa-lg\"></span>"
                            . "    </td>"
                            . "    <td class =\"realizarTarea\" title =\"Marcar Tarea como realizado\""
                            . "     style=\"text-align: center;cursor: pointer;\">"
                            . "        <span class=\"fa fa-square-o fa-lg\"></span>"
                            . "    </td>";
                    }elseif($line['estatus'] == "Realizado"){
                        $html = $html
                            . "    <td colspan=\"2\" style=\"text-align: center;\" title =\"Tarea Realizado\">"
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

        private function ObtenerTareasPDF($mantenimiento){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            		
            //Query para buscar usuario
            $query ="   SELECT  PIE.Nombre PIE_NOM,		
                                MTA.Titulo,	
                                MTA.Min_Eje,
                                MTA.Min_Asi,
                                to_char(MTA.Fec_Ini,'DD/MM/YYYY') Fec_Ini,
                                to_char(MTA.Fec_Fin,'DD/MM/YYYY') Fec_Fin,
                                MTA.estatus,	
                                MTA.Descripcion,		
                                COALESCE(USU.Nombre,'') USU_NOM,	
                                COALESCE(PRO.Raz_Soc,'') PRO_NOM,	
                                MTA.Herramientas,	
                                COALESCE(MTA.Observaciones,'') Observaciones
                        FROM MantenimientoTarea MTA
                            JOIN Piezas PIE ON PIE.PIE_ID = MTA.PIE_ID
                            LEFT JOIN Usuarios USU ON USU.USU_ID = MTA.USU_ID
                            LEFT JOIN Proveedores PRO ON PRO.PRO_ID = MTA.PRO_ID
                        WHERE MTA.MAN_ID = " . $mantenimiento ."
                        ORDER BY MTA.MTA_ID ASC";


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