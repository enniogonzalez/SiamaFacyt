
<?php
    class ResetPassword_model extends CI_Model{
        
        private $Permisos = array();
    
        function __construct(){
            parent::__construct();
            $this->load->library('libcorreosiama','libcorreosiama');
        }

        public function Insertar($data){
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();


            $token = bin2hex(openssl_random_pseudo_bytes(128));
            $error = false;
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " INSERT INTO LogContra(token,usu_id) VALUES('"
            . str_replace("'", "''",$token) . "','"
            . str_replace("'", "''",$data['usu_id']) . "');";

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $link = site_url('/restablecer/') .$token;
            $Mensaje = "<p>Estimado/a " . $data['nombre'] . ",</p> <br><br><p>Por favor haga click <a href='". $link ."'>aqu&iacute;</a> por poder restablecer su contraseña</p>";
            
            $correo = array(
                "Correo" => $data['correo'],
                "Asunto" => "Restablecer Contraseña",
                "Mensaje" => $Mensaje
            );
            $estatusCorreo = $this->libcorreosiama->EnviarCorreo($correo);


            if($estatusCorreo['enviado']){
                pg_query("COMMIT") or die("Transaction commit failed");

                $query = " INSERT INTO LogCorreo(correo,asunto,Mensaje,Tabla,Id) VALUES('"
                . str_replace("'", "''",$correo['Correo']) . "','"
                . str_replace("'", "''",$correo['Asunto']) . "','"
                . str_replace("'", "''",$correo['Mensaje']) . "','Usuarios', '"
                . str_replace("'", "''",$data['usu_id']) . "');";

                $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            }else{
                $error = true;
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                
                $query = " INSERT INTO LogCorreo(correo,asunto,Mensaje,Tabla,Id,Estatus,Error) VALUES('"
                . str_replace("'", "''",$correo['Correo']) . "','"
                . str_replace("'", "''",$correo['Asunto']) . "','"
                . str_replace("'", "''",$correo['Mensaje']) . "','Usuarios', '"
                . str_replace("'", "''",$data['usu_id']) . "','Error','"
                . str_replace("'", "''",$estatusCorreo['Mensaje']) ."');";

                $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            }

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            if($error){
                die("No se pudo enviar correo");
            }
            return $token;
        }

        public function Obtener($token){ 
            

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            //Query para buscar usuario
            $query ="   SELECT  u.username, u.nombre
                        FROM LogContra l
                            JOIN Usuarios u ON u.usu_id = l.usu_id
                        WHERE l.token = '" . $token . "'";

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

        public function CambiarClave($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " UPDATE Usuarios "
                . " SET clave ='". str_replace("'", "''",$data['password']) 
                . "',  Usu_Mod = null "
                . ", Fec_Mod = NOW()" 
                . " WHERE usu_id IN (SELECT usu_id FROM logcontra WHERE token = '" . str_replace("'", "''",$data['token']) . "')";

            //Ejecutar Query
            $result = pg_query($query);

            $query = " UPDATE logcontra "
                . " SET fec_res =NOW(),  usado = TRUE"
                . " WHERE token = '" . str_replace("'", "''",$data['token']) . "'";

            if($result)
                $result = pg_query($query);

            
            if(!$result){
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                die('La consulta fallo: ' . pg_last_error());
            }else
                pg_query("COMMIT") or die("Transaction commit failed");

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return true;
        }
    }

?>