<?php

namespace App\Core;

class Route
{

	private $routes = [];

	public function __construct()
	{
		$this->initRoutes();
	}

	protected function initRoutes()
	{

		$this->get('/', 'HomeController', 'index');

		$this->post('/convert', 'HomeController', 'convert');
	}

	public function get($path, $controller, $action)
	{

		$this->routes['GET'][$path] = [
			'controller' => $controller,
			'action' => $action
		];
	}

	public function post($path, $controller, $action)
	{

		$this->routes['POST'][$path] = [
			'controller' => $controller,
			'action' => $action
		];
	}

	public function run()
	{

		$method = $_SERVER['REQUEST_METHOD'];
		$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


		if ($path !== '/' && substr($path, -1) === '/') {
			$path = rtrim($path, '/');
		}

		if (isset($this->routes[$method][$path])) {
			$route = $this->routes[$method][$path];
			$controllerName = "App\\Controllers\\" . $route['controller'];
			$action = $route['action'];

			if (class_exists($controllerName)) {
				$controller = new $controllerName();
				if (method_exists($controller, $action)) {
					$controller->$action();
					return;
				}
			}
		}


		http_response_code(404);
		echo "Página não encontrada";
	}
}
