<?php
  
  require_once('table.php');

  class Schema
  {
    private static $tables = array();
    private static $loaded = false;

    public function define()
    {
      return $this;
    }

    public function create_table($table_name)
    {
      $table = new Table($table_name);
      Schema::$tables[$table_name] = $table;
      return $table;
    }

    public function load()
    {
      if(!Schema::$loaded)
        include(DB_PATH . '/schema.php');

      Schema::$loaded = true;
    }
  }

?>