<?php 


namespace App\Core;

class Action {


    protected function render($view, $data = []){

        $viewFile = __DIR__ . '/../Views/'.$view.'.phtml';

        if(file_exists($viewFile)){
            extract($data);
            include $viewFile;
        }else {
             echo "View $view não encontrada!";
        }

    }

    protected function json($data, $statuCode = 200){

        http_response_code($statuCode);
        header('Content-Type: application/json');
        echo json_encode($data);

        exit;
    }

    protected function getPost($key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }
        
}



?>