
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller{

    public function view($page = 'login'){


        if(!file_exists(APPPATH.'views/paginas/login.php')){
            show_404();
        }

        if($page != 'login'){
            redirect(site_url(''));
        }
        
        $this->load->view('paginas/login');

        if($this->session->userdata("nombre")){
            redirect(site_url('home'));
        }

    }

    public function validar(){
        if($this->input->post("inputPassword") == ""){
            redirect(site_url(''));
        }
        $clave = $this->input->post("inputPassword");
        $usuario = $this->input->post("inputUsuario");


        $this->load->model('Sistema/usuarios_model' , 'usuarios_model');
        $resp = $this->usuarios_model->logger($usuario,$clave);

        if($resp){
            $data = [
                "usu_id"        => $resp['usu_id'],
                "username"      => $resp['username'],
                "nombre"        => $resp['nombre'],
                "cargo"         => $resp['cargo'],
                "observaciones" => $resp['observaciones'],
            ];
            $this->session->set_userdata($data);
            echo json_encode(array("isValid"=>true,"Mensaje"=>"Ingreso Satisfactorio","url" => site_url('home')));
        }else{
            echo json_encode(array("isValid"=>false,"Mensaje"=>"Usuario y contraseña no coinciden"));
        }
    }

    public function cerrarConexion(){
        $this->session->sess_destroy();
        redirect(site_url(''));
    }
}

?>