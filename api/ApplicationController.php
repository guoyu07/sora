<?php

  class ApplicationController
  {
    private static $rendered = false;

    public function render($view_string)
    {
      $parts = explode('#', $view_string);

      if(!$rendered && count($parts) === 2)
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
        render('fatal#404');
      }
    }
  }

?>
