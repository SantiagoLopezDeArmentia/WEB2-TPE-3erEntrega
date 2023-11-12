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

        //Obtener Productos

        function get($params = []){  
           
            // Filtrado
            if (!$params){
                if (isset($_GET['filter'])){
                    /* Si el filtro [filter] se encuentra vacio,
                    se realiza un filtro por defecto por el campo nombre y valor vacio (La consulta realiza un LIKE por lo que traeria todos los valores).*/
                    $filter = 'nombre'; 
                    $value = "";

                    /* Asignar valores indicados en la consulta */
                    if (!empty($_GET['filter'])) {
                        $filter = $_GET['filter'];
                    }
                    if (!empty($_GET['value'])){
                        $value = $_GET['value'];
                    } 

                    $productos = $this->model->getProductosByFilter($filter, $value);
                    if (empty($productos)){
                        $this->view->response("No hay productos", 400);
                        return;
                    } else {
                        $this->view->response($productos, 200);
                        return;
                    }
                }
            }

            // Traer todos los productos
            if (empty($params[':ID'])) {
                if (isset($_GET['sort'])) {
                    $productos = $this->order("getAllProductByOrder"); // Obtiene productos por ordenamiento
                } else {
                    /* Valor por defecto para el paginado */
                    $page = 1;

                    /* Asignar valores indicados en la consulta */
                    if (!empty($_GET['page'])) {
                        $page = $_GET['page'];
                    }
                    $init = Pagination::calcular($page);
                    $productos = $this->model->getProductos($init, ITEMS_PER_PAGE); // Obtiene todos los productos con paginado
                }
                $this->view->response($productos, 200);
                return;

            } else { 
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
        }

        /* Obtener metodos por ordenamiento */
        private function order($modelMethod){

            /* Valores por defecto para el ordenamiento */
            $sort = 'precio';
            $order = "ASC";

            /* */
            if (!empty($_GET['sort'])) {
                $sort = $_GET['sort'];
            }
            if (!empty($_GET['order'])){
                $order = $_GET['order'];
            }

            $productos = $this->model->$modelMethod($sort, $order);
            if (empty($productos)){
                $this->view->response("No hay productos", 404);
                die;
            }
            return $productos;
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


        private function moveFile($fromFullFilePath) {
            /* Armar ruta completa del archivo */
            $fileName = basename($fromFullFilePath);
            $toFullPathFile = IMG_FOLDER_PATH . $fileName; 
            /* Mover archivo a la carpeta local del proyecto */
            move_uploaded_file($fromFullFilePath, $toFullPathFile);
            return $toFullPathFile;
        }

     
    }                 
       
?> 