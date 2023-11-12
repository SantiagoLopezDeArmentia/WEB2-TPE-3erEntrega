<?php
    require_once './app/configurations/config.php';

    class AuthHelper{

        

        function base64url_encode($data){
            return rtrim(strtr(base64_encode($data), '+/', '-_'),'=');

        }    

        function getAuthHeaders() {
            $header = "";
            if(isset($_SERVER["HTTP_AUTHORIZATION"])){
                $header = $_SERVER["HTTP_AUTHORIZATION"];
        
            }
            if(isset($_SERVER["REDIRECT_HTTP_AUTHORIZATION"])){
                $header = $_SERVER["REDIRECT_HTTP_AUTHORIZATION"];  
            }

            return $header;

        }


        function createToken($payload) {

            $header = array(
                "alg" => "HS256",
                "typ" => "JWT"
            );

            /* Asignar tiempo de expiracion del token.
            En el archivo de configuracion se puede cambiar la duracion del mismo. */
            $payload->exp = time() + TIMEOUT_EXPIRATION_TOKEN; 

            $header = $this->base64url_encode(json_encode($header));
            $payload = $this->base64url_encode(json_encode($payload));

            $signature = hash_hmac("SHA256", "$header.$payload", JWT_KEY, true);
            $signature = $this->base64url_encode($signature);

            $token = "$header.$payload.$signature";

            return $token;

        }

        function verify($token){
            // $header.$payload.$signature
            $token = explode(".", $token); // [$header, $payload, $signature]
            $header = $token[0];
            $payload = $token[1];
            $signature = $token[2];

            $new_signature = hash_hmac("SHA256", "$header.$payload", JWT_KEY, true);
            $new_signature = $this->base64url_encode($new_signature);

            if($signature != $new_signature){
                return false;
            }

            $payload = json_decode(base64_decode($payload));

            if($payload->exp < time()){ //se corrobora que el token no este vencido
                return false;
            }

            return $payload;

        } 

        function currentUser(){
            $auth = $this->getAuthHeaders(); // viene algo tipo "Bearer $token"
            $auth = explode(" ", $auth); // ["Bearer", "$token"]

            if($auth[0] != "Bearer"){
                return false;
            }

            return $this-> verify($auth[1]); //si eesta todo bien nos devuelve el token

        }

    }    