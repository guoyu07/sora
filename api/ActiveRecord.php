<?php
  
  include_once('Relation.php');
  include_once(ENGINE_PATH . '/db/schema.php');

  class ActiveRecord
  {
    public static $Schema;
    private $data = array();
    private $table_name;

    private static function get_table_name($model_name)
    {
      return strtolower($model_name) . 's';
    }

    public static function get_controller_name($model_name)
    {
      return ActiveRecord::get_table_name($model_name);
    }

    private function controller_name()
    {
      return ActiveRecord::get_table_name($this->model_name);
    }

    private function table_name()
    {
      return ActiveRecord::get_table_name($this->model_name);
    }

    /*

     ******************************************************
     *                                                    *
     *      RETREIVING OBJECTS (SINGLE & MULTIPLE)        *
     *                                                    *
     ******************************************************
    
    Retreiving a single object
    --------------------------

    1. Using a primary key
       Event::find(10);

    2. Retrieve a record without any implicit ordering
       Event::take();

    3. Find the first record ordered by primary key
       Event::first();

    4. Find the last record ordered by primary key
       Event::last();

    5. Find the first record matching some conditions
       Event::find_by(array(
         'name' => 'Flawless'
       ));

    Retreiving multiple objects
    ---------------------------

    1. Using multiple primary keys
       Event::find([1, 10]);

    2. Retreive the first number of records specified
       without any implicit ordering
       Event::take(2);

    3. Retreive the first number of records specified
       ordered by primary key
       Event::first(2);

    4. Retreive the first number of records specified
       ordered by primary key
       Event::last(2);

    5. Retreive all objects
       Event::all();

    */

    public static function find($ids)
    {
      global $con;
      $model_name = get_called_class();
      $table_name = ActiveRecord::get_table_name($model_name);

      if(is_string($ids))
      {
        $ids = (int) $ids;
      }

      if(is_int($ids))
      {
        $result = mysqli_query($con, "SELECT * FROM $table_name WHERE (id = $ids) LIMIT 1");

        if($row = mysqli_fetch_assoc($result))
          $row = new $model_name($row);

        return $row;
      }

      else if(is_array($ids))
      {
        $comma_separated_ids = implode(',', $ids);

        $result = mysqli_query($con, "SELECT * FROM $table_name WHERE (id IN ($comma_separated_ids))");

        $rows = new Relation();

        while($row = mysqli_fetch_assoc($result))
        {
          $row = new $model_name($row);
          $rows->add($row);
        }

        return $rows;
      }
    }

    public static function take($n = 1)
    {
      if(!is_int($n))
        return;

      global $con;
      $model_name = get_called_class();
      $table_name = ActiveRecord::get_table_name($model_name);
      $result = mysqli_query($con, "SELECT * FROM $table_name LIMIT $n");

      if($row = mysqli_fetch_assoc($result))
      {
        $row = new $model_name($row);
      }

      return $row;
    }

    public static function first($n = 1)
    {
      if(!is_int($n))
        return;
      
      global $con;
      $model_name = get_called_class();
      $table_name = ActiveRecord::get_table_name($model_name);
      $result = mysqli_query($con, "SELECT * FROM $table_name ORDER BY $table_name.id ASC LIMIT $n");

      if($row = mysqli_fetch_assoc($result))
      {
        $row = new $model_name($row);
      }

      return $row;
    }

    public static function last($n = 1)
    {
      if(!is_int($n))
        return;
      
      global $con;
      $model_name = get_called_class();
      $table_name = ActiveRecord::get_table_name($model_name);
      $result = mysqli_query($con, "SELECT * FROM $table_name ORDER BY $table_name.id DESC LIMIT $n");

      if($row = mysqli_fetch_assoc($result))
      {
        $row = new $model_name($row);
      }

      return $row;
    }

    public static function find_by($where_params = array())
    {
      if(!is_array($where_params))
        return;
      
      $class_name = get_called_class();
      //return $class_name::where($where_params).take();
    }

    public static function all()
    {
      global $con;
      $model_name = get_called_class();
      $table_name = ActiveRecord::get_table_name($model_name);
      $result = mysqli_query($con, "SELECT * FROM $table_name");

      $rows = new Relation();

      while($row = mysqli_fetch_assoc($result))
      {
        $row = new $model_name($row);
        $rows->add($row);
      }

      return $rows;
    }

    /*

     ******************************************************
     *                                                    *
     *                    CONDITIONS                      *
     *                                                    *
     ******************************************************

    Pure string conditions
    ------------------------
    Event.where("contacts_count = '2'")

    Array conditions
    ------------------
    1. Array conditions
       Event.where("name = ?", $event_name);
       Event.where("name = $event_name");

    2. Placeholder conditions
       Event.where("name = :name", array(
         'name' => 'Flawless'
       ));

    Hash conditions
    -----------------
    1. Hash conditions
       Event.where(array(
         'name' => 'Flawless'
       ));

    2. Subset conditions
       Event.where(array(
         'name' => ['Flawless', 'Bug Hunt']
       ));

    Not conditions
    --------------
    Relation allows you to chain .where() conditions with .not():
    Event.where().not(array(
      'name' => 'Flawless'
    ));

    */

    public static function where(/* var args */)
    {
      global $con;
      $model_name = get_called_class();
      $table_name = ActiveRecord::get_table_name($model_name);
      $argc = func_num_args();
      $argv = func_get_args();
      $query = '';

      if($argc == 0)
      {
        $class_name = get_called_class();
        return $class_name::all();
      }
      else if($argc > 0)
      {
        if(is_string($argv[0]))
        {
          // Either: Placeholder condition
          if($argc == 2 && is_array($argv[1]))
          {
            foreach($argv[1] as $key => $value)
              $argv[0] = replace_first(':' . $key, mysqli_real_escape_string($con, $value), $argv[0]);
          }

          // Or: Array condition
          else if($argc > 1)
          {
            for($i = 1; $i < 10; $i++)
              $argv[0] = replace_first('?', mysqli_real_escape_string($con, $argv[$i]), $argv[0]);
          }

          // Else: String condition (nothing to do)
          $query = $argv[0];
        }

        else if(is_array($argv[0]))
        {
          // Hash condition
          $query = "SELECT * FROM $table_name WHERE";
          $glue_needed = false;

          foreach($argv[0] as $key => $value)
          {
            if(is_string($key))
            {
              if($glue_needed)
                $query .= " AND";

              if(is_array($value))
              {
                $values = $value;

                for($i = 0; $i < count($values); $i++)
                  $values[$i] = "'" . mysqli_real_escape_string($con, $values[$i]) . "'";
                
                $comma_separated_values = implode(',', $values);
                $query .= " " . mysqli_real_escape_string($con, $key) . " IN (" . $comma_separated_values . ")";
              }
              else
              {
                $query .= " " . mysqli_real_escape_string($con, $key) . " = '" . mysqli_escape_string($con, $value) . "'";
              }

              $glue_needed = true;
            }
          }
        }
      }

      if(!empty($query))
      {
        $result = mysqli_query($con, $query);

        $rows = new Relation();

        while($row = mysqli_fetch_assoc($result))
        {
          $row = new $model_name($row);
          $rows->add($row);
        }

        return $rows;
      }

      return false;
    }

    /*

     ******************************************************
     *                                                    *
     *                     ORDERING                       *
     *                                                    *
     ******************************************************
    
    1. Order ascending by field
       Event::order('name');

    2. Specifiy direction
       Event::order('name DESC');
       Event::order(['name' => 'desc']);

    3. Order by multiple fields
       Event::order('name', 'id');
       Event::order(array(
        'name',
        'id' => 'desc'
       ));

    */

    public static function order()
    {
      global $con;
      $model_name = get_called_class();
      $table_name = ActiveRecord::get_table_name($model_name);
      $argc = func_num_args();
      $argv = func_get_args();
      $query = '';

      if($argc == 0)
      {
        $class_name = get_called_class();
        return $class_name::all();
      }
      else if($argc == 1 && is_array($argv[0]))
      {
        // Hash ordering
        $query = "SELECT * FROM $table_name ORDER BY";
        $glue_needed = false;

        foreach($argv[0] as $key => $value)
        {
          if($glue_needed)
            $query .= ",";

          if(is_int($key))
            $query .= " " . mysqli_real_escape_string($con, $value);
          else
            $query .= " " . mysqli_real_escape_string($con, $key) . " " . strtoupper(mysqli_real_escape_string($con, $value));

          $glue_needed = true;
        }
      }
      else
      {
        // Array ordering
        $query = "SELECT * FROM $table_name ORDER BY";
        $glue_needed = false;

        for($i = 0; $i < $argc; $i++)
        {
          if($glue_needed)
            $query .= ",";

          $query .= " " . mysqli_real_escape_string($con, $argv[$i]);

          $glue_needed = true;
        }
      }

      print_r($query);

      if(!empty($query))
      {
        $result = mysqli_query($con, $query);

        $rows = new Relation();

        while($row = mysqli_fetch_assoc($result))
        {
          $row = new $model_name($row);
          $rows->add($row);
        }

        return $rows;
      }

      return false;
    }

    /*

     ******************************************************
     *                                                    *
     *                                                    *
     *                                                    *
     ******************************************************

    */

    public function __toString()
    {
      return BASE_URL . '/' . $this->controller_name() . '/' . $this->data['id'];
    }

    public function __construct($assoc)
    {
      $this->model_name = get_class($this);

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