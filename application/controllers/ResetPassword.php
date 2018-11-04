
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ResetPassword extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model('Sistema/usuarios_model' , 'usuarios_model');
        $this->load->model('Sistema/resetpassword_model' , 'resetpassword_model');
    }

    public function view($page = ''){

        $JsFile = "<script src=\"". base_url() . "assets/js/Siama/Reestablecer.js\"></script>";
                
        $datafile['JsFile'] = $JsFile ;
        $dataLD['OrdenarBusqueda'] = "";

        if($page == "usuario"){

            $this->load->view('plantillas/1-header-login', $datafile);
            $this->load->view('plantillas/2-modales',$dataLD);
            $this->load->view('paginas/Reestablecer/reestablecer');
            $this->load->view('plantillas/7-footer-login');
        }else{
            $usuario = $this->resetpassword_model->Obtener($page);
            $usuario['token'] = $page;
            if($usuario){
                
                $this->load->view('plantillas/1-header-login', $datafile);
                $this->load->view('plantillas/2-modales',$dataLD);
                $this->load->view('paginas/Reestablecer/restClave',$usuario);
                $this->load->view('plantillas/7-footer-login');
            }else{
                show_404();
            }
        }

    }

    public function guardar(){
        
        $parametros = array(
            "Username"      => $this->input->post("Username")
        );

        if($this->usuarios_model->ExisteUsername($parametros['Username'])){
            $Usuario = $this->usuarios_model->ObtenerIdUsuario($parametros['Username']);
            $token = $this->resetpassword_model->Insertar($Usuario);
            echo json_encode(array(
                "isValid"=>true,
                "Correo" => $Usuario['correo'],
                "Mensaje"=>"Existe"));
        }else{
            
            echo json_encode(array(
                "isValid"=>false,
                "Mensaje"=>"No existe Usuario"));
        }

    }

    public function reset(){
        
        $parametros = array(
            "password"  => $this->input->post("password"),
            "token"     => $this->input->post("token")
        );

        if($this->resetpassword_model->CambiarClave($parametros)){
            echo json_encode(array(
                "isValid"=>true,
                "Mensaje"=>"bien"));
        }else{
            echo json_encode(array(
                "isValid"=>true,
                "Mensaje"=>"Mal"));
        }
    }

}

?>