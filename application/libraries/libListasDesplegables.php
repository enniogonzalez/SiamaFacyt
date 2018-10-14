<?php

class libListasDesplegables {

    public function FormatearListaDesplegable($datos,$PrimeroVacio = false,$seleccion = ''){

        if($PrimeroVacio)
            $htmlOpciones = "<option value =''></option>";
        else
            $htmlOpciones = "";
            
        if($datos){
            $data = $datos;

            $Opciones = json_decode($data['opciones'],true);

            foreach ($Opciones as $elemento) {
                $htmlOpciones = $htmlOpciones
                    ."<option value='". $elemento['Valor'] . "'" 
                    . ($seleccion == $elemento['Valor'] ? "selected":"" ). ">"
                    .   $elemento['Opcion'] . "</option>";;
            }
            
        }

        return $htmlOpciones;

    }

}

?>