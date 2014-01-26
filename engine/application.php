<?php
  
  include_once('routes/draw.php');

  class Application
  {
    public static function init()
    {
      Application\Routes\Draw::read_config();
    }
  }

  Application::init();

?>
