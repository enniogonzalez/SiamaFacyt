
<?php

    class Proveedoresrep_model extends CI_Model{
        
        public function listadoproveedores($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $filtros = "";

            if($data['Proveedor'] != ""){
                $filtros .= "pro_id =  ". $data['Proveedor'] ;
            }

            if($filtros != ""){
                $filtros = "WHERE " . $filtros;
            }

            $query ="
            SELECT 	rif, raz_soc, reg_nac_con
            FROM Proveedores 
            " . $filtros . "
            ORDER BY raz_soc ASC";

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