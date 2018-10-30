
<?php
    class Alertas_model extends CI_Model{
        
        public function Obtener(){
            

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query ="   SELECT  Titulo,Descripcion
                        FROM Alertas
                            JOIN PermisosUsuarios PU ON PU.menu = Alertas.menu
                        WHERE PU.usu_id = '" . $this->session->userdata("usu_id") . "'
                        ORDER BY fec_cre DESC
                    ";

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            $retorno = [];
            while($line = pg_fetch_array($result, null, PGSQL_ASSOC))
                array_push($retorno,$line);

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return $retorno;
        }

        public function CantidadAlertas(){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query ="   SELECT  COUNT(*) cantidad
                        FROM Alertas
                            JOIN PermisosUsuarios PU ON PU.menu = Alertas.menu
                        WHERE PU.usu_id = '" . $this->session->userdata("usu_id") . "'";

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            $retorno = 0;
            if($line = pg_fetch_array($result, null, PGSQL_ASSOC))
                $retorno = $line['cantidad'];

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return $retorno;
        }
        
    }

?>