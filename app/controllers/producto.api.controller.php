<?php
    require_once './app/controllers/api.controller.php';
    require_once './app/models/producto.model.php';

    
    class ProductoApiController extends ApiController {

        // Atributos
        private  $model;
        

        // Constructor

        public function __construct() {
            parent::__construct();
            $this->model = new ProductoModel();
           
        }

        //Obtener Productos

        function get($params = []){

            // Validar utilizando parametros
            if (!$params){
                $filter = $_GET['filter'] ?? null;
                if (!empty($_GET['filter'])) {   
                    $productos = $this->model->getProductosXFabricante($filter);
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
                    $this->view->response("aca", 200);
                    $productos = $this->order("getAllProductByOrder");
                } else {
                    $productos = $this->model->getProductos();
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
                        if (isset($producto->$subrecurso)) { // Existe el subrecurso en el item
                            $this->view->response($producto->$subrecurso, 200);
                        } else {
                            $this->view->response ('El producto no contiene '.$subrecurso.'.', 404);
                        }
                    } else {  
                        $this->view->response($producto, 200);
                    }
                } 
            }
        }

        function order($modelMethod){

            $sort = 'precio';
            $order = "ASC";

            if (!empty($_GET['sort'])) {
                $sort = $_GET['sort'];
            }
            if (!empty($_GET['order'])){
                $order = $_GET['order'];
            }

            $this->view->response("$sort . $order", 200);
            $productos = $this->model->$modelMethod($sort, $order);
            if (empty($productos)){
                $this->view->response("No hay productos", 404);
                die;
            }
            return $productos;
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

                // Validar que los campos de la peticion contengan datos
                if (empty($nombre) || empty($descripcion) || empty($precio) || empty($fabricante)|| empty($moneda) ) {  
                    $this->view->response("Complete todo los campos", 400);
                } else {
                    $this->model->editarProducto($nombre, $descripcion, $fabricante, $precio, $moneda, $id);
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
            $ruta_imagen=$body->ruta_imagen;

            // VER DE SEPARAR ESTA CONDICION, SE REPITE EN AL MENOS 2 SECCIONES DEL MISMO CODIGO
            if (empty($nombre) || empty($descripcion) || empty($precio) || empty($fabricante)|| empty($moneda)|| empty($ruta_imagen)) {
                $this->view->response("Complete los datos", 400);
            } else {
                $id = $this->model->agregarProducto($nombre, $descripcion, $fabricante, $precio, $moneda, $ruta_imagen);

                // en una API REST es buena práctica es devolver el recurso creado
                $producto = $this->model->getProducto($id);
                $this->view->response($producto, 201);
            }
    
        }
    }                 
       
?> 