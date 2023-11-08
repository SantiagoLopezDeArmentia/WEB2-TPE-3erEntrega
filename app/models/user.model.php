<?php
    require_once './app/configurations/config.php';
    require_once './app/models/bd.model.php';
    class UserModel extends Model{
        // Atributos

       // private $dataBase;

        // Constructor
       /* public function __construct() {
            $this->dataBase = new PDO(sprintf(CONNECTION_STRING, HOST, DATA_BASE_NAME), USER, PASSWORD);
        }*/

        // Comportamiento

        public function getUser($user) {
            $query = $this->dataBase->prepare('SELECT * FROM usuarios WHERE usuario = ?');
            $query->execute([$user]);

            $user = $query->fetch(PDO::FETCH_OBJ);
            return $user;
        }
    }

?>
