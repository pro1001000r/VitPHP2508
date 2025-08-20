<?php

class Router
{

    private $routes;

    public function __construct() {}

    private function getURI()
    {
        
        if (!empty($_SERVER['REQUEST_URI'])) {

            return trim($_SERVER['REQUEST_URI'], '/');
        }

       
    }

    /**
     * Метод для обработки запроса
     */
    public function run()
    {
        // Получаем строку запроса
        $uri = $this->getURI();



        // * Автороутинг: Разбиение строки на состовляющие Контроллер Экшн и параметры

        if (!empty($uri)) {
            $segments = explode('/', $uri);

            //Удаляем vitphp
            array_shift($segments);

            //VFunc::debug($segments);

            //$controllerName = array_shift($segments) . 'Controller';
            $controllerName = ucfirst(array_shift($segments));
            $actionName = 'action' . ucfirst(array_shift($segments));

            // допинываем оставшийся массив в параметры
            $parameters = $segments;

            // Формируем имя и путь к файлу с классом
            $path = ROOT . '/obmen/' . $controllerName . '.php';

            // Если такой файл существует, подключаем его
            if (is_file($path)) {
                include_once $path;
            }

            // Создать объект, вызвать метод (т.е. action)
            if (class_exists($controllerName)) {

                $controllerObject = new $controllerName;

                /* Вызываем необходимый метод ($actionName) у определенного 
                 * класса ($controllerObject) с заданными ($parameters) параметрами
                 */

                $result = call_user_func_array(array($controllerObject, $actionName), $parameters);

                // Если метод контроллера успешно вызван, завершаем работу роутера
                if ($result != false) {

                    return true;
                }
            }
        }

        return false;
    }
}
