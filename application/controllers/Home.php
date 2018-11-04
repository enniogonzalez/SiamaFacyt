
<?php

class Home extends CI_Controller{


    public function view(){

        if(!$this->session->userdata("nombre")){
            redirect(site_url(''));
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