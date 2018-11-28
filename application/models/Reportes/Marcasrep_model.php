
<?php

    class Marcasrep_model extends CI_Model{
        
        public function listadomarcas($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $filtros = "";

            if($data['Marca'] != ""){
                $filtros .= "mar_id =  ". $data['Marca'] ;
            }

            if($filtros != ""){
                $filtros = "WHERE " . $filtros;
            }

            $query ="
            SELECT 	nombre, COALESCE(observaciones,'') observaciones
            FROM Marcas 
            " . $filtros . "
            ORDER BY nombre ASC";

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