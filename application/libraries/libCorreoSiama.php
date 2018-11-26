<?php

class libCorreoSiama {

    public function EnviarCorreo($data){
        
        $CI =& get_instance();

        $CI->load->library('email');

        
        //configuracion para gmail
        $configGmail = array(
        'protocol' => 'smtp',
        'smtp_host' => 'ssl://smtp.gmail.com',
        'smtp_port' => 465,
        'smtp_user' => 'siamafacyt@gmail.com',
        'smtp_pass' => 'siamafacytcorreo1234',
        'mailtype' => 'html',
        'charset' => 'utf-8',
        'newline' => "\r\n"
        );    

        
        //cargamos la configuración para enviar con gmail
        $CI->email->initialize($configGmail);
        
        $CI->email->from('siamafacyt@gmail.com', "Departamento de Sistema SiamaFacyt");
        $CI->email->to($data['Correo']);
        $CI->email->subject($data['Asunto']);
        $CI->email->message($data['Mensaje']);
        
        $enviado = $CI->email->send();

        $retorno = array("enviado" => $enviado, "Mensaje" => $CI->email->print_debugger());

        return $retorno;

    }

}

?>