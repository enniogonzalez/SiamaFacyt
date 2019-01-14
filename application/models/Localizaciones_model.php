
<?php
    class Localizaciones_model extends CI_Model{

        private function ActualizarRelacion($hijo,$padre){
    
            $query = " UPDATE Pertenece "
                . " SET lop_id ='". str_replace("'", "''",$padre) 
                . "' WHERE loh_id = '" . str_replace("'", "''",$hijo) . "';";

            //Ejecutar Query
            $result = pg_query($query);
            
            //Liberar memoria
            pg_free_result($result);

            //Si existe registro, se guarda. Sino se guarda false
            if ($result)
                $retorno = true;
            else
                $retorno = false;


            return $retorno;

        }

        private function ExisteRelacion($idHijo){

            //Query para buscar usuario
            $query =" SELECT * FROM Pertenece WHERE loh_id = " . $idHijo . ";" ;

            //Ejecutar Query
            $result = pg_query($query);
            
            //Si existe registro, se guarda. Sino se guarda false
            if (pg_num_rows($result) > 0) 
                $retorno = true;
            else
                $retorno = false;

            return $retorno;
        }

        private function EliminarRelacionPadre($idHijo){
            
            //Eliminar Localizacion
            $query = " DELETE FROM Pertenece "
                . " WHERE loh_id = '" . $idHijo . "';";
            
            //Ejecutar Query
            $result = pg_query($query);
            
            if ($result) 
                $retorno = true;
            else
                $retorno = false;

            return $retorno;
        }

        private function InsertarRelacion($hijo, $padre){

            $query = " INSERT INTO Pertenece(LOH_ID,LOP_ID) VALUES("
            . str_replace("'", "",$hijo) . ","
            . str_replace("'", "",$padre) . ");";

            //Ejecutar Query
            $result = pg_query($query);
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($result){
                $retorno = true;
            } else
                $retorno = false;

            //Liberar memoria
            pg_free_result($result);

            return $retorno;
        }

        public function Actualizar($data){
            
            if(empty($this->Obtener($data['idPad'])))
                die('No se ha podido Actualizar Localizacion, el id del padre no esta registrado');

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");
            
            $query = " UPDATE Localizaciones "
                . " SET Nombre ='". str_replace("'", "''",$data['Nombre']) 
                . "', Ubicacion = '".str_replace("'", "''",$data['Ubicacion'])
                . "', Tipo = '".str_replace("'", "''",$data['Tipo'])
                . "', Usu_Mod = " . $this->session->userdata("usu_id") 
                . ", Fec_Mod = NOW()" 
                . ", Observaciones = " 
                .(($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
                . " WHERE LOC_ID = '" . str_replace("'", "''",$data['loc_id']) . "';";


            //Ejecutar Query
            $result = pg_query($query);
            $sec_act = "";
            $sec_pad = "";

            if($result){
                if($data['idPad'] == "")
                    $result = $this->EliminarRelacionPadre(str_replace("'", "''",$data['loc_id']));
                elseif($this->ExisteRelacion(str_replace("'", "''",$data['loc_id'])))
                    $result = $this->ActualizarRelacion($data['loc_id'],$data['idPad']);
                else
                    $result = $this->InsertarRelacion($data['loc_id'],$data['idPad']);
            }

            if($result){
                $valorSec = $this->ObtenerSecuencia($data['loc_id']);

                if(!is_null($valorSec)){
                    $sec_act = $valorSec['secuencia'];
                }
            }

            if($result && $data['idPad'] != ""){
                $valorSec = $this->ObtenerSecuencia($data['idPad']);

                if(!is_null($valorSec)){
                    $sec_pad = $valorSec['secuencia'];
                }
            }

            if($result){
                $secuencia = $sec_pad . "-" .$data['loc_id'] . "-";
                $query = "  UPDATE Localizaciones 
                                SET Secuencia = '" . $secuencia . "' 
                            WHERE loc_id = " . $data['loc_id'];

                $result = pg_query($query);
            }
            
            if($result){
                $query = "  UPDATE Localizaciones 
                                SET Secuencia = CONCAT('".$secuencia."-',RIGHT(secuencia,length(secuencia)-LENGTH('" . $sec_act . "-')))
                            WHERE Secuencia like '" . $sec_act . "-%';";

                $result = pg_query($query);
            }

            if($result){
                $datos = array(
                    'Opcion' => 'Actualizar',
                    'Tabla' => 'Localizaciones', 
                    'Tab_id' => $data['loc_id'],
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

        public function Busqueda($busqueda,$orden,$inicio,$fin,$id){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            $condicion ="";

            
            if($busqueda != ""){
                $condicion = " WHERE (LOWER(Hijo.Nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(Hijo.Ubicacion) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(Hijo.Tipo) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%')";
            }
            
            if($id != ""){
                
                $valorSec = $this->ObtenerSecuencia($id);
                $sec = "";
                if(!is_null($valorSec)){
                    $sec = $valorSec['secuencia'];
                }

                $condicion .= ($condicion == "" ? "WHERE ": " AND ")
                        . " Hijo.loc_id <> " . $id
                        . ($sec == "" ? "": " AND Hijo.secuencia not like '%". $sec ."%'");
            }

            $query ="   SELECT  loc_id,
                                Nombre,
                                Ubicacion,
                                Tipo,
                                Observaciones,
                                idPad,
                                NombrePadre,
                                Registros
                        FROM (
                            SELECT  Hijo.loc_id,
                                    Hijo.Nombre,
                                    Hijo.Ubicacion,
                                    Hijo.Tipo,
                                    Hijo.Observaciones,
                                    COALESCE(P.lop_id,-1) AS idPad,
                                    COALESCE(Padre.Nombre,'') AS NombrePadre,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY Hijo." . $orden .") Fila
                            FROM Localizaciones Hijo
                                LEFT JOIN Pertenece AS P ON P.loh_id = Hijo.loc_id
                                LEFT JOIN Localizaciones AS Padre ON Padre.loc_id = P.lop_id
                            " . $condicion . "

                        ) LD
                        WHERE Fila BETWEEN ". $inicio . " AND " . $fin . "
                        ORDER BY Fila ASC;";

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
        
            //Si no existe error en la consulta, se agregaran los registros a un array
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
      
            $datosActual = $this->Obtener($id);

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");
            
            $query = " DELETE FROM Localizaciones "
                . " WHERE LOC_ID = '" .str_replace("'", "''",$id) . "';";
                
            //Ejecutar Query
            $result = pg_query($query);

            if($result){
                $datos = array(
                    'Opcion' => 'Eliminar',
                    'Tabla' => 'Localizaciones', 
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

        public function Insertar($data){
            
            if($data['idPad'] != "" && empty($this->Obtener($data['idPad'])))
                die('No se ha podido guardar localizacion, el id del padre no esta registrado');
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            //Insertar Localizacion
            $query = " INSERT INTO Localizaciones( Nombre,Ubicacion,Tipo,usu_cre,usu_mod,Observaciones) VALUES('"
            . str_replace("'", "''",$data['Nombre']) . "','"
            . str_replace("'", "''",$data['Ubicacion']) . "','"
            . str_replace("'", "''",$data['Tipo']) . "',"
            . $this->session->userdata("usu_id")    . ","
            . $this->session->userdata("usu_id")    . ","
            . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
            . ") RETURNING loc_id;";

            //Ejecutar Query
            $result = pg_query($query);

            $new_id = "";
            $sec_pad = "";
            
            if($result){
                $row = pg_fetch_row($result); 
                $new_id = $row['0']; 

                if($data['idPad'] != ""){
                    $result = $this->InsertarRelacion($new_id,$data['idPad']);
                }
            }

            if($result && $data['idPad'] != ""){
                $valorSec = $this->ObtenerSecuencia($data['idPad']);

                if(!is_null($valorSec)){
                    $sec_pad = $valorSec['secuencia'];
                }
            }

            if($result){
                $secuencia = $sec_pad . "-" . $new_id . "-";
                $query = "  UPDATE Localizaciones 
                                SET Secuencia = '" . $secuencia . "' 
                            WHERE loc_id = " . $new_id;

                $result = pg_query($query);
            }


            if($result){
                $data['loc_id'] = $new_id;
                $datos = array(
                    'Opcion' => 'Insertar',
                    'Tabla' => 'Localizaciones', 
                    'Tab_id' => $data['loc_id'],
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

        public function Obtener($id = ''){
            

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query ="   SELECT  Hijo.LOC_ID,
                                Hijo.Nombre,
                                Hijo.Ubicacion,
                                Hijo.Tipo,
                                COALESCE(Hijo.Observaciones,'') Observaciones,
                                COALESCE(P.lop_id,-1) AS idPad,
                                COALESCE(Padre.Nombre,'') AS NombrePadre
                        FROM Localizaciones AS Hijo
                            LEFT JOIN Pertenece AS P ON P.loh_id = Hijo.loc_id
                            LEFT JOIN Localizaciones AS Padre ON Padre.loc_id = P.lop_id
                    ";

            if($id != ''){
                $query = $query . " WHERE Hijo.LOC_ID = '" . $id . "'";
            }

            $query = $query . " ORDER BY Hijo.LOC_ID DESC LIMIT 1;";

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

        public function ObtenerInfoPDF($id){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            $query ="   SELECT  Hijo.Loc_ID,
                                Hijo.Nombre,
                                Hijo.Ubicacion,
                                Hijo.Tipo,
                                COALESCE(Hijo.Observaciones,'') Observaciones,
                                COALESCE(Padre.Nombre,'') AS NombrePadre
                        FROM Localizaciones AS Hijo
                            LEFT JOIN Pertenece AS P ON P.loh_id = Hijo.loc_id
                            LEFT JOIN Localizaciones AS Padre ON Padre.loc_id = P.lop_id
                        WHERE Hijo.LOC_ID = '" . $id . "'";

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

        private function ObtenerSecuencia($id){
            $query = " SELECT secuencia FROM localizaciones WHERE loc_id = " .$id;
            $result = pg_query($query);
            
            $retorno = null;

            if ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) 
                $retorno = $line;

            return $retorno;
        }
    }

?>