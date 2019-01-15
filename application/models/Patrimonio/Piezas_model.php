<?php
    class Piezas_model extends CI_Model{
        
    
        public function Insertar($data){
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            //Insertar Pieza
            $query = " INSERT INTO Piezas ( Nombre, Estatus, Modelo, 
                                            Pie_ser, PRO_ID, PAR_ID, MAR_ID,Tpi_ID, Fec_Fab, 
                                            Fec_adq, Fec_ins, Tip_Adq, Inv_UC,Usu_Cre,
                                            Usu_Mod, Observaciones) 
                        VALUES('"
            . str_replace("'", "''",$data['Nombre'])    . "','"
            . str_replace("'", "''",$data['Estatus'])   . "','"
            . str_replace("'", "''",$data['Modelo'])    . "','"
            . str_replace("'", "''",$data['Pie_Ser'])    . "','"
            . str_replace("'", "''",$data['Pro_Id'])    . "',"
            . (($data['Par_Id'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Par_Id']) . "'"))
            . ",'"
            . str_replace("'", "''",$data['Mar_Id'])    . "','"
            . str_replace("'", "''",$data['Tpi_Id'])    . "','"
            . str_replace("'", "''",$data['Fec_Fab'])   . "','"
            . str_replace("'", "''",$data['Fec_adq'])   . "','"
            . str_replace("'", "''",$data['Fec_ins'])   . "','"
            . str_replace("'", "''",$data['Tip_Adq'])   . "',"
            . (($data['Inv_UC'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Inv_UC']) . "'"))
            .","
            . $this->session->userdata("usu_id")    . ","
            . $this->session->userdata("usu_id")    . ","
            . (($data['Observaciones'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Observaciones']) . "'"))
            . ")RETURNING pie_id;";

            //Ejecutar Query
            $result = pg_query($query);
            

            if($result){
                $row = pg_fetch_row($result); 
                $new_id = $row['0']; 
            }

            if($result){
                $data['idActual'] = $new_id;
                $datos = array(
                    'Opcion' => 'Insertar',
                    'Tabla' => 'Piezas', 
                    'Tab_id' => $new_id,
                    'Datos' => json_encode($data)
                );
                
                $result = $this->auditorias_model->Insertar($datos);
            }
            
            if(!$result){
                $error = pg_last_error();
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                die($error);
            }else
                pg_query("COMMIT") or die("Transaction commit failed");


            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);
            
            return true;
        }

        public function Actualizar($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            $query = " UPDATE Piezas "
                . " SET Nombre = '" . str_replace("'", "''",$data['Nombre']) 
                . "', Estatus = '" . str_replace("'", "''",$data['Estatus']) 
                . "', Modelo = '" . str_replace("'", "''",$data['Modelo']) 
                . "', Pie_Ser = '" . str_replace("'", "''",$data['Pie_Ser']) 
                . "', Pro_Id = '" . str_replace("'", "''",$data['Pro_Id']) 
                . "', Par_Id = "
                . (($data['Par_Id'] == "") ? "null" : ("'" .str_replace("'", "''", $data['Par_Id']) . "'"))
                . ", Mar_Id = '" . str_replace("'", "''",$data['Mar_Id']) 
                . "', Tpi_Id = '" . str_replace("'", "''",$data['Tpi_Id']) 
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
            $result = pg_query($query);
            

            if($result){
                $datos = array(
                    'Opcion' => 'Actualizar',
                    'Tabla' => 'Piezas', 
                    'Tab_id' => $data['idActual'],
                    'Datos' => json_encode($data)
                );
                
                $result = $this->auditorias_model->Insertar($datos);
            }

            if(!$result){
                $error = pg_last_error();
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                die($error);
            }else
                pg_query("COMMIT") or die("Transaction commit failed");

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return true;
        }

        public function Obtener($id = ''){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            //Query para buscar usuario
            $query ="   SELECT  P.Pie_Id,   COALESCE(P.Bie_Id,-1) Bie_Id,   
                                P.Nombre,   P.Estatus,    P.Modelo, 
                                P.Pie_ser,  P.PRO_ID,   COALESCE(P.PAR_ID,-1) PAR_ID ,   
                                P.MAR_ID,     P.Fec_Fab, 
                                P.Fec_adq,  P.Fec_ins,  P.Tip_Adq, 
                                P.tpi_id,
                                TPI.nombre nomtpi,
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
                            JOIN Tipopieza TPI ON TPI.tpi_id = P.tpi_id
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
                                P.Pie_ser,  P.PRO_ID,   COALESCE(P.PAR_ID,-1) PAR_ID,   
                                P.MAR_ID, 
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

        public function Busqueda($busqueda,$orden,$inicio,$fin,$id = ""){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
            $condicion ="";

            if($id != "")
                $condicion = "P.Bie_Id = " . $id ;

            if($busqueda != ""){
                $condicion = ($condicion == "" ? "": $condicion . " AND ") 
                            . "(LOWER(P.Inv_UC) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(P.nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(P.estatus) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(B.nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
                            . "%' OR LOWER(M.nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$busqueda)))
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
                                tpi_id,
                                nomtpi,
                                nomBie,
                                nomMar,
                                Registros
                        FROM (
                            SELECT  P.Pie_Id,
                                    P.nombre,
                                    P.estatus,
                                    COALESCE(P.Bie_Id,-1) Bie_Id,
                                    P.Inv_UC,
                                    P.tpi_id,
                                    TPI.nombre nomtpi,
                                    COALESCE(B.nombre,'') nomBie,
                                    M.nombre nomMar,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $orden .") Fila
                            FROM Piezas P
                                LEFT JOIN Bienes B ON B.bie_id = P.bie_id
                                JOIN Marcas M ON M.mar_id = P.mar_id
                                JOIN Tipopieza TPI ON TPI.tpi_id = P.tpi_id
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
          
        public function busquedaCorrectivo($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $condicion ="";

            if($data['busqueda'] != ""){
                $condicion = "(LOWER(P.Inv_UC) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(P.nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(P.estatus) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(B.nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(M.nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%')";
            }
            
            if($condicion != ""){
                $condicion = "AND " . $condicion;
            }

            //Query para buscar usuario
            $query ="   SELECT  *
                        FROM (
                            SELECT  P.Pie_Id,
                                    P.nombre,
                                    P.estatus,
                                    COALESCE(P.Bie_Id,-1) Bie_Id,
                                    P.Inv_UC,
                                    COALESCE(B.nombre,'') nomBie,
                                    M.nombre nomMar,
                                    FAL.fal_id,
                                    FAL.nombre fal_nom,
                                    P.tpi_id,
                                    TPI.nombre nomtpi,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $data['orden'] .") Fila
                            FROM CorrectivoPlanificadoPieza CPP
                                JOIN Piezas P ON P.pie_id = CPP.pie_id
                                LEFT JOIN Bienes B ON B.bie_id = P.bie_id
                                JOIN Marcas M ON M.mar_id = P.mar_id
                                JOIN Tipopieza TPI ON TPI.tpi_id = P.tpi_id
                                JOIN Fallas FAL ON FAL.fal_id = CPP.fal_id
                            WHERE CPP.cpl_id = " . $data['cpl_id'] ."
                            " . $condicion . "

                        ) LD
                        WHERE Fila BETWEEN ". $data['inicio'] . " AND " . $data['fin'] . "
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

        public function busquedaDisponibles($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $condicion ="";

            if(!$data['PiewBie']){
                $condicion = " P.bie_id is null ";
            }

            if($data['busqueda'] != ""){
                $condicion = ($condicion == "" ? "": $condicion . " AND ") 
                            . "(LOWER(P.Inv_UC) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(P.nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(P.estatus) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(B.nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(M.nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%')";
            }
            
            if($data['tpi_id'] != ""){
                $condicion = ($condicion == "" ? "": $condicion . " AND ") 
                        . "P.tpi_id = " . $data['tpi_id'];
            }
            
            if($condicion != ""){
                $condicion = "AND " . $condicion;
            }

            //Query para buscar usuario
            $query ="   SELECT  Pie_Id,Bie_Id,estatus,
                                nombre,Inv_UC,nomBie,
                                tpi_id,nomtpi,
                                nomMar,Registros
                        FROM (
                            SELECT  P.Pie_Id,
                                    P.nombre,
                                    P.estatus,
                                    COALESCE(P.Bie_Id,-1) Bie_Id,
                                    P.Inv_UC,
                                    COALESCE(B.nombre,'') nomBie,
                                    M.nombre nomMar,
                                    P.tpi_id,
                                    TPI.nombre nomtpi,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $data['orden'] .") Fila
                            FROM Piezas P
                                LEFT JOIN Bienes B ON B.bie_id = P.bie_id
                                JOIN Marcas M ON M.mar_id = P.mar_id
                                JOIN Tipopieza TPI ON TPI.tpi_id = P.tpi_id
                            WHERE P.PIE_ID NOT IN(   

                                    SELECT AAC.PIE_ID
                                    FROM Ajustes AJU
                                        JOIN AjustesAccion AAC ON AAC.AJU_ID = AJU.AJU_ID
                                    WHERE AJU.Estatus = 'Solicitado'
                                                                        
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
                            AND (P.Bie_Id <> " . $data['id'] . " OR P.Bie_Id is null) 
                            AND P.estatus = 'Activo'
                            " . $condicion . "

                        ) LD
                        WHERE Fila BETWEEN ". $data['inicio'] . " AND " . $data['fin'] . "
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
        
        public function busquedaAgregar($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $condicion ="";

            if($data['busqueda'] != ""){
                $condicion = "(LOWER(P.Inv_UC) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(P.nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(P.estatus) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(B.nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(M.nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%')";
            }
            
            if($condicion != ""){
                $condicion = "AND " . $condicion;
            }

            //Query para buscar usuario
            $query ="   SELECT  Pie_Id,Bie_Id,estatus,
                                nombre,Inv_UC,nomBie,
                                tpi_id,nomtpi,
                                nomMar,Registros
                        FROM (
                            SELECT  P.Pie_Id,
                                    P.nombre,
                                    P.estatus,
                                    COALESCE(P.Bie_Id,-1) Bie_Id,
                                    P.Inv_UC,
                                    COALESCE(B.nombre,'') nomBie,
                                    M.nombre nomMar,
                                    P.tpi_id,
                                    TPI.nombre nomtpi,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $data['orden'] .") Fila
                            FROM Piezas P
                                LEFT JOIN Bienes B ON B.bie_id = P.bie_id
                                JOIN Marcas M ON M.mar_id = P.mar_id
                                JOIN Tipopieza TPI ON TPI.tpi_id = P.tpi_id
                                JOIN CompatibilidadBien CBI ON CBI.tpi_id = P.tpi_id
                            WHERE P.PIE_ID NOT IN(   

                                    SELECT AAC.PIE_ID
                                    FROM Ajustes AJU
                                        JOIN AjustesAccion AAC ON AAC.AJU_ID = AJU.AJU_ID
                                    WHERE AJU.Estatus = 'Solicitado'
                                                                        
                                    UNION
                                    
                                    SELECT CCO.PCA_ID
                                    FROM MantenimientoCorrectivo MCO
                                        JOIN CambioCorrectivo CCO ON CCO.MCO_ID = MCO.MCO_ID
                                    WHERE MCO.Estatus <> 'Realizado'
                                )
                            AND P.Bie_Id is null
                            AND P.estatus = 'Activo'
                            AND CBI.bie_id = '" . $data['idBien'] . "'
                            " . $condicion . "

                        ) LD
                        WHERE Fila BETWEEN ". $data['inicio'] . " AND " . $data['fin'] . "
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
        
        public function busquedaQuitar($data){
            
            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();

            $condicion ="";

            if($data['busqueda'] != ""){
                $condicion = "(LOWER(P.Inv_UC) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(P.nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(P.estatus) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(B.nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%' OR LOWER(M.nombre) like '%" . mb_strtolower(str_replace(" ","%",str_replace("'", "''",$data['busqueda'])))
                            . "%')";
            }
            
            if($condicion != ""){
                $condicion = "AND " . $condicion;
            }

            //Query para buscar usuario
            $query ="   SELECT  Pie_Id,Bie_Id,estatus,
                                nombre,Inv_UC,nomBie,
                                tpi_id,nomtpi,
                                nomMar,Registros
                        FROM (
                            SELECT  P.Pie_Id,
                                    P.nombre,
                                    P.estatus,
                                    COALESCE(P.Bie_Id,-1) Bie_Id,
                                    P.Inv_UC,
                                    COALESCE(B.nombre,'') nomBie,
                                    M.nombre nomMar,
                                    P.tpi_id,
                                    TPI.nombre nomtpi,
                                    COUNT(*) OVER() AS Registros,
                                    ROW_NUMBER() OVER(ORDER BY " . $data['orden'] .") Fila
                            FROM Piezas P
                                LEFT JOIN Bienes B ON B.bie_id = P.bie_id
                                JOIN Marcas M ON M.mar_id = P.mar_id
                                JOIN Tipopieza TPI ON TPI.tpi_id = P.tpi_id
                            WHERE P.PIE_ID NOT IN(   

                                    SELECT AAC.PIE_ID
                                    FROM Ajustes AJU
                                        JOIN AjustesAccion AAC ON AAC.AJU_ID = AJU.AJU_ID
                                    WHERE AJU.Estatus = 'Solicitado'
                                                                        
                                    UNION
                                    
                                    SELECT CCO.PCA_ID
                                    FROM MantenimientoCorrectivo MCO
                                        JOIN CambioCorrectivo CCO ON CCO.MCO_ID = MCO.MCO_ID
                                    WHERE MCO.Estatus <> 'Realizado'
                                                                        
                                    UNION
                                    
                                    SELECT CCO.PDA_ID
                                    FROM MantenimientoCorrectivo MCO
                                        JOIN CambioCorrectivo CCO ON CCO.MCO_ID = MCO.MCO_ID
                                    WHERE MCO.Estatus <> 'Realizado'
                                                                        
                                    UNION
                                    
                                    SELECT MTA.PIE_ID
                                    FROM Mantenimiento MAN
                                        JOIN MantenimientoTarea MTA ON MTA.MAN_ID = MAN.MAN_ID
                                    WHERE MAN.Estatus <> 'Realizado'
                                )
                            AND P.bie_id = '" . $data['idBien'] . "'
                            " . $condicion . "

                        ) LD
                        WHERE Fila BETWEEN ". $data['inicio'] . " AND " . $data['fin'] . "
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
      
            $datosActual = $this->Obtener($id);

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Abrir Transaccion
            pg_query("BEGIN") or die("Could not start transaction");

            //Eliminar Pieza
            $query = " DELETE FROM Piezas "
                . " WHERE Pie_Id = '" .str_replace("'", "''",$id) . "';";
                
            //Ejecutar Query
            $result = pg_query($query);
            
            if($result){
                $datos = array(
                    'Opcion' => 'Eliminar',
                    'Tabla' => 'Piezas', 
                    'Tab_id' => $id,
                    'Datos' => json_encode($datosActual)
                );
                
                $result = $this->auditorias_model->Insertar($datos);
            }

            if(!$result){
                $error = pg_last_error();
                pg_query("ROLLBACK") or die("Transaction rollback failed");
                die($error);
            }else
                pg_query("COMMIT") or die("Transaction commit failed");

            //liberar conexion
            $this->bd_model->CerrarConexion($conexion);

            return true;

        }

        public function ExisteInventario($inventario,$id=""){

            //Abrir conexion
            $conexion = $this->bd_model->ObtenerConexion();
    
            //Query para buscar usuario
            $query =" SELECT * FROM Piezas WHERE LOWER(Inv_UC) ='" . mb_strtolower(str_replace("'", "''",$inventario)) . "' " ;

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