<?php
    class Ajustes_model extends CI_Model{
        

        /************************************/
        /*          Ajustes                 */
        /************************************/

        private function ObtenerTransaccionDocumento($id){

            $documento = substr("0000000000" . trim( $id),-10);
            $query = "UPDATE Ajustes "
                    . "SET  Documento = '" . $documento
                    ."' WHERE AJU_ID = " . $id;
            return $query;
        }

        private function ObtenerUltimoIdInsertado(){

            //Query para buscar usuario
            $query ="   SELECT AJU_ID FROM Ajustes 
                        WHERE Usu_cre = " . $this->session->userdata("usu_id") . "
                        ORDER BY AJU_ID DESC LIMIT 1;";

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

        public function Actualizar($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " UPDATE Ajustes "
                . " SET Bie_Id ='". str_replace("'", "''",$data['Bie_Id']) 
                . "', Documento = " 
                . (($data['Documento'] == "") ? "Documento" : ("'" .str_replace("'", "''", $data['Documento']) . "'")) 
                . ", Usu_Mod = " . $this->session->userdata("usu_id") 
                . ", Fec_Mod = NOW()" 
                . ", Observaciones = "
                . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
                . " WHERE AJU_ID = '" . str_replace("'", "''",$data['aju_id']) . "';";


            
            //Ejecutar Query
            $result = pg_query($query);


            if ($result){
                
                $TransAgregado = $this->ObtenerTransaccionesAgregados($data['Agregados'],$data['aju_id']);
                $TransQuitado = $this->ObtenerTransaccionesQuitados($data['Quitados'],$data['aju_id']);

                for($i = 0; $result && $i < count($TransAgregado); $i++){
                    $result = pg_query($TransAgregado[$i]);
                }
                    
                for($i = 0; $result && $i < count($TransQuitado); $i++){
                    $result = pg_query($TransQuitado[$i]);
                }

            }


            if($result){

                $datos = array(
                    'Opcion' => 'Actualizar',
                    'Tabla' => 'Ajustes', 
                    'Tab_id' => $data['aju_id'],
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

        public function AprobarAjuste($id){
            
            $datosActual = $this->Obtener($id,true);

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = "  UPDATE Ajustes  
                        SET estatus = 'Aprobado'
                            , Usu_Apr = " . $this->session->userdata("usu_id") . "
                            , Usu_Mod = " . $this->session->userdata("usu_id") . "
                            , Fec_Apr = Now()
                            , Fec_Mod = Now()
                        WHERE aju_id = '" .str_replace("'", "''",$id) . "';";
                
            //Ejecutar Query
            $result = pg_query($query);

            if ($result){

                $TransAgregado = $this->ObtenerTAprobadoAgregados($id);
                $TransQuitado = $this->ObtenerTAprobadoQuitados($id);

                for($i = 0; $result && $i < count($TransAgregado); $i++){
                    $result = pg_query($TransAgregado[$i]);
                }
                    
                for($i = 0; $result && $i < count($TransQuitado); $i++){
                    $result = pg_query($TransQuitado[$i]);
                }

                $query = " DELETE FROM Alertas WHERE Tabla = 'Ajustes' AND TAB_ID = " . $id;
                $result = pg_query($query);

            }

            if($result){
                $datosActual['estatus'] = 'Aprobado';
                $datos = array(
                    'Opcion' => 'Aprobar',
                    'Tabla' => 'Ajustes', 
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

        public function Busqueda($busqueda,$orden,$inicio,$fin){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            $condicion ="";


            if($busqueda != ""){
                $condicion = " WHERE  (LOWER(B.nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(AJU.documento) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(AJU.estatus) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%')";
            }
            
            //Query para buscar usuario
            $query ="   SELECT  Aju_Id,
                                nombre,
                                Documento,
                                Estatus,
                                Registros
                        FROM (
                            SELECT  AJU.Aju_Id,
                                    AJU.Documento,
                                    AJU.Estatus,
                                    B.nombre,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $orden .") Fila
                            FROM Ajustes AJU
                                JOIN Bienes B ON B.Bie_Id = AJU.Bie_Id
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

            $query = " DELETE FROM Ajustes "
                . " WHERE aju_id = '" .str_replace("'", "''",$id) . "';";
                
            //Ejecutar Query
            $result = pg_query($query);

            if ($result){  
                $query = " DELETE FROM Alertas WHERE Tabla = 'Ajustes' AND TAB_ID = " . $id;
                $result = pg_query($query);
            }

            if($result){
                $datos = array(
                    'Opcion' => 'Eliminar',
                    'Tabla' => 'Ajustes', 
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
            $query =" SELECT * FROM Ajustes WHERE LOWER(documento) ='" . mb_strtolower(str_replace("'", "''",$documento)) . "' " ;

            if($id != "")
                $query = $query . " AND AJU_ID <>'" . str_replace("'", "''",$id) . "' " ;

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

            $query = " INSERT INTO Ajustes ( Documento,Bie_Id, Estatus, 
                                            Usu_Cre,Usu_Mod, Observaciones) 
                        VALUES('"
            . str_replace("'", "''",$data['Documento'])    . "','"
            . str_replace("'", "''",$data['Bie_Id'])    . "','Solicitado',"
            . $this->session->userdata("usu_id")    . ","
            . $this->session->userdata("usu_id")    . ","
            . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
            . ");";

            //Ejecutar Query
            $result = pg_query($query);

            if ($result){

                $UltimoId = $this->ObtenerUltimoIdInsertado();

                $result = pg_query("DELETE FROM AjustesAccion WHERE AJU_ID = " . $UltimoId['aju_id']);

                $TransAgregado = $this->ObtenerTransaccionesAgregados($data['Agregados'],$UltimoId['aju_id']);
                $TransQuitado = $this->ObtenerTransaccionesQuitados($data['Quitados'],$UltimoId['aju_id']);

                if($data['Documento'] == "")
                    $result = pg_query($this->ObtenerTransaccionDocumento($UltimoId['aju_id']));

                for($i = 0; $result && $i < count($TransAgregado); $i++){
                    $result = pg_query($TransAgregado[$i]);
                }
                    
                for($i = 0; $result && $i < count($TransQuitado); $i++){
                    $result = pg_query($TransQuitado[$i]);
                }

                $query = "  SELECT  AJU.Documento,
                                    to_char(AJU.Fec_Cre,'DD/MM/YYYY') Fecha,
                                    BIE.nombre BIE_NOM,
                                    BIE.bie_id,
                                    USU.Nombre USU_NOM,
                                    LOC.nombre LOC_NOM,
                                    Loc.secuencia
                            FROM Ajustes AJU
                                JOIN Bienes BIE ON BIE.BIE_ID = AJU.BIE_ID
                                JOIN Usuarios USU ON USU.USU_ID = AJU.USU_CRE
                                JOIN Localizaciones LOC ON LOC.LOC_ID = BIE.LOC_ID
                            WHERE AJU.AJU_ID = " . $UltimoId['aju_id'];

                $documento = "";
                if($result){
                    $result = pg_query($query);

                    if($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                        $titulo = "Ajuste Solicitado " . $line['documento'];
                        $documento = $line['documento'];
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
                            "id"        => $UltimoId['aju_id'],
                            "Opcion"    => "Ajuste",
                            "Tabla"     => "Ajuste",
                            "Estatus"   => "Solicitado",
                            "Secuencia" => $line['secuencia'],
                            "Titulo"    => $titulo,
                            "Menu"      => "Patrimonio", 
                            "Cuerpo"    =>$MensajeCorreo
                        );

                        $query = "INSERT INTO Alertas(Titulo,bie_id, Menu, Tabla, TAB_ID,Usu_Cre,Descripcion)
                            VALUES('" . $titulo . "',"
                            . $line['bie_id'] .",'Patrimonio','Ajustes',"
                            . $UltimoId['aju_id'] . ","
                            .$this->session->userdata("usu_id") . ",'"
                            .str_replace("'", "''",$descripcion) . "')";
                            
                        $result = pg_query($query);
                    }else{
                        $result = false;
                    }
                }

                if($result){
                    $data['Documento'] = $documento;
                    $data['aju_id'] = $UltimoId['aju_id'];
                    $datos = array(
                        'Opcion' => 'Insertar',
                        'Tabla' => 'Ajustes', 
                        'Tab_id' => $UltimoId['aju_id'],
                        'Datos' => json_encode($data)
                    );
                    
                    $result = $this->auditorias_model->Insertar($datos);
                }

            }
            
            if(!$result){
                $error = pg_last_error();
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                die($error);
            }else
                pg_query("COMMIT") or die("Transaction commit failed");


            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            $this->alertas_model->EnviarCorreo($correoMasivo);

            return $UltimoId['aju_id'];
        }

        public function Obtener($id = '',$array = false){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  AJU.AJU_ID,		
                                AJU.BIE_ID,
                                AJU.Documento,
                                B.nombre Bie_Nom,		
                                AJU.Estatus,
                                COALESCE(AJU.Observaciones,'') Observaciones
                        FROM Ajustes AJU
                            JOIN Bienes B ON B.bie_id = AJU.bie_id";

            if($id != ''){
                $query = $query . " WHERE AJU_ID = '" . $id . "'";
            }

            $query = $query . " ORDER BY AJU_ID DESC LIMIT 1;";

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
                $agregados = $this->ObtenerAgregados($retorno['aju_id']);
                $quitados = $this->ObtenerQuitados($retorno['aju_id']);

                if($array){
                    $retorno['Agregados'] = $agregados['Array'];
                    $retorno['Quitados'] =  $quitados['Array'];
                }else{
                    $retorno['Agregados'] = $agregados['html'];
                    $retorno['Quitados'] =  $quitados['html'];
                }
            }

            return $retorno;
        }

        public function ObtenerInfoPDF($id){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  AJU.AJU_ID,	
                                AJU.Documento,
                                B.nombre Bie_Nom,			
                                B.Inv_UC,	
                                to_char(AJU.Fec_Cre,'DD/MM/YYYY') Fec_Cre,
                                COALESCE(to_char(AJU.Fec_Apr,'DD/MM/YYYY'),'') Fec_Apr,	
                                CRE.Nombre Solicitante,
                                COALESCE(APR.Nombre,'') Aprobador,	
                                AJU.Estatus,
                                COALESCE(AJU.Observaciones,'') Observaciones
                        FROM Ajustes AJU
                            JOIN Bienes B ON B.bie_id = AJU.bie_id
                            JOIN Usuarios CRE ON CRE.usu_id = AJU.usu_cre
                            LEFT JOIN Usuarios APR ON APR.usu_id = AJU.usu_apr
                        WHERE AJU_ID = '" . $id . "'";


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
                $retorno['Agregados'] = $this->ObtenerAgregadosPDF($retorno['aju_id']);
                $retorno['Quitados'] = $this->ObtenerQuitadosPDF($retorno['aju_id']);
            }


            return $retorno;
        }

        public function ObtenerUsuarios($id){
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  AJU.usu_cre,
                                CRE.nombre cre_nom,
                                COALESCE(AJU.usu_apr,-1) usu_apr,
                                COALESCE(APR.nombre,'') apr_nom
                        FROM Ajustes AJU
                            JOIN Usuarios CRE ON CRE.usu_id = AJU.usu_cre
                            LEFT JOIN Usuarios APR ON APR.usu_id = AJU.usu_apr
                        WHERE AJU_ID = '" . $id . "'
                        ORDER BY AJU_ID DESC LIMIT 1;";

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
                        FROM Ajustes 
                        WHERE AJU_ID = " . str_replace("'", "''",$id) . "
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
                        FROM Ajustes 
                        WHERE AJU_ID = " . str_replace("'", "''",$id) . "
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
        /*              Agregados           */
        /************************************/

        private function ObtenerAgregados($ajuste){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            
            $query ="   SELECT  AAC.AAC_ID,			
                                AAC.AJU_ID,			
                                AAC.PIE_ID,	
                                PIE.Nombre PIE_NOM,
                                COALESCE(PIE.Inv_UC,'') inv_uc,
                                AAC.Usu_Cre,			
                                AAC.Fec_Cre,			
                                AAC.Usu_Mod,			
                                AAC.Fec_Mod,
                                TPI.nombre nomtpi,	
                                COALESCE(AAC.Observaciones,'') Observaciones
                        FROM AjustesAccion AAC
                            JOIN Piezas PIE ON PIE.PIE_ID = AAC.PIE_ID
                            JOIN TipoPieza TPI ON TPI.tpi_id = PIE.tpi_id
                        WHERE AAC.AJU_ID = " . $ajuste . "
                            AND AAC.Tipo = 'Agregar'
                        ORDER BY AAC.AAC_ID ASC;";


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $html = "";
            $retorno = [];

            while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                
                array_push($retorno,$line);
                $html = $html
                    . "<tr>"
                    . "    <td style=\"display:none;\">" . $line['aac_id'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['pie_id'] . "</td>"
                    . "    <td>" . $line['pie_nom'] . "</td>"
                    . "    <td>" . $line['inv_uc'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['observaciones']
                    . "    <td>" . $line['nomtpi'] . "</td>"
                    . "    <td colspan=\"2\" class =\"editarAgregado\"  style=\"text-align: center;cursor: pointer;\">"
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

        private function ObtenerAgregadosPDF($ajuste){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            
            $query ="   SELECT  PIE.Nombre PIE_NOM,
                                COALESCE(PIE.Inv_UC,'') inv_uc,	
                                COALESCE(AAC.Observaciones,'') Observaciones
                        FROM AjustesAccion AAC
                            JOIN Piezas PIE ON PIE.PIE_ID = AAC.PIE_ID
                        WHERE AAC.AJU_ID = " . $ajuste . "
                            AND AAC.Tipo = 'Agregar'
                        ORDER BY AAC.AAC_ID ASC;";


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

        private function ObtenerTAprobadoAgregados($ajuste){
            
            $transacciones = [];

            $query = "  UPDATE Piezas 
                            SET BIE_ID = (SELECT BIE_ID FROM Ajustes WHERE AJU_ID = " . $ajuste . " LIMIT 1)
                        WHERE PIE_ID IN (
                                SELECT PIE_ID
                                FROM AjustesAccion
                                WHERE AJU_ID = " . $ajuste . "
                                    AND Tipo = 'Agregar'
                        );";
            
            array_push($transacciones,$query);

            
            return $transacciones;
        }

        private function ObtenerTransaccionesAgregados($agregados,$ajuste){
            $transacciones = [];

            $query = "DELETE FROM AjustesAccion WHERE tipo = 'Agregar' AND aju_id = " . $ajuste;

            array_push($transacciones,$query);

            if(isset($agregados)){
                foreach ($agregados as $data) {

                    $query = "INSERT INTO AjustesAccion( AJU_ID,PIE_ID,Tipo,
                                                        Usu_Cre,Usu_Mod,Observaciones)"
                            . "VALUES('"
                            . str_replace("'", "''",$ajuste)    . "','"
                            . str_replace("'", "''",$data['IdPieza']) . "','Agregar',"
                            . $this->session->userdata("usu_id")    . ","
                            . $this->session->userdata("usu_id")    . ",'"
                            . str_replace("'", "''",$data['Observacion'])    . "');";

                    array_push($transacciones,$query);
                }
            }

            return $transacciones;
        }

        /************************************/
        /*          Quitados                */
        /************************************/

        private function ObtenerQuitados($ajuste){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            //Query para buscar usuario
            $query ="   SELECT  AAC.AAC_ID,			
                                AAC.AJU_ID,			
                                AAC.PIE_ID,	
                                PIE.Nombre PIE_NOM,
                                COALESCE(PIE.Inv_UC,'') inv_uc,	
                                AAC.Usu_Cre,			
                                AAC.Fec_Cre,			
                                AAC.Usu_Mod,			
                                AAC.Fec_Mod,	
                                TPI.nombre nomtpi,	
                                COALESCE(AAC.Observaciones,'') Observaciones
                        FROM AjustesAccion AAC
                            JOIN Piezas PIE ON PIE.PIE_ID = AAC.PIE_ID
                            JOIN TipoPieza TPI ON TPI.tpi_id = PIE.tpi_id
                        WHERE AAC.AJU_ID = " . $ajuste . "
                            AND AAC.Tipo = 'Quitar'
                        ORDER BY AAC.AAC_ID ASC;";


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $html = "";
            $retorno = [];
            //Si existe registro, se guarda. Sino se guarda false
            while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                array_push($retorno,$line);
                $html = $html
                    . "<tr>"
                    . "    <td style=\"display:none;\">" . $line['aac_id'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['pie_id'] . "</td>"
                    . "    <td>" . $line['pie_nom'] . "</td>"
                    . "    <td>" . $line['inv_uc'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['observaciones']
                    . "    <td>" . $line['nomtpi'] . "</td>"
                    . "    <td colspan=\"2\" class =\"editarQuitado\"  style=\"text-align: center;cursor: pointer;\">"
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

        private function ObtenerQuitadosPDF($ajuste){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query ="   SELECT  PIE.Nombre PIE_NOM,
                            COALESCE(PIE.Inv_UC,'') inv_uc,	
                            COALESCE(AAC.Observaciones,'') Observaciones
                    FROM AjustesAccion AAC
                        JOIN Piezas PIE ON PIE.PIE_ID = AAC.PIE_ID
                    WHERE AAC.AJU_ID = " . $ajuste . "
                        AND AAC.Tipo = 'Quitar'
                    ORDER BY AAC.AAC_ID ASC;";

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
        
        private function ObtenerTAprobadoQuitados($ajuste){
            
            $transacciones = [];

            $query = "  UPDATE Piezas 
                            SET BIE_ID = null
                        WHERE PIE_ID IN (
                                SELECT PIE_ID
                                FROM AjustesAccion
                                WHERE AJU_ID = " . $ajuste . "
                                    AND Tipo = 'Quitar'
                        );";
            
            array_push($transacciones,$query);
            
            return $transacciones;
        }

        private function ObtenerTransaccionesQuitados($quitados,$ajuste){
            $transacciones = [];

            $query = "DELETE FROM AjustesAccion WHERE tipo = 'Quitar' AND aju_id = " . $ajuste;

            array_push($transacciones,$query);

            if(isset($quitados)){
                foreach ($quitados as $data) {
                    
                    $query = "INSERT INTO AjustesAccion( AJU_ID,PIE_ID,Tipo,
                                                        Usu_Cre,Usu_Mod,Observaciones)"
                            . "VALUES('"
                            . str_replace("'", "''",$ajuste)    . "','"
                            . str_replace("'", "''",$data['IdPieza']) . "','Quitar',"
                            . $this->session->userdata("usu_id")    . ","
                            . $this->session->userdata("usu_id")    . ",'"
                            . str_replace("'", "''",$data['Observacion'])    . "');";

                    array_push($transacciones,$query);
                }
            }

            return $transacciones;
        }
    }

?>