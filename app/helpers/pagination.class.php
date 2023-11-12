<?php
    require_once './app/configurations/config.php';

    class Pagination {

        public static function calcular($page, $limit) {
            return ($page-1)*$limit;
        }

    }


?>