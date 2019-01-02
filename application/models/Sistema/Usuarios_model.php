
<?php
    class Usuarios_model extends CI_Model{
        
        private $Permisos = array();
    
        function __construct(){
            parent::__construct();
            $this->Permisos = $this->ObtenerPermisosApp();
        }

        public function Actualizar($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " UPDATE Usuarios "
                . " SET Username ='". str_replace("'", "''",$data['Username']) 
                . "', Nombre = '".str_replace("'", "''",$data['Nombre'])
                . "', rol_id = '".str_replace("'", "''",$data['rol_id'])
                . "', Correo = ". (($data['Correo'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Correo']) . "'"))
                . ", loc_id = ". (($data['loc_id'] == "") ? "null" : ("'" .str_replace("'", "''", $data['loc_id']) . "'"))
                . ", Usu_Mod = " . $this->session->userdata("usu_id") 
                . ", Fec_Mod = NOW()" 
                . ", Observaciones = " 
                .(($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
                . " WHERE USU_ID = '" . str_replace("'", "''",$data['usu_id']) . "';";

            //Ejecutar Query
            $result = pg_query($query);
            
            if($result){
                $datos = array(
                    'Opcion' => 'Actualizar',
                    'Tabla' => 'Usuarios', 
                    'Tab_id' => $data['usu_id'],
                    'Datos' => json_encode($data)
                );
                
                $result = $this->auditorias_model->Insertar($datos);
            }

            if(!$result){
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                die(pg_last_error());
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
                $condicion = " WHERE " . ($id == "" ? "":("usu_id <> " . $id . " AND ")) 
                            . " (LOWER(usu.username) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(usu.nombre) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(rol.nombre) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%')";
            }elseif($id != ""){
                $condicion = " WHERE usu_id <> " . $id;
            }
            
            //Query para buscar usuario
            $query ="   SELECT  usu_id,
                                Username,
                                Nombre,
                                rol_nom,
                                Observaciones,
                                Registros
                        FROM (
                            SELECT  usu.usu_id,
                                    usu.Username,
                                    usu.Nombre,
                                    rol.nombre rol_nom,
                                    COALESCE(usu.Observaciones,'') Observaciones,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $orden .") Fila
                            FROM Usuarios USU
                                JOIN Roles ROL ON rol.rol_id = usu.rol_id
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

        public function Configurar($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query = " UPDATE Usuarios "
                . " SET Nombre = '".str_replace("'", "''",$data['Nombre'])
                . "', Correo = ". (($data['Correo'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Correo']) . "'"))
                . ", Usu_Mod = " . $this->session->userdata("usu_id") 
                . ", Fec_Mod = NOW()" 
                . " WHERE USU_ID = '" . str_replace("'", "''",$data['usu_id']) . "';";

                
            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return false;
        }
        
        public function Eliminar($id){
      
            $datosActual = $this->Obtener($id);
            unset($datosActual['Permisos']);
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " DELETE FROM Usuarios "
                . " WHERE USU_ID = '" .str_replace("'", "''",$id) . "';";
                
            //Ejecutar Query
            $result = pg_query($query);

            if($result){
                $datos = array(
                    'Opcion' => 'Eliminar',
                    'Tabla' => 'Usuarios', 
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

        public function Insertar($data){
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            //Insertar Proveedor
            $query = " INSERT INTO Usuarios(Username,Nombre,Clave,rol_id,loc_id,Correo,Usu_Cre,Usu_Mod,Observaciones) VALUES('"
            . str_replace("'", "''",$data['Username']) . "','"
            . str_replace("'", "''",$data['Nombre']) . "','e11170b8cbd2d74102651cb967fa28e5','"
            . str_replace("'", "''",$data['rol_id']) . "',"
            . (($data['loc_id'] == "") ? "null" : ("'" .str_replace("'", "''", $data['loc_id']) . "'")). ","
            . (($data['Correo'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Correo']) . "'")). ","
            . $this->session->userdata("usu_id")    . ","
            . $this->session->userdata("usu_id")    . ","
            . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
            . ") RETURNING usu_id;";

            //Ejecutar Query
            $result = pg_query($query);

            $new_id = "";
            
            if($result){
                $row = pg_fetch_row($result); 
                $new_id = $row['0']; 
            }

            if($result){
                $data['usu_id'] = $new_id;
                $datos = array(
                    'Opcion' => 'Insertar',
                    'Tabla' => 'Usuarios', 
                    'Tab_id' => $data['usu_id'],
                    'Datos' => json_encode($data)
                );
                
                $result = $this->auditorias_model->Insertar($datos);
            }

            if(!$result){
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                die(pg_last_error());
            }else
                pg_query("COMMIT") or die("Transaction commit failed");

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return true;
        }

        public function logger($usuario, $clave){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query =    " SELECT USU_ID,Username,Correo,Nombre,Rol_Id,COALESCE(Observaciones,'') Observaciones "
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
                $retorno['Permisos'] = $this->ObtenerPermisosRol($retorno['rol_id']);
            }

            return $retorno;
        }

        public function Obtener($id = ''){
            

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            //Query para buscar usuario
            $query ="   SELECT  USU.USU_ID,
                                USU.Username,
                                COALESCE(CAST(LOC.loc_id as varchar(20)),'') loc_id,		
                                COALESCE(loc.nombre,'') loc_nom,		
                                USU.Nombre,
                                USU.rol_id,
                                ROL.nombre rol_nom,
                                COALESCE(USU.Correo,'') Correo,
                                COALESCE(USU.Observaciones,'') Observaciones
                        FROM Usuarios USU
                            LEFT JOIN Localizaciones LOC ON LOC.loc_id = USU.loc_id
                            JOIN Roles ROL ON rol.rol_id = usu.rol_id
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
                $retorno['Permisos'] = $this->ObtenerPermisosRol($retorno['usu_id']);
            }

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

        public function ObtenerPermisosApp(){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            		
            //Query para buscar usuario
            $query ="   SELECT  opcion
                        FROM Permisos;";

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $arrayPer = [];
            while($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                array_push($arrayPer,$line['opcion']);
            }

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);


            return $arrayPer;
        }

        public function ObtenerPermisosRol($id){


            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            		
            //Query para buscar usuario
            $query ="   SELECT  P.opcion
                        FROM Permisos P
                            JOIN RolPermisos RP ON RP.per_id = P.per_id
                        WHERE RP.rol_id = " . $id;

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $retorno = array();
            $per = $this->Permisos;

            while($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                unset($per[array_search($line['opcion'],$per)]);
                $retorno[$line['opcion']] = true;
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

        public function ObtenerRolesApp(){


            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            		
            //Query para buscar usuario
            $query ="   SELECT  rol_id,nombre
                        FROM Roles;";

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $roles = [];
            while($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                array_push($roles,$line);
            }

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);


            return $roles;
        }
    }

?>