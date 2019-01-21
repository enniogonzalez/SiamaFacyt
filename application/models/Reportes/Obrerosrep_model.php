<?php

    class Obrerosrep_model extends CI_Model{
        
        public function listadoobreros($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $filtros = "";

            if($data['Obrero'] != ""){
                $filtros .= "obr_id =  ". $data['Obrero'] ;
            }

            if($filtros != ""){
                $filtros = "WHERE " . $filtros;
            }

            $query ="
            SELECT 	cedula, nombre, COALESCE(telefonos,'') telefonos, COALESCE(correo,'') correo
            FROM Obreros 
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
            FROM obreros 
            WHERE obr_id = " . $data['Obrero'];

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