<?php
  
  include_once('routes/draw.php');

  class Application
  {
    public static function init()
    {
      Application\Routes\Draw::read_config();
    }
  }

  /***********************************************
   * LOAD THE ENGINE                             *
   * Down here, we're preparing to boot up!      *
   ***********************************************/

  // Load core helpers
  foreach (glob(ENGINE_PATH . "/helpers/*.php") as $filename)
  {
    include $filename;
  }

  // Establish DB connection
  $db_conf = json_decode(file_get_contents(CONFIG_PATH . '/database.json'), true);
  $con = mysqli_connect($db_conf['hostname'], $db_conf['username'], $db_conf['password'], $db_conf['database']);

  if (mysqli_connect_errno())
  {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
  }

  // Load all models into namespace
  include_once(API_PATH . '/ActiveRecord.php');

  foreach (glob(APP_PATH . "/models/*.php") as $filename)
  {
    include $filename;
  }

  // Bootstrap the application
  Application::init();

?>
