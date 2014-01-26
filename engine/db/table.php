<?php
  
  class Table
  {
    private $fields;

    public function string($column_name)
    {
      $this->fields[$column_name] = 'string';
    }

    public function text($column_name)
    {
      $this->fields[$column_name] = 'text';
    }
  }

?>