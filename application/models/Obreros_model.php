
<?php
    class Obreros_model extends CI_Model{
        
        public function Insertar($data){
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            //Insertar Obreros
            $query = " INSERT INTO Obreros(Cedula,Nombre,usu_cre,Telefonos,Correo,Observaciones) VALUES('"
            . str_replace("'", "''",$data['Cedula']) . "','"
            . str_replace("'", "''",$data['Nombre']) . "',"
            . $this->session->userdata("usu_id")    . ","
            . (($data['Telefonos'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Telefonos']) . "'"))
            . ","
            . (($data['Correo'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Correo']) . "'"))
            . ","
            . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
            . ") RETURNING obr_id;";

            //Ejecutar Query
            $result = pg_query($query);
            $new_id = "";

            if($result){
                $row = pg_fetch_row($result); 
                $new_id = $row['0']; 
            }

            if($result){
                $data['obr_id'] = $new_id;
                $datos = array(
                    'Opcion' => 'Insertar',
                    'Tabla' => 'Obreros', 
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
            
            $query = " UPDATE Obreros"
                . " SET Cedula ='". str_replace("'", "''",$data['Cedula']) 
                . "', Nombre = '".str_replace("'", "''",$data['Nombre'])
                . "', Usu_Mod = " . $this->session->userdata("usu_id") 
                . ", Fec_Mod = NOW()" 
                . ", Telefonos = "
                .(($data['Telefonos'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Telefonos']) . "'"))
                . ", Correo = "
                .(($data['Correo'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Correo']) . "'"))
                . ", Observaciones = " 
                .(($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
                . " WHERE obr_id = '" . str_replace("'", "''",$data['obr_id']) . "';";


            //Ejecutar Query
            $result = pg_query($query);
            
            if($result){
                $datos = array(
                    'Opcion' => 'Actualizar',
                    'Tabla' => 'Obreros', 
                    'Tab_id' => $data['obr_id'],
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
            $query ="   SELECT  obr_id,
                                Cedula,
                                Nombre,
                                COALESCE(Telefonos,'') Telefonos,
                                COALESCE(Correo,'') Correo,
                                COALESCE(Observaciones,'') Observaciones
                        FROM Obreros
                    ";

            if($id != ''){
                $query = $query . " WHERE obr_id = '" . $id . "'";
            }

            $query = $query . " ORDER BY obr_id DESC LIMIT 1;";

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
                $condicion = " WHERE (LOWER(Cedula) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(Nombre) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%')";
            }

            //Query para buscar usuario
            $query ="   SELECT  obr_id,
                                Cedula,
                                Nombre,
                                Telefonos,
                                Correo,
                                Observaciones,
                                Registros
                        FROM (
                            SELECT  obr_id,
                                    Cedula,
                                    Nombre,
                                    COALESCE(Telefonos,'') Telefonos,
                                    COALESCE(Correo,'') Correo,
                                    COALESCE(Observaciones,'') Observaciones,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $orden .") Fila
                            FROM Obreros
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

            $datosActual = $this->Obtener($id);

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            //Eliminar Obreros
            $query = " DELETE FROM Obreros"
                . " WHERE obr_id = '" .str_replace("'", "''",$id) . "';";
                
            //Ejecutar Query
            $result = pg_query($query);
            
            if($result){
                $datos = array(
                    'Opcion' => 'Eliminar',
                    'Tabla' => 'Obreros', 
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

        public function ExisteCedula($cedula,$id=""){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query =" SELECT * FROM Obreros WHERE LOWER(cedula) ='" . strtolower(str_replace("'", "''",$cedula)) . "' " ;

            if($id != "")
                $query = $query . " AND obr_id <>'" . str_replace("'", "''",$id) . "' " ;

                
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