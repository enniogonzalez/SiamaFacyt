
<?php
    class Proveedores_model extends CI_Model{
        
        public function Insertar($data){
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

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
            // Rif,Raz_Soc,Reg_Nac_Con,Direccion,Telefonos,Correo,Observaciones
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
                . " WHERE PRO_ID = '" . str_replace("'", "''",$data['idActual']) . "';";


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

        public function Busqueda($busqueda,$orden,$inicio,$fin){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            $condicion ="";


            if($busqueda != ""){
                $condicion = " WHERE (LOWER(Rif) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(Raz_Soc) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(Direccion) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
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

            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Eliminar Proveedor
            $query = " DELETE FROM Proveedores "
                . " WHERE Pro_ID = '" .str_replace("'", "''",$id) . "';";
                
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

        public function ExisteRif($rif,$id=""){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query =" SELECT * FROM Proveedores WHERE LOWER(rif) ='" . strtolower(str_replace("'", "''",$rif)) . "' " ;

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
    }

?>