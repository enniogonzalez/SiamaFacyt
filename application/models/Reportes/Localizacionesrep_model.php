
<?php

    class Localizacionesrep_model extends CI_Model{
        
        public function ObtenerLocalizaciones($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $filtros = "";

            if($data['Localizacion'] != ""){
                $filtros .= "secuencia like CONCAT('%',(
                        SELECT secuencia 
                        FROM Localizaciones 
                        WHERE loc_id = ". $data['Localizacion'] ." limit 1),'%')";
            }

            if($filtros != ""){
                $filtros = "WHERE " . $filtros;
            }

            $query ="
            SELECT 	nombre,ubicacion,tipo,secuencia
            FROM Localizaciones 
            " . $filtros . "
            ORDER BY REPLACE(secuencia,'-','0') ASC";

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $retorno = array();
            while($line = pg_fetch_array($result, null, PGSQL_ASSOC))
                array_push($retorno,$line);

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);


            return $retorno;
        }

    }

?>