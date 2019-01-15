<?php
    class Auditorias_model extends CI_Model{
        
        public function Insertar($data){
            
            $query ="   INSERT INTO Auditorias(Opcion,Tabla,TAB_ID,Datos,Usu_Cre)
                        VALUES('"
                        . str_replace("'", "''",$data['Opcion']) . "','"
                        . str_replace("'", "''",$data['Tabla']) . "','"
                        . str_replace("'", "''",$data['Tab_id']) . "','"
                        . str_replace("'", "''",$data['Datos']) . "',"
                        . $this->session->userdata("usu_id")  .");";

            $result = pg_query($query);
            
            if($result){
                $retorno = true;
            }else{
                $retorno = false;
            }

            return $retorno;
        }

    }

?>