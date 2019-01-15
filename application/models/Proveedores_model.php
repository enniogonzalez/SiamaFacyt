<?php
    class Proveedores_model extends CI_Model{

        public function Actualizar($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");
            
            $query = " UPDATE Proveedores "
                . " SET Rif ='". str_replace("'", "''",$data['Rif']) 
                . "', Raz_Soc = '".str_replace("'", "''",$data['Raz_Soc'])
                . "', Reg_Nac_Con = '".str_replace("'", "''",$data['Reg_Nac_Con'])
                . "', Direccion = '".str_replace("'", "''",$data['Direccion'])
                . "', Telefonos = "
                .(($data['Telefonos'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Telefonos']) . "'"))
                . ", Correo = "
                .(($data['Correo'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Correo']) . "'"))
                . ", Observaciones = " 
                .(($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
                . " WHERE PRO_ID = '" . str_replace("'", "''",$data['pro_id']) . "';";

            //Ejecutar Query
            $result = pg_query($query);
            
            if($result){
                $datos = array(
                    'Opcion' => 'Actualizar',
                    'Tabla' => 'Proveedores', 
                    'Tab_id' => $data['pro_id'],
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

        public function Busqueda($busqueda,$orden,$inicio,$fin){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            $condicion ="";


            if($busqueda != ""){
                $condicion = " WHERE (LOWER(Rif) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(Raz_Soc) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(Direccion) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%')";
            }

            //Query para buscar usuario
            $query ="   SELECT  PRO_ID,
                                Rif,
                                Raz_Soc,
                                Reg_Nac_Con,
                                Direccion,
                                Telefonos,
                                Correo,
                                Observaciones,
                                Registros
                        FROM (
                            SELECT  PRO_ID,
                                    Rif,
                                    Raz_Soc,
                                    Reg_Nac_Con,
                                    Direccion,
                                    COALESCE(Telefonos,'') Telefonos,
                                    COALESCE(Correo,'') Correo,
                                    COALESCE(Observaciones,'') Observaciones,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $orden .") Fila
                            FROM Proveedores
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
    
            //Eliminar Proveedor
            $query = " DELETE FROM Proveedores "
                . " WHERE Pro_ID = '" .str_replace("'", "''",$id) . "';";
                
            //Ejecutar Query
            $result = pg_query($query);
            
            if($result){
                $datos = array(
                    'Opcion' => 'Eliminar',
                    'Tabla' => 'Proveedores', 
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

        public function ExisteRif($rif,$id=""){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query =" SELECT * FROM Proveedores WHERE LOWER(rif) ='" . mb_strtolower(str_replace("'", "''",$rif)) . "' " ;

            if($id != "")
                $query = $query . " AND pro_id <>'" . str_replace("'", "''",$id) . "' " ;

                
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

            //Insertar Proveedor
            $query = " INSERT INTO Proveedores(Rif,Raz_Soc,Reg_Nac_Con,Direccion,Telefonos,Correo,Observaciones) VALUES('"
            . str_replace("'", "''",$data['Rif']) . "','"
            . str_replace("'", "''",$data['Raz_Soc']) . "','"
            . str_replace("'", "''",$data['Reg_Nac_Con']) . "','"
            . str_replace("'", "''",$data['Direccion']) . "',"
            . (($data['Telefonos'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Telefonos']) . "'"))
            . ","
            . (($data['Correo'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Correo']) . "'"))
            . ","
            . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
            . ") RETURNING pro_id;";

            //Ejecutar Query
            $result = pg_query($query);
            
            $new_id = "";
            if($result){
                $row = pg_fetch_row($result); 
                $new_id = $row['0']; 
            }
            
            if($result){
                $data['pro_id'] = $new_id;
                $datos = array(
                    'Opcion' => 'Insertar',
                    'Tabla' => 'Proveedores', 
                    'Tab_id' => $data['pro_id'],
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
            $query ="   SELECT  PRO_ID,
                                Rif,
                                Raz_Soc,
                                Reg_Nac_Con,
                                Direccion,
                                COALESCE(Telefonos,'') Telefonos,
                                COALESCE(Correo,'') Correo,
                                COALESCE(Observaciones,'') Observaciones
                        FROM Proveedores
                    ";

            if($id != ''){
                $query = $query . " WHERE PRO_ID = '" . $id . "'";
            }

            $query = $query . " ORDER BY PRO_ID DESC LIMIT 1;";

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
    }

?>