
<?php
    class Usuarios_model extends CI_Model{
        
        private $Permisos = array();
    
        function __construct(){
            parent::__construct();
            $this->Permisos = array("Localizacion","Mantenimiento","Marcas","Partidas","Patrimonio","Proveedores","Sistema");
        }

        public function Insertar($data){
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            //Insertar Proveedor
            $query = " INSERT INTO Usuarios(Username,Nombre,Clave,Cargo,Correo,Usu_Cre,Usu_Mod,Observaciones) VALUES('"
            . str_replace("'", "''",$data['Username']) . "','"
            . str_replace("'", "''",$data['Nombre']) . "','e11170b8cbd2d74102651cb967fa28e5','"
            . str_replace("'", "''",$data['Cargo']) . "',"
            . (($data['Correo'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Correo']) . "'")). ","
            . $this->session->userdata("usu_id")    . ","
            . $this->session->userdata("usu_id")    . ","
            . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
            . ");";

            //Ejecutar Query
            $result = pg_query($query);
            
            $iterador = 0;
            $cantidad = count($this->Permisos);
            $idUsuario = $this->ObtenerUltimoIdInsertado();

            $queryPermiso = "INSERT INTO PermisosUsuarios (usu_id,menu) VALUES (" . $idUsuario . ",";

            while($result && $iterador < $cantidad){
                if($data[$this->Permisos[$iterador]]){
                    $result = pg_query($queryPermiso . "'" . $this->Permisos[$iterador] . "')");
                }
                $iterador++;
            }
            
            if(!$result){
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                die(pg_last_error());
            }else
                pg_query("COMMIT") or die("Transaction commit failed");


            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return true;
        }

        public function Actualizar($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " UPDATE Usuarios "
                . " SET Username ='". str_replace("'", "''",$data['Username']) 
                . "', Nombre = '".str_replace("'", "''",$data['Nombre'])
                . "', Cargo = '".str_replace("'", "''",$data['Cargo'])
                . "', Correo = ". (($data['Correo'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Correo']) . "'"))
                . ", Usu_Mod = " . $this->session->userdata("usu_id") 
                . ", Fec_Mod = NOW()" 
                . ", Observaciones = " 
                .(($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
                . " WHERE USU_ID = '" . str_replace("'", "''",$data['idActual']) . "';";

            //Ejecutar Query
            $result = pg_query($query);
            
            $iterador = 0;
            $cantidad = count($this->Permisos);

            $queryPermiso = "INSERT INTO PermisosUsuarios (usu_id,menu) VALUES ('" . str_replace("'", "''",$data['idActual']) . "',";
            $queryEliminar = "DELETE FROM PermisosUsuarios WHERE usu_id = '" . str_replace("'", "''",$data['idActual']) . "' AND  menu = ";

            while($result && $iterador < $cantidad){
                if($data[$this->Permisos[$iterador]]){
                    $result = pg_query($queryPermiso . "'" . $this->Permisos[$iterador] . "') on conflict do nothing;");
                }else{
                    $result = pg_query($queryEliminar . "'" . $this->Permisos[$iterador] . "';");
                }
                $iterador++;
            }
            


            if(!$result){
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                die(pg_last_error());
            }else
                pg_query("COMMIT") or die("Transaction commit failed");



            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);



            return true;
        }

        public function Configurar($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query = " UPDATE Usuarios "
                . " SET Nombre = '".str_replace("'", "''",$data['Nombre'])
                . "', Correo = ". (($data['Correo'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Correo']) . "'"))
                . ", Usu_Mod = " . $this->session->userdata("usu_id") 
                . ", Fec_Mod = NOW()" 
                . " WHERE USU_ID = '" . str_replace("'", "''",$data['idActual']) . "';";

                
            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return false;
        }

        public function Obtener($id = ''){
            

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            //Query para buscar usuario
            $query ="   SELECT  USU_ID,
                                Username,
                                Nombre,
                                Cargo,
                                COALESCE(Correo,'') Correo,
                                COALESCE(Observaciones,'') Observaciones
                        FROM Usuarios
                    ";

            if($id != ''){
                $query = $query . " WHERE USU_ID = '" . $id . "'";
            }

            $query = $query . " ORDER BY USU_ID DESC LIMIT 1;";

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
                $retorno['Permisos'] = $this->ObtenerPermisos($retorno['usu_id']);
            }

            return $retorno;
        }

        public function ObtenerPermisos($id){


            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            		
            //Query para buscar usuario
            $query ="   SELECT  menu
                        FROM PermisosUsuarios
                        WHERE usu_id = " . $id;

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $retorno = array();
            $per = $this->Permisos;

            while($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                unset($per[array_search($line['menu'],$per)]);
                $retorno[$line['menu']] = true;
            }

            foreach($per AS $p){
                $retorno[$p] = false;
            }
            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);


            return $retorno;
        }

        private function ObtenerUltimoIdInsertado(){

            //Query para buscar usuario
            $query ="   SELECT USU_ID FROM Usuarios 
                        WHERE Usu_cre = " . $this->session->userdata("usu_id") . "
                        ORDER BY USU_ID DESC LIMIT 1;";

            //Ejecutar Query
            $result = pg_query($query);
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) 
                $retorno = $line['usu_id'];
            else
                $retorno = false;

            //Liberar memoria
            pg_free_result($result);

            return $retorno;
        }

        public function Busqueda($busqueda,$orden,$inicio,$fin,$id){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            $condicion ="";


            if($busqueda != ""){
                $condicion = " WHERE " . ($id == "" ? "":("usu_id <> " . $id . " AND ")) 
                            . " (LOWER(username) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(nombre) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(cargo) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%')";
            }elseif($id != ""){
                $condicion = " WHERE usu_id <> " . $id;
            }
            
            //Query para buscar usuario
            $query ="   SELECT  usu_id,
                                Username,
                                Nombre,
                                Cargo,
                                Observaciones,
                                Registros
                        FROM (
                            SELECT  usu_id,
                                    Username,
                                    Nombre,
                                    Cargo,
                                    COALESCE(Observaciones,'') Observaciones,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $orden .") Fila
                            FROM Usuarios
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
            $query = " DELETE FROM Usuarios "
                . " WHERE USU_ID = '" .str_replace("'", "''",$id) . "';";
                
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

        public function logger($usuario, $clave){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query =    " SELECT USU_ID,Username,Correo,Nombre,Cargo,COALESCE(Observaciones,'') Observaciones "
                    .   " FROM Usuarios "
                    .   " WHERE username = '" . $usuario . "'"
                    .   "   AND clave = '" . $clave . "';";

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
                $retorno['Permisos'] = $this->ObtenerPermisos($retorno['usu_id']);
            }

            return $retorno;
        }

        public function ExisteUsername($username,$id=""){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query =" SELECT * FROM Usuarios WHERE LOWER(username) ='" . strtolower(str_replace("'", "''",$username)) . "' " ;

            if($id != "")
                $query = $query . " AND usu_id <>'" . str_replace("'", "''",$id) . "' " ;

                
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
        
        public function ObtenerIdUsuario($username){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            
            $query ="   SELECT USU_ID,correo,nombre FROM Usuarios 
                        WHERE LOWER(username) = '" . strtolower(str_replace("'", "''",$username)) . "';";

            //Ejecutar Query
            $result = pg_query($query);
            
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

        public function ExisteCorreo($correo,$id=""){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query =" SELECT * FROM Usuarios WHERE LOWER(correo) ='" . strtolower(str_replace("'", "''",$correo)) . "' " ;

            if($id != "")
                $query = $query . " AND usu_id <>'" . str_replace("'", "''",$id) . "' " ;

                
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