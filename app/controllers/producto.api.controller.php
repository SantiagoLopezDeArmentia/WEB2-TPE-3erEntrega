<?php
    require_once './app/controllers/api.controller.php';
    require_once './app/models/producto.model.php';
    require_once './app/configurations/config.php';
    require_once './app/helpers/pagination.class.php';
    require_once './app/helpers/authHelper.php';

    
    class ProductoApiController extends ApiController {

        // Atributos
        private  $model;
        private $authHelper;

        // Constructor

        public function __construct() {
            parent::__construct();
            $this->model = new ProductoModel();
            $this->authHelper = new AuthHelper();
        }

        /* Validar si el parametro principal es una columna de la tabla.  */ 
        public function sanitizeValue($queryParam) {
            $arrColumnsTable = $this->model->getColumns();

            return in_array($queryParam, $arrColumnsTable);
        }

        /* Se valid y analiza las query params de la peticion.  */
        public function validateQueryParams() {
            require_once './app/configurations/queries.params.config.php';
            
            $addQueryParams = [];
            foreach($arrQueryParams as $obj) {
                /* Hay query params para procesar.  */
                if (isset($_GET[$obj->tagParam])){
                    
                    /* Agregar valores que se encuentra en el query param. */
                    if (!empty($_GET[$obj->tagParam])) {
                        $obj->defaultTagParam = $_GET[$obj->tagParam];
                    }
                    if (!empty($_GET[$obj->tagValueParam])){
                        $obj->defaultValueParam = $_GET[$obj->tagValueParam];
                    }

                    /* De existir, ejecutar funcion. */
                    if ($obj->method != null ) {
                        $method = $obj->method;
                        $method($obj);
                    }

                    /* Hay que sanitizar los datos */
                    if ($obj->sanitizeValue) {
                        if ($this->sanitizeValue($obj->defaultTagParam)) {
                            array_push($addQueryParams, $obj);
                        } else {
                            /* Hay error en los datos provistos por la peticion. */
                            $this->view->response("Peticion con sentencia incorrecta.", 400);
                            die;
                        }
                    } else {
                        array_push($addQueryParams, $obj);
                    }  
                }
            }
            /* Devolver parametros que cumplieron con los solicitados en la peticion. */
            return $addQueryParams; 
        }

        /* Obtener producto/productos. */
        public function get($params = []) {

            if (!$params) {
                $arr = $this->validateQueryParams();
                $productos = $this->model->getAllProducts($arr);
                if (empty($productos)){
                    $this->view->response("No hay productos", 400);
                    return;
                } else {
                    $this->view->response($productos, 200);
                    return;
                }
            }
            /* Obtener producto por ID */
            $this->getById($params);
        }

        /* Obtener producto por ID. */
        function getById($params) {
            // Traer producto por ID
            $id = $params[':ID'];
            $producto = $this->model->getProducto($id);
            if(!empty($producto)) {
                
                // Subrecurso dinamico
                if(!empty($params[':subrecurso'])) {
                    $subrecurso = $params[':subrecurso'];
                    /* Existe el subrecurso en el item */
                    if (isset($producto->$subrecurso)) { 
                        /* Mostrar subrecurso del producto individual */
                        $this->view->response($producto->$subrecurso, 200);
                    } else {
                        $this->view->response ('El producto no contiene '.$subrecurso.'.', 404);
                    }
                } else { 
                    /* Mostrar producto individual completo */
                    $this->view->response($producto, 200);
                }
            }
        }
        
        //Editar producto
        function update($params =null){
            $user = $this->authHelper->currentUser();
            if (!$user){
                $this->view->response("Unauthorized",401);
                return;
            }
            $id = $params[':ID'];
            $producto=$this->model->getProducto($id);
            // El product con id 'ID' existe en la base de datos?
            if($producto){
                // Reasignar datos en variables individuales
                $body = $this->getData();
                $nombre= $body->nombre;
                $descripcion = $body->descripcion;
                $fabricante = $body->id_fabricante;
                $precio = $body->precio;
                $moneda = $body->moneda;
                //$ruta_imagen = (isset($body->ruta_imagen) && !empty($body->ruta_imagen))? $body->ruta_imagen : null;
                $ruta_imagen = $body->ruta_imagen;
                $oferta = $body->oferta;
                //$oferta = (isset($body->oferta) && !empty($body->oferta))? $body->oferta : $producto->oferta;
                


                if (!$ruta_imagen) {
                    // Si en la actualizacion no trae una nueva imagen, se mantiene la existente.
                    $fullPathFile = $producto->ruta_imagen;
                } else {
                    // Si en la actualizacion contiene una nueva imagen, actualizar.
                    $fullPathFile = $this->moveFile($ruta_imagen);
                }

                // Validar que los campos de la peticion contengan datos
                if (empty($nombre) || empty($descripcion) || empty($precio) || empty($fabricante)|| empty($moneda) ) {  
                    $this->view->response("Complete todo los campos", 400);
                } else {
                    $this->model->editarProducto($nombre, $descripcion, $fabricante,
                    $precio, $moneda, $id, $fullPathFile, $oferta);
                    $this->view->response("Se actualizo correctamente el producto con id $id", 201);
                }
            } else{
                $this->view->response('El producto con id='.$id.' no existe.', 404);
            }
        }  

        //Agregar Producto
        function create() {
            $user = $this->authHelper->currentUser();
            if (!$user){
                $this->view->response("Unauthorized",401);
                return;
            }
            $body = $this->getData();
            $nombre= $body->nombre;
            $descripcion = $body->descripcion;
            $fabricante = $body->id_fabricante;
            $precio = $body->precio;
            $moneda = $body->moneda;
            $ruta_imagen = $body->ruta_imagen;

                       
            if (empty($nombre) || empty($descripcion) || empty($precio) || empty($fabricante)|| empty($moneda)) {
                $this->view->response("Complete los datos", 400);
            } else {

                /* Imagen que se debe utilizar. */
                if (empty($ruta_imagen)) {
                    $fullPathFile = IMG_FOLDER_PATH . DEFAULT_IMG_PRODUCT;
                } else {
                    $fullPathFile = $this->moveFile($ruta_imagen);
                }

                $oferta = (isset($body->oferta) && !empty($body->oferta))? $body->oferta : 0;
                
                $id = $this->model->agregarProducto($nombre, $descripcion, $fabricante,
                $precio, $moneda, $fullPathFile, $oferta);

                // en una API REST es buena práctica es devolver el recurso creado
                $producto = $this->model->getProducto($id);
                $this->view->response($producto, 201);
            }
    
        }

        /* Copiar archivo de la ruta indicada a la local */
        private function moveFile($fromFullFilePath) {
            /* Obtener nombre del archivo. */
            $fileName = basename($fromFullFilePath);
            /* Armar ruta completa del archivo donde se copiara */
            $toFullPathFile = IMG_FOLDER_PATH . $fileName; 
            /* Copiar archivo a la carpeta local del proyecto */
            copy($fromFullFilePath, $toFullPathFile);
            return $toFullPathFile;
        }

     
    }                 
       
?> 