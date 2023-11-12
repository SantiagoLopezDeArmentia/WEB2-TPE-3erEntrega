<?php
    require_once './app/configurations/config.php';
    require_once './app/models/bd.model.php';
    class ProductoModel extends Model{

        /* Obtener todos los productos */
        function getAllProducts($arrQueryParams){
            /* Query principal de consulta. */
            $queryString = "SELECT P.*, F.fabricante 
                            FROM productos P 
                            JOIN fabricantes F 
                            ON P.id_fabricante = F.id_fabricante";

            /* Iterar query params para agregar a la consulta en la base de datos. */
            foreach($arrQueryParams as $param) {
                switch($param->validateTagPDO) {
                    case 'filter':
                        /* Se agrega fragmento para filtrado. */
                        $queryString = $queryString . "\nWHERE $param->defaultTagParam = '$param->defaultValueParam'";
                        break;
                    case 'sort':
                        /* Se agrega fragmento para realizar el ordenamiento. */
                        $queryString = $queryString . "\nORDER BY $param->defaultTagParam  $param->defaultValueParam";
                        break;
                    case 'pagination':
                        /* Se agrega fragmento para realizar la paginacion. */
                        $queryString = $queryString . "\nLIMIT $param->defaultTagParam, $param->defaultValueParam";
                        break;
                }
            }
            try {
                $query = $this->dataBase->prepare($queryString);
                $query->execute();

                $productos = $query->fetchAll(PDO::FETCH_OBJ);

                return $productos;
            } catch(Exception $e) {
                return [];
            }
            
        }

        /* Obtener unico producto por ID */
        public function getProducto($id) {
            $query = $this->dataBase->prepare('SELECT P.*, F.fabricante FROM productos P
                                            JOIN fabricantes F 
                                            ON P.id_fabricante = F.id_fabricante
                                            WHERE id_producto = ?');
            $query->execute([$id]);
            return $query->fetch(PDO::FETCH_OBJ);
        }
        
       
        /* Crear un nuevo producto en la base de datos */
        function agregarProducto($nombre, $descripcion, $fabricante, $precio, $moneda,$ruta_imagen) {
            /* Validar que se contengan todos los datos necesarios para cargar el producto */
            $query = $this->dataBase->prepare('INSERT INTO productos (nombre, descripcion,
                                        id_fabricante, ruta_imagen, precio, moneda) 
                                        VALUES (?, ?, ?, ?, ?,?)');
            $query->execute([$nombre, $descripcion, $fabricante,$ruta_imagen, $precio, $moneda]);
            return $this->dataBase->lastInsertId();
        }

       

        /* Actualizar informacion del producto en la base de datos. */
        public function editarProducto($nombre, $descripcion, $fabricante, $precio, $moneda, $id, $fullPathFile) {
            
            $query = $this->dataBase->prepare('UPDATE productos 
                                    SET nombre = ?, descripcion = ?, id_fabricante = ?, precio = ?, moneda = ?, ruta_imagen = ?
                                    WHERE id_producto = ?');
            $query->execute([$nombre, $descripcion, $fabricante, $precio, $moneda, $id, $fullPathFile]);

        }

        /* Obtener columnas de la tabla productos. */
        function getColumns() {
            $query = $this->dataBase->prepare('SHOW COLUMNS FROM productos');
            $query->execute();

            return $query->fetchAll(PDO::FETCH_COLUMN);
        }
        
    }

?>
