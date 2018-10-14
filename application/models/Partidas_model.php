
<?php
    class Partidas_model extends CI_Model{
        
        public function Insertar($data){
            
    
            if($data['idPad'] != "" && empty($this->Obtener($data['idPad'])))
                die('No se ha podido guardar Partida, el id del padre no esta registrado');
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Insertar Partida
            $query = " INSERT INTO Partidas(Codigo,Nombre,Observaciones) VALUES('"
            . str_replace("'", "''",$data['Codigo']) . "','"
            . str_replace("'", "''",$data['Nombre']) . "',"
            . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
            . ");";

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($result){
                if($data['idPad'] != ""){
                    $insertado = $this->Obtener();
                    $InsertarRelacion = $this->InsertarRelacion($insertado['par_id'],$data['idPad']);
                }
                $retorno = true;

            } else
                $retorno = false;


            return $retorno;
        }

        public function Actualizar($data){
            
            if(empty($this->Obtener($data['idPad'])))
                die('No se ha podido Actualizar Partida, el id del padre no esta registrado');
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            $query = " UPDATE Partidas "
                . " SET Codigo ='". str_replace("'", "''",$data['Codigo']) 
                . "', Nombre = '".str_replace("'", "''",$data['Nombre'])
                . "', Observaciones = " 
                .(($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
                . " WHERE par_id = '" . str_replace("'", "''",$data['idActual']) . "';";


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            //Si existe registro, se guarda. Sino se guarda false
            if ($result){

                if($data['idPad'] == "")
                    $this->EliminarRelacionPadre(str_replace("'", "''",$data['idActual']));
                elseif($this->ExisteRelacion(str_replace("'", "''",$data['idActual'])))
                    $this->ActualizarRelacion($data['idActual'],$data['idPad']);
                else
                    $this->InsertarRelacion($data['idActual'],$data['idPad']);

                $retorno = true;
            }else
                $retorno = false;


            return $retorno;
        }

        public function Obtener($id = ''){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            //Query para buscar usuario
            $query ="   SELECT  Hijo.PAR_ID,
                                Hijo.Codigo,
                                Hijo.Nombre,
                                COALESCE(Hijo.Observaciones,'') Observaciones,
                                COALESCE(Padre.par_id,-1) AS idPad,
                                COALESCE(Padre.Nombre,'') AS NombrePadre
                        FROM Partidas AS Hijo
                            LEFT JOIN EsSubpartida AS P ON P.pah_id = Hijo.par_id
                            LEFT JOIN Partidas AS Padre ON Padre.par_id = P.pap_id
                    ";

            if($id != ''){
                $query = $query . " WHERE Hijo.PAR_ID = '" . $id . "'";
            }

            $query = $query . " ORDER BY Hijo.PAR_ID DESC LIMIT 1;";

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

        public function Busqueda($busqueda,$orden,$inicio,$fin,$id){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            $condicion ="";

            
            if($busqueda != ""){
                $condicion = " WHERE " . ($id == "" ? "":("Hijo.par_id <> " . $id . " AND ")) 
                            . " (LOWER(Hijo.Nombre) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(COALESCE(Hijo.Observaciones,'')) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(Hijo.codigo) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%')";
            }elseif($id != ""){
                $condicion = " WHERE Hijo.par_id <> " . $id;
            }

            //Query para buscar usuario
            $query ="   SELECT  par_id,
                                Nombre,
                                Codigo,
                                Observaciones,
                                idPad,
                                NombrePadre,
                                Registros
                        FROM (
                            SELECT  Hijo.par_id,
                                    Hijo.Nombre,
                                    Hijo.Codigo,
                                    COALESCE(Hijo.Observaciones,'') Observaciones,
                                    COALESCE(Padre.par_id,-1) AS idPad,
                                    COALESCE(Padre.Nombre,'') AS NombrePadre,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY Hijo." . $orden .") Fila
                            FROM Partidas AS Hijo
                                LEFT JOIN EsSubpartida AS P ON P.pah_id = Hijo.par_id
                                LEFT JOIN Partidas AS Padre ON Padre.par_id = P.pap_id
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
    
            //Eliminar Partida
            $query = " DELETE FROM Partidas "
                . " WHERE PAR_ID = '" .str_replace("'", "''",$id) . "';";
                
            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($result) 
                $retorno = true;
            else
                $retorno = false;

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return $retorno;

        }

        public function ExisteCodigo($codigo,$id=""){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query =" SELECT 1 FROM Partidas WHERE LOWER(codigo) ='" . strtolower(str_replace("'", "''",$codigo)) . "' " ;

            if($id != "")
                $query = $query . " AND par_id <>'" . str_replace("'", "''",$id) . "' " ;
                
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

        public function ExisteRelacion($idHijo){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query =" SELECT * FROM EsSubpartida WHERE pah_id = " . $idHijo . ";" ;

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

        public function EliminarRelacionPadre($idHijo){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Eliminar Partida
            $query = " DELETE FROM EsSubpartida "
                . " WHERE pah_id = '" . $idHijo . "';";
            
            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($result) 
                $retorno = true;
            else
                $retorno = false;

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return $retorno;
        }

        public function InsertarRelacion($hijo, $padre){
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            $query = " INSERT INTO EsSubpartida(PAH_ID,PAP_ID) VALUES("
            . str_replace("'", "",$hijo) . ","
            . str_replace("'", "",$padre) . ");";

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($result){
                $retorno = true;

            } else
                $retorno = false;

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return $retorno;
        }

        public function ActualizarRelacion($hijo,$padre){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            $query = " UPDATE EsSubpartida "
                . " SET pap_id ='". str_replace("'", "''",$padre) 
                . "' WHERE pah_id = '" . str_replace("'", "''",$hijo) . "';";


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            //Si existe registro, se guarda. Sino se guarda false
            if ($result)
                $retorno = true;
            else
                $retorno = false;


            return $retorno;

        }
    }

?>