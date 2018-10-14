
<?php
    class Localizaciones_model extends CI_Model{
        
        public function Insertar($data){
            
    
            if($data['idPad'] != "" && empty($this->Obtener($data['idPad'])))
                die('No se ha podido guardar localizacion, el id del padre no esta registrado');
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Insertar Localizacion
            $query = " INSERT INTO Localizaciones( Nombre,Ubicacion,Tipo,Cap_Amp,Observaciones) VALUES('"
            . str_replace("'", "''",$data['Nombre']) . "','"
            . str_replace("'", "''",$data['Ubicacion']) . "','"
            . str_replace("'", "''",$data['Tipo']) . "','"
            . str_replace("'", "''",$data['Cap_Amp']) . "',"
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
                    $InsertarRelacion = $this->InsertarRelacion($insertado['loc_id'],$data['idPad']);
                }
                $retorno = true;

            } else
                $retorno = false;


            return $retorno;
        }

        public function Actualizar($data){
            
            if(empty($this->Obtener($data['idPad'])))
                die('No se ha podido Actualizar Localizacion, el id del padre no esta registrado');
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            $query = " UPDATE Localizaciones "
                . " SET Nombre ='". str_replace("'", "''",$data['Nombre']) 
                . "', Ubicacion = '".str_replace("'", "''",$data['Ubicacion'])
                . "', Tipo = '".str_replace("'", "''",$data['Tipo'])
                . "', Cap_Amp = ".str_replace("'", "''",$data['Cap_Amp'])
                . ", Observaciones = " 
                .(($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
                . " WHERE LOC_ID = '" . str_replace("'", "''",$data['idActual']) . "';";


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
            $query ="   SELECT  Hijo.LOC_ID,
                                Hijo.Nombre,
                                Hijo.Ubicacion,
                                Hijo.Tipo,
                                Hijo.Cap_Amp,
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

        public function Busqueda($busqueda,$orden,$inicio,$fin,$id){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            $condicion ="";

            
            if($busqueda != ""){
                $condicion = " WHERE " . ($id == "" ? "":("Hijo.loc_id <> " . $id . " AND ")) 
                            . " (LOWER(Hijo.Nombre) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(Hijo.Ubicacion) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(Hijo.Tipo) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%')";
            }elseif($id != ""){
                $condicion = " WHERE Hijo.loc_id <> " . $id;
            }

            //Query para buscar usuario
            $query ="   SELECT  loc_id,
                                Nombre,
                                Ubicacion,
                                Tipo,
                                Cap_Amp,
                                Observaciones,
                                idPad,
                                NombrePadre,
                                Registros
                        FROM (
                            SELECT  Hijo.loc_id,
                                    Hijo.Nombre,
                                    Hijo.Ubicacion,
                                    Hijo.Tipo,
                                    Hijo.Cap_Amp,
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
    
            //Eliminar Localizacion
            $query = " DELETE FROM Localizaciones "
                . " WHERE LOC_ID = '" .str_replace("'", "''",$id) . "';";
                
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

        public function ExisteRelacion($idHijo){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query =" SELECT * FROM Pertenece WHERE loh_id = " . $idHijo . ";" ;

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
    
            //Eliminar Localizacion
            $query = " DELETE FROM Pertenece "
                . " WHERE loh_id = '" . $idHijo . "';";
            
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
    
            $query = " INSERT INTO Pertenece(LOH_ID,LOP_ID) VALUES("
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
    
            $query = " UPDATE Pertenece "
                . " SET lop_id ='". str_replace("'", "''",$padre) 
                . "' WHERE loh_id = '" . str_replace("'", "''",$hijo) . "';";


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