<?php
    class Tipopieza_model extends CI_Model{
        
        public function Insertar($data){
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            //Insertar Tipo de Pieza
            $query = " INSERT INTO Tipopieza(Nombre,usu_cre,Observaciones) VALUES('"
            . str_replace("'", "''",$data['Nombre']) . "',"
            . $this->session->userdata("usu_id")    . ","
            . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
            . ") RETURNING tpi_id;";

            //Ejecutar Query
            $result = pg_query($query);
            $new_id = "";

            if($result){
                $row = pg_fetch_row($result); 
                $new_id = $row['0']; 
            }
            
            if($result){
                $data['idActual'] = $new_id;
                $datos = array(
                    'Opcion' => 'Insertar',
                    'Tabla' => 'Tipopieza', 
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
            


            return true;
        }

        public function Actualizar($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " UPDATE Tipopieza "
                . " SET Nombre ='". str_replace("'", "''",$data['Nombre']) 
                . "', Usu_Mod = " . $this->session->userdata("usu_id") 
                . ", Fec_Mod = NOW()" 
                . ", Observaciones = " 
                .(($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
                . " WHERE tpi_id = '" . str_replace("'", "''",$data['idActual']) . "';";


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            if($result){
                $datos = array(
                    'Opcion' => 'Actualizar',
                    'Tabla' => 'Tipopieza', 
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

        public function Obtener($id = ''){
            

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            //Query para buscar usuario
            $query ="   SELECT  tpi_id,
                                Nombre,
                                COALESCE(Observaciones,'') Observaciones
                        FROM Tipopieza
                    ";

            if($id != ''){
                $query = $query . " WHERE tpi_id = '" . $id . "'";
            }

            $query = $query . " ORDER BY tpi_id DESC LIMIT 1;";

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
            //Query para buscar usuario
            $query ="   SELECT  tpi_id,
                                Nombre,
                                COALESCE(Observaciones,'') Observaciones
                        FROM Tipopieza
                        WHERE tpi_id = '" . $id . "'";
            


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

        public function Busqueda($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            $condicion ="";

            if($data['busqueda'] != ""){
                $condicion = "WHERE (LOWER(Nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%')";
            }

            if($data['bien'] != ""){

                if($data['delBien']){
                    $cond1 = "tpi_id IN (
                        SELECT CB.tpi_id
                        FROM CompatibilidadBien CB 
                        WHERE CB.bie_id = '" . $data['bien'] . "'
                    )";
                }else{
                    $cond1 = "tpi_id NOT IN (
                        SELECT CB.tpi_id
                        FROM CompatibilidadBien CB 
                        WHERE CB.bie_id = '" . $data['bien'] . "'
                    )";
                }

                if($condicion != ""){
                    $condicion .= $condicion . " AND " . $cond1;
                }else{
                    $condicion = " WHERE " . $cond1;
                }
            }

            //Query para buscar usuario
            $query ="   SELECT  tpi_id,
                                Nombre,
                                Observaciones,
                                Registros
                        FROM (
                            SELECT  tpi_id,
                                    Nombre,
                                    COALESCE(Observaciones,'') Observaciones,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $data['orden'] .") Fila
                            FROM Tipopieza
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

        public function BusquedaDisponible($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            $condicion ="";

            if($data['busqueda'] != ""){
                $condicion = "AND (LOWER(Nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%')";
            }

            //Query para buscar usuario
            $query ="   SELECT  tpi_id,
                                Nombre,
                                Observaciones,
                                Registros
                        FROM (
                            SELECT  tpi_id,
                                    Nombre,
                                    COALESCE(Observaciones,'') Observaciones,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $data['orden'] .") Fila
                            FROM Tipopieza
                            WHERE tpi_id IN (
                                    SELECT CB.tpi_id
                                    FROM CompatibilidadBien CB 
                                    WHERE CB.bie_id = '" . $data['bien'] . "'
                                        AND NOT EXISTS(
                                                SELECT 1
                                                FROM Piezas P
                                                WHERE P.bie_id = CB.bie_id
                                                    AND P.tpi_id = CB.tpi_id
                                            )
                                        AND NOT EXISTS(
                                                SELECT 1
                                                FROM PlantillaMantenimiento PLM
                                                    JOIN PlantillaMantenimientoTarea PMT ON PMT.plm_id = PLM.plm_id
                                                WHERE PLM.bie_id = CB.bie_id
                                                    AND PMT.tpi_id = CB.tpi_id
                                            )
                                )

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

            $datosActual = $this->Obtener($id);

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");
            //Eliminar Tipopieza
            $query = " DELETE FROM Tipopieza "
                . " WHERE tpi_id = '" .str_replace("'", "''",$id) . "';";
                
            //Ejecutar Query
            $result = pg_query($query);
            

            if($result){
                $datos = array(
                    'Opcion' => 'Eliminar',
                    'Tabla' => 'Tipopieza', 
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

        public function ExisteNombre($Nombre,$id=""){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query =" SELECT * FROM Tipopieza WHERE LOWER(Nombre) ='" . mb_strtolower(str_replace("'", "''",$Nombre)) . "' " ;

            if($id != "")
                $query = $query . " AND tpi_id <>'" . str_replace("'", "''",$id) . "' " ;

                
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
    }

?>