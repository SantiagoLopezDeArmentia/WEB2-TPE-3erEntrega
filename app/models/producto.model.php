<?php
    require_once './app/configurations/config.php';
    require_once './app/models/bd.model.php';
    class ProductoModel extends Model{

       

        /* Obtener todos los productos */
        function getProductos($init, $end){
            $consulta = "SELECT *
            FROM productos
            LIMIT $init, $end";
            $query = $this->dataBase->prepare($consulta);
            $query->execute();

            $productos = $query->fetchAll(PDO::FETCH_OBJ);
            return $productos;
        }

        /* Obtener unico producto por ID */
        public function getProducto($id) {
            $query = $this->dataBase->prepare('SELECT * FROM productos
                                            WHERE id_producto = ?');
            $query->execute([$id]);
            return $query->fetch(PDO::FETCH_OBJ);
        }
        
        /* Obtener productos por fabricante. */
        function getProductosXFabricante($filter){
            $query = $this->dataBase->prepare('SELECT *, F.fabricante 
                                        FROM productos P 
                                        JOIN fabricantes F 
                                        ON P.id_fabricante = F.id_fabricante
                                        WHERE P.id_fabricante = ?');
            $query->execute([$filter]);
            $productos = $query->fetchAll(PDO::FETCH_OBJ);
            return $productos;
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

        /* Obtener todos los productos por un orden especifico */
        public function getAllProductByOrder($sort, $order) {
            try {
                $consulta = "SELECT * FROM productos ORDER BY $sort $order";
                $query = $this->dataBase->prepare($consulta);
                $query->execute();
                return $query->fetchAll(PDO::FETCH_OBJ);
            } catch (Exception $e) {
                return [];
            }
            
        }


        public function getFilteredProductAndOrder($filter, $value, $sort, $order) {
            try {
                $consulta = "SELECT * FROM productos 
                            WHERE $filter = $value
                            ORDER BY $sort $order";
                $query = $this->dataBase->prepare($consulta);
                $query->execute();
                return $query->fetchAll(PDO::FETCH_OBJ);
            } catch (Exception $e) {
                return [];
            }
            
        }

        /* Obtener productos columna especifica. */
        function getProductosByFilter($filter, $value){
            $consulta = "SELECT * 
                        FROM productos
                        WHERE $filter LIKE '%$value%'";
            $query = $this->dataBase->prepare($consulta);
            $query->execute();
            $productos = $query->fetchAll(PDO::FETCH_OBJ);
            return $productos;
        }

        
    }

?>
