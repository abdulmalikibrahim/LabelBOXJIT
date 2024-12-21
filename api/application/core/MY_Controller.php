<?php
class MY_Controller extends CI_Controller {
    
    public function __construct() {
        parent::__construct();

        // Add CORS headers
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        // header("Content-Type: application/json");

        // Handle preflight OPTIONS request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header("HTTP/1.1 200 OK");
            exit();
        }
    }

    function fb($array)
    {
        http_response_code($array["statusCode"]);
        echo json_encode($array);
        die();   
    }
}
?>