<?php
    class Fallas_model extends CI_Model{
        
        public function Insertar($data){
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Insertar Fallas
            $query = " INSERT INTO Fallas(Nombre,Tipo,usu_cre,usu_mod,Observaciones) VALUES('"
            . str_replace("'", "''",$data['Nombre']) . "','"
            . str_replace("'", "''",$data['Tipo']) . "',"
            . $this->session->userdata("usu_id")    . ","
            . $this->session->userdata("usu_id")    . ","
            . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
            . ");";

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

        public function Actualizar($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query = " UPDATE Fallas "
                . " SET Nombre ='". str_replace("'", "''",$data['Nombre']) 
                . "', Tipo ='". str_replace("'", "''",$data['Tipo'])  
                . "', Usu_Mod = " . $this->session->userdata("usu_id") 
                . ", Fec_Mod = NOW()" 
                . ", Observaciones = " 
                .(($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
                . " WHERE fal_id = '" . str_replace("'", "''",$data['idActual']) . "';";


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

        public function Obtener($id = ''){
            

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            //Query para buscar usuario
            $query ="   SELECT  fal_id,
                                Nombre,
                                Tipo,
                                COALESCE(Observaciones,'') Observaciones
                        FROM Fallas
                    ";

            if($id != ''){
                $query = $query . " WHERE fal_id = '" . $id . "'";
            }

            $query = $query . " ORDER BY fal_id DESC LIMIT 1;";

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
            $query ="   SELECT  fal_id,
                                Nombre,
                                Tipo,
                                COALESCE(Observaciones,'') Observaciones
                        FROM Fallas
                        WHERE fal_id = '" . $id . "'";
            


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

        public function Busqueda($busqueda,$orden,$inicio,$fin){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            $condicion ="";


            if($busqueda != ""){
                $condicion = " WHERE (LOWER(Nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(Tipo) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%')";
            }

            //Query para buscar usuario
            $query ="   SELECT  fal_id,
                                Nombre,
                                Tipo,
                                Observaciones,
                                Registros
                        FROM (
                            SELECT  fal_id,
                                    Nombre,
                                    Tipo,
                                    COALESCE(Observaciones,'') Observaciones,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $orden .") Fila
                            FROM Fallas
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
    
            //Eliminar Fallas
            $query = " DELETE FROM Fallas "
                . " WHERE fal_id = '" .str_replace("'", "''",$id) . "';";
                
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

        public function ExisteNombre($Nombre,$id=""){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query =" SELECT * FROM Fallas WHERE LOWER(Nombre) like '" . mb_strtolower(str_replace("'", "''",$Nombre)) . "' " ;

            if($id != "")
                $query = $query . " AND fal_id <>'" . str_replace("'", "''",$id) . "' " ;

                
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