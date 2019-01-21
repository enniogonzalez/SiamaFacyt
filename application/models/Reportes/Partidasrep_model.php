<?php

    class Partidasrep_model extends CI_Model{
        
        public function listadopartidas($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $filtros = "";

            if($data['Partida'] != ""){
                $filtros .= "par_id =  ". $data['Partida'] ;
            }

            if($filtros != ""){
                $filtros = "WHERE " . $filtros;
            }

            $query ="
            SELECT 	codigo, nombre, COALESCE(observaciones,'') observaciones
            FROM Partidas 
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

        public function ObtenerParametros($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query ="
            SELECT 	nombre
            FROM partidas 
            WHERE par_id = " . $data['Partida'];

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $retorno = array();
            while($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                array_push($retorno,$line);
            }

            //Liberar memoria
            pg_free_result($result);
    
            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return $retorno;
        }
    }

?>