
<?php
    class Plantilla_model extends CI_Model{
        

        /************************************/
        /*          Mantenimiento           */
        /************************************/

        public function Insertar($data){
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " INSERT INTO PlantillaMantenimiento ( Documento,Bie_Id, Estatus, Frecuencia, 
                                                            Usu_Cre,Usu_Mod, Observaciones) 
                        VALUES('"
            . str_replace("'", "''",$data['Documento'])    . "','"
            . str_replace("'", "''",$data['Bie_Id'])    . "','Solicitado',"
            . str_replace("'", "''",$data['Frecuencia'])    . ","
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
                $TransTarea = $this->ObtenerTransaccionesTareas($data['Tareas'],$UltimoId['plm_id']);

                if($data['Documento'] == "")
                    $result = pg_query($this->ObtenerTransaccionDocumento($UltimoId['plm_id']));

                for($i = 0; $result && $i < count($TransTarea); $i++){
                    $result = pg_query($TransTarea[$i]);
                }
                    

                $query = "  SELECT  PLM.Documento,
                                    to_char(PLM.Fec_Cre,'DD/MM/YYYY') Fecha,
                                    BIE.nombre BIE_NOM,
                                    USU.Nombre USU_NOM,
                                    LOC.nombre LOC_NOM
                            FROM PlantillaMantenimiento PLM
                                JOIN Bienes BIE ON BIE.BIE_ID = PLM.BIE_ID
                                JOIN Usuarios USU ON USU.USU_ID = PLM.USU_CRE
                                JOIN Localizaciones LOC ON LOC.LOC_ID = BIE.LOC_ID
                            WHERE PLM.plm_id = " . $UltimoId['plm_id'];

                if($result){
                    $result = pg_query($query);

                    if($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                        $titulo = "Plantilla de Mantenimiento Solicitada " . $line['documento'];
                        $descripcion = "El d&iacute;a " . $line['fecha'] . " el usuario " . $line['usu_nom'] . " solicit&oacute; la plantilla de mantenimiento "
                                . $line['documento'] . " para el bien " . $line['bie_nom'] . " ubicado en " .  $line['loc_nom'] . "."; 

                        $query = "INSERT INTO Alertas(Titulo, Menu, Tabla, TAB_ID,Usu_Cre,Descripcion)
                            VALUES('" . $titulo . "','Mantenimiento','PlantillaMantenimiento',"
                            . $UltimoId['plm_id'] . ","
                            .$this->session->userdata("usu_id") . ",'"
                            . $descripcion . "')";
                            
                        $result = pg_query($query);
                    }else{
                        $result = false;
                    }
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


            return $UltimoId['plm_id'];
        }

        public function Actualizar($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " UPDATE PlantillaMantenimiento "
                . " SET Bie_Id ='". str_replace("'", "''",$data['Bie_Id']) 
                . "', Fec_Ini = '" . str_replace("'", "''",$data['Fec_Ini']) 
                . "', Documento = " 
                . (($data['Documento'] == "") ? "Documento" : ("'" .str_replace("'", "''", $data['Documento']) . "'")) 
                . ", Fec_Fin = '" . str_replace("'", "''",$data['Fec_Fin']) 
                . "', Usu_Mod = " . $this->session->userdata("usu_id") 
                . ", Fec_Mod = NOW()" 
                . ", Observaciones = "
                . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
                . " WHERE PLM_ID = '" . str_replace("'", "''",$data['idActual']) . "';";


            
            //Ejecutar Query
            $result = pg_query($query);


            if ($result){
                
                $TransTarea = $this->ObtenerTransaccionesTareas($data['Tareas'],$data['idActual']);

                for($i = 0; $result && $i < count($TransTarea); $i++){
                    $result = pg_query($TransTarea[$i]);
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

            $query = "  UPDATE PlantillaMantenimiento
                        SET Estatus = 'Afectado'
                        WHERE PLM_ID = " . $data['idActual'] . "
                            AND EXISTS(
                                SELECT 1 
                                FROM PlantillaMantenimientoTarea 
                                WHERE PLM_ID = " . $data['idActual'] . "
                                    AND Estatus <> 'Realizado'
                        
                                UNION
                        
                                SELECT 1 
                                FROM ReparacionCorrectiva 
                                WHERE PLM_ID = " . $data['idActual'] . "
                                    AND Estatus <> 'Realizado'
                            );
            ";

            $result = pg_query($query);

            $query = "  UPDATE PlantillaMantenimiento
                        SET Estatus = 'Realizado'
                        WHERE PLM_ID = " . $data['idActual'] . "
                            AND NOT EXISTS(
                                SELECT 1 
                                FROM PlantillaMantenimientoTarea 
                                WHERE PLM_ID = " . $data['idActual'] . "
                                    AND Estatus <> 'Realizado'
                        
                                UNION
                        
                                SELECT 1 
                                FROM ReparacionCorrectiva 
                                WHERE PLM_ID = " . $data['idActual'] . "
                                    AND Estatus <> 'Realizado'
                            );
            ";

            $result = pg_query($query);

            if(!$result){
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                die(pg_last_error());
            }else
                pg_query("COMMIT") or die("Transaction commit failed");
                
            //Liberar memoria
            pg_free_result($result);
            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);


            return true;

        }

        public function Obtener($id = ''){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  PLM.PLM_ID,		
                                PLM.BIE_ID,
                                PLM.Documento,
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
                $retorno['Tareas'] = $this->ObtenerTareas($retorno['plm_id'],$retorno['estatus']);
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
        
        private function ObtenerUltimoIdInsertado(){

            //Query para buscar usuario
            $query ="   SELECT PLM_ID FROM PlantillaMantenimiento 
                        WHERE Usu_cre = " . $this->session->userdata("usu_id") . "
                        ORDER BY PLM_ID DESC LIMIT 1;";

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

        public function Busqueda($busqueda,$orden,$inicio,$fin){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            $condicion ="";


            if($busqueda != ""){
                $condicion = " WHERE  (LOWER(B.nombre) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(PLM.documento) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(PLM.estatus) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%')";
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

            if(!$result){
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                die(pg_last_error());
            }else
                pg_query("COMMIT") or die("Transaction commit failed");
                
            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return true;

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

        private function ObtenerTransaccionDocumento($id){

            $documento = substr("0000000000" . trim( $id),-10);
            $query = "UPDATE PlantillaMantenimiento "
                    . "SET  Documento = '" . $documento
                    ."' WHERE PLM_ID = " . $id;
            return $query;
        }

        public function AprobarMantenimiento($id){
            
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
            if(!$result){
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                die(pg_last_error());
            }else
                pg_query("COMMIT") or die("Transaction commit failed");
                    
            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return true;
        }

        public function ReversarMantenimiento($id){
            
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
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
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
                        $descripcion = "El d&iacute;a " . $line['fecha'] . " el usuario " . $line['usu_nom'] . " solicit&oacute; la plantilla de mantenimiento "
                                . $line['documento'] . " para el bien " . $line['bie_nom'] . " ubicado en " .  $line['loc_nom'] . "."; 

                        $query = "INSERT INTO Alertas(Titulo, Menu, Tabla, TAB_ID,Usu_Cre,Descripcion)
                            VALUES('" . $titulo . "','Mantenimiento','PlantillaMantenimiento',"
                            . $id . ","
                            .$this->session->userdata("usu_id") . ",'"
                            . $descripcion . "')";
                            
                        $result = pg_query($query);
                    }else{
                        $result = false;
                    }
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

            return true;
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

        /************************************/
        /*              Tareas             */
        /************************************/

        private function ObtenerTransaccionesTareas($tareas,$plantilla){

            $transacciones = [];

            $query = "DELETE FROM PlantillaMantenimientoTarea WHERE PLM_ID = " . $plantilla;

            array_push($transacciones,$query);

            if(isset($tareas)){
                foreach ($tareas as $data) {

                    $query = "INSERT INTO PlantillaMantenimientoTarea( PLM_ID, PIE_ID, Titulo, Minutos, Descripcion,
                                                            Herramientas, Usu_Cre, Usu_Mod, Observaciones) "
                            . "VALUES('"
                            . str_replace("'", "''",$plantilla)    . "','"
                            . str_replace("'", "''",$data['IdPieza']) . "','"
                            . str_replace("'", "''",$data['Titulo']) . "',"
                            . str_replace("'", "''",$data['Minutos']) . ",'"
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

        private function ObtenerTareas($plantilla,$estatus){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            		
            //Query para buscar usuario
            $query ="   SELECT  PMT.PMT_ID,			
                                PMT.PLM_ID,			
                                PMT.PIE_ID,	
                                PIE.Nombre PIE_NOM,		
                                PMT.Titulo,	
                                PMT.Minutos,	
                                PMT.Descripcion,	
                                PMT.Herramientas,	
                                COALESCE(PMT.Observaciones,'') Observaciones
                        FROM PlantillaMantenimientoTarea PMT
                            JOIN Piezas PIE ON PIE.PIE_ID = PMT.PIE_ID
                        WHERE PMT.PLM_ID = " . $plantilla;


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $html = "";
            //Si existe registro, se guarda. Sino se guarda false
            while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                $html = $html
                    . "<tr>"
                    . "    <td style=\"display:none;\">" . $line['pmt_id'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['pie_id'] . "</td>"
                    . "    <td>" . $line['pie_nom'] . "</td>"
                    . "    <td>" . $line['titulo'] . "</td>"
                    . "    <td>" . $line['minutos'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['herramientas'] . "</td>"
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
                                PMT.Herramientas,	
                                COALESCE(PMT.Observaciones,'') Observaciones
                        FROM PlantillaMantenimientoTarea PMT
                            JOIN Piezas PIE ON PIE.PIE_ID = PMT.PIE_ID
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
        
        private function ObtenerTareasMantenimiento($plantilla){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            		
            //Query para buscar usuario
            $query ="   SELECT  PMT.PMT_ID,			
                                PMT.PLM_ID,			
                                PMT.PIE_ID,	
                                PIE.Nombre PIE_NOM,		
                                PMT.Titulo,	
                                PMT.Minutos,	
                                PMT.Descripcion,	
                                PMT.Herramientas,	
                                COALESCE(PMT.Observaciones,'') Observaciones
                        FROM PlantillaMantenimientoTarea PMT
                            JOIN Piezas PIE ON PIE.PIE_ID = PMT.PIE_ID
                        WHERE PMT.PLM_ID = " . $plantilla;


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $html = "";
            //Si existe registro, se guarda. Sino se guarda false
            while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
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
                    . "    <td style=\"display:none;\">" . $line['herramientas'] . "</td>"
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
    }

?>