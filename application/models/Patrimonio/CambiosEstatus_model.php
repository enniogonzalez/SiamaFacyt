
<?php
    class CambiosEstatus_model extends CI_Model{
        

        /************************************/
        /*          Cambios                 */
        /************************************/

        public function Insertar($data){
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " INSERT INTO CambiosEstatus ( Documento,doc_estatus, bie_id, bie_estatus,
                                            Usu_Cre,Usu_Mod, Observaciones) 
                        VALUES('"
            . str_replace("'", "''",$data['Documento'])    . "','Solicitado','"
            . str_replace("'", "''",$data['Bie_Id'])    . "','"
            . str_replace("'", "''",$data['Bie_estatus'])    . "',"
            . $this->session->userdata("usu_id")    . ","
            . $this->session->userdata("usu_id")    . ",'"
            . str_replace("'", "''", $data['Observaciones']) . "');";


            //Ejecutar Query
            $result = pg_query($query);


            if ($result){

                $UltimoId = $this->ObtenerUltimoIdInsertado();

                $result = pg_query("DELETE FROM CambioEstatusPieza WHERE CAM_ID = " . $UltimoId['cam_id']);

                $TransAgregado = $this->ObtenerTransCEP($data['PiezaCEs'],$UltimoId['cam_id']);

                if($data['Documento'] == "")
                    $result = pg_query($this->ObtenerTransaccionDocumento($UltimoId['cam_id']));

                for($i = 0; $result && $i < count($TransAgregado); $i++){
                    $result = pg_query($TransAgregado[$i]);
                }
                    
                $query = "  SELECT  CAM.Documento,
                                    to_char(CAM.Fec_Cre,'DD/MM/YYYY') Fecha,
                                    BIE.nombre BIE_NOM,
                                    USU.Nombre USU_NOM,
                                    LOC.nombre LOC_NOM
                            FROM CambiosEstatus CAM
                                JOIN Bienes BIE ON BIE.BIE_ID = CAM.BIE_ID
                                JOIN Usuarios USU ON USU.USU_ID = CAM.USU_CRE
                                JOIN Localizaciones LOC ON LOC.LOC_ID = BIE.LOC_ID
                            WHERE CAM.CAM_ID = " . $UltimoId['cam_id'];

                if($result){
                    $result = pg_query($query);

                    if($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                        $titulo = "Cambio de Estatus Solicitado " . $line['documento'];
                        $descripcion = "El d&iacute;a " . $line['fecha'] . " el usuario " . $line['usu_nom'] . " solicit&oacute; el cambio de estatus "
                                . $line['documento'] . " para el bien " . $line['bie_nom'] . " ubicado en " .  $line['loc_nom'] . "."; 

                        $query = "INSERT INTO Alertas(Titulo, Menu, Tabla, TAB_ID,Usu_Cre,Descripcion)
                            VALUES('" . $titulo . "','Patrimonio','CambiosEstatus',"
                            . $UltimoId['cam_id'] . ","
                            .$this->session->userdata("usu_id") . ",'"
                            . $descripcion . "')";
                            
                        $result = pg_query($query);
                    }else{
                        $result = false;
                    }
                }



                if(!$result)
                    pg_query("ROLLBACK") or die("Transaction rollback failed");
                else
                    pg_query("COMMIT") or die("Transaction commit failed");
                
                $Valido = $result;
            }else
                $Valido = false;


            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            if(!$Valido)
                die();

            return $UltimoId['cam_id'];
        }

        public function Actualizar($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " UPDATE CambiosEstatus "
                . " SET Bie_Id ='". str_replace("'", "''",$data['Bie_Id']) 
                . "', Bie_estatus = '" .str_replace("'", "''",$data['Bie_estatus'])
                . "', Documento = " 
                . (($data['Documento'] == "") ? "Documento" : ("'" .str_replace("'", "''", $data['Documento']) . "'")) 
                . ", Usu_Mod = " . $this->session->userdata("usu_id") 
                . ", Fec_Mod = NOW()" 
                . ", Observaciones = "
                . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
                . " WHERE CAM_ID = '" . str_replace("'", "''",$data['idActual']) . "';";


            
            //Ejecutar Query
            $result = pg_query($query);


            if ($result){
                
                $TransAgregado = $this->ObtenerTransCEP($data['PiezaCEs'],$data['idActual']);

                for($i = 0; $result && $i < count($TransAgregado); $i++){
                    $result = pg_query($TransAgregado[$i]);
                }
                    

                if(!$result)
                    pg_query("ROLLBACK") or die("Transaction rollback failed");
                else
                    pg_query("COMMIT") or die("Transaction commit failed");
                
                $retorno = $result;
            }else
                $retorno = false;


            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            if(!$retorno)
                die();

            return $retorno;
        }

        public function Obtener($id = ''){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  CAM.CAM_ID,		
                                CAM.BIE_ID,
                                CAM.Documento,
                                B.nombre Bie_Nom,		
                                CAM.doc_estatus,
                                CAM.bie_estatus,
                                COALESCE(CAM.Observaciones,'') Observaciones
                        FROM CambiosEstatus CAM
                            JOIN Bienes B ON B.bie_id = CAM.bie_id";

            if($id != ''){
                $query = $query . " WHERE CAM_ID = '" . $id . "'";
            }

            $query = $query . " ORDER BY CAM_ID DESC LIMIT 1;";

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
                $retorno['PiezaCEs'] = $this->ObtenerCEP($retorno['cam_id']);
            }


            return $retorno;
        }

        public function ObtenerInfoPDF($id){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  CAM.CAM_ID,	
                                CAM.Documento,
                                B.nombre Bie_Nom,			
                                B.Inv_UC,	
                                to_char(CAM.Fec_Cre,'DD/MM/YYYY') Fec_Cre,
                                COALESCE(to_char(CAM.Fec_Apr,'DD/MM/YYYY'),'') Fec_Apr,	
                                CRE.Nombre Solicitante,
                                COALESCE(APR.Nombre,'') Aprobador,	
                                CAM.doc_estatus,
                                CAM.bie_estatus,
                                COALESCE(CAM.Observaciones,'') Observaciones
                        FROM CambiosEstatus CAM
                            JOIN Bienes B ON B.bie_id = CAM.bie_id
                            JOIN Usuarios CRE ON CRE.usu_id = CAM.usu_cre
                            LEFT JOIN Usuarios APR ON APR.usu_id = CAM.usu_apr
                        WHERE CAM_ID = '" . $id . "'";


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
                $retorno['PiezaCEs'] = $this->ObtenerCepPDF($retorno['cam_id']);
            }


            return $retorno;
        }

        private function ObtenerUltimoIdInsertado(){

            //Query para buscar usuario
            $query ="   SELECT CAM_ID FROM CambiosEstatus 
                        WHERE Usu_cre = " . $this->session->userdata("usu_id") . "
                        ORDER BY CAM_ID DESC LIMIT 1;";

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
                            . "%' OR LOWER(CAM.documento) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(CAM.doc_estatus) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(CAM.bie_estatus) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%')";
            }
            
            //Query para buscar usuario
            $query ="   SELECT  cam_id,
                                nombre,
                                Documento,
                                doc_estatus,
                                bie_estatus,
                                Registros
                        FROM (
                            SELECT  CAM.cam_id,
                                    CAM.Documento,
                                    CAM.doc_estatus,
                                    CAM.bie_estatus,
                                    B.nombre,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $orden .") Fila
                            FROM CambiosEstatus CAM
                                JOIN Bienes B ON B.Bie_Id = CAM.Bie_Id
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

        public function ObtenerUsuarios($id){
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  CAM.usu_cre,
                                CRE.nombre cre_nom,
                                COALESCE(CAM.usu_apr,-1) usu_apr,
                                COALESCE(APR.nombre,'') apr_nom
                        FROM CambiosEstatus CAM
                            JOIN Usuarios CRE ON CRE.usu_id = CAM.usu_cre
                            LEFT JOIN Usuarios APR ON APR.usu_id = CAM.usu_apr
                        WHERE CAM_ID = '" . $id . "'
                        ORDER BY CAM_ID DESC LIMIT 1;";

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

        public function Eliminar($id){
      
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " DELETE FROM CambiosEstatus "
                . " WHERE cam_id = '" .str_replace("'", "''",$id) . "';";
                
            //Ejecutar Query
            $result = pg_query($query);

            
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($result){
                
                $query = " DELETE FROM Alertas WHERE Tabla = 'CambiosEstatus' AND TAB_ID = " . $id;
                $result = pg_query($query);


                if(!$result){
                    pg_query("ROLLBACK") or die("Transaction rollback failed");
                    die();
                }else{
                    pg_query("COMMIT") or die("Transaction commit failed");
                }

                $retorno = true;
            }else
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
            $query =" SELECT * FROM CambiosEstatus WHERE LOWER(documento) ='" . strtolower(str_replace("'", "''",$documento)) . "' " ;

            if($id != "")
                $query = $query . " AND CAM_ID <>'" . str_replace("'", "''",$id) . "' " ;

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
            $query = "UPDATE CambiosEstatus "
                    . "SET  Documento = '" . $documento
                    ."' WHERE CAM_ID = " . $id;
            return $query;
        }

        public function AprobarCambioEstatus($id){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = "  UPDATE CambiosEstatus  
                        SET doc_estatus = 'Aprobado'
                            , Usu_Apr = " . $this->session->userdata("usu_id") . "
                            , Usu_Mod = " . $this->session->userdata("usu_id") . "
                            , Fec_Apr = Now()
                            , Fec_Mod = Now()
                        WHERE cam_id = '" .str_replace("'", "''",$id) . "';";
                
            //Ejecutar Query
            $result = pg_query($query);


            if($result){
                $query = "  UPDATE Bienes 
                                SET Estatus = CambiosEstatus.bie_estatus
                            FROM CambiosEstatus
                            WHERE Bienes.bie_id = CambiosEstatus.bie_id
                                AND CambiosEstatus.cam_id = " . $id;
                $result = pg_query($query);
            }

            if ($result){

                $TransAgregado = $this->ObtenerTansAproCEP($id);

                for($i = 0; $result && $i < count($TransAgregado); $i++){
                    $result = pg_query($TransAgregado[$i]);
                }
                    

                $query = " DELETE FROM Alertas WHERE Tabla = 'CambiosEstatus' AND TAB_ID = " . $id;
                $result = pg_query($query);
                
            }


            if(!$result){
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                die();
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
                        FROM CambiosEstatus 
                        WHERE CAM_ID = " . str_replace("'", "''",$id) . "
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
                        FROM CambiosEstatus 
                        WHERE CAM_ID = " . str_replace("'", "''",$id) . "
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

        /************************************/
        /*      Cambios Estatus Pieza       */
        /************************************/

        private function ObtenerTransCEP($ceps,$cambio){
            $transacciones = [];

            $query = "DELETE FROM CambioEstatusPieza WHERE CAM_ID = " . $cambio;

            array_push($transacciones,$query);

            if(isset($ceps)){
                foreach ($ceps as $data) {

                    $query = "INSERT INTO CambioEstatusPieza( CAM_ID,PIE_ID,Estatus,
                                                        Usu_Cre,Usu_Mod,Observaciones)"
                            . "VALUES('"
                            . str_replace("'", "''",$cambio)    . "','"
                            . str_replace("'", "''",$data['IdPieza']) . "','"
                            . str_replace("'", "''",$data['Estatus']) . "',"
                            . $this->session->userdata("usu_id")    . ","
                            . $this->session->userdata("usu_id")    . ",'"
                            . str_replace("'", "''",$data['Observacion'])    . "');";

                    array_push($transacciones,$query);
                }
            }

            return $transacciones;
        }

        private function ObtenerTansAproCEP($cambio){
            
            $transacciones = [];

            $query = "  UPDATE Piezas 
                            SET Estatus = CambioEstatusPieza.estatus
                        FROM CambioEstatusPieza
                        WHERE Piezas.pie_id = CambioEstatusPieza.pie_id
                            AND CambioEstatusPieza.cam_id = " . $cambio;
            
            array_push($transacciones,$query);

            
            return $transacciones;
        }

        private function ObtenerCEP($cambio){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            
            $query ="   SELECT  CEP.CEP_ID,			
                                CEP.CAM_ID,			
                                CEP.PIE_ID,	
                                CEP.estatus,	
                                PIE.Nombre PIE_NOM,
                                COALESCE(PIE.Inv_UC,'') inv_uc,
                                CEP.Usu_Cre,			
                                CEP.Fec_Cre,			
                                CEP.Usu_Mod,			
                                CEP.Fec_Mod,	
                                COALESCE(CEP.Observaciones,'') Observaciones
                        FROM CambioEstatusPieza CEP
                            JOIN Piezas PIE ON PIE.PIE_ID = CEP.PIE_ID
                        WHERE CEP.CAM_ID = " . $cambio . "
                        ORDER BY CEP.CEP_ID ASC;";


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $html = "";
            
            while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                $html = $html
                    . "<tr>"
                    . "    <td style=\"display:none;\">" . $line['cep_id'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['pie_id'] . "</td>"
                    . "    <td>" . $line['pie_nom'] . "</td>"
                    . "    <td>" . $line['inv_uc'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['observaciones']
                    . "    <td>" . $line['estatus'] . "</td>"
                    . "    <td colspan=\"2\" class =\"editarPiezaCE\"  style=\"text-align: center;cursor: pointer;\">"
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

        private function ObtenerCepPDF($cambio){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            
            $query ="   SELECT  PIE.Nombre PIE_NOM,
                                COALESCE(PIE.Inv_UC,'') inv_uc,	
                                CEP.estatus,	
                                COALESCE(CEP.Observaciones,'') Observaciones
                        FROM CambioEstatusPieza CEP
                            JOIN Piezas PIE ON PIE.PIE_ID = CEP.PIE_ID
                        WHERE CEP.CAM_ID = " . $cambio . "
                        ORDER BY CEP.CEP_ID ASC;";


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