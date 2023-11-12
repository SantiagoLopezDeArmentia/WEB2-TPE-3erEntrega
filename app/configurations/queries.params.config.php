<?php
    require_once './app/helpers/pagination.class.php';
    
    $filter = new stdClass();
    $filter->tagParam = "filter";
    $filter->tagValueParam = "filterValue";
    $filter->defaultTagParam = "nombre";
    $filter->defaultValueParam = "";
    $filter->sanitizeValue = true;
    $filter->validateTagPDO = "filter";
    $filter->method = null;

    $sort = new stdClass();
    $sort->tagParam = "sort";
    $sort->tagValueParam = "order";
    $sort->defaultTagParam = "id_producto";
    $sort->defaultValueParam = "asc";
    $sort->sanitizeValue = true;
    $sort->validateTagPDO = "sort";
    $sort->method = null;

    $pagination = new stdClass();
    $pagination->tagParam = "page";
    $pagination->tagValueParam = "limit";
    $pagination->defaultTagParam = "1";
    $pagination->defaultValueParam = "3";
    $pagination->sanitizeValue = false;
    $pagination->validateTagPDO = "pagination";
    $pagination->method = function($obj){
        $obj->defaultTagParam =  Pagination::calcular($obj->defaultTagParam, $obj->defaultValueParam);
    };

    

    $arrQueryParams = [];
    array_push($arrQueryParams, $filter);
    array_push($arrQueryParams, $sort);
    array_push($arrQueryParams, $pagination);
    


?>