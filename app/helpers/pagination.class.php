<?php
    require_once './app/configurations/config.php';

    class Pagination {

        public static function calcular($page) {
            return ($page-1)*ITEMS_PER_PAGE;
        }

    }


?>