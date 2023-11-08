<?php
    require_once './app/configurations/config.php';
    require_once './app/models/bd.model.php';
    class ProductoModel extends Model{

       

        /* Obtener todos los productos */
        function getProductos(){
            $query = $this->dataBase->prepare('SELECT *, F.fabricante 
                                            FROM productos P 
                                            JOIN fabricantes F 
                                            ON P.id_fabricante = F.id_fabricante');
            $query->execute();

            $productos = $query->fetchAll(PDO::FETCH_OBJ);
            return $productos;
        }


        public function getProducto($id) {
            $query = $this->dataBase->prepare('SELECT * FROM productos WHERE id_producto = ?');
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

        function agregarProducto($nombre, $descripcion, $fabricante, $precio, $moneda,$ruta_imagen) {
            /* Validar que se contengan todos los datos necesarios para cargar el producto */
            $query = $this->dataBase->prepare('INSERT INTO productos (nombre, descripcion,
            id_fabricante, ruta_imagen, precio, moneda) VALUES (?, ?, ?, ?, ?,?)');

            $query->execute([$nombre, $descripcion, $fabricante,$ruta_imagen, $precio, $moneda]);
            return $this->dataBase->lastInsertId();
        }

       

        /* Actualizar informacion del producto en la base de datos. */
        public function editarProducto($nombre, $descripcion, $fabricante, $precio, $moneda, $id) {
            
            $query = $this->dataBase->prepare('UPDATE productos 
                                    SET nombre = ?, descripcion = ?, id_fabricante = ?, precio = ?, moneda = ?
                                    WHERE id_producto = ?');
            $query->execute([$nombre, $descripcion, $fabricante, $precio, $moneda, $id]);

        }

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


        
    }

?>
