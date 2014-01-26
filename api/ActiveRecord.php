<?php
  
  include_once(ENGINE_PATH . '/db/schema.php');
  include_once(ENGINE_PATH . '/db/rows.php');

  class ActiveRecord
  {
    public static $Schema;
    private $data = array();

    public static function all()
    {
      global $con;
      $result = mysqli_query($con, "SELECT * FROM events");

      $all = new Rows();

      while($row = mysqli_fetch_assoc($result))
      {
        $row = new ActiveRecord($row);
        $all->add($row);
      }

      return $all;
    }

    public static function find($id)
    {
      global $con;
      $result = mysqli_query($con, "SELECT * FROM events WHERE id = $id");

      if($row = mysqli_fetch_assoc($result))
      {
        $row = new ActiveRecord($row);
      }

      return $row;
    }

    public function __construct($assoc)
    {
      foreach($assoc as $key => $value)
        $this->data[$key] = $value;
    }

    public function __get($key)
    {
      return $this->data[$key];
    }

    public function __set($key, $value)
    {
      $this->data[$key] = $value;
    }

    static function init()
    {
      ActiveRecord::$Schema = new Schema();
      ActiveRecord::$Schema->load();
    }
  }

  ActiveRecord::init();

?>