<?php

  namespace Application\Routes;

  include_once(API_PATH . '/ApplicationController.php');

  class Draw
  {
    private static $url_elements;
    private static $routed = false;
    private static $rendered = false;

    public static function init()
    {
      $path_info = $_SERVER['PATH_INFO'];
      $path_info = substr($path_info, 1, strlen($path_info));

      Draw::$url_elements = explode('/', $path_info);
    }

    private static function load($load_string)
    {
      $parts = explode('#', $load_string);

      if(count($parts) === 2)
      {
        $controller_name = strtolower($parts[0]);
        $action_name = strtolower($parts[1]);

        $controller_filename = $controller_name . '_controller';
        $controller_classname = ucfirst($controller_name) . 'Controller';

        include(APP_PATH . '/controllers/' . $controller_filename . '.php');
        $controller = new $controller_classname;

        $controller->$action_name();

        Draw::render($load_string);
      }
      else
      {
        Draw::load('fatal#404');
      }
    }

    private static function render($view_string)
    {
      $parts = explode('#', $view_string);

      if(!Draw::$rendered && count($parts) === 2)
      {
        $controller_name = strtolower($parts[0]);
        $action_name = strtolower($parts[1]);

        ob_start();
        include(APP_PATH . '/views/' . $controller_name . '/' . $action_name . '.html.php');
        define(yield, ob_get_contents());
        ob_end_clean();

        include(APP_PATH . '/views/layouts/application.html.php');
      }
      else
      {
        Draw::render('fatal#404');
      }
    }

    public static function root($root_string)
    {
      if(Draw::$routed)
        return;

      if(1 === count(Draw::$url_elements) && '' === trim(Draw::$url_elements[0]))
      {
        Draw::load($root_string);
        Draw::$routed = true;
      }
    }

    public static function get($get_string, $options = array())
    {
      $options['via'] = 'get';
      Draw::match($get_string, $options);
    }

    public static function post($get_string, $options = array())
    {
      $options['via'] = 'post';
      Draw::match($get_string, $options);
    }

    public static function match($match_string, $options = array())
    {
      if(Draw::$routed)
        return;

      if(!isset($options['via']))
        $options['via'] = 'get';

      if($_SERVER['REQUEST_METHOD'] !== strtoupper($options['via']))
        return;
      
      $match_string = substr($match_string, 1, strlen($match_string));
      $match_string_elements = explode('/', $match_string);

      if(!isset($options['to']))
      {
        $options['to'] = $match_string;

        // Replace the last '/' with '#'
        $slash_pos = strrpos($options['to'], '/');

        if($slash_pos !== false)
          $options['to'] = substr_replace($options['to'], '#', $slash_pos, strlen('/'));
      }

      if(count($match_string_elements) == count(Draw::$url_elements))
      {
        $matched = true;

        for($i = 0; $i < count(Draw::$url_elements); $i++)
        {
          if($match_string_elements[$i][0] === ':')
          {
            $param_name = $match_string_elements[$i][0];
            $param_name = substr($param_name, 1, strlen($param_name));

            $GLOBALS['_params'][$param_name] = $url_elements[$i];
          }
          else if($match_string_elements[$i] !== Draw::$url_elements[$i])
          {
            $matched = false;
            break;
          }
        }

        if($matched)
        {
          Draw::load($options['to']);
          Draw::$routed = true;
        }
      }
    }

    public static function resources($resources_string)
    {
      if(Draw::$routed)
        return;

      if(Draw::$url_elements[0] === $resources_string)
      {
        if(count(Draw::$url_elements) === 1)
        {
          switch($_SERVER['REQUEST_METHOD'])
          {
            case 'GET':
              Draw::load($resources_string . '#' . 'index');
              Draw::$routed = true;
              break;

            case 'POST':
              Draw::load($resources_string . '#' . 'create');
              Draw::$routed = true;
              break;
          }
        }
        else if(count(Draw::$url_elements) === 2 && Draw::$url_elements[1] === 'new' && $_SERVER['REQUEST_METHOD'] == 'GET')
        {
          Draw::load($resources_string . '#' . 'new');
          Draw::$routed = true;
        }
        else if(count(Draw::$url_elements) === 2)
        {
          switch($_SERVER['REQUEST_METHOD'])
          {
            case 'GET':
              Draw::load($resources_string . '#' . 'show');
              $GLOBALS['_params']['id'] = Draw::$url_elements[1];
              Draw::$routed = true;
              break;
            
            case 'PATCH':
            case 'PUT':
              Draw::load($resources_string . '#' . 'update');
              $GLOBALS['_params']['id'] = Draw::$url_elements[1];
              Draw::$routed = true;
              break;

            case 'DELETE':
              Draw::load($resources_string . '#' . 'destroy');
              $GLOBALS['_params']['id'] = Draw::$url_elements[1];
              Draw::$routed = true;
              break;
          }
        }
        else if(count(Draw::$url_elements) === 3 && Draw::$url_elements[2] === 'edit'  && $_SERVER['REQUEST_METHOD'] == 'GET')
        {
          Draw::load($resources_string . '#' . 'edit');
          $GLOBALS['_params']['id'] = Draw::$url_elements[1];
          Draw::$routed = true;
        }
        else
        {
          Draw::load($resources_string . '#' . '404');
          Draw::$routed = true;
        }
      }
    }

    public static function read_config()
    {
      include_once(CONFIG_PATH . '/routes.php');

      if(!Draw::$routed)
      {
        Draw::load('fatal#404');
        Draw::$routed = true;
      }
    }
  }

  Draw::init();

?>
