<?php
require_once './app/controllers/api.controller.php';
require_once './app/models/user.model.php';
require_once './app/configurations/config.php';
require_once './app/helpers/authHelper.php';

class AuthApiController extends ApiController{

    private $model;
    private $authHelper;

    public function __construct() {
        parent::__construct();
        $this->model = new UserModel();
        $this->authHelper = new AuthHelper();
       
    }

    function getToken($params = []) {
        $basic = $this-> authHelper->getAuthHeaders(); // Nos da el header "Authorization:" de la forma "Basic: base64(user:pass)"

        if(empty($basic)){
            $this->view->response("No envio encabezados de autenticacion. " , 401);
            return;
        }

        $basic = explode(" ", $basic); // ["Basic", "base64(user:pass)"]

        if($basic[0]!="Basic"){
            $this->view->response("Los encabezados de autenticacion son incorrectos. " , 401);
            return;
        }

        $userpass = base64_decode($basic[1]); //user:pass
        $userpass = explode(":", $userpass); //["user", "pass"]

        $user = $userpass[0];
        $pass = $userpass[1];

        $userData = $this->model->getUser($user);

        //compruebo si existe usuario  y la contraseña es correcta*/
        if( $userData && password_verify($pass, $userData->contrasenia)){
            
           //usuario es valido
            $token = $this-> authHelper->createToken($userData);//le pasamos la info que trajimos de BBDD (si es admin, el email, la info que queremos almacenar en el token)
            $this->view->response($token); 
        } else{
            $this->view->response("El usuario o contraseña son incorrectos. " , 401);
        }



    }


}