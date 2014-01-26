<?php

  class Rows implements Iterator
  {
    private $the_rows = array();

    public function add($record)
    {
      array_push($this->the_rows, $record);
    }

    public function first()
    {
      return $this->the_rows[0];
    }

    public function rewind()
    {
      return reset($this->the_rows);
    }

    public function current()
    {
      return current($this->the_rows);
    }

    public function key()
    {
      return key($this->the_rows);
    }

    public function next()
    {
      return next($this->the_rows);
    }

    public function valid()
    {
      return key($this->the_rows) !== null;
    }
  }

?>