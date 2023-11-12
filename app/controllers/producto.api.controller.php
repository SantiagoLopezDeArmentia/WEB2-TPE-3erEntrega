<?php
    require_once './app/controllers/api.controller.php';
    require_once './app/models/producto.model.php';
    require_once './app/configurations/config.php';
    require_once './app/helpers/pagination.class.php';

    
    class ProductoApiController extends ApiController {

        // Atributos
        private  $model;
        

        // Constructor

        public function __construct() {
            parent::__construct();
            $this->model = new ProductoModel();
           
        }

        public function sanitizeValue($queryParam) {
            $arrColumnsTable = $this->model->getColumns();

            return in_array($queryParam, $arrColumnsTable);
        }

        public function validateQueryParams() {
            require_once './app/configurations/queries.params.config.php';
            
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
                        echo $obj->defaultTagParam;
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
                $ruta_imagen = $body->ruta_imagen;


                if (!$ruta_imagen) {
                    // Si en la actualizacion no trae una nueva imagen, dejas la existente.
                    $fullPathFile = $producto->ruta_imagen;
                } else {
                    // Si en la actualizacion contiene una nueva imagen, actualizar.
                    $fullPathFile = $this->moveFile($ruta_imagen);
                }

                // Validar que los campos de la peticion contengan datos
                if (empty($nombre) || empty($descripcion) || empty($precio) || empty($fabricante)|| empty($moneda) ) {  
                    $this->view->response("Complete todo los campos", 400);
                } else {
                    $this->model->editarProducto($nombre, $descripcion, $fabricante, $precio, $moneda, $id, $fullPathFile);
                    $this->view->response("Se actualizo correctamente el producto con id $id", 201);
                }
            } else{
                $this->view->response('El producto con id='.$id.' no existe.', 404);
            }
        }  

        //Agregar Producto

        function create() {
            $body = $this->getData();
            $nombre= $body->nombre;
            $descripcion = $body->descripcion;
            $fabricante = $body->id_fabricante;
            $precio = $body->precio;
            $moneda = $body->moneda;
            $ruta_imagen = $body->ruta_imagen;
            
            if (empty($nombre) || empty($descripcion) || empty($precio) || empty($fabricante)|| empty($moneda)|| empty($ruta_imagen)) {
                $this->view->response("Complete los datos", 400);
            } else {

                $fullPathFile = $this->moveFile($ruta_imagen);
                $id = $this->model->agregarProducto($nombre, $descripcion, $fabricante, $precio, $moneda, $fullPathFile);

                // en una API REST es buena práctica es devolver el recurso creado
                $producto = $this->model->getProducto($id);
                $this->view->response($producto, 201);
            }
    
        }

        /* Copiar archivo de la ruta indicada a la local */
        private function moveFile($fromFullFilePath) {
            /* Armar ruta completa del archivo */
            $fileName = basename($fromFullFilePath);
            $toFullPathFile = IMG_FOLDER_PATH . $fileName; 
            /* Copiar archivo a la carpeta local del proyecto */
            copy($fromFullFilePath, $toFullPathFile);
            return $toFullPathFile;
        }
    }                 
       
?> 