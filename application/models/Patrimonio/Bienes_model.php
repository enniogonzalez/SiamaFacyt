
<?php
    class Bienes_model extends CI_Model{
        
    
        public function Insertar($data){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");


            $query = " INSERT INTO Bienes ( Nombre,     Modelo,     BIE_SER,    Inv_UC,     PRO_ID,     Fec_Fab, 
                                            Fec_adq,    Fec_ins,    Tip_Adq,    LOC_ID,     PAR_ID,		MAR_ID,
                                            Custodio,   Fue_Ali,    Cla_Uso,    Tipo,       med_vol,    uni_vol, 
                                            med_amp,    uni_amp,    med_pot,    uni_pot,    med_fre,
                                            uni_fre,    med_cap,    uni_cap,    med_pre,    uni_pre,    med_flu, 
                                            uni_flu,    med_tem,    uni_tem,    med_pes,    uni_pes,
                                            med_vel,    uni_vel,    Tec_Pre,    Riesgo,     Rec_Fab,    Estatus, 
                                            Usu_Cre,    Usu_Mod,    Observaciones) 
                        VALUES('"
            . str_replace("'", "''",$data['Nombre'])    . "','"
            . str_replace("'", "''",$data['Modelo'])    . "','"
            . str_replace("'", "''",$data['Bie_Ser'])   . "',"
            . (($data['Inv_UC'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Inv_UC']) . "'"))
            . ",'"
            . str_replace("'", "''",$data['Pro_Id'])    . "','"
            . str_replace("'", "''",$data['Fec_Fab'])   . "','"
            . str_replace("'", "''",$data['Fec_adq'])   . "','"
            . str_replace("'", "''",$data['Fec_ins'])   . "','"
            . str_replace("'", "''",$data['Tip_Adq'])   . "','"
            . str_replace("'", "''",$data['Loc_Id'])    . "','"
            . str_replace("'", "''",$data['Par_Id'])    . "','"
            . str_replace("'", "''",$data['Mar_Id'])    . "','"
            . str_replace("'", "''",$data['Custodio'])  . "','"
            . str_replace("'", "''",$data['Fue_Ali'])   . "','"
            . str_replace("'", "''",$data['Cla_Uso'])   . "','"
            . str_replace("'", "''",$data['Tipo'])      . "','"
            . str_replace("'", "''",$data['med_vol'])   . "','"
            . str_replace("'", "''",$data['uni_vol'])   . "','"
            . str_replace("'", "''",$data['med_amp'])   . "','"
            . str_replace("'", "''",$data['uni_amp'])   . "','"
            . str_replace("'", "''",$data['med_pot'])   . "','"
            . str_replace("'", "''",$data['uni_pot'])   . "','"
            . str_replace("'", "''",$data['med_fre'])   . "','"
            . str_replace("'", "''",$data['uni_fre'])   . "','"
            . str_replace("'", "''",$data['med_cap'])   . "','"
            . str_replace("'", "''",$data['uni_cap'])   . "','"
            . str_replace("'", "''",$data['med_pre'])   . "','"
            . str_replace("'", "''",$data['uni_pre'])   . "','"
            . str_replace("'", "''",$data['med_flu'])   . "','"
            . str_replace("'", "''",$data['uni_flu'])   . "','"
            . str_replace("'", "''",$data['med_tem'])   . "','"
            . str_replace("'", "''",$data['uni_tem'])   . "','"
            . str_replace("'", "''",$data['med_pes'])   . "','"
            . str_replace("'", "''",$data['uni_pes'])   . "','"
            . str_replace("'", "''",$data['med_vel'])   . "','"
            . str_replace("'", "''",$data['uni_vel'])   . "','"
            . str_replace("'", "''",$data['Tec_Pre'])   . "','"
            . str_replace("'", "''",$data['Riesgo'])    . "',"
            . (($data['Rec_Fab'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Rec_Fab']) . "'"))
            .",'Activo',"
            . $this->session->userdata("usu_id")    . ","
            . $this->session->userdata("usu_id")    . ","
            . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
            . ");";

            //Ejecutar Query
            $result = pg_query($query);
            
            if($result){
                $UltimoId = $this->ObtenerUltimoIdInsertado();
                $query = "INSERT INTO HistoricoCustodios( usu_cus, bie_id, usu_cre) VALUES('"   
                        . str_replace("'", "''",$data['Custodio'])  . "','"
                        . str_replace("'", "''",$UltimoId['bie_id'])  . "',"
                        . $this->session->userdata("usu_id") . ")  ON CONFLICT DO NOTHING;";
                        
                $result = pg_query($query);
            }

            if(!$result){
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                die(pg_last_error());
            }else
                pg_query("COMMIT") or die("Transaction commit failed");

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);
            



            return true;
        }

        public function Actualizar($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $CustodioActual =$this->ObtenerCustodioctual(str_replace("'", "''",$data['idActual']));

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " UPDATE Bienes "
                . " SET Nombre ='". str_replace("'", "''",$data['Nombre']) 
                . "', Modelo = '" . str_replace("'", "''",$data['Modelo']) 
                . "', Bie_Ser = '" . str_replace("'", "''",$data['Bie_Ser']) 
                . "', Inv_UC = " 
                . (($data['Inv_UC'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Inv_UC']) . "'")) 
                . ", Pro_Id = '" . str_replace("'", "''",$data['Pro_Id']) 
                . "', Fec_Fab = '" . str_replace("'", "''",$data['Fec_Fab']) 
                . "', Fec_adq = '" . str_replace("'", "''",$data['Fec_adq']) 
                . "', Fec_ins = '" . str_replace("'", "''",$data['Fec_ins']) 
                . "', Tip_Adq = '" . str_replace("'", "''",$data['Tip_Adq']) 
                . "', Loc_Id = '" . str_replace("'", "''",$data['Loc_Id']) 
                . "', Par_Id = '" . str_replace("'", "''",$data['Par_Id']) 
                . "', Mar_Id = '" . str_replace("'", "''",$data['Mar_Id']) 
                . "', Custodio = '" . str_replace("'", "''",$data['Custodio']) 
                . "', Fue_Ali = '" . str_replace("'", "''",$data['Fue_Ali']) 
                . "', Cla_Uso = '" . str_replace("'", "''",$data['Cla_Uso']) 
                . "', Tipo = '" . str_replace("'", "''",$data['Tipo']) 
                . "', med_vol = '" . str_replace("'", "''",$data['med_vol']) 
                . "', uni_vol = '" . str_replace("'", "''",$data['uni_vol']) 
                . "', med_amp = '" . str_replace("'", "''",$data['med_amp']) 
                . "', uni_amp = '" . str_replace("'", "''",$data['uni_amp']) 
                . "', med_pot = '" . str_replace("'", "''",$data['med_pot']) 
                . "', uni_pot = '" . str_replace("'", "''",$data['uni_pot']) 
                . "', med_fre = '" . str_replace("'", "''",$data['med_fre']) 
                . "', uni_fre = '" . str_replace("'", "''",$data['uni_fre']) 
                . "', med_cap = '" . str_replace("'", "''",$data['med_cap']) 
                . "', uni_cap = '" . str_replace("'", "''",$data['uni_cap']) 
                . "', med_pre = '" . str_replace("'", "''",$data['med_pre']) 
                . "', uni_pre = '" . str_replace("'", "''",$data['uni_pre']) 
                . "', med_flu = '" . str_replace("'", "''",$data['med_flu']) 
                . "', uni_flu = '" . str_replace("'", "''",$data['uni_flu']) 
                . "', med_tem = '" . str_replace("'", "''",$data['med_tem']) 
                . "', uni_tem = '" . str_replace("'", "''",$data['uni_tem']) 
                . "', med_pes = '" . str_replace("'", "''",$data['med_pes']) 
                . "', uni_pes = '" . str_replace("'", "''",$data['uni_pes']) 
                . "', med_vel = '" . str_replace("'", "''",$data['med_vel']) 
                . "', uni_vel = '" . str_replace("'", "''",$data['uni_vel']) 
                . "', Tec_Pre = '" . str_replace("'", "''",$data['Tec_Pre']) 
                . "', Riesgo = '" . str_replace("'", "''",$data['Riesgo']) 
                . "', Rec_Fab = "
                . (($data['Rec_Fab'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Rec_Fab']) . "'"))
                . ", Usu_Mod = " . $this->session->userdata("usu_id") 
                . ", Fec_Mod = NOW()" 
                . ", Observaciones = "
                . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
                . " WHERE BIE_ID = '" . str_replace("'", "''",$data['idActual']) . "';";


            //Ejecutar Query
            $result = pg_query($query);
            
            if($result){
                
                if($CustodioActual['custodio'] != $data['Custodio']){

                    $query = "INSERT INTO HistoricoCustodios( usu_cus, bie_id, usu_cre) VALUES('"   
                    . str_replace("'", "''",$data['Custodio'])  . "','"
                    . str_replace("'", "''",$data['idActual'])  . "',"
                    . $this->session->userdata("usu_id") . ") ON CONFLICT DO NOTHING;";
                    
                    $result = pg_query($query);
                }
            }

            
            if(!$result){
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                die(pg_last_error());
            }else
                pg_query("COMMIT") or die("Transaction commit failed");

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);


            return true;
        }

        public function Obtener($id = ''){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query ="   SELECT  B.Bie_Id,     B.Nombre,     B.Modelo,     B.BIE_SER,    B.PRO_ID,   COALESCE(B.Inv_UC,'') Inv_UC,    
                                B.Fec_adq,    B.Fec_ins,    B.Tip_Adq,    B.LOC_ID,     B.PAR_ID,   B.MAR_ID,
                                B.Custodio,   B.Fue_Ali,    B.Cla_Uso,    B.Tipo,       B.med_vol,  B.uni_vol,
                                B.med_amp,    B.uni_amp,    B.med_pot,    B.uni_pot,    B.med_fre,  B.Fec_Fab,
                                B.uni_fre,    B.med_cap,    B.uni_cap,    B.med_pre,    B.uni_pre,  B.med_flu,
                                B.uni_flu,    B.med_tem,    B.uni_tem,    B.med_pes,    B.uni_pes,
                                B.med_vel,    B.uni_vel,    B.Tec_Pre,    B.Riesgo,     B.Estatus,
                                COALESCE(B.Rec_Fab,'') Rec_Fab,
                                COALESCE(B.Observaciones,'') Observaciones,
                                Par.nombre  nomPar,
                                P.Raz_Soc   nomPro,
                                L.nombre    nomLoc,
                                M.nombre    nomMar,
                                U.nombre    nomCus
                        FROM Bienes B
                            JOIN Localizaciones L ON L.loc_id = B.loc_Id
                            JOIN Proveedores P ON P.pro_id = B.pro_id
                            JOIN Marcas M ON M.mar_id = B.mar_id
                            JOIN Partidas Par ON Par.par_id =B.par_id
                            JOIN Usuarios U ON U.usu_id = B.custodio
                    ";

            if($id != ''){
                $query = $query . " WHERE B.Bie_Id = '" . $id . "'";
            }

            $query = $query . " ORDER BY B.Bie_Id DESC LIMIT 1;";

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            if ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) 
                $retorno = $line;
            else
                $retorno = false;

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            if($retorno){
                $retorno['Piezas'] = $this->ObtenerPiezas($retorno['bie_id']);
            }
            return $retorno;
        }

        public function ObtenerInfoPDF($id){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query ="   SELECT  B.Bie_Id,     B.Nombre,     B.Modelo,     B.BIE_SER,    B.PRO_ID,   COALESCE(B.Inv_UC,'') Inv_UC,    
                                B.Tip_Adq,    B.LOC_ID,     B.PAR_ID,   B.MAR_ID,
                                B.Custodio,   B.Fue_Ali,    B.Cla_Uso,    B.Tipo,       B.med_vol,  B.uni_vol,
                                B.med_amp,    B.uni_amp,    B.med_pot,    B.uni_pot,    B.med_fre,  
                                B.uni_fre,    B.med_cap,    B.uni_cap,    B.med_pre,    B.uni_pre,  B.med_flu,
                                B.uni_flu,    B.med_tem,    B.uni_tem,    B.med_pes,    B.uni_pes,
                                B.med_vel,    B.uni_vel,    B.Tec_Pre,    B.Riesgo,     B.Estatus,
                                COALESCE(B.Rec_Fab,'') Rec_Fab,
                                COALESCE(B.Observaciones,'') Observaciones, 
                                to_char(B.Fec_Fab,'DD/MM/YYYY') Fec_Fab,
                                to_char(B.Fec_adq,'DD/MM/YYYY') Fec_adq,
                                to_char(B.Fec_ins,'DD/MM/YYYY') Fec_ins,
                                Par.nombre  nomPar,
                                P.Raz_Soc   nomPro,
                                L.nombre    nomLoc,
                                M.nombre    nomMar,
                                U.nombre    nomCus
                        FROM Bienes B
                            JOIN Localizaciones L ON L.loc_id = B.loc_Id
                            JOIN Proveedores P ON P.pro_id = B.pro_id
                            JOIN Marcas M ON M.mar_id = B.mar_id
                            JOIN Partidas Par ON Par.par_id =B.par_id
                            JOIN Usuarios U ON U.usu_id = B.custodio
                        WHERE B.Bie_Id = '" . $id . "'";

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) 
                $retorno = $line;
            else
                $retorno = false;

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            if($retorno){
                $retorno['Piezas'] = $this->ObtenerPiezasPDF($retorno['bie_id']);
            }
            return $retorno;
        }

        public function Busqueda($busqueda,$orden,$inicio,$fin,$disponible){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            $condicion ="";

            if($disponible){
                $condicion = " (B.BIE_ID NOT IN( SELECT BIE_ID
                                                FROM Ajustes
                                                WHERE Estatus = 'Solicitado'

                                                UNION

                                                SELECT BIE_ID
                                                FROM PlantillaMantenimiento
                                                WHERE Estatus = 'Solicitado'

                                                UNION

                                                SELECT BIE_ID
                                                FROM MantenimientoCorrectivo
                                                WHERE Estatus <> 'Realizado'

                                                UNION

                                                SELECT BIE_ID
                                                FROM Mantenimiento
                                                WHERE Estatus <> 'Realizado') 
                                AND B.estatus = 'Activo')
                ";
            }

            if($busqueda != ""){
                $condicion = ($condicion == "" ? "": $condicion . " AND ") 
                            . "(LOWER(B.Inv_UC) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(B.nombre) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(B.estatus) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(L.nombre) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(M.nombre) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%')";
            }
            
            if($condicion != ""){
                $condicion = "WHERE " . $condicion;
            }
            //Query para buscar usuario
            $query ="   SELECT  Bie_Id,
                                nombre,
                                estatus,
                                Inv_UC,
                                nomLoc,
                                nomMar,
                                Registros
                        FROM (
                            SELECT  B.Bie_Id,
                                    B.nombre,
                                    B.estatus,
                                    COALESCE(B.Inv_UC,'') Inv_UC,
                                    L.nombre nomLoc,
                                    M.nombre nomMar,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $orden .") Fila
                            FROM Bienes B
                                JOIN Localizaciones L ON L.loc_id = B.loc_Id
                                JOIN Marcas M ON M.mar_id = B.mar_id
                            " . $condicion . "

                        ) LD
                        WHERE Fila BETWEEN ". $inicio . " AND " . $fin . "
                        ORDER BY Fila ASC;";

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($result){
                $retorno = [];
                while($line = pg_fetch_array($result, null, PGSQL_ASSOC))
                    array_push($retorno,$line);

            } else
                $retorno = false;

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return $retorno;
        }
        
        public function Eliminar($id){

            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Eliminar Bien
            $query = " DELETE FROM Bienes "
                . " WHERE Bie_Id = '" .str_replace("'", "''",$id) . "';";
                
            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($result) 
                $retorno = true;
            else
                $retorno = false;

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return $retorno;

        }

        public function ExisteInventario($inventario,$id=""){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query =" SELECT * FROM Bienes WHERE LOWER(Inv_UC) ='" . strtolower(str_replace("'", "''",$inventario)) . "' " ;

            if($id != "")
                $query = $query . " AND Bie_Id <>'" . str_replace("'", "''",$id) . "' " ;

                
            $query = $query . ";" ;

            

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Si existe registro, se guarda. Sino se guarda false
            if (pg_num_rows($result) > 0) 
                $retorno = true;
            else
                $retorno = false;

            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return $retorno;
        }

        private function ObtenerPiezas($bien){
            
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query ="   SELECT  Nombre,
                                Inv_UC,
                                Estatus
                        FROM Piezas
                        WHERE bie_id = '" . $bien . "'";

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            $html = "";

            //Si existe registro, se guarda. Sino se guarda false
            while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
                $html = $html
                    . "<tr>"
                    . "    <td style=\"width:55%;\">" . $line['nombre'] . "</td>"
                    . "    <td style=\"width:35%;\">" . $line['inv_uc'] . "</td>"
                    . "    <td style=\"width:10%;\">" . $line['estatus'] . "</td>"
                    . "</tr>";

            }
            
            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);


            return $html;
        }

        private function ObtenerPiezasPDF($bien){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $query ="   SELECT  Nombre,
                                Inv_UC,
                                Estatus
                        FROM Piezas
                        WHERE bie_id = '" . $bien . "'";

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

        private function ObtenerUltimoIdInsertado(){

            //Query para buscar usuario
            $query ="   SELECT bie_id FROM bienes 
                        WHERE Usu_cre = " . $this->session->userdata("usu_id") . "
                        ORDER BY bie_id DESC LIMIT 1;";

            //Ejecutar Query
            $result = pg_query($query);
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) 
                $retorno = $line;
            else
                $retorno = false;

            //Liberar memoria
            pg_free_result($result);

            return $retorno;
        }

        private function ObtenerCustodioctual($bie_id){

            //Query para buscar usuario
            $query ="   SELECT custodio FROM bienes 
                        WHERE bie_id = '" . $bie_id . "';";

            //Ejecutar Query
            $result = pg_query($query) or die(pg_last_error());
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) 
                $retorno = $line;
            else
                $retorno = false;

            //Liberar memoria
            pg_free_result($result);

            return $retorno;
        }
    }

?>