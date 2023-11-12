<?php
    require_once './app/configurations/config.php';
    require_once './libs/router.php';

    require_once './app/controllers/producto.api.controller.php';
    require_once './app/controllers/auth.api.controller.php';

    $router = new Router();

    #                 endpoint      verbo     controller           método
    $router->addRoute('productos',     'GET',    'ProductoApiController', 'get'   );
    $router->addRoute('productos',     'POST',   'ProductoApiController', 'create');
    $router->addRoute('productos/:ID', 'GET',    'ProductoApiController', 'get'   );
    $router->addRoute('productos/:ID', 'PUT',    'ProductoApiController', 'update');
   
    $router->addRoute('productos/:ID/:subrecurso', 'GET',    'ProductoApiController', 'get'   );

    $router->addRoute('user/token',     'GET',    'AuthApiController', 'getToken'   );
    
    

    $router->route($_GET['resource'], $_SERVER['REQUEST_METHOD']);