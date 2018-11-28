
<?php
    class Listasdesplegables_model extends CI_Model{
        
        public function Insertar($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Insertar Lista desplegable
            $query = " INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) VALUES('"
            . str_replace("'", "''",$data['Codigo']) . "','"
            . str_replace("'", "''",$data['Nombre']) . "',"
            . (($data['Descripcion'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Descripcion']) . "'")) . ",'"
            . str_replace("'", "''", $data['Opciones']). "');";

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

        public function Editar($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Insertar Lista desplegable
            $query = " UPDATE Listas_Desplegables "
                . " SET Codigo ='". str_replace("'", "''",$data['Codigo']) 
                . "', Nombre = '".str_replace("'", "''",$data['Nombre'])
                . "', Descripcion = "
                . (($data['Descripcion'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Descripcion']) . "'"))
                . ", Opciones = '".str_replace("'", "''",$data['Opciones'])
                . "' WHERE LD_ID = '" .str_replace("'", "''",$data['idActual']) . "';";
                
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

        public function Eliminar($id){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Insertar Lista desplegable
            $query = " DELETE FROM Listas_Desplegables "
                . " WHERE LD_ID = '" .str_replace("'", "''",$id) . "';";
                
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

        public function ObtenerId($codigo){
            

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query =    " SELECT LD_ID"
                    .   " FROM Listas_Desplegables "
                    .   " WHERE codigo = '" . str_replace("'", "''",$codigo). "' LIMIT 1;";
            

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

        public function BusquedaLD($busqueda,$orden,$inicio,$fin){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            $condicion ="";
            if($busqueda != ""){
                $condicion = " WHERE (LOWER(codigo) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%') OR (LOWER(Nombre) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%') OR (LOWER(Descripcion) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%')";
            }
            //Query para buscar usuario
            $query ="   SELECT  Codigo,
                                Nombre,
                                Descripcion,
                                Registros,
                                Opciones,
                                ld_id
                        FROM (
                            SELECT  Codigo,
                                    Nombre,
                                    ld_id,
                                    Opciones,
                                    Descripcion,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $orden .") Fila
                            FROM Listas_Desplegables 
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

        public function Obtener($id = '',$codigo =''){
            

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query =    " SELECT LD_ID,Codigo,Nombre,COALESCE(Descripcion,'') Descripcion,Opciones "
                    .   " FROM Listas_Desplegables ";

            if($id != ''){
                $query = $query . " WHERE LD_ID = '" . $id . "'";
            }elseif($codigo != ''){
                $query = $query . " WHERE codigo = '" . $codigo . "'";
            }

            $query = $query . " ORDER BY LD_ID DESC LIMIT 1;";

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

        public function ExisteCodigo($codigo,$id=""){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query =" SELECT 1 FROM Listas_Desplegables WHERE LOWER(codigo) ='" . strtolower(str_replace("'", "''",$codigo)) . "' " ;

            if($id != "")
                $query = $query . " AND LD_ID <>'" . str_replace("'", "''",$id) . "' " ;
                
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