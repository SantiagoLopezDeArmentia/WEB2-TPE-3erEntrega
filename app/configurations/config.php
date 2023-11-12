<?php

    /* Datos de la base de datos. */
    define("DATA_BASE_NAME", "productos_gaming");
    define("HOST", "localhost");
    define("USER", 'root');
    define("PASSWORD", "");
    define("CONNECTION_STRING", 'mysql:host=%s;dbname=%s;charset=utf8');
    define("CONNECTION_STRING_CREATE_DATA_BASE", 'mysql:host=%s');

    /* Ruta definida para guardar las imagenes al momento de ingresar un producto. */
    define("IMG_FOLDER_PATH", 'img_productos/');

    /* Se utiliza para la paginacion */
    define("ITEMS_PER_PAGE", 2);

    define("JWT_KEY", "SlAGm")

?>