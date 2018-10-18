
<?php
    class Piezas_model extends CI_Model{
        
    
        public function Insertar($data){
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Insertar Bien
            $query = " INSERT INTO Piezas ( Nombre, Estatus, Modelo, 
                                            Pie_ser, PRO_ID, PAR_ID, MAR_ID, Fec_Fab, 
                                            Fec_adq, Fec_ins, Tip_Adq, Inv_UC,Usu_Cre,
                                            Usu_Mod, Observaciones) 
                        VALUES('"
            . str_replace("'", "''",$data['Nombre'])    . "','"
            . str_replace("'", "''",$data['Estatus'])   . "','"
            . str_replace("'", "''",$data['Modelo'])    . "','"
            . str_replace("'", "''",$data['Pie_Ser'])    . "','"
            . str_replace("'", "''",$data['Pro_Id'])    . "','"
            . str_replace("'", "''",$data['Par_Id'])    . "','"
            . str_replace("'", "''",$data['Mar_Id'])    . "','"
            . str_replace("'", "''",$data['Fec_Fab'])   . "','"
            . str_replace("'", "''",$data['Fec_adq'])   . "','"
            . str_replace("'", "''",$data['Fec_ins'])   . "','"
            . str_replace("'", "''",$data['Tip_Adq'])   . "',"
            . (($data['Inv_UC'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Inv_UC']) . "'"))
            .","
            . $this->session->userdata("usu_id")    . ","
            . $this->session->userdata("usu_id")    . ","
            . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
            . ");";

            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);
            
            //Si existe registro, se guarda. Sino se guarda false
            if ($result)
                $retorno = true;
            else
                $retorno = false;


            return $retorno;
        }

        public function Actualizar($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            $query = " UPDATE Piezas "
                . " SET Nombre = '" . str_replace("'", "''",$data['Nombre']) 
                . "', Estatus = '" . str_replace("'", "''",$data['Estatus']) 
                . "', Modelo = '" . str_replace("'", "''",$data['Modelo']) 
                . "', Pie_Ser = '" . str_replace("'", "''",$data['Pie_Ser']) 
                . "', Pro_Id = '" . str_replace("'", "''",$data['Pro_Id']) 
                . "', Par_Id = '" . str_replace("'", "''",$data['Par_Id']) 
                . "', Mar_Id = '" . str_replace("'", "''",$data['Mar_Id']) 
                . "', Fec_Fab = '" . str_replace("'", "''",$data['Fec_Fab']) 
                . "', Fec_adq = '" . str_replace("'", "''",$data['Fec_adq']) 
                . "', Fec_ins = '" . str_replace("'", "''",$data['Fec_ins']) 
                . "', Tip_Adq = '" . str_replace("'", "''",$data['Tip_Adq']) 
                . "', Inv_UC = "
                . (($data['Inv_UC'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Inv_UC']) . "'"))
                . ", Usu_Mod = " . $this->session->userdata("usu_id") 
                . ", Fec_Mod = NOW()" 
                . ", Observaciones = "
                . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
                . " WHERE PIE_ID = '" . str_replace("'", "''",$data['idActual']) . "';";


            //Ejecutar Query
            $result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
            
            //Liberar memoria
            pg_free_result($result);

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            //Si existe registro, se guarda. Sino se guarda false
            if ($result)
                $retorno = true;
            else
                $retorno = false;


            return $retorno;
        }

        public function Obtener($id = ''){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  P.Pie_Id,   COALESCE(P.Bie_Id,-1) Bie_Id,   
                                P.Nombre,   P.Estatus,    P.Modelo, 
                                P.Pie_ser,  P.PRO_ID,   P.PAR_ID,   P.MAR_ID,     P.Fec_Fab, 
                                P.Fec_adq,  P.Fec_ins,  P.Tip_Adq, 
                                COALESCE(P.Inv_UC,'') Inv_UC,
                                COALESCE(P.Observaciones,'') Observaciones,
                                Par.nombre  nomPar,
                                Pro.Raz_Soc nomPro,
                                COALESCE(B.Nombre,'') nomBie,
                                M.nombre    nomMar
                        FROM Piezas P
                            JOIN Proveedores Pro ON Pro.pro_id = P.pro_id
                            JOIN Marcas M ON M.mar_id = P.mar_id
                            JOIN Partidas Par ON Par.par_id =P.par_id
                            LEFT JOIN Bienes B ON b.bie_id =P.bie_id
                    ";

            if($id != ''){
                $query = $query . " WHERE P.Pie_Id = '" . $id . "'";
            }

            $query = $query . " ORDER BY P.Pie_Id DESC LIMIT 1;";

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

            return $retorno;
        }

        public function ObtenerInfoPDF($id){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  P.Pie_Id,   COALESCE(P.Bie_Id,-1) Bie_Id,   
                                P.Nombre,   P.Estatus,    P.Modelo, 
                                P.Pie_ser,  P.PRO_ID,   P.PAR_ID,   P.MAR_ID, 
                                to_char(P.Fec_Fab,'DD/MM/YYYY') Fec_Fab,
                                to_char(P.Fec_adq,'DD/MM/YYYY') Fec_adq,
                                to_char(P.Fec_ins,'DD/MM/YYYY') Fec_ins,
                                P.Tip_Adq, 
                                COALESCE(P.Inv_UC,'') Inv_UC,
                                COALESCE(P.Observaciones,'') Observaciones,
                                Par.nombre  nomPar,
                                Pro.Raz_Soc nomPro,
                                COALESCE(B.Nombre,'') nomBie,
                                M.nombre    nomMar
                        FROM Piezas P
                            JOIN Proveedores Pro ON Pro.pro_id = P.pro_id
                            JOIN Marcas M ON M.mar_id = P.mar_id
                            JOIN Partidas Par ON Par.par_id =P.par_id
                            LEFT JOIN Bienes B ON b.bie_id =P.bie_id
                        WHERE P.Pie_Id = '" . $id . "'";

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

            return $retorno;
        }

        public function Busqueda($busqueda,$orden,$inicio,$fin,$id = "",$igual = true){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            $condicion ="";

            if($id != "" && $igual )
                $condicion = "P.Bie_Id = " . $id ;
            elseif($id != ""){
                $condicion = "
                    P.PIE_ID NOT IN(    SELECT AAC.PIE_ID
                                        FROM Ajustes AJU
                                            JOIN AjustesAccion AAC ON AAC.AJU_ID = AJU.AJU_ID
                                        WHERE AJU.Estatus = 'Solicitado'
                                        
                                        UNION
                                        
                                        SELECT PMT.PIE_ID
                                        FROM PlantillaMantenimiento PLM 
                                            JOIN PlantillaMantenimientoTarea PMT ON PMT.PLM_ID = PLM.PLM_ID
                                        WHERE PLM.Estatus = 'Solicitado'
                                        
                                        UNION
                                        
                                        SELECT CCO.PDA_ID
                                        FROM MantenimientoCorrectivo MCO
                                            JOIN CambioCorrectivo CCO ON CCO.MCO_ID = MCO.MCO_ID
                                        WHERE MCO.Estatus <> 'Realizado'
                                        
                                        UNION
                                        
                                        SELECT CCO.PCA_ID
                                        FROM MantenimientoCorrectivo MCO
                                            JOIN CambioCorrectivo CCO ON CCO.MCO_ID = MCO.MCO_ID
                                        WHERE MCO.Estatus <> 'Realizado'
                                        
                                        UNION
                                        
                                        SELECT RCO.PIE_ID
                                        FROM MantenimientoCorrectivo MCO
                                            JOIN ReparacionCorrectiva RCO ON RCO.MCO_ID = MCO.MCO_ID
                                        WHERE MCO.Estatus <> 'Realizado'
                                        
                                        UNION
                                        
                                        SELECT MTA.PIE_ID
                                        FROM Mantenimiento MAN
                                            JOIN MantenimientoTarea MTA ON MTA.MAN_ID = MAN.MAN_ID
                                        WHERE MAN.Estatus <> 'Realizado')
                AND (P.Bie_Id <> " . $id . " OR P.Bie_Id is null) AND P.estatus = 'Activo'";
            }

            if($busqueda != ""){
                $condicion = ($condicion == "" ? "": $condicion . " AND ") 
                            . "(LOWER(P.Inv_UC) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(P.nombre) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(P.estatus) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(B.nombre) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(M.nombre) like '%" . strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%')";

            }
            
            if($condicion != ""){
                $condicion = "WHERE " . $condicion;
            }

            //Query para buscar usuario
            $query ="   SELECT  Pie_Id,
                                Bie_Id,
                                estatus,
                                nombre,
                                Inv_UC,
                                nomBie,
                                nomMar,
                                Registros
                        FROM (
                            SELECT  P.Pie_Id,
                                    P.nombre,
                                    P.estatus,
                                    COALESCE(P.Bie_Id,-1) Bie_Id,
                                    P.Inv_UC,
                                    COALESCE(B.nombre,'') nomBie,
                                    M.nombre nomMar,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $orden .") Fila
                            FROM Piezas P
                                LEFT JOIN Bienes B ON B.bie_id = P.bie_id
                                JOIN Marcas M ON M.mar_id = P.mar_id
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
            $query = " DELETE FROM Piezas "
                . " WHERE Pie_Id = '" .str_replace("'", "''",$id) . "';";
                
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
            $query =" SELECT * FROM Piezas WHERE LOWER(Inv_UC) ='" . strtolower(str_replace("'", "''",$inventario)) . "' " ;

            if($id != "")
                $query = $query . " AND pie_id <>'" . str_replace("'", "''",$id) . "' " ;

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
    }

?>