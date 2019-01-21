<?php
    class Alertas_model extends CI_Model{
        
        function __construct(){
            parent::__construct();
            $this->load->library('libcorreosigma','libcorreosigma');
        }

        public function Obtener(){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query ="   SELECT  DISTINCT Alertas.Titulo,Alertas.Descripcion,Alertas.fec_cre
                        FROM Alertas
                            JOIN Bienes B ON B.bie_id = Alertas.bie_id
                            JOIN Localizaciones loc ON loc.loc_id = b.loc_id
                            JOIN Permisos P ON P.opcion = Alertas.menu
                            JOIN RolPermisos RP ON RP.per_id = P.per_id
                        WHERE (RP.rol_id = '" . $this->session->userdata("rol_id") . "'
                            OR loc.secuencia like '%" . $this->session->userdata("secuencia") . "%')
                        ORDER BY Alertas.fec_cre DESC
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

            $query ="   SELECT  COUNT(Distinct Alertas.Titulo) cantidad
                        FROM Alertas
                            JOIN Bienes B ON B.bie_id = Alertas.bie_id
                            JOIN Localizaciones loc ON loc.loc_id = b.loc_id
                            JOIN Permisos P ON P.opcion = Alertas.menu
                            JOIN RolPermisos RP ON RP.per_id = P.per_id
                        WHERE (RP.rol_id = '" . $this->session->userdata("rol_id") . "'
                            OR loc.secuencia like '%" . $this->session->userdata("secuencia") . "%')
                        ";

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
                            B.bie_id,
                            LOC.secuencia,
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
                        AND NOT EXISTS( 
                            SELECT 1 
                            FROM Alertas 
                            WHERE Titulo = CONCAT('Plantilla de Mantenimiento Vencida ',plm.documento)

                        )
                    ";


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $result2 = true;
            $correoMasivo = array();
            while($result2 && $line = pg_fetch_array($result, null, PGSQL_ASSOC)){

                $titulo = "Plantilla de Mantenimiento Vencida " . $line['documento'];
                
                $descripcion = "<table style=\"width:100%\"><tr><td style=\"width:35%\"><strong>Documento:</strong></td><td style=\"width:65%\">" . $line['documento'] . "</td></tr>";
                $descripcion .= "<td><strong>Bien:</strong> </td><td>" . $line['nombre'] . "</td></tr>";
                $descripcion .= "<td><strong>Localizaci&oacute;n:</strong> </td><td>" . $line['loc_nom'] . "</td></tr>";
                $descripcion .= "<td><strong>Frecuencia de Mantenimiento:</strong> </td><td>" . $line['frecuencia'] . " meses</td></tr>";
                $descripcion .= "<td><strong>&Uacute;ltimo Mantenimiento:</strong> </td><td>" . $line['fec_ult'] . "</td></tr></table>";


                $MensajeCorreo = "<strong>Documento:</strong> " . $line['documento'] . "<br/>";
                $MensajeCorreo .= "<strong>Bien:</strong> " . $line['nombre'] . "<br/>";
                $MensajeCorreo .= "<strong>Localizaci&oacute;n:</strong> " . $line['loc_nom'] . "<br/>";
                $MensajeCorreo .= "<strong>Frecuencia de Mantenimiento:</strong> " . $line['frecuencia'] . " meses<br/>";
                $MensajeCorreo .= "<strong>&Uacute;ltimo Mantenimiento:</strong> " . $line['fec_ult'];
                
                $correoMasivo = array(
                    "id"        => $line['plm_id'],
                    "Opcion"    => "Plantilla de Mantenimiento",
                    "Tabla"     => "PlantillaMantenimiento",
                    "Estatus"   => "Vencida",
                    "Secuencia" => $line['secuencia'],
                    "Titulo"    => $titulo,
                    "Menu"      => "Mantenimiento", 
                    "Cuerpo"    =>$MensajeCorreo
                );


                $query = "INSERT INTO Alertas(Titulo,bie_id,Menu, Tabla, TAB_ID,Usu_Cre,Descripcion)
                    VALUES('" . $titulo . "',"
                    . $line['bie_id'] .",'Mantenimiento','PlantillaMantenimiento',"
                    . $line['plm_id'] . ",null,'"
                    . str_replace("'", "''",$descripcion) . "')";
                    
                $result2 = pg_query($query);
            }

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);
            
            if(count($correoMasivo) > 0)
                $this->EnviarCorreo($correoMasivo);
        }
        
        public function ValidarAlertaCorPla(){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query ="SELECT  CPL.Documento,
                            cpl.cpl_id,
                            to_char(CPL.fec_eje,'DD/MM/YYYY') fec_eje,
                            BIE.nombre BIE_NOM,
                            BIE.bie_id,
                            USU.Nombre USU_NOM,
                            LOC.nombre LOC_NOM,
                            Loc.secuencia
                    FROM CorrectivoPlanificado CPL
                        LEFT JOIN Mantenimiento MAN ON MAN.man_id = CPL.man_id
                        LEFT JOIN MantenimientoCorrectivo MCO ON MCO.mco_id = CPL.mco_id
                        JOIN Bienes BIE ON BIE.BIE_ID = COALESCE(MAN.BIE_ID,MCO.BIE_ID)
                        JOIN Usuarios USU ON USU.USU_ID = CPL.USU_CRE
                        JOIN Localizaciones LOC ON LOC.LOC_ID = BIE.LOC_ID
                    WHERE CPL.estatus = 'Aprobado'
                        AND cpl.fec_eje <= now()
                        AND NOT EXISTS(
                            SELECT 1
                            FROM MantenimientoCorrectivo MCO
                            WHERE MCO.cpl_id = cpl.cpl_id
                        ) 
                        AND NOT EXISTS( 
                            SELECT 1 
                            FROM Alertas 
                            WHERE Titulo = CONCAT('Mantenimiento Correctivo Planificado Por Ejecutar ',cpl.documento)

                        )
                    ";

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

            $result2 = true;
            $correoMasivo = array();
            while($result2 && $line = pg_fetch_array($result, null, PGSQL_ASSOC)){

                $titulo = "Mantenimiento Correctivo Planificado Por Ejecutar " . $line['documento'];
                
                $descripcion = "<table style=\"width:100%\"><tr><td style=\"width:35%\"><strong>Documento:</strong></td><td style=\"width:65%\">" . $line['documento'] . "</td></tr>";
                $descripcion .= "<td><strong>Bien:</strong> </td><td>" . $line['bie_nom'] . "</td></tr>";
                $descripcion .= "<td><strong>Localizaci&oacute;n:</strong> </td><td>" . $line['loc_nom'] . "</td></tr>";
                $descripcion .= "<td><strong>Fecha Ejecuci&oacute;n:</strong> </td><td>" . $line['fec_eje'] . "</td></tr></table>";


                $MensajeCorreo = "<strong>Documento:</strong> " . $line['documento'] . "<br/>";
                $MensajeCorreo .= "<strong>Bien:</strong> " . $line['bie_nom'] . "<br/>";
                $MensajeCorreo .= "<strong>Localizaci&oacute;n:</strong> " . $line['loc_nom'] . "<br/>";
                $MensajeCorreo .= "<strong>Fecha Ejecuci&oacute;n:</strong> " . $line['fec_eje'];
                
                $correoMasivo = array(
                    "id"        => $line['cpl_id'],
                    "Opcion"    => "Mantenimiento Correctivo Planificado",
                    "Tabla"     => "CorrectivoPlanificado",
                    "Estatus"   => "Por Ejecutar",
                    "Secuencia" => $line['secuencia'],
                    "Titulo"    => $titulo,
                    "Menu"      => "Mantenimiento", 
                    "Cuerpo"    =>$MensajeCorreo
                );


                $query = "INSERT INTO Alertas(Titulo,bie_id,Menu, Tabla, TAB_ID,Usu_Cre,Descripcion)
                    VALUES('" . $titulo . "',"
                    . $line['bie_id'] .",'Mantenimiento','CorrectivoPlanificado',"
                    . $line['cpl_id'] . ",null,'"
                    . str_replace("'", "''",$descripcion) . "')";
                    
                $result2 = pg_query($query);
            }

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);
            
            if(count($correoMasivo) > 0)
                $this->EnviarCorreo($correoMasivo);
        }
        
        public function EnviarCorreo($data){

            $correos = $this->ObtenerCorreos($data['Menu'],$data['Secuencia']);
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
                
                $estatusCorreo = $this->libcorreosigma->EnviarCorreo($Parametros);
                

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
                            AND estatus = '" . $data['Estatus'] . "'
                            AND fecha = cast(now() as date)";

            
            $result = pg_query($query);
                        
            if($result && pg_num_rows($result) > 0) 
                $retorno = true;

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return $retorno;
        }

        private function ObtenerCorreos($menu, $secuencia){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query ="   SELECT DISTINCT correo
                        FROM usuarios U
                            JOIN RolPermisos RP ON rp.rol_id = u.rol_id
                            JOIN permisos P ON P.per_id = rp.per_id
                            LEFT JOIN Localizaciones loc ON loc.loc_id = u.loc_id
                        WHERE (U.correo is not NULL OR U.correo <> '')
                            AND (   P.opcion = '" . $menu ."' OR 
                                    (   P.opcion = 'Rep" . $menu ."' 
                                    AND u.loc_id is not null 
                                    AND COALESCE(loc.secuencia) like '%" . $secuencia ."%'
                                    )
                                )
                            ";  

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