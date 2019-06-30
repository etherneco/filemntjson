<?php

class Controller
{
    public static function displayViews($data, $module)
    {
        extract($data);

        include (APP_ROOT.'views/'.$module.'.php');
    }

    public static function displayJSON($data)
    {
        
        
    }
    
    
    public static function errorHTTP($code, $msg) {
        http_response_code($code);
        echo json_encode(['error' => ['code' => intval($code), 'msg' => $msg]]);
        exit;
    }

    
    
}
