
<?php

class Home extends CI_Controller{


    public function __construct(){
        parent::__construct();
        $this->load->model('Sistema/usuarios_model' , 'usuarios_model');
        $this->load->model('Sistema/resetpassword_model' , 'resetpassword_model');
    }

    public function view(){

        if(!$this->session->userdata("nombre")){
            redirect(site_url(''));
        }

        if($this->usuarios_model->EsClaveDefecto($this->session->userdata("usu_id"))){
            $token = $this->resetpassword_model->InsertarContraDefecto($this->session->userdata("usu_id"));
            $this->session->sess_destroy();
            redirect(site_url('/restablecer/').$token);
        }
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