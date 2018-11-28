
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

        public function ValidarAlertaPlantilla(){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query ="SELECT plm.plm_id,	
                            plm.documento, 
                            to_char(plm.fec_ult,'DD/MM/YYYY') fec_ult,
                            plm.frecuencia,
                            B.nombre,
                            LOC.nombre LOC_NOM

                    FROM plantillamantenimiento plm
                        JOIN bienes B ON B.bie_id = plm.bie_id
                        JOIN Localizaciones LOC ON LOC.LOC_ID = B.LOC_ID
                    WHERE plm.estatus = 'Aprobado'
                        AND (plm.fec_ult + interval '1' MONTH * plm.frecuencia) <= now()
                        AND NOT EXISTS(
                            SELECT 1
                            FROM mantenimiento man
                            WHERE man.plm_id = plm.plm_id
                                AND man.fec_ini > (plm.fec_ult + interval '1' MONTH * plm.frecuencia)
                        )
                    ";


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $result2 = true;
            while($result2 && $line = pg_fetch_array($result, null, PGSQL_ASSOC)){

                $titulo = "Plantilla de Mantenimiento Vencida " . $line['documento'];
                
                $descripcion = "<table style=\"width:100%\"><tr><td style=\"width:30%\"><strong>Documento:</strong></td><td style=\"width:70%\">" . $line['documento'] . "</td></tr>";
                $descripcion .= "<td><strong>Bien:</strong> </td><td>" . $line['nombre'] . "</td></tr>";
                $descripcion .= "<td><strong>Localizaci&oacute;n:</strong> </td><td>" . $line['loc_nom'] . "</td></tr>";
                $descripcion .= "<td><strong>Frecuencia Man.:</strong> </td><td>" . $line['frecuencia'] . " meses</td></tr>";
                $descripcion .= "<td><strong>&Uacute;ltimo Man.:</strong> </td><td>" . $line['fec_ult'] . "</td></tr></table>";

                // $descripcion = "Se debe realizar el mantenimiento preventivo especificado en la plantilla de mantenimiento "
                //             . "<strong>" . $line['documento'] . "</strong> al bien <strong>" . $line['nombre'] . "</strong>. "
                //             . "Dicho mantenimiento se debe realizar cada <strong>" . $line['frecuencia'] . " meses</strong> y "
                //             . "se realiz&oacute; por ultima vez el d&iacute;a <strong>" . $line['fec_ult'] . "</strong>.";

                $query = "INSERT INTO Alertas(Titulo, Menu, Tabla, TAB_ID,Usu_Cre,Descripcion)
                    VALUES('" . $titulo . "','Mantenimiento','PlantillaMantenimiento',"
                    . $line['plm_id'] . ",1,'"
                    . str_replace("'", "''",$descripcion) . "') ON CONFLICT DO NOTHING";
                    
                $result2 = pg_query($query);
            }
        }
        
    }

?>