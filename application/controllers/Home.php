
<?php

class Home extends CI_Controller{


    public function view(){

        if(!$this->session->userdata("nombre")){
            redirect(site_url(''));
        }

        //cargamos la libreria email de ci
        $this->load->library("email");
        
        //configuracion para gmail
        $configGmail = array(
        'protocol' => 'smtp',
        'smtp_host' => 'ssl://smtp.gmail.com',
        'smtp_port' => 465,
        'smtp_user' => 'mr.ennio@gmail.com',
        'smtp_pass' => 'ennio21031997',
        'mailtype' => 'html',
        'charset' => 'utf-8',
        'newline' => "\r\n"
        );    
        
        // //cargamos la configuración para enviar con gmail
        // $this->email->initialize($configGmail);
        
        // $this->email->from('mr.ennio@gmail.com');
        // $this->email->to("mr.ennio@gmail.com");
        // $this->email->subject('Pedrito');
        // $this->email->message('<h2>Email enviado con codeigniter haciendo uso del smtp de gmail</h2><hr><br> Bienvenido al blog');
        // $this->email->send();
        // //con esto podemos ver el resultado
        // var_dump($this->email->print_debugger());
        
        // //cargamos la configuración para enviar con gmail
        // $this->email->initialize($configGmail);
        
        // $this->email->from('mr.ennio@gmail.com');
        // $this->email->to("egonzalez@skyflot.com");
        // $this->email->subject('Pedrito');
        // $this->email->message('<h2>Email enviado con codeigniter haciendo uso del smtp de gmail</h2><hr><br> Bienvenido al blog');
        // $this->email->send();

        $datafile['JsFile'] = '';

        $data['cantAlertas'] = $this->alertas_model->CantidadAlertas();
        
        $this->load->view('plantillas/1-header', $datafile);
        $this->load->view('plantillas/2-barranavegacion',$data);
        $this->load->view('plantillas/2-modales');
        $this->load->view('plantillas/3-iniciomain');
        $this->load->view('plantillas/4-barramenu');
        $this->load->view('plantillas/5-iniciopagina');
        $this->load->view('paginas/home');
        $this->load->view('plantillas/7-footer');
    }

}

?>