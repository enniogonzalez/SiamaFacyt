
<?php
    class Bd_model extends CI_Model{
        
        private $host='localhost';
        private $port='5432';
        private $dbname='siamafacyt'; 
        private $user='userapp';
        private $password='userapp12293';


        public function ObtenerConexion(){
            $stringconection =  "host=" . $this->host 
                . " port=" . $this->port 
                . " dbname=" . $this->dbname 
                . " user=" . $this->user 
                . " password=" . $this->password
                . " options='--client_encoding=UTF8'";

            $con = pg_connect($stringconection) or die('No se ha podido conectar: ' . pg_last_error());

            return $con;
        }

        public function CerrarConexion($conexion){
            pg_close($conexion);
        }



    }

?>