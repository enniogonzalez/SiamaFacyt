
<?php
    class Alertas_model extends CI_Model{
        
        function __construct(){
            parent::__construct();
            $this->load->library('libcorreosiama','libcorreosiama');
        }

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
                    . str_replace("'", "''",$descripcion) . "')";
                    
                $result2 = pg_query($query);
            }
        }
        
        public function EnviarCorreo($data){

            $correos = $this->ObtenerCorreos($data['Menu']);
            $Enviado = $this->ExisteEnvio($data);

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            if(!$Enviado && count($correos) > 0){

                $mensaje = "Saludos, <br/><br/> Se ha detectado un " . $data['Opcion'] . " " . $data['Estatus'] 
                    . " con la siguiente informaci&oacute;n: <br/><br/>" . $data['Cuerpo'];

                $Parametros = array(
                    "Correo" => $correos,
                    "Asunto" => $data['Titulo'],
                    "Mensaje" => $mensaje
                );
                
                $estatusCorreo = $this->libcorreosiama->EnviarCorreo($Parametros);
                

                if($estatusCorreo['enviado']){

                    $query = " INSERT INTO LogCorreo(correo,asunto,Mensaje,Tabla,Id) VALUES('"
                    . str_replace("'", "''",implode(",", $Parametros['Correo']) ) . "','"
                    . str_replace("'", "''",$Parametros['Asunto']) . "','"
                    . str_replace("'", "''",$Parametros['Mensaje']) . "','"
                    . str_replace("'", "''",$data['Tabla']) . "','"
                    . str_replace("'", "''",$data['id']) . "');";

                    $result = pg_query($query);
                    

                    $query = " INSERT INTO AlertaCorreo(Tabla,tab_id,Estatus) VALUES('"
                    . str_replace("'", "''",$data['Tabla']) . "','"
                    . str_replace("'", "''",$data['id']) . "','"
                    . str_replace("'", "''",$data['Estatus']) . "');";

                    $result = pg_query($query);

                }else{
                    
                    $query = " INSERT INTO LogCorreo(correo,asunto,Mensaje,Tabla,Id,Estatus,Error) VALUES('"
                    . str_replace("'", "''",implode(",", $Parametros['Correo']) ) . "','"
                    . str_replace("'", "''",$Parametros['Asunto']) . "','"
                    . str_replace("'", "''",$Parametros['Mensaje']) . "','"
                    . str_replace("'", "''",$data['Tabla']) . "','"
                    . str_replace("'", "''",$data['id']) . "','Error','"
                    . str_replace("'", "''",$estatusCorreo['Mensaje']) ."');";

                    $result = pg_query($query);
                }
            }
            
            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);
        }

        private function ExisteEnvio($data){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $retorno = false;

            $query = "    SELECT 1 
                        FROM alertacorreo
                        WHERE Tabla = '" . $data['Tabla'] ."'
                            AND Tab_id = '" . $data['id'] . "'
                            AND estatus = '" . $data['Estatus'] . "'";

            
            $result = pg_query($query);
                        
            if($result && pg_num_rows($result) > 0) 
                $retorno = true;

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return $retorno;
        }

        private function ObtenerCorreos($menu){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query ="   SELECT correo
                        FROM usuarios U
                            JOIN permisosusuarios PU ON PU.usu_id = U.usu_id
                        WHERE (U.correo is not NULL OR U.correo <> '')
                            AND PU.menu = '" . $menu ."'";

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            $retorno = [];
            while($line = pg_fetch_array($result, null, PGSQL_ASSOC))
                array_push($retorno,$line['correo']);

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return $retorno;
        }
    }

?>