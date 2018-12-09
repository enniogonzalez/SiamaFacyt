
<?php
    class Compatibilidad_model extends CI_Model{
        

        /************************************/
        /*          Compatibilidad          */
        /************************************/

        public function Insertar($data){
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " INSERT INTO Compatibilidad ( Documento,Bie_Id, Estatus, 
                                            Usu_Cre,Usu_Mod, Observaciones) 
                        VALUES('"
            . str_replace("'", "''",$data['Documento'])    . "','"
            . str_replace("'", "''",$data['Bie_Id'])    . "','Solicitado',"
            . $this->session->userdata("usu_id")    . ","
            . $this->session->userdata("usu_id")    . ","
            . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
            . ") RETURNING com_id;";

            //Ejecutar Query
            $result = pg_query($query);

            $new_id = "";

            if($result){
                $row = pg_fetch_row($result); 
                $new_id = $row['0']; 
            }

            if ($result){

                $TransAgregado = $this->ObtenerTransaccionesAgregados($data['Agregados'],$new_id);
                $TransQuitado = $this->ObtenerTransaccionesQuitados($data['Quitados'], $new_id);

                if($data['Documento'] == "")
                    $result = pg_query($this->ObtenerTransaccionDocumento($new_id));

                for($i = 0; $result && $i < count($TransAgregado); $i++){
                    $result = pg_query($TransAgregado[$i]);
                }
                    
                for($i = 0; $result && $i < count($TransQuitado); $i++){
                    $result = pg_query($TransQuitado[$i]);
                }

                $query = "  SELECT  COM.Documento,
                                    to_char(COM.Fec_Cre,'DD/MM/YYYY') Fecha,
                                    BIE.nombre BIE_NOM,
                                    USU.Nombre USU_NOM,
                                    LOC.nombre LOC_NOM
                            FROM Compatibilidad COM
                                JOIN Bienes BIE ON BIE.BIE_ID = COM.BIE_ID
                                JOIN Usuarios USU ON USU.USU_ID = COM.USU_CRE
                                JOIN Localizaciones LOC ON LOC.LOC_ID = BIE.LOC_ID
                            WHERE COM.com_id = " . $new_id;

                $documento = "";
                if($result){
                    $result = pg_query($query);

                    if($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                        $titulo = "Compatibilidad de Bien Solicitado " . $line['documento'];
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
                            "id"        => $new_id,
                            "Opcion"    => "Compatibilidad de Bien",
                            "Tabla"     => "Compatibilidad",
                            "Estatus"   => "Solicitado",
                            "Titulo"    => $titulo,
                            "Menu"      => "Patrimonio", 
                            "Cuerpo"    =>$MensajeCorreo
                        );

                        $query = "INSERT INTO Alertas(Titulo, Menu, Tabla, TAB_ID,Usu_Cre,Descripcion)
                            VALUES('" . $titulo . "','Patrimonio','Compatibilidad',"
                            . $new_id . ","
                            .$this->session->userdata("usu_id") . ",'"
                            .str_replace("'", "''",$descripcion) . "')";
                            
                        $result = pg_query($query);
                    }else{
                        $result = false;
                    }
                }

                if($result){
                    $data['Documento'] = $documento;
                    $data['com_id'] = $new_id;
                    $datos = array(
                        'Opcion' => 'Insertar',
                        'Tabla' => 'Compatibilidad', 
                        'Tab_id' => $new_id,
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

            //$this->alertas_model->EnviarCorreo($correoMasivo);

            return $new_id;
        }

        public function Actualizar($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " UPDATE Compatibilidad "
                . " SET Bie_Id ='". str_replace("'", "''",$data['Bie_Id']) 
                . "', Documento = " 
                . (($data['Documento'] == "") ? "Documento" : ("'" .str_replace("'", "''", $data['Documento']) . "'")) 
                . ", Usu_Mod = " . $this->session->userdata("usu_id") 
                . ", Fec_Mod = NOW()" 
                . ", Observaciones = "
                . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
                . " WHERE com_id = '" . str_replace("'", "''",$data['com_id']) . "';";

            //Ejecutar Query
            $result = pg_query($query);


            if ($result){
                
                $TransAgregado = $this->ObtenerTransaccionesAgregados($data['Agregados'],$data['com_id']);
                $TransQuitado = $this->ObtenerTransaccionesQuitados($data['Quitados'],$data['com_id']);

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
                    'Tabla' => 'Compatibilidad', 
                    'Tab_id' => $data['com_id'],
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

        public function Obtener($id = '',$array = false){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  COM.com_id,		
                                COM.BIE_ID,
                                COM.Documento,
                                B.nombre Bie_Nom,		
                                COM.Estatus,
                                COALESCE(COM.Observaciones,'') Observaciones
                        FROM Compatibilidad COM
                            JOIN Bienes B ON B.bie_id = COM.bie_id";

            if($id != ''){
                $query = $query . " WHERE com_id = '" . $id . "'";
            }

            $query = $query . " ORDER BY com_id DESC LIMIT 1;";

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
                $agregados = $this->ObtenerAgregados($retorno['com_id']);
                $quitados = $this->ObtenerQuitados($retorno['com_id']);

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
            $query ="   SELECT  COM.com_id,	
                                COM.Documento,
                                B.nombre Bie_Nom,			
                                B.Inv_UC,	
                                to_char(COM.Fec_Cre,'DD/MM/YYYY') Fec_Cre,
                                COALESCE(to_char(COM.Fec_Apr,'DD/MM/YYYY'),'') Fec_Apr,	
                                CRE.Nombre Solicitante,
                                COALESCE(APR.Nombre,'') Aprobador,	
                                COM.Estatus,
                                COALESCE(COM.Observaciones,'') Observaciones
                        FROM Compatibilidad COM
                            JOIN Bienes B ON B.bie_id = COM.bie_id
                            JOIN Usuarios CRE ON CRE.usu_id = COM.usu_cre
                            LEFT JOIN Usuarios APR ON APR.usu_id = COM.usu_apr
                        WHERE com_id = '" . $id . "'";


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
                $retorno['Agregados'] = $this->ObtenerAgregadosPDF($retorno['com_id']);
                $retorno['Quitados'] = $this->ObtenerQuitadosPDF($retorno['com_id']);
            }


            return $retorno;
        }

        public function Busqueda($busqueda,$orden,$inicio,$fin){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            $condicion ="";


            if($busqueda != ""){
                $condicion = " WHERE  (LOWER(B.nombre) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(COM.documento) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(COM.estatus) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%')";
            }
            
            //Query para buscar usuario
            $query ="   SELECT  com_id,
                                nombre,
                                Documento,
                                Estatus,
                                Registros
                        FROM (
                            SELECT  COM.com_id,
                                    COM.Documento,
                                    COM.Estatus,
                                    B.nombre,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $orden .") Fila
                            FROM Compatibilidad COM
                                JOIN Bienes B ON B.Bie_Id = COM.Bie_Id
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
            $query ="   SELECT  COM.usu_cre,
                                CRE.nombre cre_nom,
                                COALESCE(COM.usu_apr,-1) usu_apr,
                                COALESCE(APR.nombre,'') apr_nom
                        FROM Compatibilidad COM
                            JOIN Usuarios CRE ON CRE.usu_id = COM.usu_cre
                            LEFT JOIN Usuarios APR ON APR.usu_id = COM.usu_apr
                        WHERE com_id = '" . $id . "'
                        ORDER BY com_id DESC LIMIT 1;";

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
      
            $datosActual = $this->Obtener($id,true);

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " DELETE FROM Compatibilidad "
                . " WHERE com_id = '" .str_replace("'", "''",$id) . "';";
                
            //Ejecutar Query
            $result = pg_query($query);

            if ($result){  
                $query = " DELETE FROM Alertas WHERE Tabla = 'Compatibilidad' AND TAB_ID = " . $id;
                $result = pg_query($query);
            }

            if($result){
                $datos = array(
                    'Opcion' => 'Eliminar',
                    'Tabla' => 'Compatibilidad', 
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
            $query =" SELECT * FROM Compatibilidad WHERE LOWER(documento) ='" . strtolower(str_replace("'", "''",$documento)) . "' " ;

            if($id != "")
                $query = $query . " AND com_id <>'" . str_replace("'", "''",$id) . "' " ;

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
            $query = "UPDATE Compatibilidad "
                    . "SET  Documento = '" . $documento
                    ."' WHERE com_id = " . $id;
            return $query;
        }

        public function AprobarCompatibilidad($id){
            
            $datosActual = $this->Obtener($id,true);

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = "  UPDATE Compatibilidad  
                        SET estatus = 'Aprobado'
                            , Usu_Apr = " . $this->session->userdata("usu_id") . "
                            , Usu_Mod = " . $this->session->userdata("usu_id") . "
                            , Fec_Apr = Now()
                            , Fec_Mod = Now()
                        WHERE com_id = '" .str_replace("'", "''",$id) . "';";
                
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

                $query = " DELETE FROM Alertas WHERE Tabla = 'Compatibilidad' AND TAB_ID = " . $id;
                $result = pg_query($query);

            }

            if($result){
                $datosActual['estatus'] = 'Aprobado';
                $datos = array(
                    'Opcion' => 'Aprobar',
                    'Tabla' => 'Compatibilidad', 
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
                        FROM Compatibilidad 
                        WHERE com_id = " . str_replace("'", "''",$id) . "
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
                        FROM Compatibilidad 
                        WHERE com_id = " . str_replace("'", "''",$id) . "
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
        /*              Agregados           */
        /************************************/

        private function ObtenerTransaccionesAgregados($agregados,$compatibilidad){
            $transacciones = [];

            $query = "DELETE FROM CompatibilidadAccion WHERE tipo = 'Agregar' AND com_id = " . $compatibilidad;

            array_push($transacciones,$query);

            if(isset($agregados)){
                foreach ($agregados as $data) {

                    $query = "INSERT INTO CompatibilidadAccion( com_id,tpi_id,Tipo,
                                                        Usu_Cre,Usu_Mod,Observaciones)"
                            . "VALUES('"
                            . str_replace("'", "''",$compatibilidad)    . "','"
                            . str_replace("'", "''",$data['IdTipoPieza']) . "','Agregar',"
                            . $this->session->userdata("usu_id")    . ","
                            . $this->session->userdata("usu_id")    . ",'"
                            . str_replace("'", "''",$data['Observacion'])    . "');";

                    array_push($transacciones,$query);
                }
            }

            return $transacciones;
        }

        private function ObtenerTAprobadoAgregados($compatibilidad){
            
            $transacciones = [];

            $query = "  DELETE FROM CompatibilidadBien 
                        WHERE (bie_id,tpi_id) IN (
                            SELECT COM.bie_id,CAC.tpi_id
                            FROM Compatibilidad COM 
                                JOIN compatibilidadaccion CAC ON CAC.com_id = COM.com_id
                            WHERE COM.com_id = '" . $compatibilidad . "'
                                AND CAC.tipo = 'Agregar');";
            
            array_push($transacciones,$query);

            $query = "  INSERT INTO CompatibilidadBien (bie_id, tpi_id)
                        SELECT COM.bie_id,CAC.tpi_id
                        FROM Compatibilidad COM 
                            JOIN compatibilidadaccion CAC ON CAC.com_id = COM.com_id
                        WHERE COM.com_id = '" . $compatibilidad . "'
                            AND CAC.tipo = 'Agregar';";
            
            array_push($transacciones,$query);

            
            return $transacciones;
        }

        private function ObtenerAgregados($compatibilidad){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            
            $query ="   SELECT  CAC.cac_id,			
                                CAC.com_id,			
                                CAC.tpi_id,	
                                TPI.Nombre tpi_nom,
                                CAC.Usu_Cre,			
                                CAC.Fec_Cre,			
                                CAC.Usu_Mod,			
                                CAC.Fec_Mod,	
                                COALESCE(CAC.Observaciones,'') Observaciones
                        FROM CompatibilidadAccion CAC
                            JOIN TipoPieza TPI ON TPI.tpi_id = CAC.tpi_id
                        WHERE CAC.com_id = " . $compatibilidad . "
                            AND CAC.Tipo = 'Agregar'
                        ORDER BY CAC.cac_id ASC;";


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $html = "";
            $retorno = [];

            while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                
                array_push($retorno,$line);
                $html = $html
                    . "<tr>"
                    . "    <td style=\"display:none;\">" . $line['tpi_id'] . "</td>"
                    . "    <td>" . $line['tpi_nom'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['observaciones']
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

        private function ObtenerAgregadosPDF($compatibilidad){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            
            $query ="   SELECT  TPI.Nombre tpi_nom,
                                COALESCE(CAC.Observaciones,'') Observaciones
                        FROM CompatibilidadAccion CAC
                            JOIN TipoPieza TPI ON TPI.tpi_id = CAC.tpi_id
                        WHERE CAC.com_id = " . $compatibilidad . "
                            AND CAC.Tipo = 'Agregar'
                        ORDER BY CAC.cac_id ASC;";


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
        /*          Quitados                */
        /************************************/


        private function ObtenerTransaccionesQuitados($quitados,$compatibilidad){
            $transacciones = [];

            $query = "DELETE FROM CompatibilidadAccion WHERE tipo = 'Quitar' AND com_id = " . $compatibilidad;

            array_push($transacciones,$query);

            if(isset($quitados)){
                foreach ($quitados as $data) {
                    
                    $query = "INSERT INTO CompatibilidadAccion( com_id,tpi_id,Tipo,
                                                        Usu_Cre,Usu_Mod,Observaciones)"
                            . "VALUES('"
                            . str_replace("'", "''",$compatibilidad)    . "','"
                            . str_replace("'", "''",$data['IdPieza']) . "','Quitar',"
                            . $this->session->userdata("usu_id")    . ","
                            . $this->session->userdata("usu_id")    . ",'"
                            . str_replace("'", "''",$data['Observacion'])    . "');";

                    array_push($transacciones,$query);
                }
            }

            return $transacciones;
        }
        
        private function ObtenerTAprobadoQuitados($compatibilidad){
            
            $transacciones = [];

            $query = "  DELETE FROM CompatibilidadBien 
                        WHERE (bie_id,tpi_id) IN (
                            SELECT COM.bie_id,CAC.tpi_id
                            FROM Compatibilidad COM 
                                JOIN compatibilidadaccion CAC ON CAC.com_id = COM.com_id
                            WHERE COM.com_id = '" . $compatibilidad . "'
                                AND CAC.tipo = 'Quitar');";
            
            array_push($transacciones,$query);

            return $transacciones;
        }

        private function ObtenerQuitados($compatibilidad){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            //Query para buscar usuario
            $query ="   SELECT  CAC.cac_id,			
                                CAC.com_id,			
                                CAC.tpi_id,	
                                TPI.Nombre tpi_nom,
                                CAC.Usu_Cre,			
                                CAC.Fec_Cre,			
                                CAC.Usu_Mod,			
                                CAC.Fec_Mod,	
                                COALESCE(CAC.Observaciones,'') Observaciones
                        FROM CompatibilidadAccion CAC
                            JOIN TipoPieza TPI ON TPI.tpi_id = CAC.tpi_id
                        WHERE CAC.com_id = " . $compatibilidad . "
                            AND CAC.Tipo = 'Quitar'
                        ORDER BY CAC.cac_id ASC;";


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $html = "";
            $retorno = [];
            //Si existe registro, se guarda. Sino se guarda false
            while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                array_push($retorno,$line);
                $html = $html
                    . "<tr>"
                    . "    <td style=\"display:none;\">" . $line['cac_id'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['tpi_id'] . "</td>"
                    . "    <td>" . $line['tpi_nom'] . "</td>"
                    . "    <td style=\"display:none;\">" . $line['observaciones']
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

        private function ObtenerQuitadosPDF($compatibilidad){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query ="   SELECT  TPI.Nombre tpi_nom,
                            COALESCE(CAC.Observaciones,'') Observaciones
                    FROM CompatibilidadAccion CAC
                        JOIN TipoPieza TPI ON TPI.tpi_id = CAC.tpi_id
                    WHERE CAC.com_id = " . $compatibilidad . "
                        AND CAC.Tipo = 'Quitar'
                    ORDER BY CAC.cac_id ASC;";

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